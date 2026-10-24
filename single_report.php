<?php
require_once('system/header.php');
require_once('system/menu.php');

$exam_id = $student_class = $student_section = null;

if (isset($_REQUEST['exam_id'])) {
    $exam_id = $_REQUEST['exam_id'];
}
if (isset($_REQUEST['student_class'])) {
    $student_class = $_REQUEST['student_class'];
}
if (isset($_REQUEST['student_section'])) {
    $student_section = $_REQUEST['student_section'];
}
if (isset($_REQUEST['student_id'])) {
    $student_id = $_REQUEST['student_id'];
}
if (isset($_REQUEST['branch_id'])) {
    $branch_id = $_REQUEST['branch_id'];
}else{
    $branch_id = $_SESSION['branch_id'];
}

/* ---------- VALUE FORMAT ---------- */
function fmt_val($v) {
    if ($v === null || $v === '' || $v === '0' || $v === 0 || $v === '0.00' || $v === 0.0) return 'AB';
    if (!is_numeric($v)) return htmlspecialchars($v);
    if (intval($v) == $v) return intval($v);
    return rtrim(rtrim(number_format((float)$v, 2, '.', ''), '0'), '.');
}

/* ---------- PRINCIPAL SIGN ---------- */
$principal = get_data('branch_list',$branch_id)['data'];
if($principal!=''){
    $principal = $base_url.'upload/'.$principal['principal_sign'];
}else{
    $principal = 'assets/images/new_sign.png';
}

/* ---------- ATTENDANCE ---------- */
$att = [
    'I'=>113,'II'=>113,'III'=>113,'IV'=>113,'V'=>113,
    'VI'=>113,'VII'=>113,'VIII'=>113,'IX'=>132,'X'=>132
];
?>

<style>
/* ====== SAME CSS AS YOUR ORIGINAL ====== */
body { font-family: "Helvetica", Arial, sans-serif; color:#222; background: #fff; }
.content-wrapper{text-transform: uppercase !important;font-size:14px!important;font-weight:400!important;}
.report-card { min-height: 297mm !important; margin: 0px; border: 1px solid #cfcfcf;background:#fff; }
.rc-header { background: #525252; color: #fff; text-align:center; font-weight:700; }
.rc-header2 { background: #ccc; color: #000; padding:8px 12px; text-align:center; font-weight:700; }
table.sch-table { width:100%; border-collapse: collapse; }
table.sch-table th, table.sch-table td { border:1px solid #cfcfcf; padding:1px 6px; text-align:center; font-size:12px;}
</style>

<div class="content-wrapper">
<section class="content">

<?php
/* ============================================================
   FETCH SINGLE STUDENT
============================================================ */
if (!empty($student_id) && !empty($exam_id)) {

    $stu = get_data('student',$student_id)['data'];

    if(!$stu){
        echo "<h3>Student not found</h3>";
        exit;
    }

    $student_class   = $stu['student_class'];
    $student_section = $stu['student_section'];

    $subjects = subject_list($student_class);

    /* ---------- MERGED SUBJECT LOGIC (UNCHANGED) ---------- */
    $merged_subjects = [];

    if (in_array($student_class,['I','II','III','IV','V','VI','VII','VIII'])) {

        $english_done = $hindi_done = $urdu_done = false;

        foreach ($subjects as $sub) {

            $sub_id = is_array($sub)?$sub['id']:$sub;
            $sub_name = get_data('subject',$sub_id,'subject_name')['data'];

            if (!$english_done && stripos($sub_name,'English') !== false) {
                $merged_subjects[]=['id'=>'english','subject_name'=>'English'];
                $english_done = true;

            } elseif (!$hindi_done && stripos($sub_name,'Hindi') !== false) {
                $merged_subjects[]=['id'=>'hindi','subject_name'=>'Hindi'];
                $hindi_done = true;

            } elseif (!$urdu_done && stripos($sub_name,'Urdu') !== false) {
                $merged_subjects[]=['id'=>'urdu','subject_name'=>'Sanskrit / Urdu'];
                $urdu_done = true;

            } elseif (
                stripos($sub_name,'English')===false &&
                stripos($sub_name,'Hindi')===false &&
                stripos($sub_name,'Urdu')===false
            ) {
                $merged_subjects[]=['id'=>$sub_id,'subject_name'=>$sub_name];
            }
        }

    } else {

        foreach ($subjects as $sub) {
            $sub_id = is_array($sub)?$sub['id']:$sub;
            $sub_name = get_data('subject',$sub_id,'subject_name')['data'];
            $merged_subjects[]=['id'=>$sub_id,'subject_name'=>$sub_name];
        }
    }

    /* ============================================================
       START REPORT CARD
    ============================================================ */
?>

<div class="report-card">

<?php
/* ---------- BRANCH IMAGE ---------- */
$branch_images=[
    1=>'assets/images/b1.JPG',
    2=>'assets/images/b2.JPG',
    4=>'assets/images/b3.JPG'
];

if($_SESSION['branch_id']==1){
    $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
}else if($_SESSION['branch_id']==2){
    $img_url = $base_url.'student_photo/Barauli/Photo/';
}else{
    $img_url = $base_url.'student_photo/Barharia/Photo/';
}

$img_src = $branch_images[$_SESSION['branch_id']];
?>

<div style="text-align:center;min-height:170px">
    <img src="<?= $img_src ?>" height="170" width="100%">
</div>

<div class="rc-header">MY ACHIEVEMENT REPORT</div>
<div class="rc-header2"><?= strtoupper($exam_id) ?> EXAMINATION</div>

<!-- ============================================================
     STUDENT PROFILE
============================================================ -->
<table style="width:100%;border:1px solid #000;">
<tr>
<td colspan="3"><b>STUDENT PROFILE</b></td>
<td rowspan="5" style="text-align:center">
<img src="<?= htmlspecialchars($img_url.$stu['student_admission']) ?>.jpg"
     style="width:95px;height:120px;border:1px solid #ccc;">
</td>
</tr>

<tr>
<td><b>Name:</b> <?= $stu['student_name'] ?></td>
<td><b>Class:</b> <?= $stu['student_class']." - ".$stu['student_section'] ?></td>
</tr>

<tr>
<td><b>Father:</b> <?= $stu['student_father'] ?></td>
<td><b>Roll:</b> <?= $stu['student_roll'] ?></td>
</tr>

<tr>
<td><b>Mother:</b> <?= $stu['student_mother'] ?></td>
<td><b>Admission:</b> <?= $stu['student_admission'] ?></td>
</tr>

<tr>
<td><b>DOB:</b> <?= date('d-m-Y',strtotime($stu['date_of_birth'])) ?></td>
<td><b>Mobile:</b> <?= $stu['student_mobile'] ?></td>
</tr>
</table>

<!-- ============================================================
     EVERYTHING BELOW IS EXACT SAME LOGIC
     (NO CHANGE MADE)
============================================================ -->

<?php
/* ============================================================
   FULL MARKS / TABULATION / CHART / TOTAL / GRADE
   COPY YOUR ORIGINAL BLOCK BELOW
   (No loop because $stu already exists)
============================================================ */
?>

<!-- PASTE YOUR EXISTING SCHOLASTIC TABLE,
     CHART CODE,
     TOTAL CALCULATION,
     CO-SCHOLASTIC,
     SIGNATURE,
     ETC HERE WITHOUT CHANGING ANYTHING -->

<?php
}
?>

</section>
</div>

<?php require_once('system/footer2.php'); ?>
