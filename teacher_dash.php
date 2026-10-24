<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
if ($user_type == 'ACCOUNT') {
    echo "<script>window.location='acc_dash'</script>";
}
if ($user_type == 'STAFF') {
    echo "<script>window.location='studyplant'</script>";
}
?>
<?php
$emp = get_teacher_employee();
$teacher_id = $emp['id'] ?? null;
$today_day = date('l');
$first_name = !empty($emp['emp_name']) ? explode(' ', trim($emp['emp_name']))[0] : $_SESSION['user_name'];
$designation = $emp['designation'] ?? 'Teacher';

$schedule = direct_sql("SELECT * FROM timetable WHERE teacher_id='$teacher_id' AND day='$today_day' ORDER BY period ASC");
$total_periods = $schedule['count'] ?? 0;

$assigned = direct_sql("SELECT class_name, section_name FROM teacher_subject WHERE teacher_id='$teacher_id' AND status='ACTIVE' LIMIT 1");
$my_class = $assigned['data'][0]['class_name'] ?? null;
$my_section = $assigned['data'][0]['section_name'] ?? null;

$total_students = 0;
if ($my_class) {
    $sc = direct_sql("SELECT COUNT(*) as ct FROM student WHERE student_class='$my_class' AND student_section='$my_section' AND status='ACTIVE'");
    $total_students = $sc['data'][0]['ct'] ?? 0;
}

$pending_logs = direct_sql("SELECT COUNT(*) as ct FROM logbook_period WHERE teacher_id='$teacher_id' AND status='PENDING'");
$pending_count = $pending_logs['data'][0]['ct'] ?? 0;

// Greeting
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');

// Subjects this teacher teaches
$subjects_res = direct_sql("SELECT DISTINCT subject FROM timetable WHERE teacher_id='$teacher_id' AND status='ACTIVE' LIMIT 6");
$subject_count = $subjects_res['count'] ?? 0;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

    :root {
        --indigo: #4f46e5;
        --indigo-d: #3730a3;
        --indigo-l: #e0e7ff;
        --emerald: #059669;
        --emerald-l: #d1fae5;
        --amber: #d97706;
        --amber-l: #fef3c7;
        --rose: #e11d48;
        --rose-l: #ffe4e6;
        --sky: #0284c7;
        --sky-l: #e0f2fe;
        --violet: #7c3aed;
        --violet-l: #ede9fe;
        --bg: #f1f5f9;
        --card: #ffffff;
        --text: #0f172a;
        --muted: #64748b;
        --border: #e2e8f0;
        --radius: 18px;
        --shadow: 0 4px 24px rgba(0, 0, 0, 0.07);
        --shadow-lg: 0 12px 40px rgba(0, 0, 0, 0.12);
    }

    /* ── Fix AdminLTE double-scrollbar ── */
    html { scroll-behavior: smooth; overflow-y: auto !important; }
    body { overflow-y: auto !important; }
    .wrapper { overflow: visible !important; }
    .content-wrapper { overflow: visible !important; min-height: unset !important; }

    /* Custom slim scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }

    .td-wrap {
        font-family: 'Outfit', sans-serif;
        background: var(--bg);
        padding: 28px 24px;
    }

    /* ── Hero Banner ── */
    .hero {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #2563eb 100%);
        border-radius: 26px;
        padding: 36px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 16px 48px rgba(79, 70, 229, 0.35);
    }

    .hero::before {
        content: '';
        position: absolute;
        right: -80px;
        top: -80px;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.07);
        border-radius: 50%;
    }

    .hero::after {
        content: '';
        position: absolute;
        right: 60px;
        bottom: -60px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .hero-greeting {
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        opacity: 0.75;
        margin-bottom: 6px;
    }

    .hero-name {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 8px;
        line-height: 1.1;
    }

    .hero-sub {
        font-size: 15px;
        opacity: 0.8;
        margin: 0;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 50px;
        padding: 10px 20px;
        font-size: 14px;
        font-weight: 600;
    }

    .hero-date {
        font-size: 16px;
        font-weight: 700;
        opacity: 0.9;
        margin-top: 8px;
    }

    /* ── Stat Cards ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }

    .stat-card {
        background: var(--card);
        border-radius: var(--radius);
        padding: 22px 20px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        border-radius: 4px 0 0 4px;
    }

    .stat-card.c-sky::before {
        background: var(--sky);
    }

    .stat-card.c-emerald::before {
        background: var(--emerald);
    }

    .stat-card.c-amber::before {
        background: var(--amber);
    }

    .stat-card.c-rose::before {
        background: var(--rose);
    }

    .stat-card.c-indigo::before {
        background: var(--indigo);
    }

    .stat-card.c-violet::before {
        background: var(--violet);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-bottom: 14px;
    }

    .stat-icon.sky {
        background: var(--sky-l);
        color: var(--sky);
    }

    .stat-icon.emerald {
        background: var(--emerald-l);
        color: var(--emerald);
    }

    .stat-icon.amber {
        background: var(--amber-l);
        color: var(--amber);
    }

    .stat-icon.rose {
        background: var(--rose-l);
        color: var(--rose);
    }

    .stat-icon.indigo {
        background: var(--indigo-l);
        color: var(--indigo);
    }

    .stat-icon.violet {
        background: var(--violet-l);
        color: var(--violet);
    }

    .stat-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--text);
        line-height: 1;
        margin-bottom: 4px;
    }

    .stat-label {
        font-size: 12px;
        font-weight: 600;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* ── Section Cards ── */
    .section-card {
        background: var(--card);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        /*overflow: hidden;*/
        /*height: 100%;*/
    }

    .section-head {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .section-head h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
    }

    .section-head .see-all {
        font-size: 13px;
        font-weight: 600;
        color: var(--indigo);
        text-decoration: none;
    }

    .section-head .see-all:hover {
        text-decoration: underline;
    }

    .section-body {
        padding: 24px;
    }

    /* ── Schedule Timeline ── */
    .sched-empty {
        text-align: center;
        padding: 50px 20px;
        color: var(--muted);
    }

    .sched-empty i {
        font-size: 52px;
        opacity: 0.2;
        display: block;
        margin-bottom: 12px;
    }

    .sched-empty p {
        font-size: 15px;
        font-weight: 600;
        margin: 0;
    }

    .period-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .period-item {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 14px 0;
        border-bottom: 1px solid var(--border);
        transition: background 0.2s;
    }

    .period-item:last-child {
        border-bottom: none;
    }

    .period-num {
        width: 40px;
        height: 40px;
        min-width: 40px;
        background: var(--indigo-l);
        color: var(--indigo);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 800;
    }

    .period-info {
        flex: 1;
        min-width: 0;
    }

    .period-subject {
        font-size: 15px;
        font-weight: 700;
        color: var(--text);
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .period-class {
        font-size: 12px;
        color: var(--muted);
        font-weight: 500;
    }

    .period-badge {
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 11px;
        font-weight: 700;
        background: var(--emerald-l);
        color: var(--emerald);
        white-space: nowrap;
    }

    /* ── Quick Actions ── */
    .qa-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }

    .qa-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 20px 12px;
        border-radius: 16px;
        text-decoration: none;
        border: 1.5px solid var(--border);
        background: #fff;
        transition: all 0.25s ease;
        text-align: center;
    }

    .qa-btn:hover {
        border-color: var(--indigo);
        background: var(--indigo-l);
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(79, 70, 229, 0.15);
        text-decoration: none;
    }

    .qa-btn .qa-icon {
        width: 46px;
        height: 46px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        transition: transform 0.25s;
    }

    .qa-btn:hover .qa-icon {
        transform: scale(1.12);
    }

    .qa-btn span {
        font-size: 12px;
        font-weight: 700;
        color: var(--text);
        line-height: 1.2;
    }

    .qa-btn:hover span {
        color: var(--indigo);
    }

    /* ── Tip Box ── */
    .tip-box {
        margin-top: 18px;
        background: linear-gradient(135deg, var(--amber-l), #fffbeb);
        border-left: 4px solid var(--amber);
        border-radius: 14px;
        padding: 18px;
    }

    .tip-box h6 {
        font-weight: 700;
        font-size: 13px;
        color: var(--amber);
        margin-bottom: 6px;
    }

    .tip-box p {
        font-size: 13px;
        color: #78350f;
        margin: 0;
        line-height: 1.5;
    }

    /* ── Profile Card ── */
    .profile-card {
        background: linear-gradient(135deg, var(--indigo) 0%, var(--violet) 100%);
        border-radius: 20px;
        padding: 24px;
        color: #fff;
        margin-bottom: 18px;
        text-align: center;
    }

    .profile-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.2);
        border: 3px solid rgba(255, 255, 255, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        font-weight: 800;
        margin: 0 auto 12px;
    }

    .profile-card h5 {
        font-weight: 700;
        font-size: 16px;
        margin: 0 0 4px;
    }

    .profile-card small {
        opacity: 0.75;
        font-size: 13px;
    }

    @media (max-width: 768px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .qa-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .hero-name {
            font-size: 22px;
        }
    }
</style>

<div class="content-wrapper">
    <div class="td-wrap">

        <!-- Stat Cards -->
        <div class="stat-grid">
            <div class="stat-card c-sky">
                <div class="stat-icon sky"><i class="fa fa-users"></i></div>
                <div class="stat-value"><?= $total_students ?: '–' ?></div>
                <div class="stat-label">My Students<?= $my_class ? " · $my_class–$my_section" : '' ?></div>
            </div>
            <div class="stat-card c-emerald">
                <div class="stat-icon emerald"><i class="fa fa-calendar-check-o"></i></div>
                <div class="stat-value"><?= $total_periods ?></div>
                <div class="stat-label">Today's Classes</div>
            </div>
            <div class="stat-card c-amber">
                <div class="stat-icon amber"><i class="fa fa-book"></i></div>
                <div class="stat-value"><?= $pending_count ?></div>
                <div class="stat-label">Pending Logs</div>
            </div>
            <div class="stat-card c-violet">
                <div class="stat-icon violet"><i class="fa fa-graduation-cap"></i></div>
                <div class="stat-value"><?= $subject_count ?></div>
                <div class="stat-label">Subjects Taught</div>
            </div>
        </div>

        <div class="row">
            <!-- Left: Schedule -->
            <div class="col-md-8 mb-4">
                <div class="section-card">
                    <div class="section-head">
                        <h4><i class="fa fa-clock-o" style="color:var(--indigo);margin-right:8px;"></i> Today's Schedule
                            — <?= $today_day ?></h4>
                        <a href="daily_logbook" class="see-all">Open Logbook →</a>
                    </div>
                    <div class="section-body" style="padding: 0;">
                        <?php if ($total_periods > 0): ?>
                            <ul class="period-list" style="padding: 8px 24px;">
                                <?php foreach ($schedule['data'] as $p): ?>
                                    <li class="period-item">
                                        <div class="period-num"><?= $p['period'] ?></div>
                                        <div class="period-info">
                                            <p class="period-subject"><?= htmlspecialchars($p['subject']) ?></p>
                                            <span class="period-class">Class <?= $p['student_class'] ?> – Section
                                                <?= $p['student_section'] ?></span>
                                        </div>
                                        <span class="period-badge">Scheduled</span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <div class="sched-empty">
                                <i class="fa fa-calendar-o"></i>
                                <p>No classes scheduled for today</p>
                                <small style="color:var(--muted);font-size:13px;">Enjoy your free time or prepare for
                                    upcoming lessons!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Right: Profile + Quick Actions -->
            <div class="col-md-4 mb-4">
                <!-- Mini Profile -->
                <div class="profile-card">
                    <div class="profile-avatar"><?= strtoupper(substr($first_name, 0, 1)) ?></div>
                    <h5><?= htmlspecialchars($emp['emp_name'] ?? $first_name) ?></h5>
                    <small><?= htmlspecialchars($designation) ?> &nbsp;·&nbsp;
                        <?= htmlspecialchars($emp['emp_code'] ?? '') ?></small>
                </div>

                <!-- Quick Actions -->
                <div class="section-card">
                    <div class="section-head">
                        <h4><i class="fa fa-th" style="color:var(--indigo);margin-right:8px;"></i> Quick Actions</h4>
                    </div>
                    <div class="section-body">
                        <div class="qa-grid">
                            <?php if (($emp['designation'] ?? '') == 'NTT'): ?>
                                <a href="add_pre_marks" class="qa-btn">
                                    <div class="qa-icon" style="background:var(--violet-l);color:var(--violet);"><i
                                            class="fa fa-star"></i></div>
                                    <span>Montessori</span>
                                </a>
                            <?php else: ?>
                                <a href="ut_marks_entry" class="qa-btn">
                                    <div class="qa-icon" style="background:var(--indigo-l);color:var(--indigo);"><i
                                            class="fa fa-pencil-square-o"></i></div>
                                    <span>UT Marks</span>
                                </a>
                            <?php endif; ?>

                            <a href="teacher_st_att" class="qa-btn">
                                <div class="qa-icon" style="background:var(--emerald-l);color:var(--emerald);"><i
                                        class="fa fa-check-square-o"></i></div>
                                <span>Attendance</span>
                            </a>

                            <a href="daily_logbook" class="qa-btn">
                                <div class="qa-icon" style="background:var(--sky-l);color:var(--sky);"><i
                                        class="fa fa-book"></i></div>
                                <span>Log Book</span>
                            </a>

                            <a href="homework_generator" class="qa-btn">
                                <div class="qa-icon" style="background:var(--amber-l);color:var(--amber);"><i
                                        class="fa fa-send-o"></i></div>
                                <span>Homework</span>
                            </a>

                            <a href="leave_apply" class="qa-btn">
                                <div class="qa-icon" style="background:var(--rose-l);color:var(--rose);"><i
                                        class="fa fa-calendar-minus-o"></i></div>
                                <span>Apply Leave</span>
                            </a>

                            <a href="daily_routine" class="qa-btn">
                                <div class="qa-icon" style="background:var(--violet-l);color:var(--violet);"><i
                                        class="fa fa-calendar"></i></div>
                                <span>Routine</span>
                            </a>

                            <a href="syllabus_tracker" class="qa-btn">
                                <div class="qa-icon" style="background:var(--sky-l);color:var(--sky);"><i
                                        class="fa fa-list-alt"></i></div>
                                <span>Syllabus</span>
                            </a>

                            <a href="attendance_report" class="qa-btn">
                                <div class="qa-icon" style="background:var(--emerald-l);color:var(--emerald);"><i
                                        class="fa fa-bar-chart"></i></div>
                                <span>Reports</span>
                            </a>

                            <a href="teacher_profile" class="qa-btn">
                                <div class="qa-icon" style="background:var(--indigo-l);color:var(--indigo);"><i
                                        class="fa fa-user"></i></div>
                                <span>Profile</span>
                            </a>
                        </div>

                        <div class="tip-box">
                            <h6><i class="fa fa-lightbulb-o"></i> &nbsp;Daily Reminder</h6>
                            <p>Update your Daily Log Book before leaving to keep your teaching progress on track.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /.td-wrap -->
</div><!-- /.content-wrapper -->

<script>
// Live clock
function updateClock() {
    var now = new Date();
    var h = now.getHours();
    var m = now.getMinutes();
    var s = now.getSeconds();
    var ampm = h >= 12 ? 'PM' : 'AM';
    h = h % 12 || 12;
    var time = (h < 10 ? '0'+h : h) + ':' + (m < 10 ? '0'+m : m) + ':' + (s < 10 ? '0'+s : s) + ' ' + ampm;
    var el = document.getElementById('live-clock');
    if (el) el.textContent = time;
}
setInterval(updateClock, 1000);
updateClock();
</script>

<?php require_once('system/footer2.php'); ?>