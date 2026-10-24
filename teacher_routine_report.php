<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$branch_id = $_SESSION['branch_id'] ?? 1;

// Filters
$filter_class   = $_REQUEST['filter_class']   ?? '';
$filter_section = $_REQUEST['filter_section'] ?? '';
$filter_day     = $_REQUEST['filter_day']     ?? '';

// Dropdown data
$class_res   = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
$all_classes = array_column($class_res['data'] ?? [], 'student_class');

$section_res  = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
$all_sections = array_column($section_res['data'] ?? [], 'student_section');

$all_days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

// WHERE clause extras — aliased version (for queries using FROM timetable t)
$class_where_t   = $filter_class   ? "AND t.student_class = '$filter_class'"    : '';
$section_where_t = $filter_section ? "AND t.student_section = '$filter_section'" : '';
$day_where_t     = $filter_day     ? "AND t.day = '$filter_day'"                : '';

// WHERE clause extras — plain version (for queries without table alias)
$class_where   = $filter_class   ? "AND student_class = '$filter_class'"    : '';
$section_where = $filter_section ? "AND student_section = '$filter_section'" : '';
$day_where     = $filter_day     ? "AND day = '$filter_day'"                 : '';

// ── ALL active teachers — join employee + user table (same logic as get_teacher_employee())
// user_type LIKE '%TEACHER%' covers all teacher variants (TEACHER, CLASS_TEACHER, etc.)
$all_teachers_sql = "SELECT e.id, e.name, e.emp_code, e.mobile, e.class_teacher
                     FROM employee e
                     INNER JOIN user u ON LOWER(u.user_name) = LOWER(e.emp_code)
                     WHERE u.user_type LIKE '%TEACHER%'
                     AND e.status = 'ACTIVE'
                     AND e.branch_id = '$branch_id'
                     AND u.branch_id = '$branch_id'
                     ORDER BY e.name ASC";
$all_teachers = direct_sql($all_teachers_sql, 'get', false)['data'] ?? [];


// ── For each teacher, count timetable entries ────────────────────────────────
$days_list   = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
$report_rows = [];

foreach ($all_teachers as $teacher) {
    $tid = $teacher['id'];

    // Total periods assigned in timetable (with applied filters)
    $filled_sql = "SELECT COUNT(*) as filled,
                          COUNT(DISTINCT CONCAT(t.student_class,'-',t.student_section)) as cls_count,
                          COUNT(DISTINCT t.day) as day_count
                   FROM timetable t
                   WHERE t.teacher_id = '$tid'
                   AND t.status = 'ACTIVE'
                   AND t.branch_id = '$branch_id'
                   $class_where_t $section_where_t $day_where_t";
    $filled_row = direct_sql($filled_sql, 'get', false)['data'][0] ?? [];
    $filled     = (int)($filled_row['filled']    ?? 0);
    $cls_count  = (int)($filled_row['cls_count'] ?? 0);
    $day_count  = (int)($filled_row['day_count'] ?? 0);

    // Day-wise breakdown
    $day_breakdown = [];
    foreach ($days_list as $d) {
        $cnt = direct_sql(
            "SELECT COUNT(*) as c FROM timetable
             WHERE teacher_id='$tid' AND day='$d' AND status='ACTIVE' AND branch_id='$branch_id'
             $class_where $section_where",
            'get', false
        )['data'][0]['c'] ?? 0;
        $day_breakdown[$d] = (int)$cnt;
    }
    // NOTE: day breakdown uses plain WHERE clauses (no alias) — correct.

    // Classes/sections assigned (uses plain WHERE clauses — no alias)
    $cls_sql = "SELECT DISTINCT CONCAT(student_class,'-',student_section) as cls_sec
                FROM timetable
                WHERE teacher_id='$tid' AND status='ACTIVE' AND branch_id='$branch_id'
                $class_where $section_where $day_where
                ORDER BY student_class, student_section";
    $cls_res_t = direct_sql($cls_sql, 'get', false);
    $class_sections = implode(', ', array_column($cls_res_t['data'] ?? [], 'cls_sec'));

    $status = ($filled > 0) ? 'FILLED' : 'NOT_FILLED';

    $report_rows[] = [
        'teacher_name'   => $teacher['name'],
        'emp_code'       => $teacher['emp_code'],
        'mobile'         => $teacher['mobile'],
        'class_teacher'  => $teacher['class_teacher'],
        'class_sections' => $class_sections,
        'filled'         => $filled,
        'cls_count'      => $cls_count,
        'day_count'      => $day_count,
        'day_breakdown'  => $day_breakdown,
        'status'         => $status,
    ];
}

// Sort: NOT_FILLED first
usort($report_rows, fn($a, $b) => ($a['status'] == 'NOT_FILLED' ? 0 : 1) - ($b['status'] == 'NOT_FILLED' ? 0 : 1));

$total_teachers  = count($report_rows);
$filled_count    = count(array_filter($report_rows, fn($r) => $r['status'] == 'FILLED'));
$notfilled_count = $total_teachers - $filled_count;
$compliance      = $total_teachers > 0 ? round(($filled_count / $total_teachers) * 100) : 0;
?>
<title><?php echo $inst_name; ?> – Teacher Routine Report</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
.content-wrapper { font-family: 'Outfit', sans-serif; background: #f1f5f9; }

/* Banner */
.report-banner {
    background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    border-radius: 20px; padding: 28px 32px; color: #fff;
    margin-bottom: 24px; display: flex; align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 30px rgba(15,23,42,0.25);
}
.report-banner h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.report-banner p  { font-size: 13px; margin: 0; opacity: 0.7; }

/* Compliance Ring */
.compliance-ring {
    width: 80px; height: 80px; border-radius: 50%;
    background: conic-gradient(<?php echo $compliance >= 80 ? '#10b981' : ($compliance >= 50 ? '#f59e0b' : '#ef4444') ?> <?php echo $compliance * 3.6 ?>deg, rgba(255,255,255,0.15) 0deg);
    display: flex; align-items: center; justify-content: center;
    box-shadow: 0 0 0 6px rgba(255,255,255,0.08);
}
.compliance-ring-inner {
    width: 60px; height: 60px; border-radius: 50%;
    background: #1e293b; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
}
.compliance-ring-inner span { font-size: 16px; font-weight: 800; line-height: 1; }
.compliance-ring-inner small { font-size: 9px; opacity: 0.7; letter-spacing: 0.3px; }

/* Summary Cards */
.summary-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 16px; margin-bottom: 24px; }
.summary-card {
    border-radius: 16px; padding: 22px 20px; color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; overflow: hidden;
}
.summary-card::after {
    content:''; position:absolute; right:-20px; top:-20px;
    width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.1);
}
.summary-card .s-num { font-size: 40px; font-weight: 800; line-height: 1; }
.summary-card .s-lbl { font-size: 12px; font-weight: 500; opacity: 0.85; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.card-total   { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.card-filled  { background: linear-gradient(135deg, #059669, #10b981); }
.card-missing { background: linear-gradient(135deg, #dc2626, #ef4444); }

/* Filter */
.filter-card { background:#fff; border-radius:16px; padding:22px 28px; margin-bottom:24px; box-shadow:0 2px 12px rgba(0,0,0,0.05); }
.filter-card label { font-size:12px; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.5px; display:block; margin-bottom:6px; }
.filter-card .form-control { border-radius:8px; border:1.5px solid #e2e8f0; font-size:14px; padding:9px 12px; height:auto; }
.filter-card .form-control:focus { border-color:#4f46e5; box-shadow:0 0 0 3px rgba(79,70,229,0.12); }
.btn-filter {
    background: linear-gradient(135deg,#4f46e5,#4338ca); color:#fff; border:none;
    border-radius:8px; padding:10px 0; font-weight:600; font-size:14px; width:100%;
    box-shadow:0 4px 12px rgba(79,70,229,0.25); transition:all 0.2s; cursor:pointer;
}
.btn-filter:hover { transform:translateY(-1px); box-shadow:0 6px 16px rgba(79,70,229,0.35); color:#fff; }

/* Table Card */
.report-card { background:#fff; border-radius:16px; box-shadow:0 2px 12px rgba(0,0,0,0.05); overflow:hidden; }
.report-card-header {
    background: linear-gradient(135deg,#1e293b,#0f172a); color:#fff;
    padding:18px 24px; display:flex; align-items:center; justify-content:space-between;
}
.report-card-header h4 { margin:0; font-size:16px; font-weight:600; }

.table-report { width:100%; border-collapse:collapse; }
.table-report thead th {
    background:#f8fafc; color:#64748b; font-size:11px; font-weight:700;
    text-transform:uppercase; letter-spacing:0.6px; padding:14px 16px;
    border-bottom:2px solid #e2e8f0; white-space:nowrap; text-align:left;
}
.table-report tbody td { padding:13px 16px; border-bottom:1px solid #f1f5f9; font-size:14px; color:#334155; vertical-align:middle; }
.table-report tbody tr:hover td { background:#f8fafc; }
.table-report tbody tr:last-child td { border-bottom:none; }

/* Row highlight */
.row-not-filled td { background: #fff8f8 !important; }
.row-not-filled:hover td { background: #fff0f0 !important; }

/* Badges */
.badge-filled  { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; white-space:nowrap; }
.badge-missing { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:5px 14px; border-radius:20px; font-size:12px; font-weight:600; white-space:nowrap; }

/* Day dots */
.day-dot {
    display:inline-flex; align-items:center; justify-content:center;
    width:28px; height:28px; border-radius:50%;
    font-size:10px; font-weight:700; margin:1px;
    text-transform:uppercase; letter-spacing:0.3px;
}
.day-dot.has   { background:#dcfce7; color:#166534; border:1.5px solid #86efac; }
.day-dot.empty { background:#f1f5f9; color:#94a3b8; border:1.5px solid #e2e8f0; }

/* Teacher avatar */
.teacher-avatar {
    width:38px; height:38px; border-radius:50%;
    background: linear-gradient(135deg,#4f46e5,#6366f1); color:#fff;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:15px; font-weight:700; margin-right:10px; flex-shrink:0;
}
.teacher-avatar.not-filled { background: linear-gradient(135deg,#dc2626,#ef4444); }

/* Table footer */
.table-footer {
    padding:16px 20px; background:#f8fafc; border-top:1px solid #e2e8f0;
    display:flex; align-items:center; gap:20px; flex-wrap:wrap; font-size:13px; color:#64748b;
}

@media(max-width:768px){ .summary-grid { grid-template-columns:repeat(1,1fr); } }
@media print {
    .filter-card,.content-header,.main-sidebar,.main-header { display:none !important; }
    .report-banner { border-radius:0; margin:0; }
    .content-wrapper { background:#fff; }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Teacher Routine Report</h1>
        <ol class="breadcrumb">
            <li><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="daily_routine">Daily Routine</a></li>
            <li class="active">Teacher Routine Report</li>
        </ol>
    </section>

    <section class="content" style="padding:20px;">

        <!-- Banner -->
        <div class="report-banner">
            <div>
                <h2 class='text-white'><i class="fa fa-calendar" style="margin-right:10px;"></i> Teacher Weekly Routine Report</h2>
                <p>Check which teachers have filled their weekly class timetable (daily routine)</p>
                <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
                    <?php if ($filter_class): ?>
                        <span style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:8px;padding:4px 14px;font-size:12px;font-weight:600;">
                            <i class="fa fa-filter"></i> Class: <?= htmlspecialchars($filter_class) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($filter_section): ?>
                        <span style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:8px;padding:4px 14px;font-size:12px;font-weight:600;">
                            <i class="fa fa-filter"></i> Section: <?= htmlspecialchars($filter_section) ?>
                        </span>
                    <?php endif; ?>
                    <?php if ($filter_day): ?>
                        <span style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.2);border-radius:8px;padding:4px 14px;font-size:12px;font-weight:600;">
                            <i class="fa fa-filter"></i> Day: <?= htmlspecialchars($filter_day) ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                <div class="compliance-ring">
                    <div class="compliance-ring-inner">
                        <span><?= $compliance ?>%</span>
                        <small>FILLED</small>
                    </div>
                </div>
                <button onclick="window.print()" class="btn btn-xs" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;padding:5px 14px;">
                    <i class="fa fa-print"></i> Print
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card card-total">
                <div class="s-num"><?= $total_teachers ?></div>
                <div class="s-lbl"><i class="fa fa-users"></i> Total Active Teachers</div>
            </div>
            <div class="summary-card card-filled">
                <div class="s-num"><?= $filled_count ?></div>
                <div class="s-lbl"><i class="fa fa-check-circle"></i> Routine Filled</div>
            </div>
            <div class="summary-card card-missing">
                <div class="s-num"><?= $notfilled_count ?></div>
                <div class="s-lbl"><i class="fa fa-times-circle"></i> Routine Not Filled</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="get" action="">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label>Class <small style="text-transform:none;font-weight:400;">(optional)</small></label>
                        <select name="filter_class" class="form-control">
                            <option value="">All Classes</option>
                            <?php foreach ($all_classes as $cls): ?>
                                <option value="<?= htmlspecialchars($cls) ?>" <?= $filter_class == $cls ? 'selected' : '' ?>><?= htmlspecialchars($cls) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Section <small style="text-transform:none;font-weight:400;">(optional)</small></label>
                        <select name="filter_section" class="form-control">
                            <option value="">All Sections</option>
                            <?php foreach ($all_sections as $sec): ?>
                                <option value="<?= htmlspecialchars($sec) ?>" <?= $filter_section == $sec ? 'selected' : '' ?>><?= htmlspecialchars($sec) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Day <small style="text-transform:none;font-weight:400;">(optional)</small></label>
                        <select name="filter_day" class="form-control">
                            <option value="">All Days</option>
                            <?php foreach ($all_days as $d): ?>
                                <option value="<?= $d ?>" <?= $filter_day == $d ? 'selected' : '' ?>><?= $d ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-filter">
                            <i class="fa fa-search"></i> &nbsp;Generate Report
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Report Table -->
        <div class="report-card">
            <div class="report-card-header">
                <h4 class='text-light'><i class="fa fa-table" style="margin-right:8px;"></i> Teacher-wise Weekly Routine Status</h4>
                <span style="font-size:13px;opacity:0.85;">
                    Compliance: <strong style="color:<?= $compliance >= 80 ? '#4ade80' : ($compliance >= 50 ? '#fbbf24' : '#f87171') ?>;"><?= $compliance ?>%</strong>
                </span>
            </div>

            <div style="overflow-x:auto;">
            <?php if (empty($report_rows)): ?>
                <div style="padding:60px 20px;text-align:center;color:#94a3b8;">
                    <i class="fa fa-users" style="font-size:48px;display:block;margin-bottom:16px;"></i>
                    <h4 style="color:#64748b;">No teachers found</h4>
                    <p style="font-size:14px;">No active teachers in this branch.</p>
                </div>
            <?php else: ?>
                <table class="table-report">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Teacher Name</th>
                            <th>Emp Code</th>
                            <th>Class Teacher Of</th>
                            <th>Assigned Class(es)</th>
                            <th style="text-align:center;">Total<br>Periods</th>
                            <th style="text-align:center;">Days<br>Covered</th>
                            <th style="text-align:center;">Day-wise Periods</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $day_abbr = ['Monday'=>'Mo','Tuesday'=>'Tu','Wednesday'=>'We','Thursday'=>'Th','Friday'=>'Fr','Saturday'=>'Sa'];
                    $sno = 1;
                    foreach ($report_rows as $row):
                        $is_filled    = $row['status'] == 'FILLED';
                        $row_class    = $is_filled ? '' : 'row-not-filled';
                        $avatar_class = $is_filled ? '' : 'not-filled';
                        $initials     = strtoupper(substr($row['teacher_name'], 0, 1));
                    ?>
                    <tr class="<?= $row_class ?>">
                        <td style="color:#94a3b8;font-weight:500;"><?= $sno++ ?></td>
                        <td>
                            <div style="display:flex;align-items:center;">
                                <span class="teacher-avatar <?= $avatar_class ?>"><?= $initials ?></span>
                                <div>
                                    <div style="font-weight:600;"><?= htmlspecialchars($row['teacher_name']) ?></div>
                                    <div style="font-size:11px;color:#94a3b8;"><i class="fa fa-phone"></i> <?= htmlspecialchars($row['mobile'] ?: '—') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code style="color:#4f46e5;font-size:13px;"><?= htmlspecialchars($row['emp_code'] ?: '—') ?></code></td>
                        <td>
                            <?php if ($row['class_teacher']): ?>
                                <span style="background:#fef3c7;color:#92400e;border:1px solid #fde68a;border-radius:6px;padding:3px 10px;font-size:12px;font-weight:700;">
                                    <i class="fa fa-star"></i> <?= htmlspecialchars($row['class_teacher']) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:#cbd5e1;font-size:13px;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['class_sections']): ?>
                                <?php foreach (explode(', ', $row['class_sections']) as $cs): ?>
                                    <span style="background:#eff6ff;color:#1d4ed8;border-radius:6px;padding:2px 8px;font-size:12px;font-weight:600;display:inline-block;margin:2px;"><?= htmlspecialchars(trim($cs)) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span style="color:#f87171;font-size:12px;font-weight:600;"><i class="fa fa-exclamation-circle"></i> No routine added</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:20px;font-weight:800;color:<?= $is_filled ? '#059669' : '#dc2626' ?>;"><?= $row['filled'] ?></span>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:18px;font-weight:700;color:<?= $row['day_count'] > 0 ? '#4f46e5' : '#94a3b8' ?>;">
                                <?= $row['day_count'] ?>/<?= count($all_days) ?>
                            </span>
                        </td>
                        <td style="text-align:center;">
                            <?php foreach ($all_days as $d): ?>
                                <?php
                                $cnt = $row['day_breakdown'][$d] ?? 0;
                                $cls_d = $cnt > 0 ? 'has' : 'empty';
                                $title_d = $d . ': ' . $cnt . ' period(s)';
                                ?>
                                <span class="day-dot <?= $cls_d ?>" title="<?= $title_d ?>">
                                    <?= $day_abbr[$d] ?>
                                </span>
                            <?php endforeach; ?>
                        </td>
                        <td style="text-align:center;">
                            <?php if ($is_filled): ?>
                                <span class="badge-filled"><i class="fa fa-check-circle"></i> Filled</span>
                            <?php else: ?>
                                <span class="badge-missing"><i class="fa fa-times-circle"></i> Not Filled</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <a href="daily_routine" class="btn btn-xs btn-default" style="border-radius:6px;" title="Open Daily Routine">
                                <i class="fa fa-external-link"></i> Fill Routine
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            </div>

            <?php if (!empty($report_rows)): ?>
            <div class="table-footer">
                <span><strong style="color:#1e293b;"><?= $total_teachers ?></strong> active teachers</span>
                <span style="color:#059669;"><i class="fa fa-circle" style="font-size:8px;"></i> <strong><?= $filled_count ?></strong> Routine Filled</span>
                <span style="color:#dc2626;"><i class="fa fa-circle" style="font-size:8px;"></i> <strong><?= $notfilled_count ?></strong> Not Filled</span>
                <span style="margin-left:auto;">
                    Overall Compliance:
                    <strong style="color:<?= $compliance >= 80 ? '#059669' : ($compliance >= 50 ? '#d97706' : '#dc2626') ?>;">
                        <?= $compliance ?>%
                    </strong>
                </span>
            </div>
            <?php endif; ?>
        </div>

    </section>
</div>

<?php require_once('system/footer2.php'); ?>
