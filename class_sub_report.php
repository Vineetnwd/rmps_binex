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
$term            = $_REQUEST['term']            ?? '';
$exam_type       = $_REQUEST['exam_type']       ?? '';
$filter_type     = $_REQUEST['filter_type']     ?? '';
$marks_value     = $_REQUEST['marks_value']     ?? '';
$marks_from      = $_REQUEST['marks_from']      ?? '';
$marks_to        = $_REQUEST['marks_to']        ?? '';

// Get subject name for display
$subject_name = '';
if (!empty($subject)) {
    $subject_query = direct_sql_api("SELECT subject_name FROM subject WHERE id = '$subject' LIMIT 1");
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
    document.title = "Marks Range Report";
</script>

<style>
    .report-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
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
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
    }
    .marks-input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .marks-input-group input {
        width: 100px;
        text-align: center;
    }
    .marks-input-group .between-separator {
        font-weight: bold;
        color: #666;
    }
    .student-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .badge-above { background-color: #28a745; }
    .badge-below { background-color: #dc3545; }
    .badge-between { background-color: #17a2b8; }
    .highlight-row { background-color: #fff3cd !important; }
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        margin-bottom: 15px;
    }
    .stats-card h3 {
        margin: 0;
        font-size: 36px;
        font-weight: bold;
    }
    .stats-card p {
        margin: 5px 0 0 0;
        opacity: 0.9;
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
        <h1><i class="fa fa-filter"></i> Marks Range Report</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Exam Analysis</a></li>
            <li class="breadcrumb-item active">Marks Range Report</li>
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
                <form method="post" id="reportForm">
                    <!-- Row 1: Basic Filters -->
                    <div class="filter-card">
                        <h5><i class="fa fa-cog"></i> Basic Filters</h5>
                        <div class='row'>
                            <div class='col-md-3'>
                                <label>Class <span class="text-danger">*</span></label>
                                <select name='student_class' id='student_class' class='form-control' required>
                                    <option value='' disabled <?= empty($student_class) ? 'selected' : '' ?>>Select Class</option>
                                    <option value='IX' <?= $student_class == 'IX' ? 'selected' : '' ?>>IX</option>
                                    <option value='X' <?= $student_class == 'X' ? 'selected' : '' ?>>X</option>
                                </select>
                            </div> 
                            <div class='col-md-3'>
                                <label>Section <span class="text-danger">*</span></label>
                                <select name='student_section' id='student_section' class='form-control' required>
                                    <?= dropdown($section_list, $student_section) ?>
                                </select>
                            </div> 
                            <div class='col-md-3'>
                                <label>Subject <span class="text-danger">*</span></label>
                                <select name='subject' id='subject' class='form-control' required>
                                    <option value='' disabled <?= empty($subject) ? 'selected' : '' ?>>Select Subject</option>
                                    <?= dropdown_where_fis('subject', 'id', 'subject_name', ['student_class' => 'IX,X,XI,XII'], $subject) ?>
                                </select>
                            </div> 
                            <div class='col-md-3'>
                                <label>Term <span class="text-danger">*</span></label>
                                <select name='term' id='term' class='form-control' required>
                                    <option value='' disabled <?= empty($term) ? 'selected' : '' ?>>Select Term</option>
                                    <option value='TERM 1' <?= $term == 'TERM 1' ? 'selected' : '' ?>>Term 1</option>
                                    <!-- <option value='TERM 2' <?= $term == 'TERM 2' ? 'selected' : '' ?>>Term 2</option> -->
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Marks Range Filters -->
                    <div class="filter-card">
                        <h5><i class="fa fa-sliders"></i> Marks Range Filter</h5>
                        <div class='row' id="marksFilterRow">
                            <div class='col-md-3'>
                                <label>Apply On <span class="text-danger">*</span></label>
                                <select name='exam_type' id='exam_type' class='form-control' required>
                                    <option value='' disabled <?= empty($exam_type) ? 'selected' : '' ?>>Select Exam</option>
                                    <option value='ut1' <?= $exam_type == 'ut1' ? 'selected' : '' ?>>UT-1 (50 Marks)</option>
                                    <option value='ut2' <?= $exam_type == 'ut2' ? 'selected' : '' ?>>UT-2 (50 Marks)</option>
                                    <option value='mid_term' <?= $exam_type == 'mid_term' ? 'selected' : '' ?>>MTE (80 Marks)</option>
                                </select>
                            </div>
                            <div class='col-md-3'>
                                <label>Filter Type <span class="text-danger">*</span></label>
                                <select name='filter_type' id='filter_type' class='form-control' required>
                                    <option value='' disabled <?= empty($filter_type) ? 'selected' : '' ?>>Select Filter</option>
                                    <option value='above'   <?= $filter_type == 'above'   ? 'selected' : '' ?>>Above (>=)</option>
                                    <option value='below'   <?= $filter_type == 'below'   ? 'selected' : '' ?>>Below (<=)</option>
                                    <option value='between' <?= $filter_type == 'between' ? 'selected' : '' ?>>Between</option>
                                    <option value='equal'   <?= $filter_type == 'equal'   ? 'selected' : '' ?>>Equal To (=)</option>
                                </select>
                            </div>
                            
                            <!-- Single Value Input (for above, below, equal) -->
                            <div class='col-md-6 single-value-div'>
                                <label>Marks Value <span class="text-danger">*</span></label>
                                <input type='number' name='marks_value' id='marks_value' class='form-control' 
                                       value='<?= htmlspecialchars($marks_value) ?>' placeholder='Enter marks value' 
                                       step='0.01' min='0'>
                            </div>
                            
                            <!-- Range Input (for between) -->
                            <div class='col-md-3 range-value-div'>
                                <label>From Marks <span class="text-danger">*</span></label>
                                <input type='number' name='marks_from' id='marks_from' class='form-control' 
                                       value='<?= htmlspecialchars($marks_from) ?>' placeholder='Minimum marks' 
                                       step='0.01' min='0'>
                            </div>
                            <div class='col-md-3 range-value-div'>
                                <label>To Marks <span class="text-danger">*</span></label>
                                <input type='number' name='marks_to' id='marks_to' class='form-control' 
                                       value='<?= htmlspecialchars($marks_to) ?>' placeholder='Maximum marks' 
                                       step='0.01' min='0'>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class='row'>
                        <div class='col-12'>
                            <button type="submit" class='btn btn-primary btn-lg'>
                                <i class="fa fa-search"></i> Generate Report
                            </button>
                            <button type="reset" class='btn btn-secondary btn-lg' onclick="resetForm()">
                                <i class="fa fa-refresh"></i> Reset
                            </button>
                            
                            <?php if (!empty($_POST)): ?>
                                <button type="button" class='btn btn-info btn-lg' onclick="window.print()">
                                    <i class="fa fa-print"></i> Print
                                </button>
                                <!--<button type="button" class='btn btn-success btn-lg' onclick="exportToExcel()">-->
                                <!--    <i class="fa fa-file-excel-o"></i> Export Excel-->
                                <!--</button>-->
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php
        // Process form submission
        if (!empty($_POST) && !empty($student_class) && !empty($student_section) && !empty($subject) && !empty($term) && !empty($exam_type) && !empty($filter_type)):
            
            // Sanitize inputs
            $student_class   = mysqli_real_escape_string($con, $student_class);
            $student_section = mysqli_real_escape_string($con, $student_section);
            $subject         = mysqli_real_escape_string($con, $subject);
            $term            = mysqli_real_escape_string($con, $term);
            $exam_type       = mysqli_real_escape_string($con, $exam_type);
            
            // Get full marks for selected exam
            $current_full_marks = $full_marks[$exam_type] ?? 100;
            
            // Build WHERE condition based on filter type
            $marks_condition = "";
            $filter_label    = "";
            
            switch($filter_type) {
                case 'above':
                    $marks_value     = floatval($marks_value);
                    $marks_condition = "t.$exam_type >= $marks_value";
                    $filter_label    = "Marks >= $marks_value";
                    break;
                    
                case 'below':
                    $marks_value     = floatval($marks_value);
                    $marks_condition = "t.$exam_type <= $marks_value";
                    $filter_label    = "Marks <= $marks_value";
                    break;
                    
                case 'between':
                    $marks_from      = floatval($marks_from);
                    $marks_to        = floatval($marks_to);
                    $marks_condition = "t.$exam_type BETWEEN $marks_from AND $marks_to";
                    $filter_label    = "Marks between $marks_from and $marks_to";
                    break;
                    
                case 'equal':
                    $marks_value     = floatval($marks_value);
                    $marks_condition = "t.$exam_type = $marks_value";
                    $filter_label    = "Marks = $marks_value";
                    break;
                    
                default:
                    $marks_condition = "1=1";
                    $filter_label    = "All Students";
            }
            
            // Exam type label
            $exam_labels = [
                'ut1'      => 'UT-1 (50 Marks)',
                'ut2'      => 'UT-2 (50 Marks)',
                'mid_term' => 'MTE (80 Marks)'
            ];
            $exam_label = $exam_labels[$exam_type] ?? $exam_type;
            
            // Get total students count
            $total_sql = "SELECT COUNT(*) AS total_students 
                          FROM student 
                          WHERE student_class = '$student_class' 
                            AND student_section = '$student_section' 
                            AND status = 'ACTIVE'";
            $total_result   = direct_sql_api($total_sql);
            $total_students = $total_result['data'][0]['total_students'] ?? 0;
            
            // Main query to get students
            $sql = "SELECT 
                        s.id,
                        s.student_admission,
                        s.student_name,
                        s.student_father,
                        s.student_photo,
                        s.student_roll,
                        s.branch_id,
                        t.ut1,
                        t.ut2,
                        t.mid_term,
                        t.$exam_type AS selected_marks,
                        ROUND((t.$exam_type / $current_full_marks) * 100, 2) AS percentage
                    FROM student s
                    LEFT JOIN tabulation t 
                        ON s.id = t.student_id 
                       AND t.term = '$term' 
                       AND t.subject = '$subject'
                    WHERE 
                        s.student_class = '$student_class'
                        AND s.student_section = '$student_section'
                        AND s.status = 'ACTIVE'
                        AND t.$exam_type IS NOT NULL
                        AND $marks_condition
                        AND s.branch_id = {$_SESSION['branch_id']}
                    ORDER BY t.$exam_type DESC, s.student_roll ASC";

            $result   = direct_sql_api($sql);
            $students = $result['data'] ?? [];
            $filtered_count = count($students);
            
            // Calculate statistics
            $total_marks = 0;
            $max_marks   = 0;
            $min_marks   = PHP_INT_MAX;
            
            foreach($students as $student) {
                $mark = floatval($student['selected_marks']);
                $total_marks += $mark;
                if($mark > $max_marks) $max_marks = $mark;
                if($mark < $min_marks) $min_marks = $mark;
            }
            
            $avg_marks   = $filtered_count > 0 ? round($total_marks / $filtered_count, 2) : 0;
            $avg_percent = $filtered_count > 0 ? round(($avg_marks / $current_full_marks) * 100, 2) : 0;
            if($filtered_count == 0) $min_marks = 0;
        ?>

        <!-- Report Box -->
        <div class="box box-success" id="reportContent">
            <div class="box-header">
                <!-- Report Header -->
                <div class="report-header">
                    <h4><i class="fa fa-file-text"></i> Marks Range Analysis Report</h4>
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
                            <strong>Term</strong>
                            <span><?= htmlspecialchars($term) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Exam</strong>
                            <span><?= $exam_label ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Filter</strong>
                            <span class="badge badge-<?= $filter_type == 'above' ? 'success' : ($filter_type == 'below' ? 'danger' : 'info') ?>">
                                <?= ucfirst($filter_type) ?>
                            </span>
                            <span><?= $filter_label ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="box-body">
                <!-- Statistics Cards -->
                <div class="row">
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h3><?= $total_students ?></h3>
                            <p>Total Students</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                            <h3><?= $filtered_count ?></h3>
                            <p>Matching Sts.</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                            <h3><?= $total_students > 0 ? round(($filtered_count / $total_students) * 100, 1) : 0 ?>%</h3>
                            <p>Percentage</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <h3><?= $avg_marks ?></h3>
                            <p>Avg. (<?= $avg_percent ?>%)</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h3><?= $max_marks ?></h3>
                            <p>Highest Marks</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <h3><?= $min_marks ?></h3>
                            <p>Lowest Marks</p>
                        </div>
                    </div>
                </div>
                
                <?php if($filtered_count > 0): ?>
                <!-- Student Table -->
                <div class="table-responsive">
                    <table id="reportTable" class="table table-bordered table-hover table-striped display nowrap" style="width:100%">
                        <thead>
                            <tr class='op-thead'>
                                <th class='text-center'>#</th>
                                <th class='text-center'>Photo</th>
                                <th class='text-center'>Roll No</th>
                                <th>Admission No</th>
                                <th>Student Name</th>
                                <th>Father Name</th>
                                <th class='text-center'>UT-1 (50)</th>
                                <th class='text-center'>UT-2 (50)</th>
                                <th class='text-center'>MTE (80)</th>
                                <th class='text-center'>Selected Marks</th>
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
                                <td class='text-center <?= $exam_type == 'ut1' ? 'highlight-row' : '' ?>'>
                                    <?= $student['ut1'] ?? '-' ?>
                                </td>
                                <td class='text-center <?= $exam_type == 'ut2' ? 'highlight-row' : '' ?>'>
                                    <?= $student['ut2'] ?? '-' ?>
                                </td>
                                <td class='text-center <?= $exam_type == 'mid_term' ? 'highlight-row' : '' ?>'>
                                    <?= $student['mid_term'] ?? '-' ?>
                                </td>
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
                        <tfoot>
                            <tr class="op-thead">
                                <th colspan="6" class="text-right">Totals / Averages:</th>
                                <th class="text-center">-</th>
                                <th class="text-center">-</th>
                                <th class="text-center">-</th>
                                <th class="text-center"><?= $avg_marks ?></th>
                                <th class="text-center"><?= $avg_percent ?>%</th>
                                <th class="text-center">-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <?php else: ?>
                <!-- No Data Found -->
                <div class="alert alert-warning text-center">
                    <i class="fa fa-exclamation-triangle fa-3x"></i>
                    <h4>No Students Found</h4>
                    <p>No students match the selected criteria: <strong><?= $filter_label ?></strong></p>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
        
        <?php endif; ?>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>
<script>
$(document).ready(function() {

    // Toggle between single value and range inputs
    function toggleFilterInputs() {
        var filterType = $('#filter_type').val();

        if (filterType === 'between') {
            $('.single-value-div').hide();
            $('.range-value-div').show();
            $('#marks_value').prop('required', false);
            $('#marks_from, #marks_to').prop('required', true);
        } else {
            $('.single-value-div').show();
            $('.range-value-div').hide();
            $('#marks_value').prop('required', true);
            $('#marks_from, #marks_to').prop('required', false);
        }
    }

    // When filter type changes
    $('#filter_type').change(function() {
        toggleFilterInputs();
    });

    // On page load
    toggleFilterInputs();

    // Update max marks based on exam type
    $('#exam_type').change(function() {
        var examType = $(this).val();
        var maxMarks = (examType === 'mid_term') ? 80 : 50;
        $('#marks_value, #marks_from, #marks_to').attr('max', maxMarks);
    });

    // Quick filter buttons (if you add any with .quick-filter class)
    $('.quick-filter').click(function() {
        var type      = $(this).data('type');
        var value     = $(this).data('value');
        var from      = $(this).data('from');
        var to        = $(this).data('to');
        var isPercent = $(this).data('percent');

        var examType = $('#exam_type').val();
        var fullMarks = (examType === 'mid_term') ? 80 : 50;

        if (!examType) {
            alert('Please select an exam type first!');
            return;
        }

        $('#filter_type').val(type);
        toggleFilterInputs();

        if (isPercent) {
            if (type === 'between') {
                $('#marks_from').val(((from / 100) * fullMarks).toFixed(2));
                $('#marks_to').val(((to / 100) * fullMarks).toFixed(2));
            } else {
                $('#marks_value').val(((value / 100) * fullMarks).toFixed(2));
            }
        } else {
            if (type === 'between') {
                $('#marks_from').val(from);
                $('#marks_to').val(to);
            } else {
                $('#marks_value').val(value);
            }
        }
    });

    // Initialize DataTable
    if ($('#reportTable').length) {
        $('#reportTable').DataTable({
            pageLength: 25,
            order: [[9, "desc"]],
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    }

});

// Reset form
function resetForm() {
    $('#filter_type').val('');
    $('#exam_type').val('');
    $('#student_class').val('');
    $('#student_section').val('');
    $('#subject').val('');
    $('#term').val('');
    $('#marks_value').val('');
    $('#marks_from').val('');
    $('#marks_to').val('');
    $('.single-value-div').show();
    $('.range-value-div').hide();
}

// Export to Excel
function exportToExcel() {
    $('#reportTable').DataTable().button('.buttons-excel').trigger();
}
</script>

