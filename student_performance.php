<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $student_section = null;
if (isset($_REQUEST['student_class'])) {
    $student_class = $_REQUEST['student_class'];
}
if (isset($_REQUEST['student_section'])) {
    $student_section = $_REQUEST['student_section'];
}

// Fetch assigned classes/sections for Teachers
if ($user_type == 'TEACHER') {
    $emp = get_teacher_employee();
    $teacher_id = $emp['id'] ?? null;

    if (!empty($emp['class_teacher'])) {
        $parts = explode('-', $emp['class_teacher']);
        $ct_class = trim($parts[0]);
        $ct_section = isset($parts[1]) ? trim($parts[1]) : '';
        $class_list = [$ct_class];
        $section_list = [$ct_section];

        // Auto-select designated class/section for Class Teachers
        $student_class = $student_class ?? $ct_class;
        $student_section = $student_section ?? $ct_section;
    } else {
        $class_list = [];
        $section_list = [];
    }
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
?>
<title>Student Performance | RMPS</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .sp-container {
        font-family: 'Outfit', sans-serif;
        background-color: #f8fafc;
        padding: 24px;
        border-radius: 20px;
    }

    .sp-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .sp-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sp-date {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
    }

    .filter-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        margin-bottom: 24px;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .analytic-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    .analytic-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .card-strong::before {
        background: #10b981;
    }

    .card-average::before {
        background: #3b82f6;
    }

    .card-weak::before {
        background: #ef4444;
    }

    .card-class-avg::before {
        background: #f59e0b;
    }

    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .card-strong .icon-wrapper {
        background: #ecfdf5;
        color: #10b981;
    }

    .card-average .icon-wrapper {
        background: #eff6ff;
        color: #3b82f6;
    }

    .card-weak .icon-wrapper {
        background: #fef2f2;
        color: #ef4444;
    }

    .card-class-avg .icon-wrapper {
        background: #fffbeb;
        color: #f59e0b;
    }

    .card-info {
        display: flex;
        flex-direction: column;
    }

    .card-label {
        font-size: 11px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .card-value {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-top: 4px;
    }

    .pill-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .pill-tab {
        border: none;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s;
        background: #f1f5f9;
        color: #64748b;
    }

    .pill-tab.active {
        background: #0f172a;
        color: #fff;
    }

    .pill-tab-strong {
        border: 1.5px solid #10b981;
        background: transparent;
        color: #10b981;
    }

    .pill-tab-strong.active {
        background: #10b981;
        color: #fff;
    }

    .pill-tab-average {
        border: 1.5px solid #3b82f6;
        background: transparent;
        color: #3b82f6;
    }

    .pill-tab-average.active {
        background: #3b82f6;
        color: #fff;
    }

    .pill-tab-weak {
        border: 1.5px solid #ef4444;
        background: transparent;
        color: #ef4444;
    }

    .pill-tab-weak.active {
        background: #ef4444;
        color: #fff;
    }

    .premium-table-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }

    .sp-table {
        width: 100%;
        border-collapse: collapse;
    }

    .sp-table th {
        background: #0f172a;
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 20px;
        text-align: left;
    }

    .sp-table td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 13.5px;
        color: #334155;
        vertical-align: middle;
    }

    .sp-table tr:last-child td {
        border-bottom: none;
    }

    .sp-table tr:hover td {
        background-color: #f8fafc;
    }

    .roll-col {
        font-weight: 700;
        color: #0f172a;
        width: 60px;
    }

    .name-col {
        font-weight: 600;
        color: #0f172a;
    }

    .bold-col {
        font-weight: 700;
        color: #0f172a;
    }

    .perf-progress-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 160px;
    }

    .perf-progress {
        flex-grow: 1;
        height: 6px;
        background: #f1f5f9;
        border-radius: 9999px;
        overflow: hidden;
    }

    .perf-progress-bar {
        height: 100%;
        border-radius: 9999px;
    }

    .bar-strong {
        background: #10b981;
    }

    .bar-average {
        background: #3b82f6;
    }

    .bar-weak {
        background: #ef4444;
    }

    .progress-val {
        font-size: 11.5px;
        font-weight: 700;
        color: #64748b;
        min-width: 30px;
    }

    .status-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .dot-strong {
        background: #10b981;
    }

    .dot-average {
        background: #3b82f6;
    }

    .dot-weak {
        background: #ef4444;
    }

    .status-cell {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
    }

    .tag-pill {
        display: inline-block;
        padding: 4px 12px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 9999px;
        text-transform: capitalize;
    }

    .tag-strong {
        background: #d1fae5;
        color: #065f46;
    }

    .tag-average {
        background: #dbeafe;
        color: #1e40af;
    }

    .tag-weak {
        background: #fee2e2;
        color: #991b1b;
    }

    .btn-graph {
        background: #fff;
        border: 1px solid #cbd5e1;
        padding: 6px 14px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-graph:hover {
        background: #f8fafc;
        border-color: #94a3b8;
        color: #0f172a;
    }

    /* Modal Styling */
    .sp-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.4);
        z-index: 1050;
        align-items: center;
        justify-content: center;
    }

    .sp-modal-content {
        background: #fff;
        border-radius: 24px;
        width: 90%;
        max-width: 680px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        padding: 24px;
        position: relative;
        animation: modalFadeIn 0.3s ease-out;
    }

    @keyframes modalFadeIn {
        from {
            transform: translateY(20px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        font-size: 16px;
        font-weight: 700;
        transition: all 0.2s;
    }

    .modal-close:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .modal-header-info {
        margin-bottom: 20px;
    }

    .modal-student-name {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .modal-student-sub {
        font-size: 12.5px;
        color: #64748b;
        font-weight: 600;
        margin-top: 2px;
    }

    .chart-container {
        position: relative;
        height: 220px;
        width: 100%;
        margin-bottom: 24px;
    }

    .modal-metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
        margin-bottom: 24px;
    }

    .modal-metric-card {
        background: #f8fafc;
        border-radius: 12px;
        padding: 10px 14px;
        text-align: center;
    }

    .modal-metric-label {
        font-size: 10px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modal-metric-val {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 4px;
    }

    .val-highest {
        color: #10b981;
    }

    .val-lowest {
        color: #ef4444;
    }

    .phase-section {
        border-top: 1px solid #f1f5f9;
        padding-top: 20px;
    }

    .phase-title {
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .phase-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 12px;
    }

    .phase-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px;
        text-align: center;
    }

    .phase-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
    }

    .phase-val {
        font-size: 16px;
        font-weight: 800;
        color: #0f172a;
        margin-top: 4px;
    }

    .phase-sub {
        font-size: 9px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        margin-top: 2px;
    }

    th[data-sortable="true"]:hover {
        background-color: #1e293b !important;
        color: #3b82f6 !important;
    }
    
    th[data-sortable="true"] i {
        transition: color 0.2s;
        opacity: 0.7;
    }
    
    th[data-sortable="true"]:hover i {
        color: #3b82f6 !important;
        opacity: 1;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="sp-container">
            <div class="sp-header">
                <h1 class="sp-title">
                    <img src="assets/svg/education.svg" width="32" style="opacity:0.9;">
                    Student Performance
                </h1>
                <div class="sp-date">
                    <i class="fa fa-calendar mr-1"></i>
                    <?= date('l, M j, Y') ?>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-card">
                <form action="" method="get">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold mb-1"
                                    style="font-size:12px; color:#475569;">Class</label>
                                <select class="form-control form-control-premium" name="student_class" required>
                                    <?php dropdown($class_list, $student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold mb-1"
                                    style="font-size:12px; color:#475569;">Section</label>
                                <select class="form-control form-control-premium" name="student_section" required>
                                    <?php dropdown($section_list, $student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="d-block mb-1">&nbsp;</label>
                            <button type="submit" class="btn btn-premium btn-premium-primary btn-block">
                                <i class="fa fa-search mr-2"></i> Search Performance
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <?php if ($student_class && $student_section): ?>
                <?php
                // Fetch Active Students
                $students_res = direct_sql("SELECT id, student_name, student_roll FROM student WHERE student_class = '$student_class' AND student_section = '$student_section' AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY student_roll ASC");

                // Fetch Active UT Tests
                $tests_res = direct_sql("SELECT id, ut_number, max_marks FROM ut_tests WHERE student_class = '$student_class' AND (student_section = '' OR student_section IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY id ASC");
                $tests = $tests_res['data'] ?? [];

                $strong_count = 0;
                $average_count = 0;
                $weak_count = 0;
                $total_students = $students_res['count'];
                $class_perc_sum = 0;

                $calculated_students = [];

                if ($total_students > 0) {
                    foreach ($students_res['data'] as $student) {
                        $student_id = $student['id'];

                        $total_max = 0;
                        $total_scored = 0;
                        $test_history = [];
                        $scores_only = [];

                        foreach ($tests as $idx => $test) {
                            $ut_id = $test['id'];
                            $max = $test['max_marks'];

                            $mark_q = direct_sql("SELECT marks FROM ut_marks WHERE ut_id = '$ut_id' AND student_id = '$student_id' LIMIT 1");

                            if ($mark_q['count'] > 0) {
                                $scored = floatval($mark_q['data'][0]['marks']);
                                $total_max += $max;
                                $total_scored += $scored;

                                // Scale to 20 baseline for normalized graph
                                $scaled = ($max > 0) ? ($scored / $max) * 20 : 0;
                                $scores_only[] = $scaled;

                                $test_history[] = [
                                    'label' => $test['ut_number'],
                                    'score' => round($scaled, 1)
                                ];
                            }
                        }

                        $percentage = ($total_max > 0) ? ($total_scored / $total_max) * 100 : 0;
                        $avg_marks = ($percentage / 100) * 20; // Scale to 20
            
                        $class_perc_sum += $percentage;

                        // Performance classifications
                        if ($avg_marks >= 14) {
                            $tag = 'strong';
                            $status = 'On Track';
                            $color = 'strong';
                            $strong_count++;
                        } else if ($avg_marks >= 8) {
                            $tag = 'average';
                            $status = 'At Risk';
                            $color = 'average';
                            $average_count++;
                        } else {
                            $tag = 'weak';
                            $status = 'At Risk';
                            $color = 'weak';
                            $weak_count++;
                        }

                        // Phase Breakdown Calculations
                        // Foundation (UT 1-7), Intermediate (UT 8-14), Stability (UT 15-21), Final Revision (UT 22+)
                        $foundation_avg = count(array_slice($scores_only, 0, 7)) > 0 ? array_sum(array_slice($scores_only, 0, 7)) / count(array_slice($scores_only, 0, 7)) : 0;
                        $intermediate_avg = count(array_slice($scores_only, 7, 7)) > 0 ? array_sum(array_slice($scores_only, 7, 7)) / count(array_slice($scores_only, 7, 7)) : 0;
                        $stability_avg = count(array_slice($scores_only, 14, 7)) > 0 ? array_sum(array_slice($scores_only, 14, 7)) / count(array_slice($scores_only, 14, 7)) : 0;
                        $final_avg = count(array_slice($scores_only, 21)) > 0 ? array_sum(array_slice($scores_only, 21)) / count(array_slice($scores_only, 21)) : 0;

                        // Trend Arrow
                        $trend = '↑';
                        if (count($scores_only) >= 2) {
                            $last = end($scores_only);
                            $prev = prev($scores_only);
                            if ($last < $prev) {
                                $trend = '↓';
                            }
                        }

                        $calculated_students[] = [
                            'roll' => $student['student_roll'],
                            'name' => $student['student_name'],
                            'avg_marks' => round($avg_marks, 1),
                            'percentage' => round($percentage, 0),
                            'tag' => $tag,
                            'status' => $status,
                            'color' => $color,
                            'history' => $test_history,
                            'highest' => count($scores_only) > 0 ? max($scores_only) : 0,
                            'lowest' => count($scores_only) > 0 ? min($scores_only) : 0,
                            'trend' => $trend,
                            'foundation' => round($foundation_avg, 1),
                            'intermediate' => round($intermediate_avg, 1),
                            'stability' => round($stability_avg, 1),
                            'final' => round($final_avg, 1)
                        ];
                    }
                }

                $class_average_perc = ($total_students > 0) ? $class_perc_sum / $total_students : 0;
                ?>

                <!-- Analytics Summary cards -->
                <div class="analytics-grid">
                    <div class="analytic-card card-strong">
                        <div class="icon-wrapper">
                            <i class="fa fa-trophy"></i>
                        </div>
                        <div class="card-info">
                            <span class="card-label">Strong (>=14/20)</span>
                            <span class="card-value"><?= $strong_count ?></span>
                        </div>
                    </div>
                    <div class="analytic-card card-average">
                        <div class="icon-wrapper">
                            <i class="fa fa-thumbs-up"></i>
                        </div>
                        <div class="card-info">
                            <span class="card-label">Average (8-13/20)</span>
                            <span class="card-value"><?= $average_count ?></span>
                        </div>
                    </div>
                    <div class="analytic-card card-weak">
                        <div class="icon-wrapper">
                            <i class="fa fa-exclamation-triangle"></i>
                        </div>
                        <div class="card-info">
                            <span class="card-label">Weak (<8 /20)</span>
                                    <span class="card-value"><?= $weak_count ?></span>
                        </div>
                    </div>
                    <div class="analytic-card card-class-avg">
                        <div class="icon-wrapper">
                            <i class="fa fa-line-chart"></i>
                        </div>
                        <div class="card-info">
                            <span class="card-label">Class Average</span>
                            <span class="card-value"><?= round($class_average_perc, 1) ?>%</span>
                        </div>
                    </div>
                </div>

                <!-- Tabs Filter Bar -->
                <div class="pill-tabs">
                    <button class="pill-tab active" data-filter="all">All (<?= $total_students ?>)</button>
                    <button class="pill-tab pill-tab-strong" data-filter="strong">Strong (<?= $strong_count ?>)</button>
                    <button class="pill-tab pill-tab-average" data-filter="average">Average (<?= $average_count ?>)</button>
                    <button class="pill-tab pill-tab-weak" data-filter="weak">Weak (<?= $weak_count ?>)</button>
                </div>

                <!-- Main Performance Grid -->
                <div class="premium-table-card">
                    <table class="sp-table">
                        <thead>
                            <tr>
                                <th width="8%" data-sortable="true" data-type="number">Roll</th>
                                <th width="30%" data-sortable="true" data-type="string">Student Name</th>
                                <th width="12%" data-sortable="true" data-type="number">Avg Marks</th>
                                <th width="10%" data-sortable="true" data-type="number">%</th>
                                <th width="20%" data-sortable="true" data-type="number">Performance</th>
                                <th width="10%" data-sortable="true" data-type="string">Tag</th>
                                <th width="10%" data-sortable="true" data-type="string">Status</th>
                                <th width="10%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($calculated_students) > 0): ?>
                                <?php foreach ($calculated_students as $stu): ?>
                                    <tr class="student-row" data-tag="<?= $stu['tag'] ?>">
                                        <td class="roll-col"><?= htmlspecialchars($stu['roll']) ?></td>
                                        <td class="name-col"><?= htmlspecialchars($stu['name']) ?></td>
                                        <td class="bold-col"><?= $stu['avg_marks'] ?></td>
                                        <td class="bold-col"><?= $stu['percentage'] ?>%</td>
                                        <td>
                                            <div class="perf-progress-wrapper">
                                                <div class="perf-progress">
                                                    <div class="perf-progress-bar bar-<?= $stu['color'] ?>"
                                                        style="width: <?= $stu['percentage'] ?>%;"></div>
                                                </div>
                                                <span class="progress-val"><?= $stu['percentage'] ?>%</span>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="tag-pill tag-<?= $stu['color'] ?>"><?= $stu['tag'] ?></span>
                                        </td>
                                        <td>
                                            <div class="status-cell">
                                                <span class="status-dot dot-<?= $stu['color'] ?>"></span>
                                                <?= $stu['status'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn-graph" data-name="<?= htmlspecialchars($stu['name']) ?>"
                                                data-roll="<?= htmlspecialchars($stu['roll']) ?>"
                                                data-avg="<?= $stu['avg_marks'] ?>" data-highest="<?= $stu['highest'] ?>"
                                                data-lowest="<?= $stu['lowest'] ?>" data-trend="<?= $stu['trend'] ?>"
                                                data-foundation="<?= $stu['foundation'] ?>"
                                                data-intermediate="<?= $stu['intermediate'] ?>"
                                                data-stability="<?= $stu['stability'] ?>" data-final="<?= $stu['final'] ?>"
                                                data-history='<?= json_encode($stu['history']) ?>'>
                                                <i class="fa fa-line-chart"></i> View Graph
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-5 text-muted">
                                        <i class="fa fa-info-circle mb-2" style="font-size:32px;"></i>
                                        <p class="m-0">No active students or test marks found in this class & section.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<!-- Premium Chart Modal -->
<div class="sp-modal" id="graphModal">
    <div class="sp-modal-content">
        <button class="modal-close" id="modalCloseBtn">&times;</button>

        <div class="modal-header-info">
            <h2 class="modal-student-name" id="modalStudentName">Aadit Kumar</h2>
            <div class="modal-student-sub" id="modalStudentSub">Roll 1 • 27-UT Performance Analysis</div>
        </div>

        <!-- Smooth Chart Canvas -->
        <div class="chart-container">
            <canvas id="performanceChart"></canvas>
        </div>

        <!-- 4 Capsules Metrics -->
        <div class="modal-metrics-grid">
            <div class="modal-metric-card">
                <div class="modal-metric-label">Average</div>
                <div class="modal-metric-val" id="metricAvg">16.2</div>
            </div>
            <div class="modal-metric-card">
                <div class="modal-metric-label">Highest</div>
                <div class="modal-metric-val val-highest" id="metricHighest">20</div>
            </div>
            <div class="modal-metric-card">
                <div class="modal-metric-label">Lowest</div>
                <div class="modal-metric-val val-lowest" id="metricLowest">10.2</div>
            </div>
            <div class="modal-metric-card">
                <div class="modal-metric-label">Trend</div>
                <div class="modal-metric-val" id="metricTrend">↑</div>
            </div>
        </div>

        <!-- Phase Breakdown Section -->
        <div class="phase-section">
            <h4 class="phase-title">Phase Breakdown</h4>
            <div class="phase-grid">
                <div class="phase-card">
                    <div class="phase-label">Foundation</div>
                    <div class="phase-val" id="phaseF">16</div>
                    <div class="phase-sub">UT 1-7</div>
                </div>
                <div class="phase-card">
                    <div class="phase-label">Intermediate</div>
                    <div class="phase-val" id="phaseI">16</div>
                    <div class="phase-sub">UT 8-14</div>
                </div>
                <div class="phase-card">
                    <div class="phase-label">Stability</div>
                    <div class="phase-val" id="phaseS">17</div>
                    <div class="phase-sub">UT 15-21</div>
                </div>
                <div class="phase-card">
                    <div class="phase-label">Final Revision</div>
                    <div class="phase-val" id="phaseFR">15</div>
                    <div class="phase-sub">UT 22+</div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    $(document).ready(function () {
        // Sorting functionality
        $('th[data-sortable="true"]').css('cursor', 'pointer').append(' <i class="fa fa-sort text-muted" style="font-size: 10px; margin-left: 5px;"></i>');

        let sortDirections = {};

        $('th[data-sortable="true"]').on('click', function () {
            let colIndex = $(this).index();
            let colType = $(this).data('type') || 'string';
            let table = $(this).closest('table');
            let tbody = table.find('tbody');
            let rows = tbody.find('.student-row').get();
            
            let isAsc = !sortDirections[colIndex];
            sortDirections[colIndex] = isAsc;

            // Reset other headers' icons
            $('th[data-sortable="true"] i').removeClass('fa-sort-asc fa-sort-desc').addClass('fa-sort');
            
            let icon = $(this).find('i');
            icon.removeClass('fa-sort fa-sort-asc fa-sort-desc');
            icon.addClass(isAsc ? 'fa-sort-asc' : 'fa-sort-desc');

            rows.sort(function (a, b) {
                let cellA = $(a).children('td').eq(colIndex).text().trim();
                let cellB = $(b).children('td').eq(colIndex).text().trim();

                if (colType === 'number') {
                    cellA = parseFloat(cellA.replace(/[^0-9.-]/g, '')) || 0;
                    cellB = parseFloat(cellB.replace(/[^0-9.-]/g, '')) || 0;
                }

                if (cellA < cellB) {
                    return isAsc ? -1 : 1;
                }
                if (cellA > cellB) {
                    return isAsc ? 1 : -1;
                }
                return 0;
            });

            $.each(rows, function (index, row) {
                tbody.append(row);
            });
        });

        // Pill tab filters
        $('.pill-tab').on('click', function () {
            $('.pill-tab').removeClass('active');
            $(this).addClass('active');

            let filter = $(this).data('filter');
            if (filter === 'all') {
                $('.student-row').show();
            } else {
                $('.student-row').hide();
                $('.student-row[data-tag="' + filter + '"]').show();
            }
        });

        let currentChart = null;

        // View Graph Click Event
        $('.btn-graph').on('click', function () {
            let name = $(this).data('name');
            let roll = $(this).data('roll');
            let avg = $(this).data('avg');
            let highest = $(this).data('highest');
            let lowest = $(this).data('lowest');
            let trend = $(this).data('trend');

            let foundation = $(this).data('foundation');
            let intermediate = $(this).data('intermediate');
            let stability = $(this).data('stability');
            let final = $(this).data('final');

            let rawHistory = $(this).attr('data-history');
            let history = JSON.parse(rawHistory || '[]');

            // Populate text metrics
            $('#modalStudentName').text(name);
            $('#modalStudentSub').text('Roll ' + roll + ' • ' + history.length + '-UT Performance Analysis');

            $('#metricAvg').text(avg);
            $('#metricHighest').text(highest);
            $('#metricLowest').text(lowest);
            $('#metricTrend').text(trend);

            $('#phaseF').text(foundation);
            $('#phaseI').text(intermediate);
            $('#phaseS').text(stability);
            $('#phaseFR').text(final);

            // Prep chart arrays
            let labels = history.map(item => item.label);
            let scores = history.map(item => item.score);

            // Destroy previous chart instance if exists
            if (currentChart) {
                currentChart.destroy();
            }

            // Render Canvas line chart
            const ctx = document.getElementById('performanceChart').getContext('2d');

            // Gradient background fill under path
            let gradient = ctx.createLinearGradient(0, 0, 0, 200);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0)');

            currentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Score',
                        data: scores,
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#fff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { font: { family: 'Outfit', size: 10 }, color: '#64748b' }
                        },
                        y: {
                            min: 0,
                            max: 20,
                            ticks: { stepSize: 5, font: { family: 'Outfit', size: 10 }, color: '#64748b' },
                            grid: { color: '#f1f5f9' }
                        }
                    }
                }
            });

            // Show Modal
            $('#graphModal').css('display', 'flex');
        });

        // Close Modal Events
        $('#modalCloseBtn, #graphModal').on('click', function (e) {
            if (e.target === this) {
                $('#graphModal').css('display', 'none');
            }
        });
    });
</script>