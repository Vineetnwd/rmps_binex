<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// ==========================================
// GET INPUTS
// ==========================================
$selected_teacher_id = $_REQUEST['teacher_id'] ?? null;

// Determine active teacher and teacher name
$teachers_list = [];
$teacher_name = '';

if ($user_type !== 'TEACHER') {
    // Admin / Management view: Load list of all active teachers
    $teachers_res = direct_sql("SELECT id, name, emp_code FROM employee WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY name ASC");
    $teachers_list = $teachers_res['data'] ?? [];

    if ($selected_teacher_id) {
        $t_info = get_data('employee', $selected_teacher_id);
        if ($t_info['count'] > 0) {
            $teacher_name = $t_info['data']['name'];
        }
    }
} else {
    // Teacher view: Force logged in teacher's ID
    $emp = get_teacher_employee();
    $selected_teacher_id = $emp['id'] ?? null;
    $teacher_name = $emp['name'] ?? '';
}

// Fetch all active master UT tests scheduled by admin
$master_uts_res = direct_sql("SELECT DISTINCT ut_number FROM ut_tests WHERE (student_class = '' OR student_class IS NULL) AND (student_section = '' OR student_section IS NULL) AND (subject = '' OR subject IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY id ASC");
$master_uts = $master_uts_res['data'] ?? [];

// Fallback to default UTs if none are found in the DB
if (empty($master_uts)) {
    $master_uts = [
        ['ut_number' => 1],
        ['ut_number' => 2],
        ['ut_number' => 3],
        ['ut_number' => 4],
        ['ut_number' => 5],
    ];
}

// Helper function to clean UT number for display
if (!function_exists('clean_ut_number')) {
    function clean_ut_number($val) {
        return trim(preg_replace('/(unit\s*test|ut|test)\s*-*\s*/i', '', $val));
    }
}

// Safe Type Conversions for global school configurations to prevent TypeError
$h_full_name = isset($full_name) ? (is_array($full_name) ? implode(' ', $full_name) : $full_name) : 'R M PUBLIC SCHOOL';
$h_aff_no = isset($aff_no) ? (is_array($aff_no) ? implode(' ', $aff_no) : $aff_no) : '330874';
$h_school_code = isset($school_code) ? (is_array($school_code) ? implode(' ', $school_code) : $school_code) : '65899';
$h_address1 = isset($inst_address1) ? (is_array($inst_address1) ? implode(' ', $inst_address1) : $inst_address1) : 'Bishambhrapur';
$h_address2 = isset($inst_address2) ? (is_array($inst_address2) ? implode(' ', $inst_address2) : $inst_address2) : '';
$h_email = isset($inst_email) ? (is_array($inst_email) ? implode(' ', $inst_email) : $inst_email) : 'info@rmpublicschool.org';
?>

<style>
* {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

body {
    background: #f8fafc !important;
    margin: 0; padding: 0;
}

/* Premium Filter Card */
.filter-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    padding: 24px;
    margin-bottom: 24px;
    border: 1px solid #e2e8f0;
}

/* Print Styles */
@media print {
    /* Reset background, height, padding, margin, overflow, borders, and outlines on layout classes */
    html, body, .wrapper, .content-wrapper, .content, .box, .box-body, section, .row, [class*="col-"], .award-list-sheet {
        background: #fff !important;
        background-color: #fff !important;
        color: #000 !important;
        height: auto !important;
        min-height: 0 !important;
        padding: 0 !important;
        margin: 0 !important;
        border: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        border-top: 0 !important;
        border-bottom: 0 !important;
        box-shadow: none !important;
        outline: 0 !important;
        overflow: visible !important;
    }
    
    /* Disable pseudo-elements on layout wrappers to strip hidden borders/lines */
    .wrapper::before, .wrapper::after,
    .content-wrapper::before, .content-wrapper::after,
    .content::before, .content::after,
    .box::before, .box::after,
    .row::before, .row::after,
    .main-sidebar::before, .main-sidebar::after,
    .left-side::before, .left-side::after,
    .control-sidebar::before, .control-sidebar::after {
        display: none !important;
        content: none !important;
        border: 0 !important;
        width: 0 !important;
        height: 0 !important;
    }

    /* Force content area to full width with no left offset */
    .content-wrapper, .right-side {
        margin-left: 0 !important;
        left: 0 !important;
        width: 100% !important;
        min-height: 0 !important;
    }
    
    /* Global border reset for all child elements during print */
    * {
        border: 0 !important;
        border-top: 0 !important;
        border-bottom: 0 !important;
        border-left: 0 !important;
        border-right: 0 !important;
        box-shadow: none !important;
        outline: 0 !important;
    }

    .main-header, .main-sidebar, .main-footer, .control-sidebar, .control-sidebar-bg,
    .no-print, .box-header, .box-footer {
        display: none !important;
        visibility: hidden !important;
        width: 0 !important;
        height: 0 !important;
        position: static !important;
    }

    /* Hide everything inside .wrapper EXCEPT .content-wrapper */
    .wrapper > *:not(.content-wrapper) {
        display: none !important;
        visibility: hidden !important;
    }
    @page { margin: 10mm !important; size: A4 portrait; }
    
    .award-list-sheet {
        padding: 10px !important;
        margin: 0 0 20px 0 !important;
        page-break-after: always !important;
        background: #fff !important;
        overflow: visible !important;
        border: 0 !important;
    }
    
    /* Re-establish borders only for the award table elements */
    table.award-table, 
    table.award-table tr, 
    table.award-table th, 
    table.award-table td {
        border: 1px solid #000 !important;
    }
    
    /* Re-establish border for division line */
    hr {
        border-top: 2px solid #c62828 !important;
    }
}

/* Award List Sheet Styles */
.award-list-sheet {
    background: #fff;
    padding: 25px;
    margin-bottom: 30px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.02);
}

.award-table th, .award-table td {
    border: 1px solid #000 !important;
    font-family: Arial, sans-serif;
}
</style>

<script>
// Force-hide AdminLTE fixed/absolute elements on print to eliminate the right-side vertical line
var _printHideEls = [];
window.addEventListener('beforeprint', function () {
    var selectors = [
        '.main-header', '.main-sidebar', '.main-footer',
        '.control-sidebar', '.control-sidebar-bg',
        '.left-side'
    ];
    selectors.forEach(function (sel) {
        document.querySelectorAll(sel).forEach(function (el) {
            _printHideEls.push({ el: el, display: el.style.display, position: el.style.position });
            el.style.display = 'none';
            el.style.position = 'static';
        });
    });
    // Force content-wrapper to full width
    var cw = document.querySelector('.content-wrapper');
    if (cw) {
        cw._origMargin = cw.style.marginLeft;
        cw._origWidth = cw.style.width;
        cw.style.marginLeft = '0';
        cw.style.width = '100%';
    }
});
window.addEventListener('afterprint', function () {
    _printHideEls.forEach(function (item) {
        item.el.style.display = item.display;
        item.el.style.position = item.position;
    });
    _printHideEls = [];
    var cw = document.querySelector('.content-wrapper');
    if (cw) {
        cw.style.marginLeft = cw._origMargin || '';
        cw.style.width = cw._origWidth || '';
    }
});
</script>


<div class="content-wrapper">
    <!-- Header -->
    <section class="content-header no-print">
        <h1>Teacher-wise Unit Test Award Lists</h1>
    </section>

    <!-- Content -->
    <section class="content">
        <!-- Admin Teacher Filter -->
        <?php if ($user_type !== 'TEACHER'): ?>
            <div class="box box-default no-print" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 25px;">
                <div class="box-header with-border">
                    <h3 class="box-title" style="font-weight: bold; color: #1e293b;">Select Teacher to View Award Lists</h3>
                </div>
                <div class="box-body" style="padding: 20px;">
                    <form method="get" class="no-print">
                        <div class="row align-items-end">
                            <div class="col-md-9">
                                <div class="form-group" style="margin-bottom: 0;">
                                    <label style="font-weight: bold; color: #475569; margin-bottom: 8px;">Teacher</label>
                                    <select name="teacher_id" class="form-control select2" style="border-radius: 6px; height: 40px;" required>
                                        <option value="">-- Select Teacher --</option>
                                        <?php foreach ($teachers_list as $t): ?>
                                            <option value="<?= $t['id'] ?>" <?= ($selected_teacher_id == $t['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['emp_code']) ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-danger btn-block" style="border-radius: 6px; height: 40px; font-weight: bold; background: #c62828; border: none;">
                                    <i class="fa fa-search"></i> View Award Lists
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <?php
        if ($selected_teacher_id) {
            // Get all unique class-section-subject combinations assigned to the teacher from the timetable routine
            $courses_res = direct_sql("SELECT DISTINCT student_class, student_section, subject FROM timetable WHERE teacher_id = '$selected_teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class, student_section, subject ASC");
            $courses_list = $courses_res['data'] ?? [];

            if (empty($courses_list)) {
                echo "<div class='alert alert-warning no-print' style='border-radius: 8px; font-weight: 500;'>No assigned class-section-subject combinations found for this teacher in the daily timetable.</div>";
            } else {
                ?>
                <!-- Print Button (visible on screen only) -->
                <div class="no-print" style="margin-bottom: 25px; display: flex; justify-content: flex-end; background: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <button type="button" class="btn btn-success" onclick="window.print()" style="border-radius: 6px; font-weight: bold; padding: 10px 24px; background: #2e7d32; border: none; box-shadow: 0 4px 6px rgba(46, 125, 50, 0.2);">
                        <i class="fa fa-print"></i> Print All Award Lists
                    </button>
                </div>

                <?php
                foreach ($courses_list as $course) {
                    $c_class = $course['student_class'];
                    $c_section = $course['student_section'];
                    $c_subject = $course['subject'];

                    // Fetch active students in this class/section
                    $students_res = direct_sql("SELECT id, student_admission, student_name, student_father, student_roll, student_mobile FROM student WHERE student_class='$c_class' AND student_section='$c_section' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY CAST(student_roll AS UNSIGNED) ASC, student_roll ASC, student_name ASC");
                    $students_list = $students_res['data'] ?? [];
                    ?>
                    <div class="award-list-sheet page-break">
                        <!-- School Header -->
                        <div style="text-align: center; margin-bottom: 15px;">
                            <h2 style="margin: 0; font-size: 26px; font-weight: bold; color: #c62828; text-transform: uppercase;"><?= htmlspecialchars($h_full_name) ?></h2>
                            <div style="font-size: 11px; font-weight: 600; color: #475569; margin-top: 2px;">
                                AFFILIATED TO CBSE, NEW DELHI, UP TO 10+2, AFFILIATION No.- <?= htmlspecialchars($h_aff_no) ?>
                            </div>
                            <div style="font-size: 10px; color: #64748b; margin-top: 1px;">
                                CBSE SCHOOL CODE: <?= htmlspecialchars($h_school_code) ?> | ISO 9001:2015 CERTIFIED
                            </div>
                            <div style="font-size: 11px; color: #334155; margin-top: 2px;">
                                <?= htmlspecialchars($h_address1) ?><?= !empty($h_address2) ? ', ' . htmlspecialchars($h_address2) : '' ?>
                            </div>
                            <div style="font-size: 11px; color: #c62828; font-weight: 500; margin-top: 2px;">
                                www.rmpublicschool.org | <?= htmlspecialchars($h_email) ?>
                            </div>
                        </div>
                        
                        <!-- Divider Line -->
                        <hr style="border: 0; border-top: 2px solid #c62828; margin: 10px 0 15px 0;">

                        <!-- Title -->
                        <div style="text-align: center; margin-bottom: 20px;">
                            <h3 style="margin: 0; font-size: 20px; font-weight: bold; text-decoration: underline; text-transform: uppercase; letter-spacing: 1px;">
                                UNIT TEST AWARD LIST SESSION 2026-27
                            </h3>
                        </div>

                        <!-- Metadata Details -->
                        <table width="100%" style="font-size: 14px; margin-bottom: 15px; border-collapse: collapse;">
                            <tr>
                                <td style="padding: 4px 0; width: 18%;"><strong>Class:</strong> <?= htmlspecialchars($c_class) ?></td>
                                <td style="padding: 4px 0; width: 22%;"><strong>Section:</strong> <?= htmlspecialchars($c_section) ?></td>
                                <td style="padding: 4px 0; width: 30%;"><strong>Subject:</strong> <?= htmlspecialchars($c_subject) ?></td>
                                <td style="padding: 4px 0; width: 30%;"><strong>Name of Subject Teacher:</strong> <?= htmlspecialchars($teacher_name) ?></td>
                            </tr>
                        </table>

                        <?php
                        $num_uts = count($master_uts);
                        $ut_width = ($num_uts > 0) ? (32 / $num_uts) : 8;
                        ?>

                        <!-- Student UT Marks Table -->
                        <table class="award-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <!-- Row 1: Merged cell for Date and Scheduled UT Dates -->
                                <tr>
                                    <th colspan="5" style="border: 1px solid #000; background: #fff; text-align: center; font-weight: bold; font-size: 13px; height: 35px;">Date</th>
                                    <?php foreach ($master_uts as $master) {
                                        $ut_num = $master['ut_number'];
                                        $ut_date_str = '';
                                        // Fetch class/subject-wise scheduled test date
                                        $test_sched = direct_sql("SELECT date FROM ut_tests WHERE student_class = '$c_class' AND (student_section = '' OR student_section IS NULL) AND subject = '$c_subject' AND ut_number = '$ut_num' AND status = 'ACTIVE' AND branch_id = '$branch_id' LIMIT 1");
                                        if ($test_sched['count'] > 0 && !empty($test_sched['data'][0]['date'])) {
                                            $ut_date_str = date('d-m-Y', strtotime($test_sched['data'][0]['date']));
                                        }
                                        ?>
                                        <th style="border: 1px solid #000; background: #fff; text-align: center; font-size: 11px; font-weight: bold; height: 35px; width: <?= $ut_width ?>%;"><?= $ut_date_str ?></th>
                                    <?php } ?>
                                </tr>
                                <!-- Row 2: Header Columns -->
                                <tr style="background: #c62828; color: white;">
                                    <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-size: 13px; font-weight: bold; width: 5%;">Roll No.</th>
                                    <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-size: 13px; font-weight: bold; width: 8%;">Adm. No.</th>
                                    <th style="border: 1px solid #000; padding: 8px 8px; text-align: left; font-size: 13px; font-weight: bold; width: 22%;">Name of Student</th>
                                    <th style="border: 1px solid #000; padding: 8px 8px; text-align: left; font-size: 13px; font-weight: bold; width: 22%;">Father's Name</th>
                                    <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-size: 13px; font-weight: bold; width: 11%;">Mobile No.</th>
                                    <?php foreach ($master_uts as $master) { ?>
                                        <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-size: 13px; font-weight: bold; width: <?= $ut_width ?>%;">UT-<?= htmlspecialchars(clean_ut_number($master['ut_number'])) ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (empty($students_list)) {
                                    $col_span = 5 + count($master_uts);
                                    echo "<tr><td colspan='{$col_span}' style='border: 1px solid #000; padding: 20px; text-align: center; color: #666;'>No active students found in this class-section.</td></tr>";
                                } else {
                                    foreach ($students_list as $stu) {
                                        $sid = $stu['id'];
                                        $adm = $stu['student_admission'];
                                        $s_roll = $stu['student_roll'];
                                        $s_name = $stu['student_name'];
                                        $s_father = $stu['student_father'];
                                        $s_mobile = $stu['student_mobile'];
                                        ?>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 12px;"><?= htmlspecialchars($s_roll) ?></td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 12px;"><?= htmlspecialchars($adm) ?></td>
                                            <td style="border: 1px solid #000; padding: 6px 8px; font-size: 12px; text-transform: uppercase; font-weight: 500;"><?= htmlspecialchars($s_name) ?></td>
                                            <td style="border: 1px solid #000; padding: 6px 8px; font-size: 12px; text-transform: uppercase; color: #334155;"><?= htmlspecialchars($s_father) ?></td>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 12px;"><?= htmlspecialchars($s_mobile) ?></td>
                                            <?php
                                            foreach ($master_uts as $master) {
                                                $ut_num = $master['ut_number'];
                                                $mark_val = '';
                                                
                                                // Find scheduled test ID for this subject under this UT number
                                                $ut_test = direct_sql("SELECT id FROM ut_tests WHERE student_class = '$c_class' AND (student_section = '' OR student_section IS NULL) AND subject = '$c_subject' AND ut_number = '$ut_num' AND status = 'ACTIVE' AND branch_id = '$branch_id' LIMIT 1");
                                                
                                                if ($ut_test['count'] > 0) {
                                                    $ut_id = $ut_test['data'][0]['id'];
                                                    
                                                    // Fetch student marks from ut_marks table
                                                    $marks_res = direct_sql("SELECT marks FROM ut_marks WHERE ut_id = '$ut_id' AND student_id = '$sid' LIMIT 1");
                                                    if ($marks_res['count'] > 0) {
                                                        $mark_val = $marks_res['data'][0]['marks'];
                                                    }
                                                }
                                                ?>
                                                <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 12px; font-weight: bold;"><?= ($mark_val !== '' && $mark_val !== null) ? htmlspecialchars($mark_val) : '-' ?></td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>

                        <!-- Footer Statistics and Signatures -->
                        <div style="margin-top: 30px; display: flex; justify-content: space-between; font-size: 13px;">
                            <div style="display: flex; gap: 20px;">
                                <div><strong>Total Students:</strong> ________________</div>
                                <div><strong>Total Present:</strong> ________________</div>
                                <div><strong>Total Absent:</strong> ________________</div>
                            </div>
                            <div style="text-align: right; margin-right: 20px;">
                                <strong>Signature of Subject Teacher:</strong> _______________________
                            </div>
                        </div>
                    </div>
                    <?php
                }
            }
        } else {
            echo "<div class='alert alert-info no-print' style='border-radius: 8px; font-weight: 500; margin-top: 10px;'>Please select a teacher to load their assigned class-section-subject UT award lists.</div>";
        }
        ?>
    </section>
</div>

<?php require_once('system/footer.php'); ?>
