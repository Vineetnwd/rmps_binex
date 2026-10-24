<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('system/function.php');

// echo "<pre>";
// print_r($_REQUEST);
// echo "</pre>";

$exam_name = $_REQUEST['exam_id'] ?? '';
$exam_name = strtoupper(trim($exam_name));

if (empty($exam_name)) {
    die('Exam Name Missing');
}

// Fetch numeric exam_id
$exam_name_esc = mysqli_real_escape_string($con, $exam_name);
$branch_id_esc = $_SESSION['branch_id'];
$exam_res = direct_sql("SELECT id FROM exam_list WHERE UPPER(exam_name) = '$exam_name_esc' AND branch_id = '$branch_id_esc' LIMIT 1");
if (empty($exam_res['data'])) {
    $exam_res = direct_sql("SELECT id FROM exam_list WHERE UPPER(exam_name) = '$exam_name_esc' LIMIT 1");
}
if (!empty($exam_res['data'])) {
    $_REQUEST['exam_id'] = $exam_res['data'][0]['id'];
}

$student_class = $_REQUEST['student_class'] ?? '';
$student_section = $_REQUEST['student_section'] ?? '';

$student_class = strtoupper(trim($student_class));
$student_section = strtoupper(trim($student_section));

if (empty($student_class)) {
    die('Student Class Missing');
}

/* ---------------- PT CHECK ---------------- */

$is_periodic_test = false;

if (
    strpos($exam_name, 'PT') !== false ||
    strpos($exam_name, 'PERIODIC') !== false
) {
    $is_periodic_test = true;
}

if (!$is_periodic_test) {
    die('This Exam Is Not PT');
}

/* ---------------- STUDENT TYPE ---------------- */

$student_type = $_REQUEST['student_type'] ?? [];

$type_sql = '';

if (!empty($student_type)) {

    $types = [];

    foreach ($student_type as $type) {

        $types[] = "'" . addslashes($type) . "'";
    }

    $type_sql = " AND student_type IN (" . implode(',', $types) . ")";
}

/* ---------------- FETCH STUDENTS ---------------- */

$sql = "
SELECT *
FROM student
WHERE student_class='$student_class'
AND student_section='$student_section'
AND status='ACTIVE'
AND branch_id='{$_SESSION['branch_id']}'
$type_sql
ORDER BY student_roll
";

$res = direct_sql($sql);

if (
    empty($res['data']) ||
    count($res['data']) == 0
) {
    die('No Student Found');
}

/* ---------------- TEMPLATE ---------------- */

$template_file = '';

if (in_array($student_class, ['NUR','NURSERY','LKG','UKG'])) {

    $template_file = 'pt_templates/PT_NUR_TO_UKG.php';

} elseif (in_array($student_class, ['I','II','1','2'])) {

    $template_file = 'pt_templates/PT_I_TO_II.php';

} elseif (in_array($student_class, ['III','IV','V','3','4','5'])) {

    $template_file = 'pt_templates/PT_III_TO_V.php';

} elseif (in_array($student_class, ['VI','VII','VIII','6','7','8'])) {

    $template_file = 'pt_templates/PT_VI_TO_VIII.php';

} elseif (in_array($student_class, ['IX','X','9','10'])) {

    $template_file = 'pt_templates/PT_IX_TO_X.php';
}

if (empty($template_file)) {
    die('Template Not Found');
}

/* ---------------- TEMPLATE EXISTS ---------------- */

if (!file_exists($template_file)) {

    die('Template File Missing : ' . $template_file);
}

/* ---------------- GENERATE REPORT ---------------- */

foreach ($res['data'] as $student) {

    $student_id = $student['id'];

    // echo "<hr>";
    // echo "<h2 style='text-align:center'>";
    // echo $student['student_name'];
    // echo "</h2>";

    try {

        include($template_file);

    } catch (Throwable $e) {

        ?>

        <div style="
            width:780px;
            margin:20px auto;
            border:2px solid red;
            padding:20px;
            font-family:arial;
        ">

            <h2>Blank Report Card</h2>

            <table width="100%" cellpadding="10" border="1" cellspacing="0">

                <tr>
                    <td><b>Name</b></td>
                    <td><?php echo $student['student_name']; ?></td>
                </tr>

                <tr>
                    <td><b>Admission</b></td>
                    <td><?php echo $student['student_admission']; ?></td>
                </tr>

                <tr>
                    <td><b>Roll</b></td>
                    <td><?php echo $student['student_roll']; ?></td>
                </tr>

                <tr>
                    <td><b>Class</b></td>
                    <td>
                        <?php
                        echo $student['student_class']
                        . ' - ' .
                        $student['student_section'];
                        ?>
                    </td>
                </tr>

            </table>

            <br>

            <table width="100%" border="1" cellspacing="0" cellpadding="10">

                <tr>
                    <th>Subject</th>
                    <th>Marks</th>
                    <th>Grade</th>
                </tr>

                <tr>
                    <td colspan="3" align="center">
                        Marks Not Available
                    </td>
                </tr>

            </table>

            <br>

            <div style="color:red">

                <b>Error :</b>

                <?php echo $e->getMessage(); ?>

            </div>

        </div>

        <?php
    }

    echo '<div style="page-break-after:always;"></div>';
}

?>