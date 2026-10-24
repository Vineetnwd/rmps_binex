<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $_REQUEST['student_class'] ?? $_REQUEST['class_name'] ?? null;
$student_section = $_REQUEST['student_section'] ?? $_REQUEST['section'] ?? null;
$month = $_REQUEST['month'] ?? date('Y-m');

$class_name = $student_class;
$section = $student_section;

// Fetch assigned classes/sections for Teachers
if (strtoupper($user_type) == 'TEACHER') {
    $emp = get_data('employee', strtoupper($_SESSION['user_name']), null, 'emp_code')['data'];
    $teacher_id = $emp['id'];

    $assigned_classes = direct_sql("SELECT DISTINCT class_name FROM teacher_subject WHERE teacher_id = '$teacher_id' AND status='ACTIVE'");
    $class_list = array_column($assigned_classes['data'] ?? [], 'class_name');

    $assigned_sections = direct_sql("SELECT DISTINCT section_name FROM teacher_subject WHERE teacher_id = '$teacher_id' AND status='ACTIVE'");
    $section_list = array_column($assigned_sections['data'] ?? [], 'section_name');
} else {
    // For Admin/Dev, fetch all classes and sections
    if (empty($class_list)) {
        $assigned_classes = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
        $class_list = array_column($assigned_classes['data'] ?? [], 'student_class');
    }
    if (empty($section_list)) {
        $assigned_sections = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
        $section_list = array_column($assigned_sections['data'] ?? [], 'student_section');
    }
}


$m_value = remove_space(date('M_Y', strtotime($month)));
$days_in_month = date('t', strtotime($month));
?>
<title> <?php echo $inst_name; ?> - Attendance Report </title>
<style>
    .table-responsive {
        overflow-x: auto !important;
        overflow-y: visible !important;
        -webkit-overflow-scrolling: touch;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
    }

    .att-cell {
        text-align: center;
        font-weight: 600;
        padding: 8px 4px !important;
        min-width: 38px;
        font-size: 11px;
        border: 1px solid #eef2f7 !important;
    }

    .att-P { background-color: #d1fae5; color: #065f46; }
    .att-A { background-color: #fee2e2; color: #991b1b; }
    .att-L { background-color: #fef3c7; color: #92400e; }
    .att-H { background-color: #e0f2fe; color: #0369a1; }

    .sun-cell {
        background-color: #f8fafc;
        color: #ef4444;
    }

    .sticky-col {
        position: sticky !important;
        left: 0;
        background-color: white !important;
        z-index: 20 !important;
        border-right: 2px solid #e2e8f0 !important;
        box-shadow: 2px 0 5px rgba(0,0,0,0.05);
    }

    thead .sticky-col {
        z-index: 30 !important; /* Higher for header sticky */
    }

    .summary-card {
        padding: 15px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .summary-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .bg-blue-light { background: #e0f2fe; color: #0369a1; }
    .bg-green-light { background: #d1fae5; color: #065f46; }
    .text-blue { color: #0369a1; }
    .text-green { color: #065f46; }
    
    .defaulter { color: #ef4444 !important; }
    

.table tbody td {
    padding: 5px 10px !important;
}
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1> Attendance Report <small>Module 2</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Attendance Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Monthly Attendance Register</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success btn-sm"
                        onclick="exportTableToExcel('att-table', 'Attendance_Report_<?= $class_name ?>_<?= $month ?>')"><i
                            class="fa fa-file-excel-o"></i> Export to Excel</button>
                </div>
            </div>
            <div class="box-body">
                <div class="premium-card">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Class</label>
                                    <select class="form-control form-control-premium" name="student_class" required>
                                        <?php dropdown($class_list, $class_name); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Section</label>
                                    <select class="form-control form-control-premium" name="student_section" required>
                                        <?php dropdown($section_list, $section); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Month</label>
                                    <input type="month" name="month" class="form-control form-control-premium"
                                        value="<?= $month ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-premium btn-premium-primary btn-block">
                                    <i class="fa fa-refresh"></i> Generate Detailed Report
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if ($class_name && $section && $month): ?>
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="summary-card bg-white">
                                <div class="summary-icon bg-blue-light text-blue"><i class="fa fa-users"></i></div>
                                <div><small class="text-muted">Total Students</small>
                                    <h4 class="m-0 font-weight-bold" id="total-count">0</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="summary-card bg-white">
                                <div class="summary-icon bg-green-light text-green"><i class="fa fa-check-circle"></i></div>
                                <div><small class="text-muted">Avg. Attendance</small>
                                    <h4 class="m-0 font-weight-bold" id="avg-att">0%</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="att-table" style="background: white; margin-bottom: 0;">
                            <thead>
                                <tr style="background: #f8fafc;">
                                    <th rowspan="2" class="sticky-col" style="vertical-align:middle;">
                                        #</th>
                                    <th rowspan="2" class="sticky-col" style="vertical-align:middle; min-width: 150px;">
                                        Student Details</th>
                                    <th colspan="<?= $days_in_month ?>"
                                        class="text-center text-uppercase small font-weight-bold"
                                        style="letter-spacing: 1px; border-bottom: 2px solid #e2e8f0;">
                                        <?= date('F Y', strtotime($month)) ?>
                                    </th>
                                    <th colspan="4" class="text-center bg-light" style="vertical-align:middle">Summary</th>
                                </tr>
                                <tr style="background: #f8fafc;">
                                    <?php
                                    for ($d = 1; $d <= $days_in_month; $d++) {
                                        $day_name = date('D', strtotime("$month-$d"));
                                        $cls = ($day_name == 'Sun') ? 'sun-cell' : '';
                                        echo "<th class='att-cell $cls'>$d<br><span style='font-size:9px'>$day_name</span></th>";
                                    }
                                    ?>
                                    <th class="att-cell bg-success text-white">P</th>
                                    <th class="att-cell bg-danger text-white">A</th>
                                    <th class="att-cell bg-warning text-white">L</th>
                                    <th class="att-cell bg-primary text-white">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $students = get_all('student', '*', ['student_class' => $class_name, 'student_section' => $section, 'status' => 'ACTIVE'], 'student_roll');

                                if (!empty($students['data'])) {
                                    $grand_total_p = 0;
                                    $grand_total_l = 0;
                                    $grand_total_students = count($students['data']);
                                    $days_total_working = 0; // Days in month excluding Sundays
                                    $i=1;
                                    foreach ($students['data'] as $s) {
                                        $stu_id = $s['id'];
                                        $att_row = get_all('student_att', '*', ['student_id' => $stu_id, 'att_month' => $m_value]);
                                        $p_count = 0;
                                        $a_count = 0;
                                        $l_count = 0;
                                        $total_working = 0;

                                        echo "<tr>";
                                        echo "<td class='sticky-col' >$i</td>";$i++;
                                        echo "<td class='sticky-col' style='white-space:nowrap'>
                                            <div class='font-weight-bold text-dark'>{$s['student_name']}</div>
                                            <small class='text-muted'>Adm: {$s['student_admission']} | Roll: {$s['student_roll']}</small>
                                           </td>";

                                        for ($d = 1; $d <= $days_in_month; $d++) {
                                            $status = '';
                                            if ($att_row['count'] > 0) {
                                                $status = $att_row['data'][0]['d_' . $d];
                                            }

                                            $day_name = date('D', strtotime("$month-$d"));
                                            $cls = ($day_name == 'Sun') ? 'sun-cell' : '';
                                            if ($status == 'P') {
                                                $cls .= ' att-P';
                                                $p_count++;
                                            }
                                            if ($status == 'A') {
                                                $cls .= ' att-A';
                                                $a_count++;
                                            }
                                            if ($status == 'L') {
                                                $cls .= ' att-L';
                                                $l_count++;
                                            }

                                            if ($day_name != 'Sun')
                                                $total_working++;

                                            echo "<td class='att-cell $cls' style='vertical-align:middle'>$status</td>";
                                        }

                                        $att_per = ($total_working > 0) ? round((($p_count + $l_count) / $total_working) * 100, 1) : 0;
                                        $per_cls = ($att_per < 75) ? 'defaulter' : '';

                                        echo "<td class='text-center font-weight-bold'>$p_count</td>";
                                        echo "<td class='text-center font-weight-bold'>$a_count</td>";
                                        echo "<td class='text-center font-weight-bold'>$l_count</td>";
                                        echo "<td class='text-center font-weight-bold $per_cls'>$att_per%</td>";
                                        echo "</tr>";

                                        $grand_total_p += $p_count;
                                        $grand_total_l += $l_count;
                                        $days_total_working = $total_working; // Same for all students
                                    }

                                    $class_avg_att = ($days_total_working > 0 && $grand_total_students > 0)
                                        ? round((($grand_total_p + $grand_total_l) / ($days_total_working * $grand_total_students)) * 100, 1)
                                        : 0;
                                } else {
                                    echo "<tr><td colspan='" . ($days_in_month + 5) . "' class='text-center p-5 text-muted'>No active students found in this class/section.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <script>
                        document.getElementById('total-count').innerText = "<?= $grand_total_students ?? 0 ?>";
                        document.getElementById('avg-att').innerText = "<?= $class_avg_att ?? 0 ?>%";
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    function exportTableToExcel(tableID, filename = '') {
        var tableSelect = document.getElementById(tableID);
        var tableHTML = tableSelect.outerHTML;

        // Create a proper HTML document wrapper for Excel
        var excelTemplate = `
            <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
            <head>
                <meta charset="utf-8">
                <style>
                    table { border-collapse: collapse; }
                    th, td { border: 1px solid #ccc; padding: 5px; }
                    .att-P { background-color: #d1fae5; }
                    .att-A { background-color: #fee2e2; }
                    .att-L { background-color: #fef3c7; }
                    .sun-cell { background-color: #f8fafc; color: #ef4444; }
                </style>
            </head>
            <body>
                ${tableHTML}
            </body>
            </html>`;

        var dataType = 'application/vnd.ms-excel';
        filename = filename ? filename + '.xls' : 'attendance_report.xls';

        // Use Blob for better handling of large data and special characters
        var blob = new Blob(['\ufeff', excelTemplate], { type: dataType });

        if (window.navigator && window.navigator.msSaveOrOpenBlob) {
            window.navigator.msSaveOrOpenBlob(blob, filename);
        } else {
            var downloadLink = document.createElement("a");
            var url = URL.createObjectURL(blob);

            downloadLink.href = url;
            downloadLink.download = filename;

            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
            URL.revokeObjectURL(url);
        }
    }
</script>