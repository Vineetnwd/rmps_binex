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
$term            = $_REQUEST['term']            ?? '';
$filter_type     = $_REQUEST['filter_type']     ?? '';
$marks_value     = $_REQUEST['marks_value']     ?? '';
$marks_from      = $_REQUEST['marks_from']      ?? '';
$marks_to        = $_REQUEST['marks_to']        ?? '';

// Full marks per subject (UT-1 + UT-2 + MTE block(mid_term+ma+portfolio+se))
$per_subject_full = 50 + 50 + 100; // 200

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
    .student-photo {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
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
            <li class="breadcrumb-item"><a href="#">Report</a></li>
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
                                    <option value='X'  <?= $student_class == 'X'  ? 'selected' : '' ?>>X</option>
                                </select>
                            </div> 
                            <div class='col-md-3'>
                                <label>Section <span class="text-danger">*</span></label>
                                <select name='student_section' id='student_section' class='form-control' required>
                                    <?= dropdown($section_list, $student_section) ?>
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
                        <h5><i class="fa fa-sliders"></i> Marks Range Filter (applied on Grand Total)</h5>
                        <div class='row' id="marksFilterRow">
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
                                <label>Marks Value (Grand Total) <span class="text-danger">*</span></label>
                                <input type='number' name='marks_value' id='marks_value' class='form-control' 
                                       value='<?= htmlspecialchars($marks_value) ?>' placeholder='Enter total marks value' 
                                       step='0.01' min='0'>
                            </div>
                            
                            <!-- Range Input (for between) -->
                            <div class='col-md-3 range-value-div'>
                                <label>From Marks (Grand Total) <span class="text-danger">*</span></label>
                                <input type='number' name='marks_from' id='marks_from' class='form-control' 
                                       value='<?= htmlspecialchars($marks_from) ?>' placeholder='Minimum total marks' 
                                       step='0.01' min='0'>
                            </div>
                            <div class='col-md-3 range-value-div'>
                                <label>To Marks (Grand Total) <span class="text-danger">*</span></label>
                                <input type='number' name='marks_to' id='marks_to' class='form-control' 
                                       value='<?= htmlspecialchars($marks_to) ?>' placeholder='Maximum total marks' 
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
        if (!empty($_POST) && !empty($student_class) && !empty($student_section) && !empty($term) && !empty($filter_type)):
            
            // Sanitize inputs
            $student_class   = mysqli_real_escape_string($con, $student_class);
            $student_section = mysqli_real_escape_string($con, $student_section);
            $term            = mysqli_real_escape_string($con, $term);
            
            // Build HAVING condition based on filter type on GRAND TOTAL
            $having_condition = "1=1";
            $filter_label     = "";

            switch($filter_type) {
                case 'above':
                    $marks_value      = floatval($marks_value);
                    $having_condition = "grand_total >= $marks_value";
                    $filter_label     = "Grand Total >= $marks_value";
                    break;
                    
                case 'below':
                    $marks_value      = floatval($marks_value);
                    $having_condition = "grand_total <= $marks_value";
                    $filter_label     = "Grand Total <= $marks_value";
                    break;
                    
                case 'between':
                    $marks_from       = floatval($marks_from);
                    $marks_to         = floatval($marks_to);
                    $having_condition = "grand_total BETWEEN $marks_from AND $marks_to";
                    $filter_label     = "Grand Total between $marks_from and $marks_to";
                    break;
                    
                case 'equal':
                    $marks_value      = floatval($marks_value);
                    $having_condition = "grand_total = $marks_value";
                    $filter_label     = "Grand Total = $marks_value";
                    break;
                    
                default:
                    $having_condition = "1=1";
                    $filter_label     = "All Students";
            }

            // Get total students count
            $total_sql = "SELECT COUNT(*) AS total_students 
                          FROM student 
                          WHERE student_class = '$student_class' 
                            AND student_section = '$student_section' 
                            AND status = 'ACTIVE'";
            $total_result   = direct_sql_api($total_sql);
            $total_students = $total_result['data'][0]['total_students'] ?? 0;
            
            // Main query: SUM all subjects marks for each student, and count distinct subjects
            $sql = "
                SELECT 
                    s.id,
                    s.student_admission,
                    s.student_name,
                    s.student_father,
                    s.student_photo,
                    s.student_roll,
                    s.branch_id,

                    COALESCE(SUM(t.ut1), 0) AS total_ut1,
                    COALESCE(SUM(t.ut2), 0) AS total_ut2,

                    -- MTE total = mid_term + ma + portfolio + se (for all subjects)
                    COALESCE(SUM(t.mid_term + t.ma + t.portfolio + t.se), 0) AS total_mte,

                    COALESCE(COUNT(DISTINCT t.subject), 0) AS subject_count,

                    -- Grand Total = UT1 total + UT2 total + MTE total
                    (
                        COALESCE(SUM(t.ut1), 0) 
                      + COALESCE(SUM(t.ut2), 0) 
                      + COALESCE(SUM(t.mid_term + t.ma + t.portfolio + t.se), 0)
                    ) AS grand_total

                FROM student s
                LEFT JOIN tabulation t 
                    ON s.id = t.student_id 
                   AND t.term = '$term'
                WHERE 
                    s.student_class   = '$student_class'
                    AND s.student_section = '$student_section'
                    AND s.status      = 'ACTIVE'
                    AND s.branch_id   = {$_SESSION['branch_id']}
                GROUP BY s.id
                HAVING $having_condition
                ORDER BY grand_total DESC, s.student_roll ASC
            ";

            $result   = direct_sql_api($sql);
            $students = $result['data'] ?? [];
            $filtered_count = count($students);
            
            // Calculate statistics based on grand_total
            $total_grand = 0;
            $max_grand   = 0;
            $min_grand   = PHP_INT_MAX;
            $sum_percent = 0;

            foreach($students as $student) {
                $grand    = floatval($student['grand_total']);
                $subjects = intval($student['subject_count']);
                $full_marks = $subjects > 0 ? $subjects * $per_subject_full : 0;
                $percent    = $full_marks > 0 ? round(($grand / $full_marks) * 100, 2) : 0;

                $total_grand += $grand;
                $sum_percent += $percent;

                if($grand > $max_grand) $max_grand = $grand;
                if($grand < $min_grand) $min_grand = $grand;
            }
            
            $avg_grand   = $filtered_count > 0 ? round($total_grand / $filtered_count, 2) : 0;
            $avg_percent = $filtered_count > 0 ? round($sum_percent / $filtered_count, 2) : 0;
            if($filtered_count == 0) $min_grand = 0;
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
                            <strong>Term</strong>
                            <span><?= htmlspecialchars($term) ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Filter (on Grand Total)</strong>
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
                            <p>Coverage</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <h3><?= $avg_grand ?></h3>
                            <p>Avg. Grand (<?= $avg_percent ?>%)</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h3><?= $max_grand ?></h3>
                            <p>Highest Grand Total</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <h3><?= $min_grand ?></h3>
                            <p>Lowest Grand Total</p>
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
                                <th class='text-center'>UT-1 (Total)</th>
                                <th class='text-center'>UT-2 (Total)</th>
                                <th class='text-center'>MTE (Total)</th>
                                <th class='text-center'>Grand Total</th>
                                <th class='text-center'>Percentage</th>
                                <th class='text-center'>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sr = 0;
                            foreach ($students as $student): 
                                $sr++;
                                $total_ut1   = floatval($student['total_ut1']);
                                $total_ut2   = floatval($student['total_ut2']);
                                $total_mte   = floatval($student['total_mte']); // mid_term+ma+portfolio+se sum
                                $grand_total = floatval($student['grand_total']);
                                $subjects    = intval($student['subject_count']);
                                $full_marks  = $subjects > 0 ? $subjects * $per_subject_full : 0;
                                $percentage  = $full_marks > 0 ? round(($grand_total / $full_marks) * 100, 2) : 0;
                                
                                // Determine grade based on percentage
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
                                
                                // Photo base URL per branch
                                if($_SESSION['branch_id']==1){
                                    $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
                                }else if($_SESSION['branch_id']==2){
                                    $img_url = $base_url.'student_photo/Barauli/Photo/';
                                }else{
                                    $img_url = $base_url.'student_photo/Barharia/Photo/';
                                }
                                $photo = $img_url . $student['student_admission'] . ".jpg";
                            ?>
                            <tr>
                                <td class='text-center'><?= $sr ?></td>
                                <td class='text-center'>
                                    <img src="<?= $photo ?>" 
                                         alt="Photo" 
                                         class="student-photo"
                                         onerror="this.src='https://img.freepik.com/premium-psd/3d-realistic-user-cartoon-vector-concept-3d-vector-render-illustration_996812-1951.jpg';">
                                </td>
                                <td class='text-center'><?= htmlspecialchars($student['student_roll']) ?></td>
                                <td><?= htmlspecialchars($student['student_admission']) ?></td>
                                <td><?= htmlspecialchars($student['student_name']) ?></td>
                                <td><?= htmlspecialchars($student['student_father']) ?></td>
                                <td class='text-center'><?= $total_ut1 ?></td>
                                <td class='text-center'><?= $total_ut2 ?></td>
                                <td class='text-center'><?= $total_mte ?></td>
                                <td class='text-center'>
                                    <strong><?= $grand_total ?></strong>
                                    <?php if($full_marks > 0): ?>
                                        / <?= $full_marks ?>
                                    <?php endif; ?>
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
                                <th colspan="9" class="text-right">Averages (Grand Total & %):</th>
                                <th class="text-center"><?= $avg_grand ?></th>
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
        
        <?php endif; // end if POST ?>
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

    // Initialize DataTable
    if ($('#reportTable').length) {
        $('#reportTable').DataTable({
            pageLength: 25,
            order: [[9, "desc"]], // grand total column index
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    }

});

// Reset form
function resetForm() {
    $('#filter_type').val('');
    $('#student_class').val('');
    $('#student_section').val('');
    $('#term').val('');
    $('#marks_value').val('');
    $('#marks_from').val('');
    $('#marks_to').val('');
    $('.single-value-div').show();
    $('.range-value-div').hide();
}

// Export to Excel (if enabled)
function exportToExcel() {
    $('#reportTable').DataTable().button('.buttons-excel').trigger();
}
</script>
