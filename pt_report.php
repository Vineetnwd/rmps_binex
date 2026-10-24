<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// ==============================
// GET INPUTS
// ==============================
$class     = $_REQUEST['student_class']   ?? '';
$section   = $_REQUEST['student_section'] ?? '';
$exam_name = $_REQUEST['exam_id']         ?? '';

// ==============================
// EXAM → COLUMN + TERM MAPPING
// ==============================
$exam_col = '';
$term     = '';

if ($exam_name == 'UNIT TEST 1') { $exam_col = 'ut1'; $term = 'TERM 1'; }
elseif ($exam_name == 'UNIT TEST 2') { $exam_col = 'ut2'; $term = 'TERM 1'; }
elseif ($exam_name == 'UNIT TEST 3') { $exam_col = 'ut3'; $term = 'TERM 2'; }
elseif ($exam_name == 'UNIT TEST 4') { $exam_col = 'ut4'; $term = 'TERM 2'; }

// ==============================
// FETCH ACTIVE STUDENTS
// ==============================
$students = [];
if ($class && $section) {
    $students = get_all(
        'student',
        '*',
        [
            'student_class'   => $class,
            'student_section' => $section,
            'status'          => 'ACTIVE'
        ],
        'student_roll ASC'
    )['data'] ?? [];
}

// ==============================
// FETCH SUBJECTS USING FIND_IN_SET
// ==============================
$subjects = [];
if ($class) {
    $sql_sub = "
        SELECT * FROM subject
        WHERE FIND_IN_SET('$class', student_class)
        AND status='ACTIVE'
        ORDER BY subject_name ASC
    ";
    $subjects = direct_sql($sql_sub)['data'] ?? [];
}

// ==============================
// LANGUAGE SUBJECT CHECKERS
// ==============================
function isLanguageSubject($name) {
    $n = strtolower($name);
    return (
        str_contains($n, 'hindi') ||
        str_contains($n, 'english') ||
        str_contains($n, 'sanskrit') ||
        str_contains($n, 'urdu')
    );
}

function isGrammarOrLiterature($name) {
    $n = strtolower($name);
    return (
        str_contains($n, 'grammar') ||
        str_contains($n, 'literature')
    );
}

// ==============================
// APPLY LANGUAGE SUBJECT RULES
// IX & X → Show ALL language subjects
// Other classes → Show only Grammar/Literature
// ==============================
$final_subjects = [];

foreach ($subjects as $sub) {
    $name = $sub['subject_name'];
        $isLang = isLanguageSubject($name);
        $isGL   = isGrammarOrLiterature($name);

    // IX & X show ALL subjects (no filtering)
    if (in_array(strtoupper($class), ['NUR','LKG','UKG','IX','X'])) {
        $final_subjects[] = $sub;
        continue;
    }

    // Other classes → apply previous rule
    if ($isLang) {
        if ($isGL) {  // Only Grammar/Literature for UNIT TEST
            $final_subjects[] = $sub;
        }
    } else {
        $final_subjects[] = $sub; // Non-language always show
    }
}

// ==============================
// GRADE FUNCTION
// ==============================
function grade_calc($p) {
    if ($p >= 90) return "A1";
    if ($p >= 80) return "A2";
    if ($p >= 70) return "B1";
    if ($p >= 60) return "B2";
    if ($p >= 50) return "C1";
    if ($p >= 40) return "C2";
    if ($p >= 33) return "D";
    return "E";
}

?>
<style>
/* ================= COLORS & PRINT FIXES ================== */

* {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

body {
    background: #fff8e1 !important;
    margin: 0; padding: 0;
}

/* ================= REPORT CARD ================== */

.report-wrapper {
    margin-bottom: 20px;
    page-break-inside: avoid;
}

.school-header {
    text-align: center;
    padding: 10px 0;
}

.school-header h2 {
    margin: 0;
    font-weight: 800;
    font-size: 22px;
    color: #b71c1c !important;
}

.school-header h4 {
    margin: 0;
    font-size: 15px;
    font-weight: 700;
    color: #d84315 !important;
}

.report-card {
    border: 3px solid #c62828;
    border-radius: 12px;
    padding: 15px;
    background: #fffde7;
    box-shadow: 0 0 10px rgba(200,0,0,0.25);
}

.student-info td {
    padding: 4px;
    font-size: 14px;
    color: #b71c1c !important;
    font-weight: 600;
}

.subject-table th {
    background: #c62828 !important;
    color: #fff8e1 !important;
    text-align: center;
}

.subject-table td {
    background: #fffef5 !important;
    text-align: center;
    font-weight: 600;
}

.subject-table .highlight-total td {
    background: #ffeb3b !important;
    color: #b71c1c !important;
    font-weight: 800;
}

.footer-sign {
    display: flex;
    justify-content: space-between;
    margin-top: 15px;
    color: #b71c1c;
}

.footer-sign div {
    width: 33%;
    text-align: center;
    font-weight: bold;
}

/* ================= FILTER BOX ================== */
.no-print .row {
    background: #fff3cd !important;
    border-left: 6px solid #c62828 !important;
}

.no-print label {
    color: #c62828; font-weight: 700;
}

/* ================= PRINT ================== */
@media print {

    body { background: #fff !important; }

    .no-print { display: none !important; }

    @page { margin: 0 !important; size: A4 portrait; }

    .report-wrapper:nth-child(2n) {
        page-break-after: always !important;
    }
    td{
        padding: 5px !important;
    }
    .report-card {
        margin: 2px !important;
        box-shadow: none !important;
        height:180mm !important;
    }

    .content-wrapper, .box, section {
        padding: 0 !important;
        margin: 0 !important;
    }
}
</style>


<div class="content-wrapper">

<section class="content-header">
    <h1>Unit Test Report Card</h1>
</section>

<section class="content">

<div class="box box-default">
<div class="box-header with-border no-print">
    <h3>Generate Report</h3>
</div>

<div class="box-body">

<!-- ================= FILTER FORM ================= -->
<form method="post" class="no-print">
<div class="row" style="padding:15px;border-radius:8px;">

    <div class="col-md-3">
        <label>Class</label>
        <select name="student_class" class="form-control" required>
            <option value="">-- Select --</option>
            <?php foreach(['I','II','III','IV','V','VI','VII','VIII','IX','X'] as $cls){
                $sel = ($class==$cls)?'selected':''; echo "<option $sel>$cls</option>";
            } ?>
        </select>
    </div>

    <div class="col-md-3">
        <label>Section</label>
        <select name="student_section" class="form-control" required>
            <option value="">-- Select --</option>
            <?php foreach(['A','B','C','D','E'] as $sec){
                $sel = ($section==$sec)?'selected':''; echo "<option $sel>$sec</option>";
            } ?>
        </select>
    </div>

    <div class="col-md-3">
        <label>Unit Test Exam</label>
        <select name="exam_id" class="form-control" required>
            <?= dropdown_list('exam_list','exam_name','exam_name',$exam_name); ?>
        </select>
    </div>

    <div class="col-md-3" style="margin-top:25px;">
        <button class="btn btn-danger"><i class="fa fa-search"></i> Generate</button>

        <?php if(!empty($students)){ ?>
        <button type="button" class="btn btn-primary" onclick="window.print()">
            <i class="fa fa-print"></i> Print All
        </button>
        <?php } ?>
    </div>

</div>
</form>



<?php
// ======================= GENERATE REPORT CARDS =======================

if ($class && $section && $exam_col) {

    if (empty($students)) {
        echo "<div class='alert alert-danger'>No students found.</div>";
    }
    elseif (empty($final_subjects)) {
        echo "<div class='alert alert-danger'>No subjects found for this class.</div>";
    }
    else {

        $count = 0;

        foreach ($students as $stu) {
            $count++;

            $sid    = $stu['id'];
            $adm    = $stu['student_admission'];
            $name   = $stu['student_name'];
            $roll   = $stu['student_roll'];
            $father = $stu['student_father'] ?? '';

?>
<!-- ======================= REPORT CARD ======================= -->
<div class="report-wrapper">

<div class="report-card">

    <div class="school-header">
        <h2>R M PUBLIC SCHOOL</h2>
        <h4>UNIT TEST REPORT CARD - <?= $exam_name ?> (2025-2026)</h4>
    </div>

    <table class="student-info" width="100%">
        <tr>
            <td><b>Name:</b> <?= $name ?></td>
            <td><b>Adm No:</b> <?= $adm ?></td>
            <td><b>Roll:</b> <?= $roll ?></td>
        </tr>
        <tr>
            <td><b>Father's Name:</b> <?= $father ?></td>
            <td><b>Class:</b> <?= $class ?>-<?= $section ?></td>
            <td><b>Term:</b>  <?= $term ?></td>
        </tr>
    </table>

    <table class="table table-bordered subject-table">
        <thead>
            <tr>
                <th>S.No</th>
                <th>Subject</th>
                <th>Max Marks</th>
                <th>Obtained</th>
            </tr>
        </thead>
        <tbody>

<?php
$sn = 1;
$total_obt = 0;
$total_max = 0;

foreach ($final_subjects as $sub) {

    $sub_id   = $sub['id'];
    $sub_name = $sub['subject_name'];

    // ============ DYNAMIC FULL MARKS RULE ============
    $cu = strtoupper($class);

    if (in_array($cu, ['NUR','LKG','UKG'])) {
        $sub_max = 10;
    } elseif (in_array($cu, ['I','II','III','IV','V','VI','VII','VIII'])) {
        $sub_max = 30;
    } elseif (in_array($cu, ['IX','X'])) { 
        if($sub_id=='46'){
            $sub_max='50';
            
        }else{
        $sub_max = '80';
        }
    } else {
        $sub_max = 20;
    }

    // ============ MARKS FETCH ============
    $tab = get_all(
        'tabulation',
        '*',
        [
            'student_id'        => $sid,
            'student_admission' => $adm,
            'term'              => $term,
            'subject'           => $sub_id,
            'branch_id'         => $branch_id
        ]
    )['data'][0] ?? [];

    $marks = $tab[$exam_col] ?? 0;

    $total_obt += $marks;
    $total_max += $sub_max;
?>
            <tr>
                <td><?= $sn++ ?></td>
                <td style="text-align:left;"><?= $sub_name ?></td>
                <td><?= $sub_max ?></td>
                <td><?= $marks ?></td>
            </tr>
<?php } ?>

            <tr class="highlight-total">
                <td colspan="2" style="text-align:right;">Total</td>
                <td><?= $total_max ?></td>
                <td><?= $total_obt ?></td>
            </tr>

<?php
$perc  = ($total_max > 0) ? round(($total_obt / $total_max) * 100, 2) : 0;
$grade = grade_calc($perc);
?>

            <tr class="highlight-total">
                <td colspan="2" style="text-align:right;">Percentage</td>
                <td colspan="2"><?= $perc ?>%</td>
            </tr>

            <tr class="highlight-total">
                <td colspan="2" style="text-align:right;">Grade</td>
                <td colspan="2"><?= $grade ?></td>
            </tr>

        </tbody>
    </table>

    <div class="footer-sign">
        <div>________________<br>Class Teacher</div>
        <!--<div>________________<br>Exam Incharge</div>-->
        <div>________________<br>Principal</div>
    </div>

</div>
</div>

<?php
        } // end foreach student
    }
}
?>

</div>
</div>

</section>
</div>

<?php require_once('system/footer.php'); ?>
