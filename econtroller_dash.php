<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
if ($user_type != 'ECONTROLLER' && $user_type != 'ADMIN' && $user_type != 'DEV') {
    echo "<script>window.location='dashboard'</script>";
}
?>
<?php
$today_day = date('l');
$first_name = $_SESSION['user_name'];
$branch_id = $_SESSION['branch_id'] ?? 1;

// Fetch some quick stats
$students = direct_sql("SELECT COUNT(*) as ct FROM student WHERE status='ACTIVE' AND branch_id='$branch_id'")['data'][0]['ct'] ?? 0;
$teachers = direct_sql("SELECT COUNT(*) as ct FROM employee e INNER JOIN user u ON LOWER(u.user_name) = LOWER(e.emp_code) WHERE u.user_type LIKE '%TEACHER%' AND e.status='ACTIVE' AND e.branch_id='$branch_id'")['data'][0]['ct'] ?? 0;
$exams = direct_sql("SELECT COUNT(*) as ct FROM exam_list WHERE branch_id='$branch_id'")['data'][0]['ct'] ?? 0;
$subjects = direct_sql("SELECT COUNT(DISTINCT subject_name) as ct FROM subject WHERE branch_id='$branch_id'")['data'][0]['ct'] ?? 0;

// Greeting
$hour = (int) date('H');
$greeting = $hour < 12 ? 'Good Morning' : ($hour < 17 ? 'Good Afternoon' : 'Good Evening');
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
    .sidebar { overflow-y: auto !important; }

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
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 26px;
        padding: 36px 40px;
        color: #fff;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 16px 48px rgba(15, 23, 42, 0.35);
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

    .stat-card.c-sky::before { background: var(--sky); }
    .stat-card.c-emerald::before { background: var(--emerald); }
    .stat-card.c-amber::before { background: var(--amber); }
    .stat-card.c-rose::before { background: var(--rose); }
    .stat-card.c-indigo::before { background: var(--indigo); }
    .stat-card.c-violet::before { background: var(--violet); }

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

    .stat-icon.sky { background: var(--sky-l); color: var(--sky); }
    .stat-icon.emerald { background: var(--emerald-l); color: var(--emerald); }
    .stat-icon.amber { background: var(--amber-l); color: var(--amber); }
    .stat-icon.rose { background: var(--rose-l); color: var(--rose); }
    .stat-icon.indigo { background: var(--indigo-l); color: var(--indigo); }
    .stat-icon.violet { background: var(--violet-l); color: var(--violet); }

    .stat-val { font-size: 32px; font-weight: 800; color: var(--text); line-height: 1; margin-bottom: 6px; }
    .stat-lbl { font-size: 13px; font-weight: 600; color: var(--muted); text-transform: uppercase; letter-spacing: 0.5px; }

    /* ── Quick Links ── */
    .section-title { font-size: 18px; font-weight: 700; color: var(--text); margin: 0 0 16px; display: flex; align-items: center; gap: 8px; }
    .action-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 28px; }
    .action-btn {
        background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 18px; display: flex; align-items: center; gap: 16px; color: var(--text); text-decoration: none; font-weight: 600; transition: all 0.2s ease; box-shadow: var(--shadow);
    }
    .action-btn:hover { background: var(--bg); border-color: #cbd5e1; transform: translateY(-2px); text-decoration: none; color: var(--indigo); box-shadow: var(--shadow-lg); }
    .action-btn i { font-size: 24px; color: var(--indigo); background: var(--indigo-l); width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px; }

    @media (max-width: 991px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } .action-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 575px) { .stat-grid { grid-template-columns: 1fr; } .action-grid { grid-template-columns: 1fr; } .hero { padding: 24px; } .hero-name { font-size: 24px; } }
</style>

<div class="content-wrapper td-wrap">
    
    <!-- Hero Section -->
    <div class="hero">
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="hero-greeting"><?= $greeting ?></div>
                <h1 class="hero-name">Welcome back, <?= htmlspecialchars($first_name) ?>! 👋</h1>
                <p class="hero-sub">Exam Controller Dashboard</p>
                <div class="hero-date" style="margin-top: 15px;"><i class="fa fa-calendar" style="margin-right: 5px;"></i> <?= date('l, d F Y') ?></div>
            </div>
            <div class="col-md-5 text-md-right mt-4 mt-md-0">
                <div class="hero-badge">
                    <i class="fa fa-shield"></i> ECONTROLLER
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <h3 class="section-title"><i class="fa fa-line-chart text-primary"></i> Examination Overview</h3>
    <div class="stat-grid">
        <div class="stat-card c-indigo">
            <div class="stat-icon indigo"><i class="fa fa-users"></i></div>
            <div class="stat-val"><?= number_format($students) ?></div>
            <div class="stat-lbl">Total Students</div>
        </div>
        <div class="stat-card c-emerald">
            <div class="stat-icon emerald"><i class="fa fa-graduation-cap"></i></div>
            <div class="stat-val"><?= number_format($teachers) ?></div>
            <div class="stat-lbl">Active Teachers</div>
        </div>
        <div class="stat-card c-sky">
            <div class="stat-icon sky"><i class="fa fa-book"></i></div>
            <div class="stat-val"><?= number_format($subjects) ?></div>
            <div class="stat-lbl">Subjects</div>
        </div>
        <div class="stat-card c-amber">
            <div class="stat-icon amber"><i class="fa fa-file-text"></i></div>
            <div class="stat-val"><?= number_format($exams) ?></div>
            <div class="stat-lbl">Exams Configured</div>
        </div>
    </div>

    <!-- Actions -->
    <h3 class="section-title"><i class="fa fa-bolt text-primary"></i> Quick Actions</h3>
    <div class="action-grid">
        <a href="exam_marks_entry" class="action-btn">
            <i class="fa fa-edit"></i>
            <div>
                <div style="font-size:15px; margin-bottom:2px;">Exam Marks Entry</div>
                <div style="font-size:12px; color:var(--muted); font-weight:400;">View or override marks</div>
            </div>
        </a>
        <a href="teacher_marks_report" class="action-btn">
            <i class="fa fa-check-square-o"></i>
            <div>
                <div style="font-size:15px; margin-bottom:2px;">Marks Compliance</div>
                <div style="font-size:12px; color:var(--muted); font-weight:400;">Track teacher progress</div>
            </div>
        </a>
        <a href="student_performance" class="action-btn">
            <i class="fa fa-bar-chart"></i>
            <div>
                <div style="font-size:15px; margin-bottom:2px;">Student Performance</div>
                <div style="font-size:12px; color:var(--muted); font-weight:400;">Analyze results</div>
            </div>
        </a>
        <a href="exam_master" class="action-btn">
            <i class="fa fa-cogs"></i>
            <div>
                <div style="font-size:15px; margin-bottom:2px;">Exam Master</div>
                <div style="font-size:12px; color:var(--muted); font-weight:400;">Manage exams</div>
            </div>
        </a>
        <a href="tabulation_class_report" class="action-btn">
            <i class="fa fa-table"></i>
            <div>
                <div style="font-size:15px; margin-bottom:2px;">Tabulation Report</div>
                <div style="font-size:12px; color:var(--muted); font-weight:400;">Class-wise tabulation</div>
            </div>
        </a>
        <a href="print_report_card" class="action-btn">
            <i class="fa fa-print"></i>
            <div>
                <div style="font-size:15px; margin-bottom:2px;">Print Report Cards</div>
                <div style="font-size:12px; color:var(--muted); font-weight:400;">Generate student cards</div>
            </div>
        </a>
    </div>

</div>

<?php require_once('system/footer2.php'); ?>
