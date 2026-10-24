<?php
require_once('system/header.php');
require_once('system/menu.php');

$exam_id = $student_class = $student_section = null;
if (isset($_REQUEST['exam_id']))
    $exam_id = $_REQUEST['exam_id'];
if (isset($_REQUEST['student_class']))
    $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section']))
    $student_section = $_REQUEST['student_section'];

function fmt_val($v)
{
    if ($v === null || $v === '' || $v === '0' || $v === 0 || $v === '0.00' || $v === 0.0)
        return 'AB';
    if (!is_numeric($v))
        return htmlspecialchars($v);
    if (intval($v) == $v)
        return intval($v);
    return rtrim(rtrim(number_format((float) $v, 2, '.', ''), '0'), '.');
}

$branch_id = $_SESSION['branch_id'] ?? 1;
$principal = get_data('branch_list', $branch_id)['data'] ?? '';
$principal = $principal ? $base_url . 'upload/' . $principal['principal_sign'] : 'assets/images/new_sign.png';

$class_list = array_column(direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}' ORDER BY student_class ASC")['data'] ?? [], 'student_class');
$section_list = array_column(direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}' ORDER BY student_section ASC")['data'] ?? [], 'student_section');
?>
<style>
    .content-wrapper {
        text-transform: uppercase !important;
        font-size: 12px !important;
        font-weight: 400 !important;
    }

    /* ── REPORT CARD: flex column ─────────────────────────── */
    .report-card {
        min-height: 297mm;
        height: auto;
        width: 100%;
        margin: 0 0 25px 0;
        padding: 8px 12px;
        box-sizing: border-box;
        background: #fff;
        border: 1px solid #cfcfcf;
        display: flex;
        flex-direction: column;
        overflow: visible;
        page-break-after: always;
        page-break-inside: avoid;
    }

    /* ── HEADERS ────────────────────────────────────────────────────────────── */
    .rc-header {
        background: #CC0000;
        color: #fff;
        text-align: center;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 3px 5px;
        font-size: 16px;
        flex-shrink: 0;
    }

    .rc-header2 {
        background: #FFCC00;
        color: #000;
        text-align: center;
        font-weight: 700;
        letter-spacing: 1px;
        padding: 3px 5px;
        font-size: 12px;
        border-bottom: 2px solid #CC0000;
        flex-shrink: 0;
    }

    /* ── STUDENT INFO TABLE ─────────────────────────────────────────────────── */
    .stu-info-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3px;
        flex-shrink: 0;
    }

    .stu-info-table td {
        border: 1px solid #333;
        padding: 4px 5px;
        font-weight: 700;
        font-size: 11px;
        vertical-align: middle;
    }

    .stu-info-table td.label-cell {
        background: #C5E0B4;
        width: 20%;
    }

    .stu-photo-cell {
        width: 15%;
        border: 1px solid #CC0000 !important;
        background: #CC0000;
        padding: 4px;
        text-align: center;
        vertical-align: middle;
    }

    .stu-photo-circle {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #000;
        display: inline-block;
        object-fit: cover;
    }

    /* ── MARKS TABLE ─────────────────────────────────────────────────────────── */
    .marks-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3px;
        border: 1px solid #000;
        table-layout: fixed;
        flex-shrink: 0;
    }

    .marks-table th,
    .marks-table td {
        border: 1px solid #000;
        padding: 3px 4px;
        text-align: center;
        font-size: 11px;
        font-weight: 700;
        vertical-align: middle;
    }

    .marks-table th.subj-head {
        background: #FFCC00;
        text-align: left;
    }

    .marks-table th.mark-head {
        background: #A9D18E;
    }

    .marks-table td.subj-name {
        text-align: left;
        font-weight: 600;
    }

    /* ── TOTALS BAR ─────────────────────────────────────────────────────────── */
    .totals-bar {
        background: #1F4E79;
        color: #fff;
        display: flex;
        font-weight: 700;
        margin-top: 3px;
        padding: 4px 8px;
        border: 1px solid #000;
        font-size: 11px;
        flex-shrink: 0;
    }

    .totals-bar div {
        flex: 1;
        border-right: 1px solid #fff;
        padding: 1px 6px;
    }

    .totals-bar div:last-child {
        border-right: none;
    }

    .mid-section {
        display: flex;
        width: 100%;
        align-items: stretch;
        flex: none;
        min-height: 0;
        margin-top: 3px;
        overflow: visible;
    }

    .mid-left {
        width: 49%;
        flex: 0 0 49%;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }

    .mid-chart {
        width: 49%;
        flex: 0 0 49%;
        max-width: 49%;
        min-width: 0;
        min-height: 0;
        margin-left: 2%;
        border: 1px solid #000;
        overflow: hidden;
        box-sizing: border-box;
    }

    .mid-chart>div {
        width: 100% !important;
        height: 100% !important;
        min-width: 0 !important;
        min-height: 0 !important;
    }

    .mid-chart svg {
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .co-sch-table,
    .remarks-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .co-sch-table tr,
    .remarks-table tr {
        height: auto;
    }

    .co-sch-table td,
    .remarks-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        font-size: 10px;
        font-weight: 600;
        vertical-align: middle;
        box-sizing: border-box;
    }

    /* ── HEALTH & ATTENDANCE ────────────────────────────────────────────────── */
    .health-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-top: 4px;
        text-align: center;
        flex-shrink: 0;
    }

    .health-table td {
        padding: 3px 5px;
        border: 1px solid #000;
        font-size: 11px;
        font-weight: 700;
    }

    .health-table .hh {
        background: #1F4E79;
        color: #fff;
    }

    .att-table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #000;
        margin-top: 4px;
        flex: 1 1 auto;
        min-height: 0;
    }

    .att-table td {
        padding: 4px 8px;
        font-size: 11px;
        font-weight: bold;
        border-bottom: 1px solid #000;
    }

    /* ── GRADING SCALE ──────────────────────────────────────────────────────── */
    .grading-scale {
        background: #CC0000;
        color: #fff;
        font-size: 9px;
        text-align: center;
        padding: 3px 5px;
        margin-top: 3px;
        font-weight: 700;
        flex-shrink: 0;
    }

    /* ── REMARKS TABLE ──────────────────────────────────────────────────────── */
    .remarks-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 3px;
        border: 1px solid #000;
        flex-shrink: 0;
        table-layout: fixed;
    }

    .remarks-table th,
    .remarks-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        font-size: 11px;
        font-weight: 600;
        vertical-align: middle;
    }

    .remarks-table th {
        background: #FFCC00;
        text-align: left;
    }

    .remarks-table td.subj-name {
        width: 30%;
    }

    /* ── SIGNATURES ─────────────────────────────────────────────────────────── */
    .signatures-box {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        padding: 0 10px 5px 10px;
        width: 100%;
        box-sizing: border-box;
        flex-shrink: 0;
        margin-top: auto;
        padding-top: 12px;
    }

    .sign-space {
        height: 50px;
        position: relative;
    }

    .sign-line {
        border-top: 1px dotted #000;
        width: 180px;
        text-align: center;
        font-size: 10px;
        font-weight: 700;
        padding-top: 4px;
    }

    .co-sch-table td,
    .remarks-table td {
        border: 1px solid #000;
        padding: 3px 5px;
        font-size: 10px;
        font-weight: 600;
        vertical-align: middle;
        box-sizing: border-box;
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 0;
        }

        html,
        body {
            margin: 0 !important;
            padding: 0 !important;
        }

        .report-card {
            width: 100% !important;
            height: auto !important;
            min-height: 0 !important;
            margin: 0 !important;
            padding: 6px 10px !important;
            border: none !important;
            box-shadow: none !important;
            overflow: visible !important;
            page-break-after: always;
            page-break-inside: avoid;
            box-sizing: border-box !important;
        }

        .mid-section {
            flex: none !important;
            min-height: 0 !important;
            overflow: visible !important;
        }

        .no_print {
            display: none !important;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header no_print">
        <h1>Nursery Marksheet</h1>
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
                        <select class="form-control" name="student_class"
                            required><?php dropdown($class_list, $student_class); ?></select>
                    </div>
                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Section&nbsp;</label>
                        <select class="form-control" name="student_section"
                            required><?php dropdown($section_list, $student_section); ?></select>
                    </div>
                    <div class="form-group" style="margin-right:12px;">
                        <label>Select Exam&nbsp;</label>
                        <select class="form-control" name="exam_id" required>
                            <option value="" disabled <?= (!isset($exam_id) || $exam_id == '') ? 'selected' : '' ?>>Select
                                Exam</option>
                            <?= dropdown_list('exam_list', 'id', 'exam_name', $exam_id) ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary" value="Show & Print">
                    </div>
                </form>
            </div>

            <?php if (!empty($student_class) && !empty($student_section) && !empty($exam_id)):

                /* ── Students ─────────────────────────────────────────── */
                $students = direct_sql("SELECT * FROM student WHERE student_class='$student_class' AND student_section='$student_section' AND branch_id='{$_SESSION['branch_id']}' AND status IN ('ACTIVE','PROMOTED')")['data'];
                usort($students, fn($a, $b) => (int) $a['student_roll'] - (int) $b['student_roll']);

                /* ── Exam ─────────────────────────────────────────────── */
                $exam_name = strtoupper(get_data('exam_list', $exam_id)['data']['exam_name'] ?? '');

                /* ── Subjects ─────────────────────────────────────────── */
                $subjects = direct_sql("SELECT * FROM subject WHERE FIND_IN_SET('$student_class',student_class)>0 AND subject_type='Scholastic' AND is_extra='NO' AND IFNULL(show_in_report,'YES')='YES' AND status='ACTIVE' AND branch_id='{$_SESSION['branch_id']}' ORDER BY subject_order ASC")['data'];
                $sub_count = count($subjects);

                /* ── Dynamic row height based on subject count ────────── */
                $row_h = max(16, min(52, (int) (210 / max($sub_count, 1))));

                /* ── Branch header image ──────────────────────────────── */
                $branch_img = [1 => 'assets/images/b1.JPG', 2 => 'assets/images/b2.JPG', 4 => 'assets/images/b3.JPG'];
                $img_src = $branch_img[$_SESSION['branch_id']] ?? '';

                /* ── Rank calculation ─────────────────────────────────── */
                $student_totals = [];
                foreach ((array) $students as $stu) {
                    $gt = 0;
                    foreach ((array) $subjects as $sub) {
                        $t = direct_sql("SELECT pt,ma,sea,mo FROM tabulation WHERE student_id='{$stu['id']}' AND subject='{$sub['id']}' AND exam_id='$exam_id' LIMIT 1")['data'][0] ?? [];
                        $gt += (float) ($t['pt'] ?? 0) + (float) ($t['ma'] ?? 0) + (float) ($t['sea'] ?? 0) + (float) ($t['mo'] ?? 0);
                    }
                    $student_totals[$stu['id']] = $gt;
                }
                arsort($student_totals);
                $ranks = [];
                $r = $rr = 1;
                $prev = null;
                foreach ($student_totals as $sid => $tot) {
                    if ($prev !== null && $tot < $prev)
                        $r = $rr;
                    $ranks[$sid] = $r;
                    $prev = $tot;
                    $rr++;
                }
                ?>
                <script src="https://www.gstatic.com/charts/loader.js"></script>
                <script>google.charts.load('current', { packages: ['corechart'] });</script>

                <?php foreach ((array) $students as $stu):
                    $stu_id = $stu['id'];
                    $bid = $_SESSION['branch_id'];
                    $img_url = $base_url . 'student_photo/' . ($bid == 1 ? 'Bishambharpur' : ($bid == 2 ? 'Barauli' : 'Barharia')) . '/Photo/';
                    // $photo = $stu['student_photo'] ? $img_url . $stu['student_photo'] : 'assets/images/blank_person.png';
                    $photo = !empty($stu['student_photo']) ? $base_url . 'upload/' . $stu['student_photo'] : 'assets/images/blank_person.png';

                    /* Per-student urdu/sanskrit filter */
                    $show_urdu = !empty($stu['urdu']) && $stu['urdu'] == 1;
                    $stu_subjects = array_filter((array) $subjects, function ($sub) use ($show_urdu) {
                        $n = strtolower($sub['subject_name']);
                        if ($show_urdu && stripos($n, 'sanskrit') !== false && stripos($n, 'urdu') === false)
                            return false;
                        if (!$show_urdu && stripos($n, 'urdu') !== false && stripos($n, 'sanskrit') === false)
                            return false;
                        return true;
                    });
                    $max_total = count($stu_subjects) * 100;
                    $row_h = max(16, min(52, (int) (210 / max(count($stu_subjects), 1))));
                    ?>
                    <div class="report-card">

                        <?php if ($img_src): ?>
                            <div style="text-align:center;margin-bottom:4px;flex-shrink:0;">
                                <img src="<?= $img_src ?>" height="160px" width="100%" alt="Branch">
                            </div>
                        <?php endif; ?>

                        <div class="rc-header">STUDENT ACHIEVEMENT REPORT</div>
                        <div class="rc-header2"><?= $exam_name ?></div>

                        <!-- Student Info -->
                        <table class="stu-info-table">
                            <tr>
                                <td rowspan="4" class="stu-photo-cell"><img src="<?= $photo ?>" class="stu-photo-circle"></td>
                                <td class="label-cell">NAME</td>
                                <td colspan="2"><?= $stu['student_name'] ?></td>
                                <td class="label-cell">CLASS</td>
                                <td><?= $stu['student_class'] ?></td>
                            </tr>
                            <tr>
                                <td class="label-cell">MOTHER'S NAME</td>
                                <td colspan="2"><?= $stu['student_mother'] ?? '' ?></td>
                                <td class="label-cell">SECTION</td>
                                <td><?= $stu['student_section'] ?></td>
                            </tr>
                            <tr>
                                <td class="label-cell">FATHER'S NAME</td>
                                <td colspan="2"><?= $stu['student_father'] ?? '' ?></td>
                                <td class="label-cell">STUDENT ID</td>
                                <td><?= $stu['student_admission'] ?></td>
                            </tr>
                            <tr>
                                <td class="label-cell">DATE OF BIRTH</td>
                                <td colspan="2">
                                    <?= (!empty($stu['date_of_birth']) && $stu['date_of_birth'] != '0000-00-00') ? date('d-m-Y', strtotime($stu['date_of_birth'])) : '' ?>
                                </td>
                                <td class="label-cell">ROLL NUMBER</td>
                                <td><?= $stu['student_roll'] ?></td>
                            </tr>
                        </table>

                        <div class="rc-header2"
                            style="background:#CC0000;color:#fff;margin-top:4px;border:none;padding:3px;font-size:13px;">
                            <?= $exam_name ?>
                        </div>

                        <!-- Marks Table -->
                        <?php
                        $grand_total = 0;
                        $chartData = [['Subject', 'OBTAINED MARKS', 'TOTAL MARKS']];
                        ?>
                        <table class="marks-table">
                            <thead>
                                <tr>
                                    <th class="subj-head" style="width:25%;">SUBJECTS</th>
                                    <th class="mark-head">PT-1<br>(10)</th>
                                    <th class="mark-head">MA<br>(15)</th>
                                    <th class="mark-head">SEA<br>(15)</th>
                                    <th class="mark-head">HYE<br>(60)</th>
                                    <th class="mark-head">TOTAL<br>(100)</th>
                                    <th class="mark-head">GRADE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stu_subjects as $sub):
                                    $tab = direct_sql("SELECT pt,ma,sea,mo FROM tabulation WHERE student_id='$stu_id' AND subject='{$sub['id']}' AND exam_id='$exam_id' LIMIT 1")['data'][0] ?? [];
                                    $pt = $tab['pt'] ?? '';
                                    $ma = $tab['ma'] ?? '';
                                    $sea = $tab['sea'] ?? '';
                                    $mo = $tab['mo'] ?? '';
                                    $total = (is_numeric($pt) ? (float) $pt : 0) + (is_numeric($ma) ? (float) $ma : 0) + (is_numeric($sea) ? (float) $sea : 0) + (is_numeric($mo) ? (float) $mo : 0);
                                    $grand_total += $total;
                                    $grd = grade($total);
                                    $sn = strtoupper(substr($sub['subject_name'], 0, 3));
                                    $chartData[] = [$sn, (float) $total, 100 - (float) $total];
                                    ?>
                                    <tr style="height:<?= $row_h ?>px;">
                                        <td class="subj-name"><?= $sub['subject_name'] ?></td>
                                        <td><?= fmt_val($pt) ?></td>
                                        <td><?= fmt_val($ma) ?></td>
                                        <td><?= fmt_val($sea) ?></td>
                                        <td><?= fmt_val($mo) ?></td>
                                        <td><?= $total ?></td>
                                        <td><?= $grd ?></td>
                                    </tr>
                                <?php endforeach;
                                $percentage = $max_total > 0 ? round(($grand_total / $max_total) * 100, 2) : 0;
                                $overall_grade = grade($percentage);
                                $student_rank = $ranks[$stu_id] ?? '-';
                                ?>
                            </tbody>
                        </table>

                        <!-- Totals -->
                        <div class="totals-bar">
                            <div>TOTAL = <?= $grand_total ?></div>
                            <div>PERCENTAGE = <?= $percentage ?>%</div>
                            <div>GRADE = <?= $overall_grade ?></div>
                            <div>RANK = <?= $student_rank ?></div>
                        </div>

                        <?php
                        $co_subjects = (array) co_subject_list($student_class);
                        $co_count = count($co_subjects);
                        $co_row_h = max(18, min(32, (int) (140 / max($co_count, 1))));
                        $mid_h = (($co_count + 1) * $co_row_h) + 90;
                        $remark_count = count($stu_subjects);
                        $remark_row_h = max(18, min(30, (int) (130 / max($remark_count, 1))));
                        ?>
                        <div class="mid-section" style="min-height:<?= $mid_h ?>px;">
                            <div class="mid-left">
                                <table class="co-sch-table">
                                    <thead>
                                        <tr style="height:<?= $co_row_h ?>px;">
                                            <th colspan="2">CO-SCHOLASTIC ASSESSMENT</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($co_subjects as $co_sub):
                                            $gr = direct_sql("SELECT grade FROM co_scholastic_grades WHERE exam_id='$exam_id' AND student_id='$stu_id' AND subject_id='{$co_sub['id']}' LIMIT 1")['data'][0]['grade'] ?? 'NA';
                                            ?>
                                            <tr style="height:<?= $co_row_h ?>px;">
                                                <td><?= $co_sub['subject_name'] ?></td>
                                                <td><?= $gr ? $gr : 'NA' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>

                                <table class="health-table">
                                    <tr>
                                        <td class="hh">HEIGHT</td>
                                        <td class="hh">WEIGHT</td>
                                        <td class="hh">BODY MASS INDEX</td>
                                    </tr>
                                    <tr>
                                        <td><?= $stu['height'] ?? '-' ?> cm</td>
                                        <td><?= $stu['weight'] ?? '-' ?> kg</td>
                                        <td><?= $stu['bmi'] ?? '-' ?></td>
                                    </tr>
                                </table>

                                <table class="att-table">
                                    <tr>
                                        <td style="border-bottom:1px solid #000;font-weight:bold;">ATTENDANCE</td>
                                    </tr>
                                    <tr>
                                        <td><?= $stu['total_present'] ?? '-' ?> / <?= $stu['total_working_days'] ?? '-' ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="mid-chart" style="height:<?= $mid_h ?>px;">
                                <div id="chart_div_<?= $stu_id ?>" style="width:100%;height:<?= $mid_h - 2 ?>px;"></div>
                            </div>
                            <script>
                                google.charts.setOnLoadCallback(function () {
                                    var chartDiv = document.getElementById('chart_div_<?= $stu_id ?>');
                                    if (!chartDiv) return;
                                    var data = google.visualization.arrayToDataTable(<?= json_encode($chartData, JSON_NUMERIC_CHECK) ?>);

                                    function drawChart_<?= $stu_id ?>() {
                                        var midSec = chartDiv.closest('.mid-section');
                                        var midLeft = midSec ? midSec.querySelector('.mid-left') : null;
                                        var measuredH = (midLeft && midLeft.offsetHeight > 50) ? (midLeft.offsetHeight - 2) : <?= $mid_h - 2 ?>;
                                        var targetH = Math.max(measuredH, <?= $mid_h - 2 ?>);
                                        var parentW = chartDiv.parentElement ? chartDiv.parentElement.clientWidth : 0;
                                        var targetW = parentW > 50 ? parentW : (chartDiv.clientWidth > 50 ? chartDiv.clientWidth : 340);

                                        var options = {
                                            title: 'GRAPHICAL REPRESENTATION OF RESULT',
                                            titleTextStyle: { fontSize: 11, bold: true, color: '#555' },
                                            isStacked: true,
                                            width: targetW,
                                            height: targetH,
                                            chartArea: { left: 45, top: 25, width: '70%', height: '74%' },
                                            vAxis: { viewWindow: { min: 0, max: 100 }, ticks: [0, 50, 100], format: "#'%'", textStyle: { color: '#555', fontSize: 9 }, gridlines: { color: '#e0e0e0' } },
                                            hAxis: { textStyle: { color: '#333', fontSize: 8 }, slantedText: false, maxAlternation: 1 },
                                            legend: { position: 'bottom', textStyle: { color: '#555', fontSize: 9 } },
                                            colors: ['#175b7b', '#b90000'],
                                            bar: { groupWidth: '50%' }
                                        };
                                        var chart = new google.visualization.ColumnChart(chartDiv);
                                        chart.draw(data, options);
                                    }

                                    drawChart_<?= $stu_id ?>();
                                    window.addEventListener('resize', drawChart_<?= $stu_id ?>);
                                });
                            </script>
                        </div>

                        <!-- Grading Scale -->
                        <div class="grading-scale">
                            8-Point Grading Scale: A1 - Outstanding | A2 - Excellent | B1 - Very Good | B2 - Good | C1 -
                            Satisfactory | C2 - Average | D - Needs Improvement | E - Unsatisfactory<br>
                            PT-Periodic Test | MA- Multi Assessment | SEA- Subject Enrichment Assessment | HYE- Half Yearly
                            Examination
                        </div>

                        <!-- Remarks -->
                        <table class="remarks-table">
                            <thead>
                                <tr>
                                    <th class="subj-name">SUBJECTS</th>
                                    <th>REMARKS</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stu_subjects as $sub):
                                    $tab = direct_sql("SELECT pt,ma,sea,mo FROM tabulation WHERE student_id='$stu_id' AND subject='{$sub['id']}' AND exam_id='$exam_id' LIMIT 1")['data'][0] ?? [];
                                    $t2 = (is_numeric($tab['pt'] ?? '') ? (float) $tab['pt'] : 0) + (is_numeric($tab['ma'] ?? '') ? (float) $tab['ma'] : 0) + (is_numeric($tab['sea'] ?? '') ? (float) $tab['sea'] : 0) + (is_numeric($tab['mo'] ?? '') ? (float) $tab['mo'] : 0);
                                    $rmk = get_remarks($student_class, $sub['id'], grade($t2), $sub['subject_name']);
                                    if (!empty($rmk)): ?>
                                        <tr style="height:<?= $remark_row_h ?>px;">
                                            <td class="subj-name" style="height:<?= $remark_row_h ?>px;">
                                                <?= $sub['subject_name'] ?>
                                            </td>
                                            <td style="height:<?= $remark_row_h ?>px;">
                                                <?= $rmk ?>
                                            </td>
                                        </tr>
                                    <?php endif; endforeach; ?>
                            </tbody>
                        </table>

                        <!-- Signatures -->
                        <div class="signatures-box">
                            <div>
                                <div class="sign-space"></div>
                                <div class="sign-line">SIGNATURE OF CLASS TEACHER</div>
                            </div>
                            <div>
                                <div class="sign-space"></div>
                                <div class="sign-line">SIGNATURE OF PARENT</div>
                            </div>
                            <div>
                                <div class="sign-space" style="position:relative;">
                                    <img src="<?= $principal ?>" height="40"
                                        style="position:absolute;bottom:5px;left:50%;transform:translateX(-50%);">
                                </div>
                                <div class="sign-line">SIGNATURE OF PRINCIPAL</div>
                            </div>
                        </div>

                    </div><!-- /.report-card -->
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php require_once('system/footer.php'); ?>