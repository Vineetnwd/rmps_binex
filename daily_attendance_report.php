<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
$att_date = $_REQUEST['att_date'] ?? date('Y-m-d');
$m_value = remove_space(date('M_Y', strtotime($att_date)));
$day_num = date('j', strtotime($att_date));
$day_name = date('l', strtotime($att_date));
?>

<title><?php echo $CONFIG['inst_name'] ?? 'RMPS'; ?> - Daily Attendance Report</title>

<style>
    .report-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 24px;
        margin-bottom: 25px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .stat-card {
        border-radius: 10px;
        padding: 16px;
        color: #ffffff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-2px);
    }

    .bg-gradient-total {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    }

    .bg-gradient-present {
        background: linear-gradient(135deg, #10b981, #047857);
    }

    .bg-gradient-leave {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .bg-gradient-absent {
        background: linear-gradient(135deg, #ef4444, #b91c1c);
    }

    .stat-value {
        font-size: 26px;
        font-weight: 800;
        margin-top: 5px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.9;
    }

    .table-premium {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .table-premium th {
        background-color: #0f172a !important;
        color: #ffffff !important;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
        border: none !important;
        padding: 12px 15px !important;
    }

    .table-premium td {
        vertical-align: middle !important;
        border-bottom: 1px solid #f1f5f9 !important;
        padding: 10px 15px !important;
        font-size: 13px;
        color: #334155;
    }

    .table-premium tbody tr:hover {
        background-color: #f8fafc;
    }

    .present-input, .leave-input {
        width: 80px;
        text-align: center;
        font-weight: 700;
        border-radius: 6px;
        border: 1.5px solid #cbd5e1;
        padding: 4px 8px;
        color: #0f172a;
        background-color: #ffffff;
        transition: border-color 0.2s;
    }

    .present-input:focus, .leave-input:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .disabled-value {
        font-weight: 700;
        color: #475569;
    }

    .grand-total-row {
        background-color: #f8fafc !important;
        font-weight: 800 !important;
        border-top: 2px solid #0f172a !important;
    }

    .percentage-badge {
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
    }

    .badge-present {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-leave {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-absent {
        background-color: #fee2e2;
        color: #991b1b;
    }

    @media print {
        .no-print {
            display: none !important;
        }

        .content-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }

        .report-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
        }

        .table-premium th {
            background-color: #334155 !important;
            color: #ffffff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .grand-total-row {
            background-color: #f1f5f9 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .present-input, .leave-input {
            border: none !important;
            background: transparent !important;
            padding: 0 !important;
            width: auto !important;
        }
    }
</style>

<div class="content-wrapper">
    <section class="content-header no-print">
        <h1>Daily Attendance Report <small>Summary & Stats</small></h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Daily Attendance Report</li>
        </ol>
    </section>

    <section class="content">
        <div class="report-card">
            <!-- Filter Section -->
            <div class="no-print mb-4" style="border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                <form action="" method="get" class="row align-items-end">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group mb-0">
                            <label class="font-weight-bold text-dark" style="font-size: 13px;"><i class="fa fa-calendar"></i> Select Date</label>
                            <input type="date" name="att_date" class="form-control form-control-premium" value="<?= htmlspecialchars($att_date) ?>" required>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6">
                        <button type="submit" class="btn btn-premium btn-premium-primary btn-block">
                            <i class="fa fa-refresh"></i> Generate Report
                        </button>
                    </div>
                    <div class="col-md-5 col-sm-12 text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-success" onclick="exportToExcel('attendance-table', 'Daily_Attendance_Report_<?= $att_date ?>')">
                                <i class="fa fa-file-excel-o"></i> Export to Excel
                            </button>
                            <button type="button" class="btn btn-warning" onclick="window.print()">
                                <i class="fa fa-print"></i> Print Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Print Header -->
            <div class="visible-print-block d-none d-print-block text-center mb-4">
                <h2 class="m-0 font-weight-bold" style="text-transform: uppercase;"><?= htmlspecialchars($CONFIG['full_name'] ?? 'R M Public School') ?></h2>
                <p class="m-1" style="font-size: 12px; font-weight: 600;">DAILY ATTENDANCE SUMMARY REPORT</p>
                <h4 class="m-1 font-weight-bold" style="border: 2px solid #000; display: inline-block; padding: 4px 15px; border-radius: 4px;">
                    DATE: <?= date('d-M-Y', strtotime($att_date)) ?> (<?= strtoupper($day_name) ?>)
                </h4>
            </div>

            <!-- Summary Badges Dashboard -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card bg-gradient-total">
                        <div class="stat-label">Total Strength</div>
                        <div class="stat-value" id="dashboard-strength">0</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card bg-gradient-present">
                        <div class="stat-label">Total Present</div>
                        <div class="stat-value" id="dashboard-present">0</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card bg-gradient-leave">
                        <div class="stat-label">Total Leave</div>
                        <div class="stat-value" id="dashboard-leave">0</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="stat-card bg-gradient-absent">
                        <div class="stat-label">Total Absent</div>
                        <div class="stat-value" id="dashboard-absent">0</div>
                    </div>
                </div>
            </div>

            <!-- Main Data Table -->
            <div class="table-responsive table-premium">
                <table class="table table-bordered mb-0" id="attendance-table">
                    <thead>
                        <tr>
                            <th width="6%" class="text-center">S.No.</th>
                            <th width="22%">Class/Section</th>
                            <th width="15%" class="text-center">Student Present</th>
                            <th width="15%" class="text-center">Student Leave</th>
                            <th width="15%" class="text-center">Student Absent</th>
                            <th width="13%" class="text-center">Total Strength</th>
                            <th width="14%" class="text-center">Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $class_sec_q = "SELECT student_class, student_section, COUNT(*) as total_strength 
                                        FROM student 
                                        WHERE status='ACTIVE' AND branch_id='$branch_id' 
                                        GROUP BY student_class, student_section 
                                        ORDER BY student_class ASC, student_section ASC";
                        $class_sec_res = direct_sql($class_sec_q);

                        $sno = 1;
                        if (($class_sec_res['count'] ?? 0) > 0) {
                            foreach ($class_sec_res['data'] as $row) {
                                $c_name = $row['student_class'];
                                $s_name = $row['student_section'];

                                if (empty($c_name) || empty($s_name)) {
                                    continue;
                                }

                                $c_name_esc = mysqli_real_escape_string($con, $c_name);
                                $s_name_esc = mysqli_real_escape_string($con, $s_name);

                                // Query count of students marked 'P' on this day
                                $p_q = "SELECT COUNT(*) as count 
                                        FROM student_att sa 
                                        JOIN student s ON sa.student_id = s.id 
                                        WHERE s.student_class = '$c_name_esc' 
                                          AND s.student_section = '$s_name_esc' 
                                          AND s.status = 'ACTIVE' 
                                          AND sa.att_month = '$m_value' 
                                          AND sa.d_$day_num = 'P' 
                                          AND sa.branch_id = '$branch_id'";
                                $p_res = direct_sql($p_q);
                                $present_count = intval($p_res['data'][0]['count'] ?? 0);

                                // Query count of students marked 'L' on this day
                                $l_q = "SELECT COUNT(*) as count 
                                        FROM student_att sa 
                                        JOIN student s ON sa.student_id = s.id 
                                        WHERE s.student_class = '$c_name_esc' 
                                          AND s.student_section = '$s_name_esc' 
                                          AND s.status = 'ACTIVE' 
                                          AND sa.att_month = '$m_value' 
                                          AND sa.d_$day_num = 'L' 
                                          AND sa.branch_id = '$branch_id'";
                                $l_res = direct_sql($l_q);
                                $leave_count = intval($l_res['data'][0]['count'] ?? 0);

                                $total_strength = intval($row['total_strength']);
                                
                                // Auto-calculated Absent and Percentage
                                $absent_count = $total_strength - $present_count - $leave_count;
                                $percentage = ($total_strength > 0) ? round((($present_count + $leave_count) / $total_strength) * 100, 1) : 0;
                                ?>
                                <tr class="attendance-row" data-sno="<?= $sno ?>">
                                    <td class="text-center font-weight-bold"><?= $sno ?></td>
                                    <td class="font-weight-bold text-dark"><?= htmlspecialchars($c_name) ?>-<?= htmlspecialchars($s_name) ?></td>
                                    <td class="text-center">
                                        <input type="number" 
                                               class="present-input no-print" 
                                               value="<?= $present_count ?>" 
                                               min="0" 
                                               max="<?= $total_strength ?>" 
                                               data-strength="<?= $total_strength ?>">
                                        <span class="visible-print-inline d-none d-print-inline disabled-value print-present"><?= $present_count ?></span>
                                    </td>
                                    <td class="text-center">
                                        <input type="number" 
                                               class="leave-input no-print" 
                                               value="<?= $leave_count ?>" 
                                               min="0" 
                                               max="<?= $total_strength ?>" 
                                               data-strength="<?= $total_strength ?>">
                                        <span class="visible-print-inline d-none d-print-inline disabled-value print-leave"><?= $leave_count ?></span>
                                    </td>
                                    <td class="text-center font-weight-bold text-danger disabled-value row-absent"><?= $absent_count ?></td>
                                    <td class="text-center font-weight-bold text-primary disabled-value row-strength" data-strength="<?= $total_strength ?>"><?= $total_strength ?></td>
                                    <td class="text-center">
                                        <span class="percentage-badge badge-present row-percentage"><?= $percentage ?>%</span>
                                    </td>
                                </tr>
                                <?php
                                $sno++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center text-muted p-4'>No active classes found with students.</td></tr>";
                        }
                        ?>
                    </tbody>
                    <tfoot>
                        <tr class="grand-total-row">
                            <td colspan="2" class="text-right text-uppercase">Grand Total:</td>
                            <td class="text-center text-success" id="total-present">0</td>
                            <td class="text-center text-warning" id="total-leave">0</td>
                            <td class="text-center text-danger" id="total-absent">0</td>
                            <td class="text-center text-primary" id="total-strength">0</td>
                            <td class="text-center" style="padding: 0 !important; vertical-align: middle;">
                                <div style="display: flex; flex-direction: column; gap: 4px; padding: 6px; font-size: 11px; align-items: center; justify-content: center; line-height: 1.2;">
                                    <span class="percentage-badge badge-present" id="overall-present-pct" style="display: block; width: 100%; max-width: 105px; text-align: center;">Present: 0%</span>
                                    <span class="percentage-badge badge-leave" id="overall-leave-pct" style="display: block; width: 100%; max-width: 105px; text-align: center;">Leave: 0%</span>
                                    <span class="percentage-badge badge-absent" id="overall-absent-pct" style="display: block; width: 100%; max-width: 105px; text-align: center;">Absent: 0%</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
$(document).ready(function() {
    // Function to calculate and update all totals dynamically
    function recalculateReport() {
        let totalStrength = 0;
        let totalPresent = 0;
        let totalLeave = 0;
        let totalAbsent = 0;

        $('.attendance-row').each(function() {
            let row = $(this);
            let strength = parseInt(row.find('.row-strength').data('strength')) || 0;
            
            let presentInput = row.find('.present-input');
            let presentVal = parseInt(presentInput.val());
            if (isNaN(presentVal) || presentVal < 0) presentVal = 0;

            let leaveInput = row.find('.leave-input');
            let leaveVal = parseInt(leaveInput.val());
            if (isNaN(leaveVal) || leaveVal < 0) leaveVal = 0;

            // Prevent combined inputs exceeding total strength
            if (presentVal + leaveVal > strength) {
                // If present exceeds, cap present first
                if (presentVal > strength) {
                    presentVal = strength;
                    presentInput.val(strength);
                    leaveVal = 0;
                    leaveInput.val(0);
                } else {
                    // Cap leave so total matches strength
                    leaveVal = strength - presentVal;
                    leaveInput.val(leaveVal);
                }
            }

            // Sync print span values
            row.find('.print-present').text(presentVal);
            row.find('.print-leave').text(leaveVal);

            let absentVal = strength - presentVal - leaveVal;
            let percentage = (strength > 0) ? (((presentVal + leaveVal) / strength) * 100).toFixed(1) : "0.0";

            // Update row fields in real-time
            row.find('.row-absent').text(absentVal);
            row.find('.row-percentage').text(percentage + '%');

            // Accumulate totals
            totalStrength += strength;
            totalPresent += presentVal;
            totalLeave += leaveVal;
            totalAbsent += absentVal;
        });

        // Calculate overall percentages
        let presentPct = (totalStrength > 0) ? ((totalPresent / totalStrength) * 100).toFixed(1) : "0.0";
        let leavePct = (totalStrength > 0) ? ((totalLeave / totalStrength) * 100).toFixed(1) : "0.0";
        let absentPct = (totalStrength > 0) ? ((totalAbsent / totalStrength) * 100).toFixed(1) : "0.0";

        // Update Grand Totals row
        $('#total-strength').text(totalStrength);
        $('#total-present').text(totalPresent);
        $('#total-leave').text(totalLeave);
        $('#total-absent').text(totalAbsent);
        $('#overall-present-pct').text('Present: ' + presentPct + '%');
        $('#overall-leave-pct').text('Leave: ' + leavePct + '%');
        $('#overall-absent-pct').text('Absent: ' + absentPct + '%');

        // Update Widgets Dashboard
        $('#dashboard-strength').text(totalStrength);
        $('#dashboard-present').text(totalPresent);
        $('#dashboard-leave').text(totalLeave);
        $('#dashboard-absent').text(totalAbsent);
    }

    // Bind event handler to inputs
    $('.present-input, .leave-input').on('keyup change input', function() {
        recalculateReport();
    });

    // Run initial calculation on page load
    recalculateReport();
});

// Excel exporter function matching premium styles
function exportToExcel(tableID, filename = '') {
    var tableSelect = document.getElementById(tableID);
    
    // Create a copy of the table to manipulate for Excel (replace inputs with static text)
    var tableCopy = tableSelect.cloneNode(true);
    $(tableCopy).find('.present-input, .leave-input').remove(); // Remove inputs
    $(tableCopy).find('.visible-print-inline').removeClass('visible-print-inline d-none d-print-inline'); // Make print values visible in Excel
    
    var tableHTML = tableCopy.outerHTML;

    var excelTemplate = `
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="utf-8">
            <style>
                table { border-collapse: collapse; width: 100%; }
                th { background-color: #0f172a; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #cbd5e1; }
                td { border: 1px solid #e2e8f0; text-align: center; padding: 8px; }
                .grand-total-row { background-color: #f1f5f9; font-weight: bold; }
                .text-left { text-align: left; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
            <h3 style="text-align:center;">Daily Attendance Report - ${filename.replace('Daily_Attendance_Report_', '')}</h3>
            ${tableHTML}
        </body>
        </html>`;

    var dataType = 'application/vnd.ms-excel';
    filename = filename ? filename + '.xls' : 'daily_attendance_report.xls';

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