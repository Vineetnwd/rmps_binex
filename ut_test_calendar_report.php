<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// Convert potential arrays from branch_config() back to strings if they contained commas
$full_name = is_array($full_name) ? implode(', ', $full_name) : $full_name;
$inst_name = is_array($inst_name) ? implode(', ', $inst_name) : $inst_name;
$school_code = is_array($school_code) ? implode(', ', $school_code) : $school_code;
$aff_no = is_array($aff_no) ? implode(', ', $aff_no) : $aff_no;
$inst_address1 = is_array($inst_address1) ? implode(', ', $inst_address1) : $inst_address1;
$inst_address2 = is_array($inst_address2) ? implode(', ', $inst_address2) : $inst_address2;
$inst_email = is_array($inst_email) ? implode(', ', $inst_email) : $inst_email;

// Get current session and branch
$session = $_SESSION['current_session'] ?? $current_session ?? '2026-27';
$years = explode('-', $session);
$start_year = intval($years[0]);
if ($start_year < 100) {
    $start_year = 2000 + $start_year;
}
$end_year = $start_year + 1;
$branch_id = $_SESSION['branch_id'] ?? 1;

// Get selected class and section from filter if any
$class_filter = $_REQUEST['class_filter'] ?? '';
$sel_class = '';
$sel_section = '';
if ($class_filter) {
    $parts = explode('-', $class_filter);
    $sel_class = $parts[0] ?? '';
    $sel_section = $parts[1] ?? '';
}

// 1. Fetch holidays
$holidays = [];
$h_res = direct_sql("SELECT holiday_date, holiday_name, status FROM holiday WHERE status NOT IN ('HIDE') AND branch_id = '$branch_id'");
if ($h_res['count'] > 0) {
    foreach ($h_res['data'] as $h) {
        $holidays[$h['holiday_date']] = $h['holiday_name'];
    }
}

// 2. Fetch UT Tests
$ut_tests = [];
if ($sel_class && $sel_section) {
    // Class-specific UT Tests (including subject)
    $ut_res = direct_sql("SELECT date, ut_number, subject FROM ut_tests WHERE student_class = '$sel_class' AND (student_section = '' OR student_section IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id'");
    if ($ut_res['count'] > 0) {
        foreach ($ut_res['data'] as $ut) {
            $subject_lbl = !empty($ut['subject']) ? ' (' . $ut['subject'] . ')' : '';
            $ut_tests[$ut['date']] = 'UT-' . $ut['ut_number'] . $subject_lbl;
        }
    }
} else {
    // Master UT tests schedule
    $ut_res = direct_sql("SELECT date, ut_number FROM ut_tests WHERE (student_class = '' OR student_class IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id'");
    if ($ut_res['count'] > 0) {
        foreach ($ut_res['data'] as $ut) {
            $ut_tests[$ut['date']] = 'UT-' . $ut['ut_number'];
        }
    }
}

// Define months structure (April to March)
$months_config = [
    ['month' => 4, 'year' => $start_year, 'name' => 'APRIL'],
    ['month' => 5, 'year' => $start_year, 'name' => 'MAY'],
    ['month' => 6, 'year' => $start_year, 'name' => 'JUNE'],
    ['month' => 7, 'year' => $start_year, 'name' => 'JULY'],
    ['month' => 8, 'year' => $start_year, 'name' => 'AUGUST'],
    ['month' => 9, 'year' => $start_year, 'name' => 'SEPTEMBER'],
    ['month' => 10, 'year' => $start_year, 'name' => 'OCTOBER'],
    ['month' => 11, 'year' => $start_year, 'name' => 'NOVEMBER'],
    ['month' => 12, 'year' => $start_year, 'name' => 'DECEMBER'],
    ['month' => 1, 'year' => $end_year, 'name' => 'JANUARY'],
    ['month' => 2, 'year' => $end_year, 'name' => 'FEBRUARY'],
    ['month' => 3, 'year' => $end_year, 'name' => 'MARCH'],
];

// Helper function to classify and color code the calendar day
function get_day_classification($date, $is_sunday, $holiday_name, $ut_name) {
    if (!$holiday_name && !$ut_name && $is_sunday) {
        return [
            'class' => 'cell-sunday',
            'text' => 'SUN',
            'bg' => '#10b981',
            'color' => '#ffffff'
        ];
    }
    
    // Check if there is a UT test first or if they coexist
    if ($ut_name) {
        return [
            'class' => 'cell-exam',
            'text' => $ut_name,
            'bg' => '#fef08a',
            'color' => '#854d0e'
        ];
    }
    
    if ($holiday_name) {
        $upper_name = strtoupper($holiday_name);
        
        // 1. School Reopens
        if (str_contains($upper_name, 'REOPEN')) {
            return [
                'class' => 'cell-reopen',
                'text' => $holiday_name,
                'bg' => '#3b82f6',
                'color' => '#ffffff'
            ];
        }
        
        // 2. Examination
        if (str_contains($upper_name, 'EXAM') || str_contains($upper_name, 'HALF YEARLY') || str_contains($upper_name, 'ANNUAL') || str_contains($upper_name, 'TEST') || str_contains($upper_name, 'PT-')) {
            return [
                'class' => 'cell-exam',
                'text' => $holiday_name,
                'bg' => '#fef08a',
                'color' => '#854d0e'
            ];
        }
        
        // 3. Special Day
        if (str_contains($upper_name, 'PTM') || str_contains($upper_name, 'PARENT TEACHER') || str_contains($upper_name, 'SPORTS') || str_contains($upper_name, 'REPUBLIC') || str_contains($upper_name, 'INDEPENDENCE') || str_contains($upper_name, 'GANDHI JAYANTI') || str_contains($upper_name, 'BUDDHA PURNIMA')) {
            // Wait, Buddha Purnima is classified as special day or holiday in image?
            // In image Buddha Purnima is light green, PTM is orange.
            // Let's only map PTM, SPORTS, REPUBLIC, INDEPENDENCE, GANDHI to special.
            if (str_contains($upper_name, 'BUDDHA PURNIMA')) {
                return [
                    'class' => 'cell-holiday',
                    'text' => $holiday_name,
                    'bg' => '#86efac',
                    'color' => '#14532d'
                ];
            }
            return [
                'class' => 'cell-special',
                'text' => $holiday_name,
                'bg' => '#fdba74',
                'color' => '#7c2d12'
            ];
        }
        
        // 4. Default Holiday
        return [
            'class' => 'cell-holiday',
            'text' => $holiday_name,
            'bg' => '#86efac',
            'color' => '#14532d'
        ];
    }
    
    if ($is_sunday) {
        return [
            'class' => 'cell-sunday',
            'text' => 'SUN',
            'bg' => '#10b981',
            'color' => '#ffffff'
        ];
    }
    
    return [
        'class' => 'cell-working',
        'text' => '',
        'bg' => '#ffffff',
        'color' => '#000000'
    ];
}
?>

<style>
/* Adjust print options */
* {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

.calendar-container {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    padding: 24px;
    margin-bottom: 30px;
    border: 1px solid #e2e8f0;
}

.calendar-header-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #b71c1c;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 25px;
    background: #fff;
    position: relative;
}

.calendar-header-banner .logo-container {
    position: absolute;
    left: 20px;
}

.calendar-header-banner .logo-container img {
    height: 70px;
    width: auto;
}

.calendar-header-banner .school-details {
    text-align: center;
    width: 100%;
}

.calendar-header-banner .school-details h1 {
    font-size: 28px;
    font-weight: 800;
    color: #b71c1c;
    margin: 0 0 5px 0;
    letter-spacing: 1px;
}

.calendar-header-banner .school-details .badge-info-text {
    background: #b71c1c;
    color: #ffffff;
    font-size: 11px;
    font-weight: bold;
    padding: 3px 12px;
    border-radius: 4px;
    display: inline-block;
    margin-bottom: 5px;
    text-transform: uppercase;
}

.calendar-header-banner .school-details p {
    margin: 2px 0;
    font-size: 12px;
    color: #334155;
    font-weight: 500;
}

.calendar-table-wrapper {
    overflow-x: auto;
    margin-top: 15px;
}

.calendar-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
}

.calendar-table th, .calendar-table td {
    border: 1px solid #cbd5e1;
    text-align: center;
    vertical-align: middle;
    padding: 6px 4px;
    height: 52px;
    font-size: 9px;
}

.calendar-table th {
    background-color: #f8fafc;
    color: #1e293b;
    font-weight: 700;
    font-size: 11px;
    text-transform: uppercase;
    border-bottom: 3px solid #cbd5e1;
}

.calendar-table th.day-col-header {
    width: 40px;
    background-color: #e2e8f0;
}

.calendar-table td.day-num {
    background-color: #f1f5f9;
    font-weight: 700;
    color: #475569;
    font-size: 12px;
}

/* Cell States */
.cell-working {
    background-color: #ffffff;
}

.cell-sunday {
    background-color: #10b981 !important;
    color: #ffffff !important;
    font-weight: 700;
}

.cell-holiday {
    background-color: #86efac !important;
    color: #14532d !important;
    font-weight: 700;
    font-size: 9px;
    word-break: break-word;
}

.cell-special {
    background-color: #fdba74 !important;
    color: #7c2d12 !important;
    font-weight: 700;
    font-size: 9px;
    word-break: break-word;
}

.cell-reopen {
    background-color: #3b82f6 !important;
    color: #ffffff !important;
    font-weight: 700;
    font-size: 9px;
    word-break: break-word;
}

.cell-exam {
    background-color: #fef08a !important;
    color: #854d0e !important;
    font-weight: 700;
    font-size: 9px;
    word-break: break-word;
    border: 2px dashed #eab308;
}

.cell-invalid {
    background-color: #e2e8f0 !important;
    background-image: repeating-linear-gradient(45deg, transparent, transparent 5px, rgba(255,255,255,0.6) 5px, rgba(255,255,255,0.6) 10px) !important;
}

/* Legend Styling */
.legend-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 15px;
    margin-top: 25px;
    padding: 15px;
    background: #f8fafc;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
}

.legend-item {
    display: flex;
    align-items: center;
    font-size: 11px;
    font-weight: 700;
    color: #334155;
    text-transform: uppercase;
}

.legend-color-box {
    width: 25px;
    height: 18px;
    margin-right: 8px;
    border: 1px solid #cbd5e1;
    border-radius: 3px;
}

/* Print Styling */
@media print {
    .no-print {
        display: none !important;
    }
    
    .content-wrapper {
        margin-left: 0 !important;
        padding: 0 !important;
    }
    
    .calendar-container {
        box-shadow: none !important;
        padding: 0 !important;
        border: none !important;
    }
    
    .calendar-table th, .calendar-table td {
        padding: 4px 2px !important;
        height: auto !important;
        min-height: 40px !important;
        font-size: 9px !important;
    }
    
    .calendar-header-banner {
        border-color: #b71c1c !important;
        margin-bottom: 15px !important;
    }
    
    @page {
        size: A4 landscape;
        margin: 5mm;
    }
}
</style>

<div class="content-wrapper">
    <section class="content-header no-print">
        <h1>Academic Calendar & UT Planner <small>Overview</small></h1>
        <ol class="breadcrumb">
            <li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="active">UT Planner Calendar</li>
        </ol>
    </section>

    <section class="content">
        <!-- Filters Card -->
        <div class="box box-default no-print">
            <div class="box-header with-border">
                <h3 class="box-title">Filters & Options</h3>
            </div>
            <div class="box-body">
                <form method="get" class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Filter by Class-Section (Optional)</label>
                            <select name="class_filter" class="form-control">
                                <option value="">-- Master Schedule (Global) --</option>
                                <?php
                                $classes = active_class_list();
                                if (!empty($classes)) {
                                    foreach ($classes as $cls) {
                                        $selected = ($class_filter == $cls) ? 'selected' : '';
                                        echo "<option value='$cls' $selected>$cls</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4" style="margin-top: 24px;">
                        <button type="submit" class="btn btn-info"><i class="fa fa-filter"></i> Apply Filter</button>
                        <a href="ut_test_calendar_report" class="btn btn-default"><i class="fa fa-refresh"></i> Clear</a>
                        <button type="button" onclick="window.print()" class="btn btn-primary"><i class="fa fa-print"></i> Print Calendar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Calendar Report Card -->
        <div class="calendar-container">
            <!-- Institutional Header -->
            <div class="calendar-header-banner">
                <div class="logo-container">
                    <img src="<?= $base_url ?>/assets/images/logo.png" alt="RMPS Logo">
                </div>
                <div class="school-details">
                    <h1><?= htmlspecialchars($full_name ?: 'R M PUBLIC SCHOOL') ?></h1>
                    <span class="badge-info-text">AFFILIATED TO CBSE, NEW DELHI, UP TO 10+2, AFFILIATION No. - <?= htmlspecialchars($aff_no) ?></span>
                    <p>CBSE SCHOOL CODE: <?= htmlspecialchars($school_code) ?> | ISO 9001:2015 CERTIFIED</p>
                    <p><?= htmlspecialchars($inst_address1) ?>, <?= htmlspecialchars($inst_address2) ?></p>
                    <p><i class="fa fa-globe"></i> www.rmpublicschool.org | <i class="fa fa-envelope"></i> <?= htmlspecialchars($inst_email) ?></p>
                </div>
            </div>

            <div class="text-center" style="margin-bottom: 20px;">
                <h3 style="font-weight: 800; margin: 0; color: #1e293b; text-transform: uppercase;">
                    Academic Calendar & UT Planner (Session <?= htmlspecialchars($session) ?>)
                    <?= $class_filter ? " - Class " . htmlspecialchars($class_filter) : " - Master Schedule" ?>
                </h3>
            </div>

            <!-- Calendar Grid Table -->
            <div class="calendar-table-wrapper">
                <table class="calendar-table">
                    <thead>
                        <tr>
                            <th class="day-col-header">Day</th>
                            <?php foreach ($months_config as $m_config) { ?>
                                <th><?= $m_config['name'] ?></th>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Print days 1 to 31
                        for ($day = 1; $day <= 31; $day++) {
                            echo "<tr>";
                            // Row Header (Day Number)
                            echo "<td class='day-num'>$day</td>";
                            
                            // Check for each month
                            foreach ($months_config as $m_config) {
                                $month_num = $m_config['month'];
                                $year_num = $m_config['year'];
                                
                                // Check if this is a valid date
                                $is_valid = checkdate($month_num, $day, $year_num);
                                
                                if (!$is_valid) {
                                    echo "<td class='cell-invalid'></td>";
                                    continue;
                                }
                                
                                // Construct date string
                                $current_date = sprintf("%04d-%02d-%02d", $year_num, $month_num, $day);
                                
                                // Determine day of week
                                $day_of_week = date('N', strtotime($current_date)); // 1 (Mon) - 7 (Sun)
                                $is_sunday = ($day_of_week == 7);
                                
                                // Get Holiday & UT Name
                                $holiday_name = $holidays[$current_date] ?? null;
                                $ut_name = $ut_tests[$current_date] ?? null;
                                
                                // Get classification details
                                $classification = get_day_classification($current_date, $is_sunday, $holiday_name, $ut_name);
                                
                                // Render TD
                                $title_attr = !empty($classification['text']) ? "title='" . htmlspecialchars($classification['text']) . "'" : "";
                                echo "<td class='{$classification['class']}' $title_attr>";
                                echo htmlspecialchars($classification['text']);
                                echo "</td>";
                            }
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <!-- Color Legend -->
            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color-box" style="background-color: #ffffff;"></div>
                    <span>Working Day</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color-box" style="background-color: #10b981;"></div>
                    <span>Sunday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color-box" style="background-color: #86efac;"></div>
                    <span>Holiday</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color-box" style="background-color: #fdba74;"></div>
                    <span>Special Day</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color-box" style="background-color: #3b82f6;"></div>
                    <span>School Reopens</span>
                </div>
                <div class="legend-item">
                    <div class="legend-color-box" style="background-color: #fef08a; border: 2px dashed #eab308;"></div>
                    <span>Examination</span>
                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>
