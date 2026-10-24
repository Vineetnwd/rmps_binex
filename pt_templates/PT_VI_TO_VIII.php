<?php


$student = get_data('student',$student_id)['data'];

$branch_images = [
    1 => 'assets/images/b1.JPG',
    2 => 'assets/images/b2.JPG',
    4 => 'assets/images/b3.JPG',
];

if($_SESSION['branch_id']==1){
    $img_url = $base_url . 'upload/' . $student['student_photo'];
}elseif($_SESSION['branch_id']==2){
    $img_url = $base_url . 'student_photo/Barauli/Photo/' . $student['student_admission'] . '.jpg';
}else{
    $img_url = $base_url . 'student_photo/Barharia/Photo/' . $student['student_admission'] . '.jpg';
}

$img_src = $branch_images[$_SESSION['branch_id']] ?? '';

$principal = get_data('branch_list',$branch_id)['data'];

if($principal!=''){

    $principal = $base_url.'upload/'.$principal['principal_sign'];

}else{

    $principal = 'assets/images/new_sign.png';
}

// DYNAMIC SUBJECTS AND MARKS SETUP
$class_esc = mysqli_real_escape_string($con, $student['student_class']);
$branch_esc = mysqli_real_escape_string($con, $_SESSION['branch_id']);
$exam_id_esc = mysqli_real_escape_string($con, $_REQUEST['exam_id'] ?? $exam_name);

// Get main subjects
$main_query = "SELECT * FROM subject WHERE FIND_IN_SET('$class_esc', student_class) AND status='ACTIVE' AND is_extra='NO' AND (show_in_report = 'YES' OR show_in_report IS NULL) AND branch_id='$branch_esc' ORDER BY subject_order, subject_name";
$main_res = direct_sql($main_query);
$main_subjects = $main_res['data'] ?? [];

// Get extra/additional subjects
$extra_query = "SELECT * FROM subject WHERE FIND_IN_SET('$class_esc', student_class) AND status='ACTIVE' AND is_extra='YES' AND (show_in_report = 'YES' OR show_in_report IS NULL) AND branch_id='$branch_esc' ORDER BY subject_order, subject_name";
$extra_res = direct_sql($extra_query);
$extra_subjects = $extra_res['data'] ?? [];

if (!function_exists('format_mark')) {
    function format_mark($val) {
        if ($val === null || $val === '' || strtoupper((string)$val) === 'NA' || (is_numeric($val) && (float)$val == 0)) {
            return 'AB';
        }
        return $val;
    }
}

if (!function_exists('get_grade')) {
    function get_grade($total, $max_total = 50) {
        if (!is_numeric($total) || (float)$total == 0) return 'AB';
        $percent = ($total / $max_total) * 100;
        if ($percent >= 91) return 'A1';
        if ($percent >= 81) return 'A2';
        if ($percent >= 71) return 'B1';
        if ($percent >= 61) return 'B2';
        if ($percent >= 51) return 'C1';
        if ($percent >= 41) return 'C2';
        if ($percent >= 31) return 'D';
        return 'E';
    }
}

if (!function_exists('get_subject_remark')) {
    function get_subject_remark($student_class, $subject_id, $grade, $subject_name = '')
    {
        global $con;
        if ($grade == 'AB' || $grade == 'NA' || empty($grade)) return '';
        
        $class_cat = 'JUNIOR';
        if (in_array($student_class, ['VI','VII','VIII','IX','X','XI','XII','6','7','8','9','10','11','12'])) {
            $class_cat = 'SENIOR';
        }
        
        $subject_id = (int)$subject_id;
        $grade_esc = mysqli_real_escape_string($con, $grade);
        $sub_name_esc = mysqli_real_escape_string($con, $subject_name);
        
        $sql = "SELECT `$grade_esc` FROM remark WHERE (subject = $subject_id OR subject_name = '$sub_name_esc' OR subject = 0 OR subject = '' OR subject IS NULL) AND status = 'ACTIVE' AND branch_id = '{$_SESSION['branch_id']}' ORDER BY (class_cat = '$class_cat') DESC, subject DESC, subject_name DESC LIMIT 1";
        $res = direct_sql($sql);
        if (!empty($res['data'])) {
            return $res['data'][0][$grade] ?? '';
        }
        return '';
    }
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<title>PT VI TO VIII REPORT CARD</title>

<style>

body{
    margin:0;
    padding:0;
    /*background:#efefef;*/
    font-family:Arial, Helvetica, sans-serif;
}

.page{
    width:930px;
    height:1280px;
    margin:0 auto;
    background:#fff;
    padding:10px 15px 12px;
    position:relative;
    overflow:hidden;
    box-sizing:border-box;
    display:flex;
    flex-direction:column;
}

.watermark{
    position:absolute;
    width:600px;
    opacity:0.07;
    left:50%;
    top:56%;
    transform:translate(-50%,-50%);
    z-index:0;
}

.top-logo{
    width:100%;
    text-align:center;
    position:relative;
    z-index:2;
}

.top-logo img{
    width:720px;
    height:150px;
}

.exam-title{
    width:340px;
    margin:10px auto;
    background:#ffc000;
    border:4px solid #e97b23;
    text-align:center;
    font-size:22px;
    font-weight:bold;
    padding:7px;
    position:relative;
    z-index:2;
}

.student-section{
    display:flex;
    gap:12px;
    margin-top:12px;
    position:relative;
    z-index:2;
}

.photo-box{
    width:150px;
    height:190px;
    border:4px solid red;
    background:#dbead3;
    overflow:hidden;
    flex-shrink:0;
}

.photo-box img{
    width:100%;
    height:100%;
    object-fit:cover;
}

.info-box{
    flex:1;
    border:4px solid #e97b23;
    padding:4px;
    box-sizing:border-box;
}

.info-table{
    width:100%;
    border-collapse:collapse;
}

.info-table td{
    border:2px solid #e7b28b;
    padding:4px 6px;
    font-size:15px;
    font-weight:bold;
}

.label{
    width:42%;
    background:#ffc000;
}

.marks-table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
    position:relative;
    z-index:2;
}

.marks-table th,
.marks-table td{
    border:2px solid #e97b23;
    padding:2px 4px;
    text-align:center;
    font-size:14px;
    font-weight:bold;
    height:22px;
    line-height:1.1;
}

.marks-table th{
    background:#ffc000;
}

.additional-table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    position:relative;
    z-index:2;
}

.additional-table th,
.additional-table td{
    border:2px solid #e97b23;
    padding:2px 4px;
    text-align:center;
    font-size:14px;
    font-weight:bold;
    height:22px;
    line-height:1.1;
}

.additional-table th{
    background:#ffc000;
}

.remarks-table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
    position:relative;
    z-index:2;
}

.remarks-table th,
.remarks-table td{
    border:2px solid #e97b23;
    padding:2px 4px;
    text-align:center;
    font-size:14px;
    font-weight:bold;
    height:20px;
    line-height:1;
}

.remarks-table th{
    background:#ffc000;
}

.footer{
    margin-top:auto;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:flex-end;
    font-size:18px;
    font-weight:bold;
    position:relative;
    z-index:2;
}

@media print{

    @page{
        size:A4 portrait;
        margin:0;
    }

    body{
        background:#fff;
    }

    .page{
        width:100%;
        height:295mm;
        margin:0;
        padding:8px 35px;
        page-break-after:avoid;
        page-break-inside:avoid;
    }

    table,
    tr,
    td,
    th{
        page-break-inside:avoid !important;
    }
}

</style>

</head>

<body>

<div class="page">

<img src="assets/images/logo.png" class="watermark">

<div class="top-logo">

<?php
if($img_src!=''){
    echo "<img src='{$img_src}'>";
}
?>

</div>

<div class="exam-title">

<?php echo strtoupper($exam_name); ?>

</div>

<div class="student-section">

<div class="photo-box">

<?php

$photo = $student['student_photo'] ?? '';

if(
    $photo!='' &&
    file_exists('upload/'.$photo)
){
?>

<img src="upload/<?php echo $photo; ?>">

<?php

}else{

    echo "
    <img src='{$img_url}'>
    ";
}

?>

</div>

<div class="info-box">

<table class="info-table">

<tr>
<td class="label">NAME</td>
<td><?php echo strtoupper($student['student_name']); ?></td>
</tr>

<tr>
<td class="label">CLASS & SECTION</td>
<td>
<?php
echo strtoupper(
    $student['student_class']
    .' '.
    $student['student_section']
);
?>
</td>
</tr>
<tr>
<td class="label">ROLL NO.</td>
<td><?php echo $student['student_roll']; ?></td>
</tr>

<tr>
<td class="label">ADMISSION NUMBER</td>
<td><?php echo $student['student_admission']; ?></td>
</tr>

<tr>
<td class="label">DATE OF BIRTH</td>
<td>

<?php

if(
    !empty($student['date_of_birth']) &&
    $student['date_of_birth']!='0000-00-00'
){
    echo date(
        'd.m.Y',
        strtotime($student['date_of_birth'])
    );
}

?>

</td>
</tr>

<tr>
<td class="label">MOTHER'S NAME</td>
<td><?php echo strtoupper($student['student_mother']); ?></td>
</tr>

<tr>
<td class="label">FATHER'S NAME</td>
<td><?php echo strtoupper($student['student_father']); ?></td>
</tr>

</table>

</div>

</div>

<!-- MAIN SUBJECT TABLE -->

<table class="marks-table">

<tr>

<th>SUBJECTS</th>
<th>SEA- I (5)</th>
<th>MA-I (5)</th>
<th>PT-I (40)</th>
<th>TOTAL (50)</th>
<th>GRADE</th>

</tr>

<?php
$grand_total = 0;
$total_max_possible = 0;
$has_any_marks = false;
$subject_grades = []; // cache grades for remarks lookup

foreach ($main_subjects as $sub) {
    $sub_id = $sub['id'];
    $marks_query = "SELECT * FROM tabulation WHERE student_id = '{$student['id']}' AND student_admission = '{$student['student_admission']}' AND exam_id = '$exam_id_esc' AND subject = '$sub_id' AND branch_id = '$branch_esc' LIMIT 1";
    $marks_res = direct_sql($marks_query);
    $tabulation = $marks_res['data'][0] ?? [];
    
    $sea_val = isset($tabulation['sea']) ? format_mark($tabulation['sea']) : 'AB';
    $ma_val = isset($tabulation['ma']) ? format_mark($tabulation['ma']) : 'AB';
    $pt_val = isset($tabulation['pt']) ? format_mark($tabulation['pt']) : 'AB';
    
    // Calculate total
    $numeric_vals = [];
    if (is_numeric($sea_val)) $numeric_vals[] = $sea_val;
    if (is_numeric($ma_val)) $numeric_vals[] = $ma_val;
    if (is_numeric($pt_val)) $numeric_vals[] = $pt_val;
    
    if (empty($numeric_vals)) {
        $sub_name_upper = strtoupper($sub['subject_name']);
        if (strpos($sub_name_upper, 'URDU') !== false) {
            continue;
        }
        
        if (strpos($sub_name_upper, 'SANSKRIT') !== false) {
            $u_has_marks = false;
            foreach ($main_subjects as $usub) {
                if (strpos(strtoupper($usub['subject_name']), 'URDU') !== false) {
                    $uid = $usub['id'];
                    $u_q = "SELECT * FROM tabulation WHERE student_id = '{$student['id']}' AND student_admission = '{$student['student_admission']}' AND exam_id = '$exam_id_esc' AND subject = '$uid' AND branch_id = '$branch_esc' LIMIT 1";
                    $u_res = direct_sql($u_q);
                    $u_tab = $u_res['data'][0] ?? [];
                    $u_sea = isset($u_tab['sea']) ? format_mark($u_tab['sea']) : 'AB';
                    $u_ma = isset($u_tab['ma']) ? format_mark($u_tab['ma']) : 'AB';
                    $u_pt = isset($u_tab['pt']) ? format_mark($u_tab['pt']) : 'AB';
                    if (is_numeric($u_sea) || is_numeric($u_ma) || is_numeric($u_pt)) {
                        $u_has_marks = true;
                    }
                    break;
                }
            }
            if ($u_has_marks) {
                continue;
            }
        }
        $total_val = 'AB';
        $grade = 'AB';
        $total_max_possible += 50;
        $has_any_marks = true;
    } else {
        $total_val = array_sum($numeric_vals);
        $grade = get_grade($total_val, 50);
        $grand_total += $total_val;
        $total_max_possible += 50;
        $has_any_marks = true;
    }
    
    $subject_grades[$sub_id] = $grade;
    
    echo "<tr>
        <td>" . htmlspecialchars($sub['subject_name']) . "</td>
        <td>$sea_val</td>
        <td>$ma_val</td>
        <td>$pt_val</td>
        <td>$total_val</td>
        <td>$grade</td>
    </tr>";
}
?>

<?php
$att_data = calculate_student_attendance($student['id'], $exam_id_esc, $class_esc, $branch_esc);
$present_days = $att_data['present_days'];
$t_att = $att_data['total_open_days'];
$att_text = ($t_att > 0) ? "$present_days/$t_att" : "-";

if ($has_any_marks && $total_max_possible > 0) {
    $percentage = number_format(($grand_total / $total_max_possible) * 100, 2);
} else {
    $percentage = 'AB';
}
?>
    <tr>
        <td colspan="1">ATTENDANCE</td>
        <td colspan="1"><?= $att_text ?></td>
        <td colspan="1">PERCENTAGE</td>
        <td colspan="1"><?= $percentage ?>%</td>
        <td colspan="1">GRAND TOTAL</td>
        <td colspan="1"><?= $has_any_marks ? "$grand_total / $total_max_possible" : 'AB' ?></td>
    </tr>

<tr>
<td colspan="6">
GRADE:
A1=91-100%,
A2=81-90%,
B1=71-80%,
B2=61-70,
C1=51-60,
C2=41-50,
D=31-40,
E= LESS THAN 30

</td>

</tr>

</table>

<!-- ADDITIONAL SUBJECT -->

<?php if (!empty($extra_subjects)) { ?>
<table class="additional-table">

<tr>

<th>ADDITIONAL SUBJECTS</th>
<th>MARKS OBTAINED (30)</th>
<th>PROJECT (10)</th>
<th>TOTAL MARKS (40)</th>

</tr>

<?php
foreach ($extra_subjects as $sub) {
    $sub_id = $sub['id'];
    $marks_query = "SELECT * FROM tabulation WHERE student_id = '{$student['id']}' AND student_admission = '{$student['student_admission']}' AND exam_id = '$exam_id_esc' AND subject = '$sub_id' AND branch_id = '$branch_esc' LIMIT 1";
    $marks_res = direct_sql($marks_query);
    $tabulation = $marks_res['data'][0] ?? [];
    
    $pt_val = isset($tabulation['pt']) ? format_mark($tabulation['pt']) : 'AB';
    $sea_val = isset($tabulation['sea']) ? format_mark($tabulation['sea']) : 'AB';
    
    $numeric_vals = [];
    if (is_numeric($pt_val)) $numeric_vals[] = $pt_val;
    if (is_numeric($sea_val)) $numeric_vals[] = $sea_val;
    
    if (empty($numeric_vals)) {
        $total_val = 'AB';
    } else {
        $total_val = array_sum($numeric_vals);
    }
    
    echo "<tr>
        <td>" . htmlspecialchars($sub['subject_name']) . "</td>
        <td>$pt_val</td>
        <td>$sea_val</td>
        <td>$total_val</td>
    </tr>";
}
?>

</table>
<?php } ?>

<!-- REMARKS TABLE -->

<table class="remarks-table">

<tr>

<th width="35%">
SUBJECTS
</th>

<th>
REMARKS
</th>

</tr>

<?php
foreach ($main_subjects as $sub) {
    $sub_id = $sub['id'];
    if (!isset($subject_grades[$sub_id])) {
        continue;
    }
    $grade = $subject_grades[$sub_id] ?? 'AB';
    $remark_text = get_subject_remark($student['student_class'], $sub_id, $grade, $sub['subject_name']);
    
    echo "<tr>
        <td>" . htmlspecialchars($sub['subject_name']) . "</td>
        <td>" . htmlspecialchars($remark_text) . "</td>
    </tr>";
}
?>

</table>

<div class="footer">

<div style='margin-top:50px'>

CLASS TEACHER

</div>

<div style='text-align:center'>

<img src='<?= $principal ?>' width='180px' height='50px'><br>

PRINCIPAL

</div>

</div>

</div>

</body>

</html>