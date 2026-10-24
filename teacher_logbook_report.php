<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$branch_id = $_SESSION['branch_id'] ?? 1;

// Filters
$filter_date    = $_REQUEST['filter_date']    ?? date('Y-m-d');
$filter_class   = $_REQUEST['filter_class']   ?? '';
$filter_section = $_REQUEST['filter_section'] ?? '';

// All classes & sections for dropdowns
$class_res   = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
$all_classes = array_column($class_res['data'] ?? [], 'student_class');

$section_res  = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
$all_sections = array_column($section_res['data'] ?? [], 'student_section');

$day_name      = date('l', strtotime($filter_date));
$class_where   = $filter_class   ? "AND t.student_class = '$filter_class'"    : '';
$section_where = $filter_section ? "AND t.student_section = '$filter_section'" : '';

// All active teachers who have a timetable slot on this day
// Join with user table (user_type LIKE '%TEACHER%') — same pattern as get_teacher_employee()
$teachers_sql = "SELECT DISTINCT e.id as teacher_id, e.name as teacher_name, e.emp_code, e.mobile
                 FROM employee e
                 INNER JOIN user u ON LOWER(u.user_name) = LOWER(e.emp_code)
                 JOIN timetable t ON t.teacher_id = e.id
                 WHERE u.user_type LIKE '%TEACHER%'
                 AND e.status = 'ACTIVE'
                 AND e.branch_id = '$branch_id'
                 AND u.branch_id = '$branch_id'
                 AND t.day = '$day_name'
                 AND t.status = 'ACTIVE'
                 AND t.branch_id = '$branch_id'
                 $class_where $section_where
                 ORDER BY e.name ASC";
$teachers_res = direct_sql($teachers_sql, 'get', false);
$teachers     = $teachers_res['data'] ?? [];


$report_rows = [];
foreach ($teachers as $teacher) {
    $tid = $teacher['teacher_id'];

    // Total timetable periods for this teacher on this day
    $total_sql = "SELECT COUNT(*) as total FROM timetable
                  WHERE teacher_id='$tid' AND day='$day_name' AND status='ACTIVE' AND branch_id='$branch_id'
                  $class_where $section_where";
    $total_periods = direct_sql($total_sql, 'get', false)['data'][0]['total'] ?? 0;

    // Filled + signed-off logbook periods for this teacher on this date
    $filled_sql = "SELECT COUNT(*) as filled,
                          SUM(CASE WHEN lp.status='DONE' THEN 1 ELSE 0 END) as done_count
                   FROM logbook_period lp
                   JOIN logbook lb ON lb.id = lp.logbook_id
                   WHERE lp.teacher_id='$tid' AND lb.date='$filter_date' AND lp.branch_id='$branch_id'";
    $filled_row  = direct_sql($filled_sql, 'get', false)['data'][0] ?? [];
    $filled      = $filled_row['filled']     ?? 0;
    $done        = $filled_row['done_count'] ?? 0;

    // Status
    if ($filled == 0) {
        $status = 'NOT_FILLED';
    } elseif ($done >= $total_periods && $total_periods > 0) {
        $status = 'COMPLETE';
    } else {
        $status = 'PARTIAL';
    }

    // Classes/sections assigned on this day
    $cls_res = direct_sql(
        "SELECT DISTINCT CONCAT(student_class,'-',student_section) as cls_sec
         FROM timetable
         WHERE teacher_id='$tid' AND day='$day_name' AND status='ACTIVE' AND branch_id='$branch_id'
         $class_where $section_where",
        'get', false
    );
    $class_sections = implode(', ', array_column($cls_res['data'] ?? [], 'cls_sec'));

    $report_rows[] = [
        'teacher_name'   => $teacher['teacher_name'],
        'emp_code'       => $teacher['emp_code'],
        'mobile'         => $teacher['mobile'],
        'class_sections' => $class_sections,
        'total_periods'  => $total_periods,
        'filled'         => $filled,
        'done'           => $done,
        'status'         => $status,
    ];
}

// Sort: NOT_FILLED first, PARTIAL next, COMPLETE last
usort($report_rows, function ($a, $b) {
    $order = ['NOT_FILLED' => 0, 'PARTIAL' => 1, 'COMPLETE' => 2];
    return ($order[$a['status']] ?? 3) - ($order[$b['status']] ?? 3);
});

$total_teachers   = count($report_rows);
$complete_count   = count(array_filter($report_rows, fn($r) => $r['status'] == 'COMPLETE'));
$partial_count    = count(array_filter($report_rows, fn($r) => $r['status'] == 'PARTIAL'));
$not_filled_count = count(array_filter($report_rows, fn($r) => $r['status'] == 'NOT_FILLED'));
$compliance       = $total_teachers > 0 ? round(($complete_count / $total_teachers) * 100) : 0;
?>
<title><?php echo $inst_name; ?> – Teacher Logbook Report</title>

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
.content-wrapper { font-family: 'Outfit', sans-serif; background: #f1f5f9; }

/* Banner */
.report-banner {
    background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
    border-radius: 20px; padding: 28px 32px; color: #fff;
    margin-bottom: 24px; display: flex; align-items: center;
    justify-content: space-between;
    box-shadow: 0 10px 30px rgba(15,23,42,0.25);
}
.report-banner h2 { font-size: 22px; font-weight: 700; margin: 0 0 4px; }
.report-banner p  { font-size: 13px; margin: 0; opacity: 0.7; }
.banner-day-badge {
    background: rgba(255,255,255,0.12); border: 1px solid rgba(255,255,255,0.2);
    border-radius: 12px; padding: 8px 20px; font-size: 15px; font-weight: 600;
}

/* Summary Cards */
.summary-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 24px; }
.summary-card {
    border-radius: 16px; padding: 22px 20px; color: #fff;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1); position: relative; overflow: hidden;
}
.summary-card::after {
    content:''; position:absolute; right:-20px; top:-20px;
    width:80px; height:80px; border-radius:50%; background:rgba(255,255,255,0.1);
}
.summary-card .s-num { font-size: 38px; font-weight: 800; line-height: 1; }
.summary-card .s-lbl { font-size: 12px; font-weight: 500; opacity: 0.85; margin-top: 4px; text-transform: uppercase; letter-spacing: 0.5px; }
.card-total    { background: linear-gradient(135deg, #4f46e5, #6366f1); }
.card-complete { background: linear-gradient(135deg, #059669, #10b981); }
.card-partial  { background: linear-gradient(135deg, #d97706, #f59e0b); }
.card-missing  { background: linear-gradient(135deg, #dc2626, #ef4444); }

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
.table-report tbody td { padding:14px 16px; border-bottom:1px solid #f1f5f9; font-size:14px; color:#334155; vertical-align:middle; }
.table-report tbody tr:hover td { background:#f8fafc; }
.table-report tbody tr:last-child td { border-bottom:none; }

/* Badges */
.badge-complete { background:#d1fae5; color:#065f46; border:1px solid #a7f3d0; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; white-space:nowrap; }
.badge-partial  { background:#fef3c7; color:#92400e; border:1px solid #fde68a; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; white-space:nowrap; }
.badge-missing  { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; padding:5px 12px; border-radius:20px; font-size:12px; font-weight:600; white-space:nowrap; }

/* Mini progress bar */
.mini-bar-wrap { background:#f1f5f9; border-radius:20px; height:8px; width:80px; overflow:hidden; display:inline-block; vertical-align:middle; margin-left:6px; }
.mini-bar      { height:100%; border-radius:20px; }

/* Teacher avatar */
.teacher-avatar {
    width:36px; height:36px; border-radius:50%;
    background: linear-gradient(135deg,#4f46e5,#6366f1); color:#fff;
    display:inline-flex; align-items:center; justify-content:center;
    font-size:14px; font-weight:700; margin-right:10px; flex-shrink:0;
}

/* Table footer */
.table-footer {
    padding:16px 20px; background:#f8fafc; border-top:1px solid #e2e8f0;
    display:flex; align-items:center; gap:20px; flex-wrap:wrap; font-size:13px; color:#64748b;
}

@media(max-width:768px){ .summary-grid { grid-template-columns:repeat(2,1fr); } }
@media print {
    .filter-card,.content-header,.main-sidebar,.main-header { display:none !important; }
    .report-banner { border-radius:0; margin:0; }
    .content-wrapper { background:#fff; }
}
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Teacher Logbook Report</h1>
        <ol class="breadcrumb">
            <li><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Teacher Logbook Report</li>
        </ol>
    </section>

    <section class="content" style="padding:20px;">

        <!-- Banner -->
        <div class="report-banner">
            <div>
                <h2 class='text-white'><i class="fa fa-book" style="margin-right:10px;"></i> Daily Logbook Compliance Report</h2>
                <p>Track which teachers have filled their daily log book entries for a given date</p>
            </div>
            <div class="text-right">
                <div class="banner-day-badge">
                    <i class="fa fa-calendar-check-o" style="margin-right:6px;"></i>
                    <?= date('d M Y', strtotime($filter_date)) ?> &nbsp;|&nbsp; <?= $day_name ?>
                </div>
                <div style="margin-top:10px;">
                    <button onclick="window.print()" class="btn btn-xs" style="background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:8px;padding:5px 14px;">
                        <i class="fa fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card card-total">
                <div class="s-num"><?= $total_teachers ?></div>
                <div class="s-lbl"><i class="fa fa-users"></i> Teachers Scheduled</div>
            </div>
            <div class="summary-card card-complete">
                <div class="s-num"><?= $complete_count ?></div>
                <div class="s-lbl"><i class="fa fa-check-circle"></i> Fully Filled</div>
            </div>
            <div class="summary-card card-partial">
                <div class="s-num"><?= $partial_count ?></div>
                <div class="s-lbl"><i class="fa fa-adjust"></i> Partially Filled</div>
            </div>
            <div class="summary-card card-missing">
                <div class="s-num"><?= $not_filled_count ?></div>
                <div class="s-lbl"><i class="fa fa-times-circle"></i> Not Filled</div>
            </div>
        </div>

        <!-- Filters -->
        <div class="filter-card">
            <form method="get" action="">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label>Date</label>
                        <input type="date" name="filter_date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>" required>
                    </div>
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
                <h4 class='text-white'><i class="fa fa-table" style="margin-right:8px;"></i>
                    Teacher-wise Logbook Status — <?= $day_name ?>, <?= date('d M Y', strtotime($filter_date)) ?>
                </h4>
                <span style="font-size:13px;opacity:0.85;">
                    Compliance: <strong style="color:<?= $compliance >= 80 ? '#4ade80' : ($compliance >= 50 ? '#fbbf24' : '#f87171') ?>;"><?= $compliance ?>%</strong>
                </span>
            </div>

            <div style="overflow-x:auto;">
            <?php if (empty($report_rows)): ?>
                <div style="padding:60px 20px;text-align:center;color:#94a3b8;">
                    <i class="fa fa-calendar-times-o" style="font-size:48px;display:block;margin-bottom:16px;"></i>
                    <h4 style="color:#64748b;">No teachers scheduled on <?= $day_name ?></h4>
                    <p style="font-size:14px;">No active timetable entries found for this day<?= $filter_class ? " in class $filter_class" : '' ?>.</p>
                </div>
            <?php else: ?>
                <table class="table-report">
                    <thead>
                        <tr>
                            <th style="width:40px;">#</th>
                            <th>Teacher Name</th>
                            <th>Emp Code</th>
                            <th>Assigned Class(es)</th>
                            <th style="text-align:center;">Periods<br>Scheduled</th>
                            <th style="text-align:center;">Periods<br>Filled</th>
                            <th style="text-align:center;">Signed<br>Off</th>
                            <th style="text-align:center;">Progress</th>
                            <th style="text-align:center;">Status</th>
                            <th style="text-align:center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $sno = 1;
                    foreach ($report_rows as $row):
                        $pct = ($row['total_periods'] > 0) ? round(($row['done'] / $row['total_periods']) * 100) : 0;
                        $bar_color = match($row['status']) {
                            'COMPLETE'   => 'linear-gradient(90deg,#10b981,#059669)',
                            'PARTIAL'    => 'linear-gradient(90deg,#f59e0b,#d97706)',
                            'NOT_FILLED' => 'linear-gradient(90deg,#ef4444,#dc2626)',
                            default      => '#94a3b8'
                        };
                        $initials = strtoupper(substr($row['teacher_name'], 0, 1));
                    ?>
                    <tr>
                        <td style="color:#94a3b8;font-weight:500;"><?= $sno++ ?></td>
                        <td>
                            <div style="display:flex;align-items:center;">
                                <span class="teacher-avatar"><?= $initials ?></span>
                                <div>
                                    <div style="font-weight:600;"><?= htmlspecialchars($row['teacher_name']) ?></div>
                                    <div style="font-size:11px;color:#94a3b8;"><i class="fa fa-phone"></i> <?= htmlspecialchars($row['mobile'] ?: '—') ?></div>
                                </div>
                            </div>
                        </td>
                        <td><code style="color:#4f46e5;font-size:13px;"><?= htmlspecialchars($row['emp_code'] ?: '—') ?></code></td>
                        <td>
                            <?php foreach (explode(', ', $row['class_sections']) as $cs): ?>
                                <span style="background:#eff6ff;color:#1d4ed8;border-radius:6px;padding:2px 8px;font-size:12px;font-weight:600;display:inline-block;margin:2px;"><?= htmlspecialchars(trim($cs)) ?></span>
                            <?php endforeach; ?>
                        </td>
                        <td style="text-align:center;font-weight:700;font-size:17px;"><?= $row['total_periods'] ?></td>
                        <td style="text-align:center;font-weight:700;font-size:17px;color:<?= $row['filled'] > 0 ? '#059669' : '#dc2626' ?>;">
                            <?= $row['filled'] ?>
                        </td>
                        <td style="text-align:center;font-weight:700;font-size:17px;color:<?= $row['done'] > 0 ? '#4f46e5' : '#94a3b8' ?>;">
                            <?= $row['done'] ?>
                        </td>
                        <td style="text-align:center;">
                            <span style="font-size:12px;font-weight:600;color:#475569;"><?= $pct ?>%</span>
                            <div class="mini-bar-wrap">
                                <div class="mini-bar" style="width:<?= $pct ?>%;background:<?= $bar_color ?>;"></div>
                            </div>
                        </td>
                        <td style="text-align:center;">
                            <?php if ($row['status'] == 'COMPLETE'): ?>
                                <span class="badge-complete"><i class="fa fa-check-circle"></i> Complete</span>
                            <?php elseif ($row['status'] == 'PARTIAL'): ?>
                                <span class="badge-partial"><i class="fa fa-adjust"></i> Partial</span>
                            <?php else: ?>
                                <span class="badge-missing"><i class="fa fa-times-circle"></i> Not Filled</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:center;">
                            <a href="daily_logbook" class="btn btn-xs btn-default" style="border-radius:6px;">
                                <i class="fa fa-external-link"></i> View Log
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
                <span><strong style="color:#1e293b;"><?= $total_teachers ?></strong> teachers scheduled on <?= $day_name ?></span>
                <span style="color:#059669;"><i class="fa fa-circle" style="font-size:8px;"></i> <strong><?= $complete_count ?></strong> Fully Filled</span>
                <span style="color:#d97706;"><i class="fa fa-circle" style="font-size:8px;"></i> <strong><?= $partial_count ?></strong> Partial</span>
                <span style="color:#dc2626;"><i class="fa fa-circle" style="font-size:8px;"></i> <strong><?= $not_filled_count ?></strong> Not Filled</span>
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
