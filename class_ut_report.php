<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// ==========================================
// GET INPUTS
// ==========================================
$selected_class = $_REQUEST['class_name'] ?? null;
$selected_section = $_REQUEST['section_name'] ?? null;

// Load branch class list
$branch_data = get_data('branch_list', $branch_id);
$class_list = [];
if ($branch_data['count'] > 0) {
    $class_list = explode(",", $branch_data['data']['class_list']);
    $class_list = array_map('trim', $class_list);
    $class_list = array_filter($class_list);
}

// Fetch all active master UT tests scheduled by admin to determine column headers
$master_uts_res = direct_sql("SELECT DISTINCT ut_number FROM ut_tests WHERE (student_class = '' OR student_class IS NULL) AND (student_section = '' OR student_section IS NULL) AND (subject = '' OR subject IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY CAST(ut_number AS UNSIGNED) ASC, ut_number ASC, id ASC");
$master_uts = $master_uts_res['data'] ?? [];

// Fallback to default 14 UTs if none are found in the DB (as shown in the template screenshot)
if (empty($master_uts)) {
    for ($i = 1; $i <= 14; $i++) {
        $master_uts[] = ['ut_number' => $i];
    }
}

// Helper to clean UT numbers
if (!function_exists('clean_ut_number')) {
    function clean_ut_number($ut_name) {
        $clean = preg_replace('/^ut\s*-\s*/i', '', trim($ut_name));
        $clean = preg_replace('/^unit\s*test\s*/i', '', $clean);
        return trim($clean);
    }
}
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

    .main-header, .main-sidebar, .main-footer, .control-sidebar, .no-print, .box-header, .box-footer {
        display: none !important;
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
        <h1>Class-wise Unit Test Award Lists</h1>
    </section>

    <!-- Content -->
    <section class="content">
        <!-- Admin Class Filter -->
        <div class="box box-default no-print" style="border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.05); margin-bottom: 25px;">
            <div class="box-header with-border">
                <h3 class="box-title" style="font-weight: bold; color: #1e293b;">Select Class & Section to View UT Award Lists</h3>
            </div>
            <div class="box-body" style="padding: 20px;">
                <form method="get" class="no-print" id="filter-form">
                    <div class="row align-items-end">
                        <div class="col-md-5">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-weight: bold; color: #475569; margin-bottom: 8px;">Class</label>
                                <select name="class_name" id="class_name" class="form-control select2" style="border-radius: 6px; height: 40px;" required>
                                    <option value="">-- Select Class --</option>
                                    <option value="ALL" <?= ($selected_class == 'ALL') ? 'selected' : '' ?>>All Class</option>
                                    <?php foreach ($class_list as $cls): ?>
                                        <option value="<?= htmlspecialchars($cls) ?>" <?= ($selected_class == $cls) ? 'selected' : '' ?>>
                                            Class <?= htmlspecialchars($cls) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-5" id="section-wrapper">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label style="font-weight: bold; color: #475569; margin-bottom: 8px;">Section</label>
                                <select name="section_name" id="section_name" class="form-control select2" style="border-radius: 6px; height: 40px;">
                                    <option value="ALL" <?= ($selected_section == 'ALL') ? 'selected' : '' ?>>All Section</option>
                                    <?php 
                                    // Populate standard sections list
                                    $standard_sections = ['A', 'B', 'C', 'D', 'E', 'F'];
                                    foreach ($standard_sections as $sec): ?>
                                        <option value="<?= $sec ?>" <?= ($selected_section == $sec) ? 'selected' : '' ?>>
                                            Section <?= $sec ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-danger btn-block" style="border-radius: 6px; height: 40px; font-weight: bold; background: #c62828; border: none;">
                                Load Award Lists
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
        $(document).ready(function() {
            // If Class is "ALL", section is automatically forced to "ALL" and disabled/hidden
            function checkClassVal() {
                var classVal = $('#class_name').val();
                if (classVal === 'ALL') {
                    $('#section_name').val('ALL').trigger('change');
                    $('#section-wrapper').hide();
                } else {
                    $('#section-wrapper').show();
                }
            }
            $('#class_name').on('change', checkClassVal);
            checkClassVal();
        });
        </script>

        <?php
        if ($selected_class) {
            // Build list of class-section combinations to print
            $combinations = [];

            if ($selected_class === 'ALL') {
                // Loop through all classes, get active sections
                foreach ($class_list as $cls) {
                    $sections_res = direct_sql("SELECT DISTINCT student_section FROM student WHERE student_class = '$cls' AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY student_section ASC");
                    $sections = array_column($sections_res['data'] ?? [], 'student_section');
                    foreach ($sections as $sec) {
                        if (!empty($sec)) {
                            $combinations[] = ['class' => $cls, 'section' => $sec];
                        }
                    }
                }
            } else {
                // Single class
                if ($selected_section === 'ALL' || empty($selected_section)) {
                    $sections_res = direct_sql("SELECT DISTINCT student_section FROM student WHERE student_class = '$selected_class' AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY student_section ASC");
                    $sections = array_column($sections_res['data'] ?? [], 'student_section');
                    foreach ($sections as $sec) {
                        if (!empty($sec)) {
                            $combinations[] = ['class' => $selected_class, 'section' => $sec];
                        }
                    }
                } else {
                    $combinations[] = ['class' => $selected_class, 'section' => $selected_section];
                }
            }

            if (empty($combinations)) {
                echo "<div class='alert alert-warning no-print' style='border-radius: 8px; font-weight: 500;'>No active classes/sections with students found matching selection.</div>";
            } else {
                ?>
                <!-- Print Button (visible on screen only) -->
                <div class="no-print" style="margin-bottom: 25px; display: flex; justify-content: flex-end; background: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
                    <button type="button" class="btn btn-success" onclick="window.print()" style="border-radius: 6px; font-weight: bold; padding: 10px 24px; background: #2e7d32; border: none; box-shadow: 0 4px 6px rgba(46, 125, 50, 0.2);">
                        <i class="fa fa-print"></i> Print All Award Lists
                    </button>
                </div>

                <?php
                foreach ($combinations as $comb) {
                    $c_class = $comb['class'];
                    $c_section = $comb['section'];

                    // Fetch active students
                    $students_res = direct_sql("SELECT id, student_admission, student_name, student_father, student_roll FROM student WHERE student_class='$c_class' AND student_section='$c_section' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY CAST(student_roll AS UNSIGNED) ASC, student_roll ASC, student_name ASC");
                    $students_list = $students_res['data'] ?? [];

                    // Skip if no students are found (unless it was explicitly requested for a single class)
                    if (empty($students_list) && $selected_class === 'ALL') {
                        continue;
                    }

                    // Bulk fetch scheduled tests for this class to optimize database calls
                    $class_tests_res = direct_sql("SELECT id, ut_number, subject, date FROM ut_tests WHERE student_class = '$c_class' AND status = 'ACTIVE' AND branch_id = '$branch_id'");
                    $class_tests = [];
                    $test_ids = [];
                    if ($class_tests_res['count'] > 0) {
                        foreach ($class_tests_res['data'] as $t) {
                            $class_tests[$t['ut_number']] = $t;
                            $test_ids[] = $t['id'];
                        }
                    }

                    // Bulk fetch marks for the students in this class/section
                    $marks_lookup = [];
                    if (!empty($test_ids)) {
                        $test_ids_csv = implode("','", array_map('intval', $test_ids));
                        $marks_res = direct_sql("SELECT ut_id, student_id, marks FROM ut_marks WHERE ut_id IN ('$test_ids_csv')");
                        if ($marks_res['count'] > 0) {
                            foreach ($marks_res['data'] as $m) {
                                $marks_lookup[$m['student_id']][$m['ut_id']] = $m['marks'];
                            }
                        }
                    }

                    $num_uts = count($master_uts);
                    $ut_width = ($num_uts > 0) ? (51 / $num_uts) : 3.64;
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

                        <!-- Class-Section Metadata Row (same layout as template) -->
                        <div style="display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 10px; font-weight: bold; font-family: Arial, sans-serif;">
                            <div style="width: 50%;">Class : <?= htmlspecialchars($c_class) ?></div>
                            <div style="width: 50%; text-align: left;">Section : <?= htmlspecialchars($c_section) ?></div>
                        </div>

                        <!-- Main Award Table -->
                        <table class="award-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <!-- Row 1: Subject headers -->
                                <tr>
                                    <th colspan="3" style="border: 1px solid #000; background: #fff; text-align: left; font-weight: bold; font-size: 13px; padding: 6px 8px;">Subject</th>
                                    <?php foreach ($master_uts as $master) {
                                        $ut_num = $master['ut_number'];
                                        $subj_name = $class_tests[$ut_num]['subject'] ?? '';
                                        ?>
                                        <th style="border: 1px solid #000; background: #fff; text-align: center; font-size: 10px; font-weight: bold; padding: 6px 2px; width: <?= $ut_width ?>%;"><?= htmlspecialchars($subj_name) ?></th>
                                    <?php } ?>
                                </tr>
                                <!-- Row 2: Date headers -->
                                <tr>
                                    <th colspan="3" style="border: 1px solid #000; background: #fff; text-align: left; font-weight: bold; font-size: 13px; padding: 6px 8px;">Date</th>
                                    <?php foreach ($master_uts as $master) {
                                        $ut_num = $master['ut_number'];
                                        $test_date_str = isset($class_tests[$ut_num]['date']) ? date('d-m-Y', strtotime($class_tests[$ut_num]['date'])) : '';
                                        ?>
                                        <th style="border: 1px solid #000; background: #fff; text-align: center; font-size: 9px; font-weight: bold; padding: 6px 2px; width: <?= $ut_width ?>%;"><?= $test_date_str ?></th>
                                    <?php } ?>
                                </tr>
                                <!-- Row 3: Column description headers -->
                                <tr style="background: #c62828; color: white;">
                                    <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-size: 13px; font-weight: bold; width: 5%;">Roll No.</th>
                                    <th style="border: 1px solid #000; padding: 8px 8px; text-align: left; font-size: 13px; font-weight: bold; width: 22%;">Name of Student</th>
                                    <th style="border: 1px solid #000; padding: 8px 8px; text-align: left; font-size: 13px; font-weight: bold; width: 22%;">Father's Name</th>
                                    <?php foreach ($master_uts as $master) { ?>
                                        <th style="border: 1px solid #000; padding: 8px 4px; text-align: center; font-size: 13px; font-weight: bold; width: <?= $ut_width ?>%;">UT-<?= htmlspecialchars(clean_ut_number($master['ut_number'])) ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (empty($students_list)) {
                                    $col_span = 3 + count($master_uts);
                                    echo "<tr><td colspan='{$col_span}' style='border: 1px solid #000; padding: 20px; text-align: center; color: #666;'>No active students found in this class-section.</td></tr>";
                                } else {
                                    foreach ($students_list as $stu) {
                                        $sid = $stu['id'];
                                        $s_roll = $stu['student_roll'];
                                        $s_name = $stu['student_name'];
                                        $s_father = $stu['student_father'];
                                        ?>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 12px;"><?= htmlspecialchars($s_roll) ?></td>
                                            <td style="border: 1px solid #000; padding: 6px 8px; font-size: 12px; text-transform: uppercase; font-weight: 500;"><?= htmlspecialchars($s_name) ?></td>
                                            <td style="border: 1px solid #000; padding: 6px 8px; font-size: 12px; text-transform: uppercase; color: #334155;"><?= htmlspecialchars($s_father) ?></td>
                                            <?php
                                            foreach ($master_uts as $master) {
                                                $ut_num = $master['ut_number'];
                                                $ut_id = $class_tests[$ut_num]['id'] ?? null;
                                                $mark_val = ($ut_id && isset($marks_lookup[$sid][$ut_id])) ? $marks_lookup[$sid][$ut_id] : '';
                                                ?>
                                                <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; font-size: 12px; font-weight: bold;"><?= ($mark_val !== '' && $mark_val !== null) ? htmlspecialchars($mark_val) : '-' ?></td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }

                                    // Add 5 extra empty rows for manual entry buffer space
                                    for ($empty_idx = 0; $empty_idx < 5; $empty_idx++) {
                                        ?>
                                        <tr>
                                            <td style="border: 1px solid #000; padding: 6px 4px; text-align: center; height: 26px;"></td>
                                            <td style="border: 1px solid #000; padding: 6px 8px;"></td>
                                            <td style="border: 1px solid #000; padding: 6px 8px;"></td>
                                            <?php foreach ($master_uts as $master) { ?>
                                                <td style="border: 1px solid #000; padding: 6px 4px;"></td>
                                            <?php } ?>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
                                <!-- Present Row -->
                                <tr>
                                    <td colspan="3" style="border: 1px solid #000; padding: 8px 10px; text-align: right; font-weight: bold; font-size: 13px; background: #fff;">Present:</td>
                                    <?php foreach ($master_uts as $master) { ?>
                                        <td style="border: 1px solid #000; padding: 8px 4px; background: #fff;"></td>
                                    <?php } ?>
                                </tr>
                                <!-- Absent Row -->
                                <tr>
                                    <td colspan="3" style="border: 1px solid #000; padding: 8px 10px; text-align: right; font-weight: bold; font-size: 13px; background: #fff;">Absent:</td>
                                    <?php foreach ($master_uts as $master) { ?>
                                        <td style="border: 1px solid #000; padding: 8px 4px; background: #fff;"></td>
                                    <?php } ?>
                                </tr>
                                <!-- Total Row -->
                                <tr>
                                    <td colspan="3" style="border: 1px solid #000; padding: 8px 10px; text-align: right; font-weight: bold; font-size: 13px; background: #fff;">Total:</td>
                                    <?php foreach ($master_uts as $master) { ?>
                                        <td style="border: 1px solid #000; padding: 8px 4px; background: #fff;"></td>
                                    <?php } ?>
                                </tr>
                                <!-- Sign. Row -->
                                <tr style="height: 35px;">
                                    <td colspan="3" style="border: 1px solid #000; padding: 8px 10px; text-align: right; font-weight: bold; font-size: 13px; vertical-align: middle; background: #fff;">Sign.:</td>
                                    <?php foreach ($master_uts as $master) { ?>
                                        <td style="border: 1px solid #000; padding: 8px 4px; background: #fff; vertical-align: middle;"></td>
                                    <?php } ?>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <?php
                }
            }
        } else {
            echo "<div class='alert alert-info no-print' style='border-radius: 8px; font-weight: 500; margin-top: 10px;'>Please select a class and section to view unit test award lists.</div>";
        }
        ?>
    </section>
</div>

<?php require_once('system/footer.php'); ?>
