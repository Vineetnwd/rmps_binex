<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// ===============================
// GET FILTER INPUT
// ===============================
$class     = $_REQUEST['student_class']   ?? '';
$section   = $_REQUEST['student_section'] ?? '';
$exam_name = $_REQUEST['exam_id']         ?? '';

$cls = strtoupper($class);

// Allowed classes
$allowed = ['NUR','LKG','UKG'];

if ($class && !in_array($cls, $allowed)) {
    echo "<div class='alert alert-danger'>This page is ONLY for NUR, LKG, UKG.</div>";
    require_once('system/footer.php');
    exit;
}

// ===============================
// TERM MAPPING
// ===============================
$term = '';

if ($exam_name == 'UNIT TEST 1') $term = 'TERM 1';
if ($exam_name == 'UNIT TEST 2') $term = 'TERM 1';
if ($exam_name == 'UNIT TEST 3') $term = 'UNIT TEST 3'; // You manually changed this
if ($exam_name == 'UNIT TEST 4') $term = 'UNIT TEST 4';

// ===============================
// FETCH STUDENTS
// ===============================
$students = [];

if ($class && $section) {
    $students = get_all(
        'student', '*',
        [
            'student_class' => $class,
            'student_section' => $section,
            'status' => 'ACTIVE'
        ],
        'student_roll ASC'
    )['data'] ?? [];
}

// ===============================
// FETCH SUBJECTS
// ===============================
$subjects = [];

if ($class) {
    $subjects = direct_sql("
        SELECT * FROM subject
        WHERE FIND_IN_SET('$class', student_class)
        AND status='ACTIVE'
        ORDER BY subject_name ASC
    ")['data'] ?? [];
}

?>

<style>
*{ -webkit-print-color-adjust:exact!important; print-color-adjust:exact!important; }
body{ background:#fff8e1; margin:0; }

.report-wrapper{ margin-top:25px; page-break-inside:avoid; }
.report-card{
    border:3px solid #c62828;
    border-radius:12px;
    background:#fffde7;
    padding:20px;
}

.school-header h2{ margin:0; font-size:26px; font-weight:800; color:#b71c1c; }
.school-header h4{ margin:0; font-size:16px; font-weight:700; color:#d84315; }

.subject-table th{
    background:#c62828!important;
    color:#fff8e1!important;
    text-align:center;
}
.subject-table td{
    background:#fffef5!important;
    text-align:center;
    font-weight:600;
}
.highlight-total td{
    background:#ffeb3b!important;
    color:#b71c1c!important;
    font-weight:800;
}

.footer-sign{
    display:flex;
    justify-content:space-between;
    margin-top:20px;
}
.footer-sign div{ width:33%; text-align:center; font-weight:bold; }

@media print{
    .no-print{ display:none!important; }
    /*@page{ size:A4 portrait; margin:0; }*/
    
    .report-card{ margin:2px!important;margin-top:25px !important; height:189mm!important; }
}
</style>

<div class="content-wrapper">
<section class="content-header">
    <h1>Pre-Primary Unit Test Report Card</h1>
</section>

<section class="content">
<div class="box box-default">

<div class="box-header with-border no-print">
    <h3>Generate Report</h3>
</div>

<div class="box-body">

<!-- FILTER FORM -->
<form method="post" class="no-print">
<div class="row" style="background:#fff3cd;padding:15px;border-left:5px solid #c62828;border-radius:6px;">

    <div class="col-md-3">
        <label>Class</label>
        <select name="student_class" class="form-control" required>
            <option value="">--Select--</option>
            <?php foreach($allowed as $c){
                echo "<option ".($class==$c?'selected':'').">$c</option>";
            } ?>
        </select>
    </div>

    <div class="col-md-3">
        <label>Section</label>
        <select name="student_section" class="form-control" required>
            <option value="">--Select--</option>
            <?php foreach(['A','B','C','D','E','F'] as $s){
                echo "<option ".($section==$s?'selected':'').">$s</option>";
            } ?>
        </select>
    </div>

    <div class="col-md-3">
        <label>Unit Test</label>
        <select name="exam_id" class="form-control" required>
            <?= dropdown_list('exam_list','exam_name','exam_name',$exam_name); ?>
        </select>
    </div>

    <div class="col-md-3" style="margin-top:25px;">
        <button class="btn btn-danger"><i class="fa fa-search"></i> Generate</button>
        <?php if(!empty($students)){ ?>
        <button onclick="window.print()" type="button" class="btn btn-primary"><i class="fa fa-print"></i> Print All</button>
        <?php } ?>
    </div>

</div>
</form>

<?php
// ===================================================
// START REPORT GENERATION
// ===================================================
if ($class && $section && $exam_name && !empty($students)) {

foreach ($students as $stu) {

    $sid    = $stu['id'];
    $adm    = $stu['student_admission'];
    $name   = $stu['student_name'];
    $roll   = $stu['student_roll'];
    $father = $stu['student_father'];
?>

<div class="report-wrapper">
<div class="report-card">

    <div class="school-header">
        <h2 class='text-center'> R M PUBLIC SCHOOL</h2>
        <h4 class='text-center'>UNIT TEST REPORT CARD - <?= $exam_name ?></h4>
    </div>

    <table width="100%">
        <tr>
            <td><b>Name:</b> <?= $name ?></td>
            <td><b>Adm No:</b> <?= $adm ?></td>
            <td><b>Roll:</b> <?= $roll ?></td>
        </tr>
        <tr>
            <td><b>Father's Name:</b> <?= $father ?></td>
            <td><b>Class:</b> <?= $class ?>-<?= $section ?></td>
            <td><b>Term:</b> <?= $term ?></td>
        </tr>
    </table>

    <table class="table table-bordered subject-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Subject</th>
                <th>Oral (20)</th>
                <th>Written (30)</th>
                <th>Max</th>
                <th>Obtained</th>
            </tr>
        </thead>
        <tbody>

<?php
// ================================
// BUILD ORAL + WRITTEN PAIRS
// ================================
$pair = [];

foreach ($subjects as $sub) {
    $sname = trim($sub['subject_name']);
    $sid_sub = $sub['id'];

    if (str_ends_with(strtolower($sname), " ws")) {
        $base = trim(substr($sname, 0, -3));
        $pair[$base]['written'][] = $sid_sub;
    } else {
        $pair[$sname]['oral'][] = $sid_sub;
    }
}

// EVS + GK merge
$gkKey = isset($pair['GK']) ? 'GK' : (isset($pair['G.K']) ? 'G.K' : null);

if (isset($pair['EVS']) || $gkKey) {

    $pair['EVS/GK'] = [
        'oral'    => array_merge($pair['EVS']['oral'] ?? [],   $gkKey ? ($pair[$gkKey]['oral'] ?? []) : []),
        'written' => array_merge($pair['EVS']['written'] ?? [],$gkKey ? ($pair[$gkKey]['written'] ?? []) : [])
    ];

    unset($pair['EVS']);
    if ($gkKey) unset($pair[$gkKey]);
}

// ================================
// MARKS CALCULATION
// ================================
$sn = 1;
$total_oral = 0;
$total_written = 0;
$total_max = 0;
$total_obt = 0;

foreach ($pair as $subject => $p) {

    // ORAL FINAL VALUE
    $oral_total_raw = 0;
    $oral_sub_count = 0;

    if (!empty($p['oral'])) {
        foreach ($p['oral'] as $oid) {

            $res = direct_sql("
                SELECT SUM(obtained) AS total, COUNT(id) AS sub_count
                FROM pre_marks
                WHERE student_id='$sid'
                AND student_admission='$adm'
                AND term='$term'
                AND subject_id='$oid'
            ");

            $oral_total_raw += (int)($res['data'][0]['total'] ?? 0);
            $oral_sub_count += (int)($res['data'][0]['sub_count'] ?? 0);
        }
    }

    if ($oral_sub_count > 0) {
        $oral_max_raw = $oral_sub_count * 10;
        $oral_final = round(($oral_total_raw / $oral_max_raw) * 20);
    } else {
        $oral_final = 0;
    }

    // WRITTEN TOTAL
    $written = 0;

    if (!empty($p['written'])) {
        foreach ($p['written'] as $wid) {

            $rw = direct_sql("
                SELECT SUM(obtained) AS total
                FROM pre_marks
                WHERE student_id='$sid'
                AND student_admission='$adm'
                AND term='$term'
                AND subject_id='$wid'
            ");

            $written += (int)($rw['data'][0]['total'] ?? 0);
        }
    }

    // FINAL OBTAINED
    $max = 50;
    $obt = $oral_final + $written;

    // ADD TO TOTALS
    $total_oral += $oral_final;
    $total_written += $written;
    $total_max += $max;
    $total_obt += $obt;
?>

<tr>
    <td><?= $sn++ ?></td>
    <td style="text-align:left;"><?= strtoupper($subject) ?></td>
    <td><?= $oral_final ?></td>
    <td><?= $written ?></td>
    <td><?= $max ?></td>
    <td><?= $obt ?></td>
</tr>

<?php } ?>

<tr class="highlight-total">
    <td colspan="2" align="right"><b>Total</b></td>
    <td><b><?= $total_oral ?></b></td>
    <td><b><?= $total_written ?></b></td>
    <td><b><?= $total_max ?></b></td>
    <td><b><?= $total_obt ?></b></td>
</tr>

        </tbody>
    </table>

    <div class="footer-sign">
        <div>________________<br>Class Teacher</div>
        <div>________________<br>Principal</div>
    </div>

</div>
</div>

<?php } } ?>

</div>
</div>
</section>
</div>

<?php require_once('system/footer.php'); ?>
