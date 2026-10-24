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

// this is per-subject full marks for IX/X logic:
// each subject term total (t1t) is effectively out of 100 (internal + mid term)
// so English + Top 4 => full marks = 5 x 100 = 500
$per_subject_full = 100;

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
    .badge-success { background-color: #28a745; }
    .badge-primary { background-color: #007bff; }
    .badge-info    { background-color: #17a2b8; }
    .badge-warning { background-color: #ffc107; }
    .badge-danger  { background-color: #dc3545; }

    @media print {
        .no-print { display: none !important; }
        .content-wrapper { margin-left: 0 !important; }
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1><i class="fa fa-filter"></i> Marks Range Report</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#">Exam Analysis</a></li>
            <li class="breadcrumb-item active">Marks Range Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default no-print">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-filter"></i> Filter Options</h3>
            </div>
            <div class="box-body">
                <form method="post" id="reportForm">
                    <!-- Row 1 -->
                    <div class="filter-card">
                        <h5><i class="fa fa-cog"></i> Basic Filters</h5>
                        <div class='row'>
                            <div class='col-md-3'>
                                <label>Class <span class="text-danger">*</span></label>
                                <select name='student_class' id='student_class' class='form-control' required>
                                    <option value='' disabled <?= empty($student_class) ? 'selected' : '' ?>>Select Class</option>
                                    <?php dropdown($class_list, $student_class); ?>
                                </select>
                            </div> 
                            <div class='col-md-3'>
                                <label>Section <span class="text-danger">*</span></label>
                                <select name='student_section' id='student_section' class='form-control' required>
                                    <?php dropdown($section_list, $student_section); ?>
                                </select>
                            </div> 
                            <div class='col-md-3'>
                                <label>Term <span class="text-danger">*</span></label>
                                <select name='term' id='term' class='form-control' required>
                                    <option value='' disabled <?= empty($term) ? 'selected' : '' ?>>Select Term</option>
                                    <option value='TERM 1' <?= $term == 'TERM 1' ? 'selected' : '' ?>>TERM 1</option>
                                    <option value='TERM 2' <?= $term == 'TERM 2' ? 'selected' : '' ?>>TERM 2</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Percentage Filter -->
                    <div class="filter-card">
                        <h5><i class="fa fa-sliders"></i> Percentage Filter (applied on Overall %)</h5>
                        <div class='row'>
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

                            <!-- Single value -->
                            <div class='col-md-6 single-value-div'>
                                <label>Percentage Value <span class="text-danger">*</span></label>
                                <input type='number' name='marks_value' id='marks_value' class='form-control' 
                                       value='<?= htmlspecialchars($marks_value) ?>' placeholder='Enter percentage' 
                                       step='0.01' min='0' max='100'>
                            </div>

                            <!-- Range value -->
                            <div class='col-md-3 range-value-div'>
                                <label>From % <span class="text-danger">*</span></label>
                                <input type='number' name='marks_from' id='marks_from' class='form-control' 
                                       value='<?= htmlspecialchars($marks_from) ?>' placeholder='Minimum %' 
                                       step='0.01' min='0' max='100'>
                            </div>
                            <div class='col-md-3 range-value-div'>
                                <label>To % <span class="text-danger">*</span></label>
                                <input type='number' name='marks_to' id='marks_to' class='form-control' 
                                       value='<?= htmlspecialchars($marks_to) ?>' placeholder='Maximum %' 
                                       step='0.01' min='0' max='100'>
                            </div>
                        </div>
                    </div>

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
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

<?php
// MAIN PROCESS
if (!empty($_POST) && !empty($student_class) && !empty($student_section) && !empty($term) && !empty($filter_type)):

    // only IX & X logic is guaranteed same-as-tabulation
    $student_class   = mysqli_real_escape_string($con, $student_class);
    $student_section = mysqli_real_escape_string($con, $student_section);
    $term            = mysqli_real_escape_string($con, $term);

    // build filter label
    switch($filter_type) {
        case 'above':
            $filter_label = "Percentage >= " . floatval($marks_value) . "%";
            break;
        case 'below':
            $filter_label = "Percentage <= " . floatval($marks_value) . "%";
            break;
        case 'between':
            $filter_label = "Percentage between " . floatval($marks_from) . "% and " . floatval($marks_to) . "%";
            break;
        case 'equal':
            $filter_label = "Percentage = " . floatval($marks_value) . "%";
            break;
        default:
            $filter_label = "All Students";
    }

    // total students
    $total_sql = "SELECT COUNT(*) AS total_students 
                  FROM student 
                  WHERE student_class = '$student_class' 
                    AND student_section = '$student_section' 
                    AND status = 'ACTIVE'
                    AND branch_id = {$_SESSION['branch_id']}";
    $total_result   = direct_sql_api($total_sql);
    $total_students = $total_result['data'][0]['total_students'] ?? 0;

    // fetch students
    $stu_sql = "SELECT * FROM student 
                WHERE student_class = '$student_class'
                  AND student_section = '$student_section'
                  AND status = 'ACTIVE'
                  AND branch_id = {$_SESSION['branch_id']}
                ORDER BY CAST(student_roll AS UNSIGNED)";
    $stu_res  = direct_sql_api($stu_sql);
    $students = $stu_res['data'] ?? [];

    $rows = [];

    foreach($students as $stu){

        $student_id = $stu['id'];

        // --------------------------
        // TABULATION LOGIC FOR IX/X
        // --------------------------
        $tab_sql = "SELECT subject, ut1, ut2, ma, portfolio, se, mid_term
                    FROM tabulation
                    WHERE student_id = '$student_id'
                      AND term = '$term'";
        $tab_res  = direct_sql($tab_sql, 'get', false);
        $tab_data = $tab_res['data'] ?? [];

        $subject_totals = [];
        $total_ut1 = 0;
        $total_ut2 = 0;
        $total_mte = 0; // sum(mid + ma + portfolio + se)

        if (!empty($tab_data)) {
            foreach($tab_data as $row){
                $sub_id = $row['subject'];

                $ut1 = floatval($row['ut1']);
                $ut2 = floatval($row['ut2']);
                $ma1 = floatval($row['ma']);
                $portfolio1 = floatval($row['portfolio']);
                $se1 = floatval($row['se']);
                $mid1 = floatval($row['mid_term']);

                // same as your IX/X logic:
                $mut1 = 0;
                if ($sub_id != '46') {
                    $mut1 = max($ut1, $ut2) / 10; // internal from best UT
                }

                $prem = $mut1 + $ma1 + $portfolio1 + $se1;
                $t1t  = $mid1 + $prem; // TERM total per subject (out of 100)

                $total_ut1 += $ut1;
                $total_ut2 += $ut2;
                $total_mte += ($mid1 + $ma1 + $portfolio1 + $se1);

                $subject_totals[$sub_id] = $t1t;
            }
        }

        $subject_count = count($subject_totals);

        $grand_total        = 0;
        $effective_subjects = 0;

        if (($student_class == 'IX' || $student_class == 'X') && $subject_count > 0) {

            // exact same idea: English + Top 4
            $main_subject = 41;  // English subject id – change if needed
            $tp5 = 0;

            if(isset($subject_totals[$main_subject])){
                $tp5 += $subject_totals[$main_subject];
            }

            $other_subjects = $subject_totals;
            unset($other_subjects[$main_subject]);

            if(!empty($other_subjects)){
                arsort($other_subjects);               // high to low
                $top4 = array_slice($other_subjects,0,4);
                $tp5 += array_sum($top4);
            }

            $grand_total        = $tp5;
            $effective_subjects = 5;      // english + top 4
            $full_marks         = $effective_subjects * $per_subject_full; // 5 x 100 = 500
            $percentage         = $full_marks > 0 ? round(($grand_total / $full_marks) * 100, 2) : 0;
        } else {
            // for safety: if not IX/X or no subjects, skip
            $grand_total = 0;
            $full_marks  = 0;
            $percentage  = 0;
        }

        // FILTER ON PERCENTAGE
        $include = false;
        $p = $percentage;

        switch($filter_type){
            case 'above':
                $include = ($p >= floatval($marks_value));
                break;
            case 'below':
                $include = ($p <= floatval($marks_value));
                break;
            case 'between':
                $include = ($p >= floatval($marks_from) && $p <= floatval($marks_to));
                break;
            case 'equal':
                $include = ($p == floatval($marks_value));
                break;
            default:
                $include = true;
        }

        if(!$include) continue;

        // grade on percentage (0–100)
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

        // photo url according to branch
        if($_SESSION['branch_id']==1){
            $img_url = $base_url.'student_photo/Bishambharpur/Photo/';
        }else if($_SESSION['branch_id']==2){
            $img_url = $base_url.'student_photo/Barauli/Photo/';
        }else{
            $img_url = $base_url.'student_photo/Barharia/Photo/';
        }
        $photo = $img_url . $stu['student_admission'] . ".jpg";

        $rows[] = [
            'student'      => $stu,
            'photo'        => $photo,
            'total_ut1'    => $total_ut1,
            'total_ut2'    => $total_ut2,
            'total_mte'    => $total_mte,
            'grand_total'  => $grand_total,
            'full_marks'   => $full_marks ?? 0,
            'percentage'   => $percentage,
            'grade'        => $grade,
            'grade_class'  => $grade_class,
        ];
    } // foreach students

    $filtered_count = count($rows);

    // stats
    $total_grand = 0;
    $sum_percent = 0;
    $max_grand   = 0;
    $min_grand   = PHP_INT_MAX;

    foreach($rows as $r){
        $g = $r['grand_total'];
        $p = $r['percentage'];
        $total_grand += $g;
        $sum_percent += $p;
        if($g > $max_grand) $max_grand = $g;
        if($g < $min_grand) $min_grand = $g;
    }

    $avg_grand   = $filtered_count ? round($total_grand / $filtered_count, 2) : 0;
    $avg_percent = $filtered_count ? round($sum_percent / $filtered_count, 2) : 0;
    if($filtered_count == 0) $min_grand = 0;
?>

        <div class="box box-success" id="reportContent">
            <div class="box-header">
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
                            <strong>Filter (on Percentage)</strong>
                            <span><?= $filter_label ?></span>
                        </div>
                        <div class="report-info-item">
                            <strong>Grand Total Logic</strong>
                            <span>English + Top 4 Subjects (IX & X)</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box-body">
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
                            <p>Avg. Grand</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <h3><?= $avg_percent ?>%</h3>
                            <p>Avg. Percentage</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="stats-card" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <h3><?= $max_grand ?></h3>
                            <p>Highest Total</p>
                        </div>
                    </div>
                </div>

                <?php if($filtered_count > 0): ?>
                <div class="table-responsive">
                    <table id="reportTable" class="table table-bordered table-hover table-striped display nowrap" style="width:100%">
                        <thead>
                            <tr class="op-thead">
                                <th class='text-center'>#</th>
                                <th class='text-center'>Photo</th>
                                <th class='text-center'>Roll No</th>
                                <th>Admission No</th>
                                <th>Student Name</th>
                                <th>Father Name</th>
                                <th class='text-center'>UT-1 (Total)</th>
                                <th class='text-center'>UT-2 (Total)</th>
                                <th class='text-center'>MTE (mid+ma+portfolio+se)</th>
                                <th class='text-center'>Total (Eng+Top4)</th>
                                <th class='text-center'>Percentage</th>
                                <th class='text-center'>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $sr = 0;
                            foreach($rows as $r):
                                $sr++;
                                $stu         = $r['student'];
                                $photo       = $r['photo'];
                                $total_ut1   = $r['total_ut1'];
                                $total_ut2   = $r['total_ut2'];
                                $total_mte   = $r['total_mte'];
                                $grand_total = $r['grand_total'];
                                $full_marks  = $r['full_marks'];
                                $percentage  = $r['percentage'];
                                $grade       = $r['grade'];
                                $grade_class = $r['grade_class'];
                            ?>
                            <tr>
                                <td class='text-center'><?= $sr ?></td>
                                <td class='text-center'>
                                    <img src="<?= $photo ?>" alt="Photo" class="student-photo"
                                        onerror="this.src='https://img.freepik.com/premium-psd/3d-realistic-user-cartoon-vector-concept-3d-vector-render-illustration_996812-1951.jpg';">
                                </td>
                                <td class='text-center'><?= htmlspecialchars($stu['student_roll']) ?></td>
                                <td><?= htmlspecialchars($stu['student_admission']) ?></td>
                                <td><?= htmlspecialchars($stu['student_name']) ?></td>
                                <td><?= htmlspecialchars($stu['student_father']) ?></td>
                                <td class='text-center'><?= $total_ut1 ?></td>
                                <td class='text-center'><?= $total_ut2 ?></td>
                                <td class='text-center'><?= $total_mte ?></td>
                                <td class='text-center'>
                                    <strong><?= $grand_total ?></strong>
                                    <?php if($full_marks > 0): ?>
                                        / <?= $full_marks ?>
                                    <?php endif; ?>
                                </td>
                                <td class='text-center'><strong><?= $percentage ?>%</strong></td>
                                <td class='text-center'>
                                    <span class="badge badge-<?= $grade_class ?>"><?= $grade ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="op-thead">
                                <th colspan="9" class="text-right">Averages:</th>
                                <th class="text-center"><?= $avg_grand ?></th>
                                <th class="text-center"><?= $avg_percent ?>%</th>
                                <th class="text-center">-</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <?php else: ?>
                    <div class="alert alert-warning text-center">
                        <i class="fa fa-exclamation-triangle fa-3x"></i>
                        <h4>No Students Found</h4>
                        <p>No students match: <strong><?= $filter_label ?></strong></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

<?php endif; // end main if ?>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
$(document).ready(function() {
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

    $('#filter_type').change(function() {
        toggleFilterInputs();
    });

    toggleFilterInputs();

    if ($('#reportTable').length) {
        $('#reportTable').DataTable({
            pageLength: 25,
            order: [[10, "desc"]], // sort by Percentage column
            dom: 'Bfrtip',
            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
        });
    }
});

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
</script>
