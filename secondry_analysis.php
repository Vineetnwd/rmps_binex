<?php 
require_once('system/header.php');
require_once('system/menu.php');

if($user_type != 'ADMIN' && $user_type != 'DEV') {
    echo "<script> window.location='dashboard' </script>";
    exit;
}

// Get form values
$student_class   = $_REQUEST['student_class']   ?? '';
$student_section = $_REQUEST['student_section'] ?? '';
$subject         = $_REQUEST['subject']         ?? '';
$exam_id         = $_REQUEST['exam_id']         ?? '';
$action          = $_REQUEST['action']          ?? '';

// Get subject name for display
$subject_name = '';
if (!empty($subject)) {
    $subject_query = direct_sql("SELECT subject_name FROM subject WHERE id = '$subject' LIMIT 1");
    if (!empty($subject_query['data'][0])) {
        $subject_name = $subject_query['data'][0]['subject_name'];
    }
}

// Define full marks
$full_marks = [
    'ut1'      => 50,
    'ut2'      => 50,
    'mid_term' => 80
];
?>

<script> 
    document.title = "Subject Wise Report";
</script>

<style>
    .report-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .report-header h4 {
        margin: 0;
        font-weight: 600;
    }
    .report-info {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 15px;
    }
    .report-info-item {
        background: rgba(255,255,255,0.2);
        padding: 8px 15px;
        border-radius: 5px;
    }
    .report-info-item strong {
        display: block;
        font-size: 12px;
        opacity: 0.8;
    }
    .report-info-item span {
        font-size: 16px;
        font-weight: 600;
    }
    .op-thead {
        background: #343a40;
        color: white;
    }
    .btn-download {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-download:hover {
        opacity: 0.9;
        color: white;
    }
    .filter-form {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }
    .filter-form select {
        min-width: 150px;
        padding: 8px 12px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }
    .student-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    @media print {
        .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
    }
</style>

<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Content Header -->
    <section class="content-header">
        <h1><i class="fa fa-bar-chart"></i> Subject Wise Report</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Exam Analysis</a></li>
            <li class="breadcrumb-item active">Subject Wise Report</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <!-- Filter Box -->
        <div class="box box-default no-print">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Filter Options</h3>
            </div>
            <div class="box-body">
                <form method="post" class="filter-form" id="reportForm">
                    <div class='row'>
                    <div class='col-3'>
                        <select name='student_class' id='student_class' class='form-control' required>
                            <option value='' disabled <?= empty($student_class) ? 'selected' : '' ?>>Select Class</option>
                            <?php 
                            $all_classes = ['NUR','LKG','UKG','I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
                            foreach($all_classes as $cls): 
                            ?>
                            <option value='<?= $cls ?>' <?= $student_class == $cls ? 'selected' : '' ?>><?= $cls ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div> 
                    <div class='col-3'>
                        <select name='student_section' id='student_section' class='form-control' required>
                            <?= dropdown($section_list, $student_section) ?>
                        </select>
                    </div> 
                    <div class='col-3'>
                        <select name='subject' id='subject' class='form-control' required>
                            <option value='' disabled <?= empty($subject) ? 'selected' : '' ?>>Select Subject</option>
                            <?= dropdown_list('subject', 'id', 'subject_name', $subject) ?>
                        </select>
                    </div> 
                    <div class='col-3'>
                        <select name='exam_id' id='exam_id' class='form-control' required>
                            <option value='' disabled <?= empty($exam_id) ? 'selected' : '' ?>>Select Exam</option>
                            <?= dropdown_list('exam_list', 'id', 'exam_name', $exam_id) ?>
                        </select>
                    </div> 
                    <div class='col-12 mt-2'>
                        <input type="hidden" name="action" id="formAction" value="generate">
                        <button type="submit" class='btn btn-primary'>
                            <i class="fa fa-search"></i> Generate Report
                        </button>
                        
                        <?php if (!empty($_POST) && $action != 'download_pdf'): ?>
                        <button type="button" class='btn btn-info' onclick="window.print()">
                            <i class="fa fa-print"></i> Print
                        </button>
                    </div> 
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <?php
        // Process form submission
        if (!empty($_POST) && !empty($student_class) && !empty($student_section) && !empty($subject) && !empty($exam_id)):
            
            // Sanitize inputs
            $student_class   = mysqli_real_escape_string($con, $student_class);
            $student_section = mysqli_real_escape_string($con, $student_section);
            $subject         = mysqli_real_escape_string($con, $subject);
            $exam_id         = mysqli_real_escape_string($con, $exam_id);
            $branch_id       = $_SESSION['branch_id'];
            
            // Get exam name
            $exam_label = get_data('exam_list', $exam_id, 'exam_name')['data'] ?? 'Unknown Exam';
            
            // Get full marks for selected exam and subject
            $current_full_marks = 100;
            $mm_res = direct_sql("SELECT nb, ma, mo FROM max_marks WHERE class='$student_class' AND exam_id='$exam_id' AND (subject_id='$subject' OR subject_id='' OR subject_id IS NULL) AND branch_id='$branch_id' ORDER BY subject_id DESC LIMIT 1");
            if ($mm_res['count'] > 0) {
                $row = $mm_res['data'][0];
                $current_full_marks = (float)$row['nb'] + (float)$row['ma'] + (float)$row['mo'];
            }
            if ($current_full_marks <= 0) $current_full_marks = 100;
            
            $sql = "SELECT 
                        s.id,
                        s.student_admission,
                        s.student_name,
                        s.student_father,
                        s.student_photo,
                        s.student_roll,
                        s.branch_id,
                        (IFNULL(t.mo, 0) + IFNULL(t.pt, 0) + IFNULL(t.sea, 0) + IFNULL(t.nb, 0) + IFNULL(t.ma, 0)) AS selected_marks,
                        ROUND(((IFNULL(t.mo, 0) + IFNULL(t.pt, 0) + IFNULL(t.sea, 0) + IFNULL(t.nb, 0) + IFNULL(t.ma, 0)) / $current_full_marks) * 100, 2) AS percentage
                    FROM student s
                    JOIN tabulation t 
                        ON s.id = t.student_id 
                       AND t.subject = '$subject'
                    WHERE 
                        s.student_class = '$student_class'
                        AND s.student_section = '$student_section'
                        AND s.status = 'ACTIVE'
                        AND t.exam_id = '$exam_id'
                        AND s.branch_id = '$branch_id'
                    ORDER BY selected_marks DESC, s.student_roll ASC";

            $result   = direct_sql($sql, 'get', false);
            $students = $result['data'] ?? [];
            $filtered_count = count($students);
            
        ?>
        
        <!-- Report Box -->
        <div class="box box-primary" id="reportContent">
            <div class="box-header">
                <!-- Report Header with Class, Section, Subject, Exam -->
                <div class="report-header">
                    <h4><i class="fa fa-file-text"></i> Subject Wise Analysis Report</h4>
                    <div class="report-info">
                        <div class="report-info-item">
                            <strong>Class</strong>
                            <span><?= htmlspecialchars($student_class) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Section</strong>
                            <span><?= htmlspecialchars($student_section) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Subject</strong>
                            <span><?= htmlspecialchars($subject_name) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Exam</strong>
                            <span><?= $exam_label ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Total Students</strong>
                            <span><?= $filtered_count ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Generated On</strong>
                            <span><?= date('d-M-Y H:i A') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box-body">
                <?php if($filtered_count > 0): ?>
                <div class="table-responsive">
                    <table id="reportTable" class="table table-bordered table-hover table-striped display nowrap margin-top-10">
                        <thead>
                            <tr class='op-thead'>
                                <th class='text-center'>#</th>
                                <th class='text-center'>Photo</th>
                                <th class='text-center'>Roll No</th>
                                <th>Admission No</th>
                                <th>Student Name</th>
                                <th>Father Name</th>
                                <th class='text-center'>Marks Obtained</th>
                                <th class='text-center'>Percentage</th>
                                <th class='text-center'>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sr = 0;
                            foreach ($students as $student): 
                                $sr++;
                                $percentage = floatval($student['percentage']);
                                
                                // Determine grade
                                if($percentage >= 90) {
                                    $grade = 'A+';
                                    $grade_class = 'success';
                                } elseif($percentage >= 80) {
                                    $grade = 'A';
                                    $grade_class = 'primary';
                                } elseif($percentage >= 70) {
                                    $grade = 'B+';
                                    $grade_class = 'info';
                                } elseif($percentage >= 60) {
                                    $grade = 'B';
                                    $grade_class = 'info';
                                } elseif($percentage >= 50) {
                                    $grade = 'C';
                                    $grade_class = 'warning';
                                } elseif($percentage >= 33) {
                                    $grade = 'D';
                                    $grade_class = 'warning';
                                } else {
                                    $grade = 'F';
                                    $grade_class = 'danger';
                                }
                                
                                if($_SESSION['branch_id']==1){
                                    $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
                                }else if($_SESSION['branch_id']==2){
                                    $img_url = $base_url.'student_photo/Barauli/Photo/';
                                }else{
                                    $img_url = $base_url.'student_photo/Barharia/Photo/';
                                }
                                $photo_path = $img_url . $student['student_admission'] . ".jpg";

                                if (!file_exists($photo_path)) {
                                    $photo = $photo_path;
                                } else {
                                    $photo = "https://img.freepik.com/premium-psd/3d-realistic-user-cartoon-vector-concept-3d-vector-render-illustration_996812-1951.jpg";
                                }
                            ?>
                            <tr>
                                <td class='text-center'><?= $sr ?></td>
                                <td class='text-center'>
                                    <img src="<?= $photo ?>" alt="Photo" class="student-photo">
                                </td>
                                <td class='text-center'><?= htmlspecialchars($student['student_roll']) ?></td>
                                <td><?= htmlspecialchars($student['student_admission']) ?></td>
                                <td><?= htmlspecialchars($student['student_name']) ?></td>
                                <td><?= htmlspecialchars($student['student_father']) ?></td>
                                <td class='text-center'>
                                    <strong><?= $student['selected_marks'] ?></strong> / <?= $current_full_marks ?>
                                </td>
                                <td class='text-center'>
                                    <strong><?= $percentage ?>%</strong>
                                </td>
                                <td class='text-center'>
                                    <span class="badge badge-<?= $grade_class ?>"><?= $grade ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="alert alert-warning text-center">
                    <i class="fa fa-exclamation-triangle fa-3x"></i>
                    <h4>No Students Found</h4>
                    <p>No students match the selected criteria.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php endif; ?>
        
    </section>
</div>

<script>
$(document).ready(function() {
    if ($('#reportTable').length) {
        $('#reportTable').DataTable({
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    }
});
</script>

<?php require_once('system/footer2.php'); ?>