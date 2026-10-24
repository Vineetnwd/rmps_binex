<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('system/header.php');
require_once('system/menu.php');

$branch_id = $_SESSION['branch_id'] ?? null;

$exam_id = $student_class = $student_section = null;
if (isset($_REQUEST['exam_id']))       $exam_id       = $_REQUEST['exam_id'];
if (isset($_REQUEST['student_class'])) $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section'])) $student_section = $_REQUEST['student_section'];
$from_roll = $_REQUEST['from_roll'] ?? '';
$to_roll   = $_REQUEST['to_roll']   ?? '';

/* ---------------------------------------------------------------
 * fmt_val : show number or AB
 * ------------------------------------------------------------- */
function fmt_val($v) {
    if ($v === null || $v === '' || $v === '0' || $v === 0 || $v === '0.00' || $v === 0.0) return 'AB';
    if (!is_numeric($v)) return htmlspecialchars($v);
    if (intval($v) == $v) return intval($v);
    return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
}

/* ---------------------------------------------------------------
 * Principal signature
 * ------------------------------------------------------------- */
$branch_info = get_data('branch_list', $branch_id)['data'] ?? [];
$principal   = 'assets/images/new_sign.png';
if (!empty($branch_info['principal_sign'])) {
    $principal = $base_url . 'upload/' . $branch_info['principal_sign'];
}

/* ---------------------------------------------------------------
 * Total working days per class (for attendance)
 * ------------------------------------------------------------- */
$att = ['NUR' => 212, 'LKG' => 212, 'UKG' => 212];

/* ---------------------------------------------------------------
 * Oral-skill normalisation divisors differ by branch
 * ------------------------------------------------------------- */
function get_os_divisors($branch_id, $subject_name) {
    if ($branch_id != 2) {
        if (in_array($subject_name, ['English', 'Hindi']))  return ['ut' => 8,   'term' => 8];
        if (in_array($subject_name, ['Numbers', 'EVS/GK'])) return ['ut' => 3.5, 'term' => 3.5];
        return ['ut' => 1, 'term' => 1];
    } else {
        if (in_array($subject_name, ['English', 'Hindi']))  return ['ut' => 24,   'term' => 24];
        if (in_array($subject_name, ['Numbers', 'EVS/GK'])) return ['ut' => 10.5, 'term' => 10.5];
        return ['ut' => 1, 'term' => 1];
    }
}

/* ---------------------------------------------------------------
 * Per-subject score calculation for a given term label
 *   Returns: ['pm'=>..., 'os'=>..., 'mt'=>..., 'total'=>...]
 * ------------------------------------------------------------- */
function calc_subject_scores($stu_id, $subs, $subject_name, $term_label, $branch_id) {
    $best = [];
    $term = [];

    foreach ($subs as $sub) {
        $sub_id = $sub['id'] ?? 0;
        $ut1 = (float)(get_subject_total($stu_id, 'UNIT TEST 1', $sub_id, $branch_id) ?? 0);
        $ut2 = (float)(get_subject_total($stu_id, 'UNIT TEST 2', $sub_id, $branch_id) ?? 0);
        // For Term 2 we check UT3 / UT4 if your helper supports that label;
        // if get_subject_total only supports UNIT TEST 1/2 swap the labels:
        if ($term_label === 'TERM 2') {
            $ut1 = (float)(get_subject_total($stu_id, 'UNIT TEST 3', $sub_id, $branch_id) ?? 0);
            $ut2 = (float)(get_subject_total($stu_id, 'UNIT TEST 4', $sub_id, $branch_id) ?? 0);
        }
        $best[$sub['subject_name']] = max($ut1, $ut2);
        $term[$sub['subject_name']] = (float)(get_subject_total($stu_id, $term_label, $sub_id, $branch_id) ?? 0);
    }

    $div = get_os_divisors($branch_id, $subject_name);

    $os1 = ($best[$subject_name] ?? 0) / $div['ut'];
    $os  = ($term[$subject_name] ?? 0) / $div['term'];
    
    // Branch 2 → double oral
    if ($branch_id == 2) {
        $os1 = min($os1 * 2, 20);
        $os  = min($os * 2, 20);
    }
    
    $mt  = $term[$subject_name . ' WS'] ?? 0;
    $mt1 = $best[$subject_name . ' WS'] ?? 0;
    
    // Pre Mid calculation
    $pm = (($mt1 + $os1) * 3) / 5;
    
    // 🔥 Fix overflow
    $pm = min($pm, 30);
    
    $total = $pm + $os + $mt;
    // $mt  = $term[$subject_name . ' WS'] ?? 0;
    // $mt1 = $best[$subject_name . ' WS'] ?? 0;

    // $pm    = (($mt1 + $os1) * 3) / 5;
    // $total = $pm + $os + $mt;

    return [
        'pm'    => round($pm,    2),
        'os'    => round($os,    2),
        'mt'    => round($mt,    2),
        'total' => round($total, 2),
    ];
}
?>
<style>
    body { font-family: "Helvetica", Arial, sans-serif; color:#222; background: #fff; }
    .content-wrapper { text-transform: uppercase !important; font-size:14px !important; font-weight:400 !important; }
    .report-card { min-height: 297mm !important; margin: 0px; border: 1px solid #cfcfcf; background:#fff; box-shadow: none; }
    
    .rc-header { background: #b20d0c; color: #fff; text-align:center; font-weight:700; letter-spacing:0.6px; }
    .rc-header2 { background: #f5bb03; color: #000; padding:8px 12px; text-align:center; font-weight:700; letter-spacing:0.6px; }
    .rc-top { display:flex; padding:10px; border:1px solid #e0e0e0; margin-top:10px; align-items:center; gap:10px; }
    .stu-left, .stu-right { width: 34%; }
    .stu-center { width: 32%; text-align:left; }
    .stu-left p, .stu-right p { margin:6px 2px; text-transform: uppercase;font-size:14 px;font-weight:600 }
    .info-label { color:#555; font-size:12px; display:inline-block; width:110px; }
    .info-value { font-weight:700; text-transform: uppercase;font-size:14px;font-weight:600 display:inline-block; }

    .stu-photo { width:88px; height:128px; border-radius:6px; border:1px solid #ddd; object-fit:cover; }
    /*table{width:100% !important;};*/
    table.sch-table { width:100%; border-collapse: collapse; text-transform: uppercase;font-size:14px;font-weight:600 }
    table.sch-table th, table.sch-table td { border:1px solid #607d8b; padding:1px 6px; vertical-align:middle; text-align:center; font-size:12px;text-transform: uppercase;font-weight:400}
    table.sch-table th.subject { text-align:left; padding-left:10px; background:#e4f8cd; font-weight:700; }
    table.sch-table td.subject { text-align:left; padding-left:10px; }
    .term1-head { background:#f5bb03; }
    .term2-head { background:#b20c0b;color:#fff; }
    .final-head { background:#73eb78; }

    .rc-bottom { display:flex; gap:12px; margin-top:12px; }
    .co-scholastic { width:38%; border:1px solid #e0e0e0; padding:8px; text-transform: uppercase;font-size:14px;font-weight:600 }
    .attendance { width:26%; border:1px solid #e0e0e0; padding:8px; text-transform: uppercase;font-size:14px;font-weight:600 }
    .performance { width:36%; border:1px solid #e0e0e0; padding:8px; text-transform: uppercase;font-size:14px;font-weight:600 }

    .signatures { display:flex; justify-content:space-between; margin-top:18px; align-items:flex-end; }
    .sign-box { width:30%; text-align:center; text-transform: uppercase;font-size:14px;font-weight:600 }
    .sign-line { border-top:1px solid #444; margin-top:10px; padding-top:6px; }
    @media print {
        @page { size: a4 portrait; margin: 0; }
        body  { margin:0; padding:0; background:#fff !important; }
        .report-card { page-break-after:always; background:#fff !important; height:297mm !important; }
        .no_print { display:none !important; }
    }
</style>

<div class="content-wrapper">
<section class="content-header">
    <h1>Tabulation / Report Cards</h1>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="breadcrumb-item active">Report Cards</li>
    </ol>
</section>

<section class="content">
<div class="box box-default">
    <div class="box-header with-border no_print">
        <h3 class="box-title">Print Report Cards</h3>
    </div>
    <div class="box-body no_print">
        <form action="" method="post" class="form-inline">
            <div class="form-group" style="margin-right:12px;">
                <label>Select Class&nbsp;</label>
                <select class="form-control" name="student_class" required>
                    <option value="" disabled <?= (!$student_class) ? 'selected' : '' ?>>Select Class</option>
                    <option value="NUR" <?= ($student_class == 'NUR') ? 'selected' : '' ?>>NUR</option>
                    <option value="LKG" <?= ($student_class == 'LKG') ? 'selected' : '' ?>>LKG</option>
                    <option value="UKG" <?= ($student_class == 'UKG') ? 'selected' : '' ?>>UKG</option>
                </select>
            </div>

            <div class="form-group" style="margin-right:12px;">
                <label>Select Section&nbsp;</label>
                <select class="form-control" name="student_section" required>
                    <?php dropdown($section_list, $student_section); ?>
                </select>
            </div>

            <div class="form-group" style="margin-right:12px;">
                <label>Select Exam&nbsp;</label>
                <select class="form-control" name="exam_id" required>
                    <option value=""      disabled <?= (!$exam_id) ? 'selected' : '' ?>>Select Term</option>
                    <option value="TERM 1" <?= ($exam_id == 'TERM 1') ? 'selected' : '' ?>>TERM 1</option>
                    <option value="TERM 2" <?= ($exam_id == 'TERM 2') ? 'selected' : '' ?>>TERM 2</option>
                    <option value="both"   <?= ($exam_id == 'both')   ? 'selected' : '' ?>>Both (Annual)</option>
                </select>
            </div>

            <div class="form-group" style="margin-right:12px;">
                <label>From Roll&nbsp;</label>
                <input type="text" name="from_roll" class="form-control" value="<?= htmlspecialchars($from_roll) ?>">
            </div>
            <div class="form-group" style="margin-right:12px;">
                <label>To Roll&nbsp;</label>
                <input type="text" name="to_roll" class="form-control" value="<?= htmlspecialchars($to_roll) ?>">
            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-primary" value="Show & Print">
            </div>
        </form>
    </div>

<?php
if (!empty($student_class) && !empty($student_section) && !empty($exam_id)):

    $branch_id = $_SESSION['branch_id'];

    /* --- fetch students --- */
    if ($from_roll !== '') {
        $sql = "SELECT * FROM student
                WHERE student_class='$student_class'
                  AND student_section='$student_section'
                  AND student_roll BETWEEN $from_roll AND $to_roll
                  AND status IN ('ACTIVE','PROMOTED')
                  AND branch_id=$branch_id";
    } else {
        $sql = "SELECT * FROM student
                WHERE student_class='$student_class'
                  AND student_section='$student_section'
                  AND status='ACTIVE'
                  AND branch_id=$branch_id";
    }
    $students = direct_sql($sql)['data'] ?? [];
    usort($students, function($a, $b) { return (int)$a['student_roll'] - (int)$b['student_roll']; });

    /* --- subjects --- */
    $subjects = subject_list($student_class) ?? [];
    $merged_subjects = [];
    foreach ($subjects as $sub) {
        $sub_id   = is_array($sub) ? $sub['id'] : $sub;
        $sub_name = get_data('subject', $sub_id, 'subject_name')['data'] ?? '';
        $merged_subjects[] = ['id' => $sub_id, 'subject_name' => $sub_name];
    }

    /* --- branch photo path --- */
    $img_url_map = [1 => 'Bishambharpur', 2 => 'Barauli', 4 => 'Barharia'];
    $img_url = $base_url . 'student_photo/' . ($img_url_map[$branch_id] ?? 'Bishambharpur') . '/Photo/';

    $branch_images = [1 => 'assets/images/b1.JPG', 2 => 'assets/images/b2.JPG', 4 => 'assets/images/b3.JPG'];

    /* --- build merged subject map (base name → subs) --- */
    $merged_map = [];
    foreach ($merged_subjects as $sub) {
        $base = str_replace(' WS', '', $sub['subject_name']);
        $merged_map[$base][] = $sub;
    }

    /* ================================================================
     * STUDENT LOOP
     * ============================================================== */
    foreach ($students as $stu):
        /* ------- exam heading ------- */
        if ($exam_id === 'TERM 1') {
            $exam_heading = 'TERM 1 EXAMINATION 2025-2026';
        } elseif ($exam_id === 'TERM 2') {
            $exam_heading = 'TERM 2 EXAMINATION 2025-2026';
        } else {
            $exam_heading = 'ANNUAL EXAMINATION 2025-2026';
        }

        /* ------- pre-compute all subject scores for chart + table ------- */
        $scores_t1    = [];   // [subject_name => ['pm','os','mt','total']]
        $scores_t2    = [];
        $scores_final = [];   // averaged

        foreach ($merged_map as $subject_name => $subs) {
            if ($exam_id === 'TERM 1' || $exam_id === 'both') {
                $scores_t1[$subject_name] = calc_subject_scores($stu['id'], $subs, $subject_name, 'TERM 1', $branch_id);
            }
            if ($exam_id === 'TERM 2' || $exam_id === 'both') {
                $scores_t2[$subject_name] = calc_subject_scores($stu['id'], $subs, $subject_name, 'TERM 2', $branch_id);
            }
            if ($exam_id === 'both') {
                $t1tot = $scores_t1[$subject_name]['total'] ?? 0;
                $t2tot = $scores_t2[$subject_name]['total'] ?? 0;
                $scores_final[$subject_name] = round(($t1tot + $t2tot) / 2, 2);
            }
        }

        /* grand totals */
        $gt1 = array_sum(array_column($scores_t1, 'total'));
        $gt2 = array_sum(array_column($scores_t2, 'total'));
        $gt_final = array_sum($scores_final);

        $sub_count = count($merged_map) ?: 1;

        /* ------- chart data ------- */
        if ($exam_id === 'both') {
            $chartData = [['Subject', 'TERM 1', 'TERM 2']];
            foreach ($merged_map as $sn => $subs) {
                $chartData[] = [$sn, (float)($scores_t1[$sn]['total'] ?? 0), (float)($scores_t2[$sn]['total'] ?? 0)];
            }
        } else {
            $chartData = [['Subject', 'Marks', ['role' => 'annotation']]];
            $src = ($exam_id === 'TERM 1') ? $scores_t1 : $scores_t2;
            foreach ($merged_map as $sn => $subs) {
                $v = (float)($src[$sn]['total'] ?? 0);
                $chartData[] = [$sn, $v, $v];
            }
        }
?>

<div class="report-card">

    <!-- Branch Logo -->
    <div style="text-align:center; margin-bottom:10px; min-height:170px">
        <?php if (!empty($branch_images[$branch_id])): ?>
            <img src="<?= htmlspecialchars($branch_images[$branch_id]) ?>" height="170px" width="100%" alt="Branch Logo">
        <?php endif; ?>
    </div>

    <div class="rc-header" style="font-size:20px; padding:0px">MY ACHIEVEMENT REPORT</div>
    <div class="rc-header2" style="padding:0px"><?= htmlspecialchars($exam_heading) ?></div>

    <!-- Student Profile -->
    <table style="width:100%; border-collapse:collapse; font-family:'Segoe UI',Arial,sans-serif; border:1px solid #000; margin-bottom:8px;">
        <tr style="background:#f2f2f2;">
            <td colspan="2" style="font-size:14px; color:#000; text-align:center; border:1px solid #000; padding:0px;">STUDENT PROFILE</td>
            <td rowspan="5" style="width:110px; text-align:center; border:1px solid #000; vertical-align:middle; background:#fafafa;">
                <img src="<?= htmlspecialchars($img_url . ($stu['student_admission'] ?? '')) ?>.jpg"
                     alt="Photo"
                     style="width:95px; height:120px; border:1px solid #ccc; border-radius:8px; object-fit:cover; box-shadow:0 2px 5px rgba(0,0,0,.1);">
            </td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Name:</strong> <?= htmlspecialchars($stu['student_name'] ?? '') ?></td>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Class:</strong> <?= htmlspecialchars(($stu['student_class'] ?? '') . ' - ' . ($stu['student_section'] ?? '')) ?></td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Father's Name:</strong> <?= htmlspecialchars($stu['student_father'] ?? '') ?></td>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Roll No.:</strong> <?= htmlspecialchars($stu['student_roll'] ?? '') ?></td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Mother's Name:</strong> <?= htmlspecialchars($stu['student_mother'] ?? '') ?></td>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Admission No.:</strong> <?= htmlspecialchars($stu['student_admission'] ?? '') ?></td>
        </tr>
        <tr>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Date of Birth:</strong> <?= !empty($stu['date_of_birth']) ? date('d-m-Y', strtotime($stu['date_of_birth'])) : '' ?></td>
            <td style="border:1px solid #000; padding-left:8px;"><strong>Contact:</strong> <?= htmlspecialchars($stu['student_mobile'] ?? '') ?></td>
        </tr>
    </table>

    <!-- Graphical Representation -->
    <div style="flex:1; border:1px solid #ccc; border-radius:5px; padding:10px; margin-bottom:10px;">
        <div style="background:#525252; font-size:16px; font-weight:700; color:#fff; padding-left:5px;">
            GRAPHICAL REPRESENTATION OF RESULT
        </div>

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', { packages: ['corechart'] });
            google.charts.setOnLoadCallback(drawChart<?= $stu['student_admission'] ?>);

            function drawChart<?= $stu['student_admission'] ?>() {
                var data = google.visualization.arrayToDataTable(<?= json_encode($chartData, JSON_NUMERIC_CHECK) ?>);

                var options = {
                    title: 'Student Performance',
                    chartArea: { width: '70%', height: '70%' },
                    hAxis: { title: 'Subjects', slantedText: false, textStyle: { fontSize: 8 } },
                    vAxis: {
                        title: 'Marks',
                        viewWindow: { min: 0, max: 100 },
                        ticks: [0, 20, 40, 60, 80, 100],
                        minorGridlines: { count: 0 },
                        baselineColor: '#000',
                        gridlines: { color: '#ccc' }
                    },
                    legend: { position: 'top' },
                    colors: ['#f5bb03', '#b20c0b'],
                    annotations: { alwaysOutside: true, textStyle: { fontSize: 12, color: '#000', bold: true } },
                    bar: { groupWidth: '60%' }
                };

                var chart = new google.visualization.ColumnChart(
                    document.getElementById('columnchart_values_<?= htmlspecialchars($stu['student_admission']) ?>')
                );
                chart.draw(data, options);
            }
        </script>
        <div id="columnchart_values_<?= htmlspecialchars($stu['student_admission']) ?>" style="width:950px; height:270px;"></div>
    </div>

    <!-- ============================================================
         SCHOLASTIC AREAS TABLE  — mirrors the Class I-X layout
         ========================================================== -->
    <div class="rc-header">SCHOLASTIC AREAS</div>
    <table class="sch-table" aria-label="Scholastic">
        <thead>
            <tr bgcolor="lightyellow">
                <th class="subject"></th>
                <?php if ($exam_id === 'TERM 1' || $exam_id === 'both'): ?>
                    <th class="term1-head" colspan="5">TERM 1</th>
                <?php endif; ?>
                <?php if ($exam_id === 'TERM 2' || $exam_id === 'both'): ?>
                    <th class="term2-head" colspan="5" style='font-weight:800'>TERM 2</th>
                <?php endif; ?>
                <?php if ($exam_id === 'both'): ?>
                    <th class="final-head" colspan="2">FINAL</th>
                <?php endif; ?>
            </tr>
            <tr style="background-color:lightyellow">
                <th class="subject text-left">Subject</th>
                <?php if ($exam_id === 'TERM 1' || $exam_id === 'both'): ?>
                    <th style="background-color:#f5bb03">Pre Mid (30)</th>
                    <th style="background-color:#f5bb03">Oral Skill (20)</th>
                    <th style="background-color:#f5bb03">Term 1 (50)</th>
                    <th style="background-color:#f5bb03">Total (100)</th>
                    <th style="background-color:#f5bb03">Grade</th>
                <?php endif; ?>
                <?php if ($exam_id === 'TERM 2' || $exam_id === 'both'): ?>
                    <th style="background-color:#b20c0b;color:#fff;font-weight:800">Post Mid (30)</th>
                    <th style="background-color:#b20c0b;color:#fff;font-weight:800">Oral Skill (20)</th>
                    <th style="background-color:#b20c0b;color:#fff;font-weight:800">Term 2 (50)</th>
                    <th style="background-color:#b20c0b;color:#fff;font-weight:800">Total (100)</th>
                    <th style="background-color:#b20c0b;color:#fff;font-weight:800">Grade</th>
                <?php endif; ?>
                <?php if ($exam_id === 'both'): ?>
                    <th style="background-color:#73ea78">TOTAL</th>
                    <th style="background-color:#73ea78">GRADE</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($merged_map as $subject_name => $subs): ?>
            <tr>
                <td class="subject text-left"><?= htmlspecialchars($subject_name) ?></td>

                <?php if ($exam_id === 'TERM 1' || $exam_id === 'both'):
                    $s1 = $scores_t1[$subject_name] ?? ['pm'=>0,'os'=>0,'mt'=>0,'total'=>0];
                    $g1 = pre_grade($s1['total']);
                ?>
                    <td><?= fmt_val($s1['pm'])    ?></td>
                    <td><?= fmt_val($s1['os'])    ?></td>
                    <td><?= fmt_val($s1['mt'])    ?></td>
                    <td><?= fmt_val($s1['total']) ?></td>
                    <td><?= htmlspecialchars($g1) ?></td>
                <?php endif; ?>

                <?php if ($exam_id === 'TERM 2' || $exam_id === 'both'):
                    $s2 = $scores_t2[$subject_name] ?? ['pm'=>0,'os'=>0,'mt'=>0,'total'=>0];
                    $g2 = pre_grade($s2['total']);
                ?>
                    <td><?= fmt_val($s2['pm'])    ?></td>
                    <td><?= fmt_val($s2['os'])    ?></td>
                    <td><?= fmt_val($s2['mt'])    ?></td>
                    <td><?= fmt_val($s2['total']) ?></td>
                    <td><?= htmlspecialchars($g2) ?></td>
                <?php endif; ?>

                <?php if ($exam_id === 'both'):
                    $fin = $scores_final[$subject_name] ?? 0;
                    $gf  = pre_grade($fin);
                ?>
                    <td><?= fmt_val($fin) ?></td>
                    <td><?= htmlspecialchars($gf) ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <?php
                /* ----- TERM 1 footer ----- */
                if ($exam_id === 'TERM 1' || $exam_id === 'both') {
                    $per1 = ($gt1 > 0) ? number_format($gt1 / $sub_count, 2) : 'AB';
                    $gr1  = pre_grade_marks($gt1, $sub_count);
                    echo "<td colspan='3' style='background:#f5bb03;color:#000;height:30px;font-weight:800;'>Total: " . fmt_val($gt1) . "</td>";
                    echo "<td colspan='2' style='background:#f5bb03;color:#000;height:30px;font-weight:800;'>Percentage: {$per1}%</td>";
                    echo "<td colspan='1' style='background:#f5bb03;color:#000;height:30px;font-weight:800;'>Grade: " . htmlspecialchars($gr1) . "</td>";
                } else {
                    /* placeholder columns so Term 2 footer aligns */
                }

                /* ----- TERM 2 footer ----- */
                if ($exam_id === 'TERM 2' || $exam_id === 'both') {
                    $per2 = ($gt2 > 0) ? number_format($gt2 / $sub_count, 2) : 'AB';
                    $gr2  = pre_grade_marks($gt2, $sub_count);
                    echo "<td colspan='2' style='background:#b20c0b;color:#fff;height:30px;font-weight:800;'>Total: " . fmt_val($gt2) . "</td>";
                    echo "<td colspan='2' style='background:#b20c0b;color:#fff;height:30px;font-weight:800;'>Percentage: {$per2}%</td>";
                    echo "<td colspan='1' style='background:#b20c0b;color:#fff;height:30px;font-weight:800;'>Grade: " . htmlspecialchars($gr2) . "</td>";
                }

                /* ----- FINAL footer (both) ----- */
                if ($exam_id === 'both') {
                    $per_f = ($gt_final > 0) ? number_format(($gt_final / ($sub_count * 100)) * 100, 2) : 'AB';
                    $gr_f  = pre_grade_marks($gt_final, $sub_count);
                    echo "<td colspan='2' style='background:#73ea78;color:#000;height:30px;font-weight:800;text-align:left;min-width:150px'>Total: " . fmt_val($gt_final) . " <br> 
                    Per: {$per_f}% <br> 
                    Grade: " . htmlspecialchars($gr_f) . "</td>";
                }
                ?>
            </tr>
        </tfoot>
    </table>

    <!-- Co-Scholastic + Remarks -->
    <div style="width:100%; display:flex; gap:20px; margin-top:2px;">

        <!-- LEFT: Co-Scholastic & Attendance -->
        <div style="flex:0 0 29%;">
            <table style="width:100%; border-collapse:collapse; border:1px solid #ccc;">
                <tr class="rc-header"><th colspan="2" style="padding:10px;font-weight:800">Co-Scholastic Areas</th></tr>
                <tr style="background:#eef5fc;"><td style="padding:4px 6px;font-size:11px">Discipline &amp; Moral Values</td><td>A</td></tr>
                <tr style="background:#eef5fc;"><td style="padding:4px 6px;font-size:11px">Art Education</td><td>A</td></tr>
                <tr style="background:#eef5fc;"><td style="padding:4px 6px;font-size:11px">Work Education</td><td>A</td></tr>
                <tr style="background:#eef5fc;"><td style="padding:4px 6px;font-size:11px">Health &amp; Physical Education</td><td>A</td></tr>
                <tr style="background:#eef5fc;">
                    <td style="padding:4px 6px;font-size:11px">Attendance</td>
                    <td style="font-size:16px; font-weight:700; color:#2d8cf0;">
                        <?php
                        $st_att = get_data('attendance', $stu['id'], 'present_days', 'student_id')['data'] ?? 0;
                        $t_att  = $att[$student_class] ?? 0;
                        echo intval($st_att) . ' / ' . intval($t_att);
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <!-- RIGHT: Subject Remarks -->
        <div style="flex:0 0 69%;">
            <table style="width:100%; border-collapse:collapse; text-align:center; font-size:14px;">
                <thead>
                    <tr class="rc-header">
                        <th style="padding:8px; border:1px solid #cfcfcf;font-weight:800">Subject</th>
                        <th style="padding:8px; border:1px solid #cfcfcf;font-weight:800">Remarks</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                /* Use the final/t1 grade for remarks */
                $grade_for_remark = ($exam_id === 'both')
                    ? pre_grade_marks($gt_final, $sub_count)
                    : (($exam_id === 'TERM 1') ? pre_grade_marks($gt1, $sub_count) : pre_grade_marks($gt2, $sub_count));

                /* Map pre-grade to standard grade scheme for get_remarks */
                $grade_map = ['A+'=>'A1','A'=>'A2','B+'=>'B1','B'=>'B2','C'=>'C1','D'=>'D','E'=>'E'];
                $remark_grade = $grade_map[$grade_for_remark] ?? '';

                foreach ($merged_map as $subject_name => $subs) {
                    $s_id = '';
                    if (strtoupper($subject_name) === 'NUMBERS') $s_id = 36;
                    if (strtoupper($subject_name) === 'EVS/GK')  $s_id = 37;

                    $sub_rem = get_remarks($student_class, $s_id, $remark_grade, $subject_name);
                    if (!empty($sub_rem)):
                ?>
                    <tr style="background:#f7fbff;">
                        <td style="padding:2px 3px; border:1px solid #cfcfcf; text-align:left; font-size:11px"><?= htmlspecialchars($subject_name) ?></td>
                        <td style="padding:2px 3px; border:1px solid #cfcfcf; text-align:left; font-size:11px"><?= htmlspecialchars($sub_rem) ?></td>
                    </tr>
                <?php endif; } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grading Scale -->
    <p class="rc-header" style="text-align:center; margin:4px 0; font-size:14px; font-weight:600;">8 Point Grading Scale</p>
    <table style="width:100%; border-collapse:collapse; text-align:center; font-size:14px;">
        <thead>
            <tr style="background:#b20c0b; color:#fff;">
                <th style="padding:1px 2px; border:1px solid #cfcfcf;font-weight:800">Marks Range</th>
                <th style="padding:1px 2px; border:1px solid #cfcfcf;font-weight:800">Grade</th>
                <th style="padding:1px 2px; border:1px solid #cfcfcf;font-weight:800">Marks Range</th>
                <th style="padding:1px 2px; border:1px solid #cfcfcf;font-weight:800">Grade</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background:#f7fbff;"><td style="border:1px solid #cfcfcf;">91 – 100</td><td style="border:1px solid #cfcfcf;">A1</td><td style="border:1px solid #cfcfcf;">81 – 90.99</td><td style="border:1px solid #cfcfcf;">A2</td></tr>
            <tr style="background:#f7fbff;"><td style="border:1px solid #cfcfcf;">71 – 80.99</td><td style="border:1px solid #cfcfcf;">B1</td><td style="border:1px solid #cfcfcf;">61 – 70.99</td><td style="border:1px solid #cfcfcf;">B2</td></tr>
            <tr style="background:#f7fbff;"><td style="border:1px solid #cfcfcf;">51 – 60.99</td><td style="border:1px solid #cfcfcf;">C1</td><td style="border:1px solid #cfcfcf;">41 – 50.99</td><td style="border:1px solid #cfcfcf;">C2</td></tr>
            <tr style="background:#f7fbff;"><td style="border:1px solid #cfcfcf;">33 – 40.99</td><td style="border:1px solid #cfcfcf;">D</td><td style="border:1px solid #cfcfcf;">0 – 32.99</td><td style="border:1px solid #cfcfcf;">E (NEEDS IMPROVEMENT)</td></tr>
        </tbody>
    </table>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sign-box"><div class="sign-line">Class Teacher</div></div>
        <div class="sign-box">
            <img src="<?= htmlspecialchars($principal) ?>" width="180" height="50" alt="Principal Sign">
            <div class="sign-line">Principal</div>
        </div>
    </div>

</div><!-- /.report-card -->

<?php
    endforeach; // end student loop
endif; // end if selection
?>
</div><!-- /.box -->
</section>
</div><!-- /.content-wrapper -->

<?php require_once('system/footer2.php'); ?>