<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

// Define branch ID dynamically from session
$branch_id = $_SESSION['branch_id'] ?? $branch_id;

// Fetch dynamic bottom stats
// 1. UTs Conducted
$ut_count_q = direct_sql("SELECT COUNT(*) AS total FROM ut_tests WHERE status='ACTIVE' AND branch_id='$branch_id' AND student_class IS NOT NULL AND student_class != ''");
$uts_conducted = ($ut_count_q['count'] > 0) ? $ut_count_q['data'][0]['total'] : 27;

// 2. Class details & Students
$emp_code = strtoupper($_SESSION['user_name'] ?? '');
$emp_q = direct_sql("SELECT * FROM employee WHERE emp_code='$emp_code' AND status='ACTIVE' LIMIT 1");
$emp = ($emp_q['count'] > 0) ? $emp_q['data'][0] : null;

$ct_class = '';
$ct_section = '';
if ($emp && !empty($emp['class_teacher'])) {
    $parts = explode('-', $emp['class_teacher']);
    $ct_class = trim($parts[0]);
    $ct_section = isset($parts[1]) ? trim($parts[1]) : '';
}

$std_count = 0;
if ($ct_class && $ct_section) {
    $std_q = direct_sql("SELECT COUNT(*) AS total FROM student WHERE student_class='$ct_class' AND student_section='$ct_section' AND status='ACTIVE' AND branch_id='$branch_id'");
    $std_count = ($std_q['count'] > 0) ? $std_q['data'][0]['total'] : 0;
}

if ($std_count == 0) {
    // Dynamic fallback: average active students per class in this branch
    $total_std_q = direct_sql("SELECT COUNT(*) AS total FROM student WHERE status='ACTIVE' AND branch_id='$branch_id'");
    $total_std = ($total_std_q['count'] > 0) ? $total_std_q['data'][0]['total'] : 0;
    
    $classes_q = direct_sql("SELECT COUNT(DISTINCT student_class, student_section) AS total_classes FROM student WHERE status='ACTIVE' AND branch_id='$branch_id'");
    $total_classes = ($classes_q['count'] > 0) ? $classes_q['data'][0]['total_classes'] : 0;
    
    $std_count = ($total_classes > 0) ? round($total_std / $total_classes) : 49;
}

// 3. Subject Teachers
$teacher_count_q = direct_sql("SELECT COUNT(*) AS total FROM employee WHERE emp_type='TEACHER' AND status='ACTIVE' AND branch_id='$branch_id'");
$subject_teachers = ($teacher_count_q['count'] > 0) ? $teacher_count_q['data'][0]['total'] : 15;

// 4. Classes, sections, subjects and teachers list for modals
$classes_list = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC")['data'] ?? [];
$sections_list = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC")['data'] ?? [];
$subjects_list = direct_sql("SELECT id, subject_name FROM subject WHERE branch_id='$branch_id' AND status='ACTIVE' ORDER BY subject_name ASC")['data'] ?? [];
$teachers_list = direct_sql("SELECT id, name FROM employee WHERE emp_type='TEACHER' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY name ASC")['data'] ?? [];
?>

<title>Reports Control Center | RMPS</title>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    .rep-container {
        font-family: 'Outfit', sans-serif;
        background-color: #f8fafc;
        padding: 24px;
        border-radius: 20px;
    }

    .rep-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 28px;
        background: #fff;
        padding: 20px 24px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01);
    }

    .rep-title {
        font-size: 24px;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rep-title i {
        color: #f59e0b;
    }

    .rep-date {
        font-size: 14px;
        color: #64748b;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Grid layout */
    .rep-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 24px;
        margin-bottom: 32px;
    }

    .rep-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        padding: 28px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        position: relative;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }

    .rep-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        transition: all 0.3s ease;
    }

    /* Card Themes */
    .theme-red::before { background: #ef4444; }
    .theme-green::before { background: #10b981; }
    .theme-blue::before { background: #3b82f6; }
    .theme-purple::before { background: #8b5cf6; }
    .theme-orange::before { background: #f59e0b; }
    .theme-teal::before { background: #0d9488; }

    .rep-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -8px rgba(0, 0, 0, 0.08);
        border-color: #cbd5e1;
    }

    .card-top {
        display: flex;
        gap: 16px;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        flex-shrink: 0;
    }

    .theme-red .icon-wrapper { background: #fef2f2; color: #ef4444; }
    .theme-green .icon-wrapper { background: #ecfdf5; color: #10b981; }
    .theme-blue .icon-wrapper { background: #eff6ff; color: #3b82f6; }
    .theme-purple .icon-wrapper { background: #f5f3ff; color: #8b5cf6; }
    .theme-orange .icon-wrapper { background: #fffbeb; color: #f59e0b; }
    .theme-teal .icon-wrapper { background: #f0fdfa; color: #0d9488; }

    .card-details h3 {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px 0;
    }

    .card-details p {
        font-size: 13.5px;
        color: #64748b;
        margin: 0;
        line-height: 1.5;
    }

    .rep-btn {
        width: 100%;
        padding: 11px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        border: 1px solid transparent;
        background: transparent;
    }

    .theme-red .rep-btn { border-color: #fca5a5; color: #dc2626; }
    .theme-red .rep-btn:hover { background: #ef4444; color: #fff; border-color: #ef4444; }

    .theme-green .rep-btn { border-color: #a7f3d0; color: #059669; }
    .theme-green .rep-btn:hover { background: #10b981; color: #fff; border-color: #10b981; }

    .theme-blue .rep-btn { border-color: #bfdbfe; color: #2563eb; }
    .theme-blue .rep-btn:hover { background: #3b82f6; color: #fff; border-color: #3b82f6; }

    .theme-purple .rep-btn { border-color: #ddd6fe; color: #7c3aed; }
    .theme-purple .rep-btn:hover { background: #8b5cf6; color: #fff; border-color: #8b5cf6; }

    .theme-orange .rep-btn { border-color: #fde68a; color: #d97706; }
    .theme-orange .rep-btn:hover { background: #f59e0b; color: #fff; border-color: #f59e0b; }

    .theme-teal .rep-btn { border-color: #99f6e4; color: #0d9488; }
    .theme-teal .rep-btn:hover { background: #0d9488; color: #fff; border-color: #0d9488; }

    /* Summary Section */
    .rep-summary-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .summary-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        text-align: center;
        transition: transform 0.2s ease;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .summary-val {
        font-size: 32px;
        font-weight: 800;
        color: #1e293b;
        margin-bottom: 4px;
        line-height: 1;
    }

    .summary-lbl {
        font-size: 11px;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Modal Styling */
    .rep-modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
    }

    .modal-content {
        background-color: #ffffff;
        margin: 8% auto;
        padding: 32px;
        border: 1px solid #e2e8f0;
        width: 90%;
        max-width: 500px;
        border-radius: 24px;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        position: relative;
        animation: modalFadeIn 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes modalFadeIn {
        from { transform: scale(0.9); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .modal-close {
        position: absolute;
        right: 24px;
        top: 24px;
        color: #64748b;
        font-size: 20px;
        font-weight: bold;
        cursor: pointer;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .modal-close:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .modal-header {
        margin-bottom: 24px;
    }

    .modal-header h2 {
        font-size: 20px;
        font-weight: 800;
        color: #0f172a;
        margin: 0 0 6px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .modal-header p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.02em;
    }

    .form-control {
        width: 100%;
        height: 44px;
        padding: 8px 16px;
        font-size: 14px;
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        background-color: #fff;
        color: #1e293b;
        font-family: 'Outfit', sans-serif;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    .modal-footer {
        margin-top: 28px;
        display: flex;
        gap: 12px;
    }

    .btn-submit {
        flex: 1;
        height: 46px;
        border-radius: 12px;
        background: #0f172a;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-submit:hover {
        background: #1e293b;
        transform: translateY(-1px);
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="rep-container">
            
            <!-- Header section -->
            <div class="rep-header">
                <div>
                    <h1 class="rep-title">
                        <i class="fa fa-file-text"></i> Reports Control Center
                    </h1>
                    <p style="font-size: 13.5px; color: #64748b; margin: 4px 0 0 0;">
                        Generate academic performance matrix and analytical registers for session 2026-27.
                    </p>
                </div>
                <div class="rep-date">
                    <i class="fa fa-calendar"></i>
                    <span><?= date('l, F j, Y') ?></span>
                </div>
            </div>

            <!-- Grid section -->
            <div class="rep-grid">
                
                <!-- Report Card 1 -->
                <div class="rep-card theme-red">
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <i class="fa fa-bar-chart"></i>
                        </div>
                        <div class="card-details">
                            <h3>Phase Performance Report</h3>
                            <p>Generate PDF with phase-wise student performance, charts, and teacher analysis.</p>
                        </div>
                    </div>
                    <button class="rep-btn" onclick="openModal('modal-phase')">Generate Report</button>
                </div>

                <!-- Report Card 2 -->
                <div class="rep-card theme-green">
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <i class="fa fa-file-excel-o"></i>
                        </div>
                        <div class="card-details">
                            <h3>Attendance Register</h3>
                            <p>Monthly attendance register in Excel format for administrative and audit records.</p>
                        </div>
                    </div>
                    <button class="rep-btn" onclick="openModal('modal-att')">Generate Report</button>
                </div>

                <!-- Report Card 3 -->
                <div class="rep-card theme-blue">
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <i class="fa fa-id-card-o"></i>
                        </div>
                        <div class="card-details">
                            <h3>Teacher Performace</h3>
                            <p>Individual teacher performance scorecards detailing all weighting and final scores.</p>
                        </div>
                    </div>
                    <button class="rep-btn" onclick="openModal('modal-teacher')">Generate Report</button>
                </div>

                <!-- Report Card 4 -->
                <div class="rep-card theme-purple">
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <i class="fa fa-line-chart"></i>
                        </div>
                        <div class="card-details">
                            <h3>Student Performance</h3>
                            <p>Comprehensive 27-UT student progress cards with full trend lines.</p>
                        </div>
                    </div>
                    <button class="rep-btn" onclick="openModal('modal-student')">Generate Report</button>
                </div>

                <!-- Report Card 5 -->
                <div class="rep-card theme-orange">
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <i class="fa fa-book"></i>
                        </div>
                        <div class="card-details">
                            <h3>Syllabus Status Report</h3>
                            <p>Subject-wise syllabus completion status tracking and gap-analysis matrices.</p>
                        </div>
                    </div>
                    <button class="rep-btn" onclick="openModal('modal-syllabus')">Generate Report</button>
                </div>

                <!-- Report Card 6 -->
                <div class="rep-card theme-teal">
                    <div class="card-top">
                        <div class="icon-wrapper">
                            <i class="fa fa-check-square-o"></i>
                        </div>
                        <div class="card-details">
                            <h3>Homework Log</h3>
                            <p>Daily class homework assignment logs exportable for any specified date range.</p>
                        </div>
                    </div>
                    <button class="rep-btn" onclick="openModal('modal-homework')">Generate Report</button>
                </div>

            </div>

            <!-- Bottom summary metric row -->
            <div class="rep-summary-row">
                <div class="summary-card">
                    <div class="summary-val"><?= $uts_conducted ?></div>
                    <div class="summary-lbl">UTs Conducted</div>
                </div>
                <div class="summary-card">
                    <div class="summary-val"><?= $std_count ?></div>
                    <div class="summary-lbl">Students Per Class</div>
                </div>
                <div class="summary-card">
                    <div class="summary-val"><?= $subject_teachers ?></div>
                    <div class="summary-lbl">Subject Teachers</div>
                </div>
                <div class="summary-card">
                    <div class="summary-val">249</div>
                    <div class="summary-lbl">Working Days</div>
                </div>
            </div>

        </div>
    </section>
</div>

<!-- ================= MODALS ================= -->

<!-- 1. Phase Performance Modal -->
<div id="modal-phase" class="rep-modal">
    <div class="modal-content" style="border-top: 5px solid #ef4444;">
        <span class="modal-close" onclick="closeModal('modal-phase')">&times;</span>
        <div class="modal-header">
            <h2><i class="fa fa-bar-chart" style="color: #ef4444;"></i> Phase Performance Report</h2>
            <p>Select filter parameters to compile the phase analytical matrix.</p>
        </div>
        <form action="student_performance" method="GET">
            <div class="form-group">
                <label>Class</label>
                <select name="student_class" class="form-control" required>
                    <option value="ALL">All Classes</option>
                    <?php foreach ($classes_list as $c): ?>
                        <option value="<?= htmlspecialchars($c['student_class']) ?>"><?= htmlspecialchars($c['student_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="student_section" class="form-control" required>
                    <option value="ALL">All Sections</option>
                    <?php foreach ($sections_list as $s): ?>
                        <option value="<?= htmlspecialchars($s['student_section']) ?>"><?= htmlspecialchars($s['student_section']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Phase</label>
                <select name="phase" class="form-control">
                    <option value="ALL">All Phases</option>
                    <option value="1">Phase 1 (Foundation)</option>
                    <option value="2">Phase 2 (Mid-Term)</option>
                    <option value="3">Phase 3 (Revision)</option>
                    <option value="4">Phase 4 (Finals)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-submit" style="background: #ef4444;">
                    <i class="fa fa-download"></i> Generate Report
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 2. Attendance Register Modal -->
<div id="modal-att" class="rep-modal">
    <div class="modal-content" style="border-top: 5px solid #10b981;">
        <span class="modal-close" onclick="closeModal('modal-att')">&times;</span>
        <div class="modal-header">
            <h2><i class="fa fa-file-excel-o" style="color: #10b981;"></i> Attendance Register</h2>
            <p>Generate monthly class-wide student attendance register sheet.</p>
        </div>
        <form action="attendance_report" method="GET">
            <div class="form-group">
                <label>Class</label>
                <select name="student_class" class="form-control" required>
                    <option value="ALL">All Classes</option>
                    <?php foreach ($classes_list as $c): ?>
                        <option value="<?= htmlspecialchars($c['student_class']) ?>"><?= htmlspecialchars($c['student_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="student_section" class="form-control" required>
                    <option value="ALL">All Sections</option>
                    <?php foreach ($sections_list as $s): ?>
                        <option value="<?= htmlspecialchars($s['student_section']) ?>"><?= htmlspecialchars($s['student_section']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Month</label>
                <select name="month" class="form-control" required>
                    <option value="<?= date('m') ?>">Current Month</option>
                    <option value="04">April</option>
                    <option value="05">May</option>
                    <option value="06">June</option>
                    <option value="07">July</option>
                    <option value="08">August</option>
                    <option value="09">September</option>
                    <option value="10">October</option>
                    <option value="11">November</option>
                    <option value="12">December</option>
                    <option value="01">January</option>
                    <option value="02">February</option>
                    <option value="03">March</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-submit" style="background: #10b981;">
                    <i class="fa fa-file-excel-o"></i> Export to Excel
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 3. Teacher Scorecard Modal -->
<div id="modal-teacher" class="rep-modal">
    <div class="modal-content" style="border-top: 5px solid #3b82f6;">
        <span class="modal-close" onclick="closeModal('modal-teacher')">&times;</span>
        <div class="modal-header">
            <h2><i class="fa fa-id-card-o" style="color: #3b82f6;"></i> Teacher Scorecard</h2>
            <p>Select dynamic parameters to view weighted teacher performance records.</p>
        </div>
        <form action="teacher_performance" method="GET">
            <div class="form-group">
                <label>Academic Period</label>
                <select name="session" class="form-control" required>
                    <option value="2026-27">Session 2026-27</option>
                </select>
            </div>
            <div class="form-group">
                <label>Class Filter (Optional)</label>
                <select name="filter_class" class="form-control">
                    <option value="ALL">All Classes</option>
                    <?php foreach ($classes_list as $c): ?>
                        <option value="<?= htmlspecialchars($c['student_class']) ?>"><?= htmlspecialchars($c['student_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-submit" style="background: #3b82f6;">
                    <i class="fa fa-id-card"></i> View Scorecard Matrix
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 4. Student Progress Card Modal -->
<div id="modal-student" class="rep-modal">
    <div class="modal-content" style="border-top: 5px solid #8b5cf6;">
        <span class="modal-close" onclick="closeModal('modal-student')">&times;</span>
        <div class="modal-header">
            <h2><i class="fa fa-line-chart" style="color: #8b5cf6;"></i> Student Progress Card</h2>
            <p>Generate detailed progress report cards for active students.</p>
        </div>
        <form action="student_performance" method="GET">
            <div class="form-group">
                <label>Class</label>
                <select id="spc_class" name="student_class" class="form-control" onchange="loadStudentsForSPC()" required>
                    <option value="ALL">All Classes</option>
                    <?php foreach ($classes_list as $c): ?>
                        <option value="<?= htmlspecialchars($c['student_class']) ?>"><?= htmlspecialchars($c['student_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select id="spc_section" name="student_section" class="form-control" onchange="loadStudentsForSPC()" required>
                    <option value="ALL">All Sections</option>
                    <?php foreach ($sections_list as $s): ?>
                        <option value="<?= htmlspecialchars($s['student_section']) ?>"><?= htmlspecialchars($s['student_section']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Student</label>
                <select id="spc_student" name="student_id" class="form-control" required>
                    <option value="ALL">All Students (Bulk Report)</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-submit" style="background: #8b5cf6;">
                    <i class="fa fa-line-chart"></i> Generate Progress Card
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 5. Syllabus Status Modal -->
<div id="modal-syllabus" class="rep-modal">
    <div class="modal-content" style="border-top: 5px solid #f59e0b;">
        <span class="modal-close" onclick="closeModal('modal-syllabus')">&times;</span>
        <div class="modal-header">
            <h2><i class="fa fa-book" style="color: #f59e0b;"></i> Syllabus Status Report</h2>
            <p>Select subject and class details to track progress timelines.</p>
        </div>
        <form action="syllabus_tracker" method="GET">
            <div class="form-group">
                <label>Class</label>
                <select name="student_class" class="form-control" required>
                    <option value="ALL">All Classes</option>
                    <?php foreach ($classes_list as $c): ?>
                        <option value="<?= htmlspecialchars($c['student_class']) ?>"><?= htmlspecialchars($c['student_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="student_section" class="form-control" required>
                    <option value="ALL">All Sections</option>
                    <?php foreach ($sections_list as $s): ?>
                        <option value="<?= htmlspecialchars($s['student_section']) ?>"><?= htmlspecialchars($s['student_section']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-submit" style="background: #f59e0b;">
                    <i class="fa fa-search"></i> View Syllabus Status
                </button>
            </div>
        </form>
    </div>
</div>

<!-- 6. Homework Log Modal -->
<div id="modal-homework" class="rep-modal">
    <div class="modal-content" style="border-top: 5px solid #0d9488;">
        <span class="modal-close" onclick="closeModal('modal-homework')">&times;</span>
        <div class="modal-header">
            <h2><i class="fa fa-check-square-o" style="color: #0d9488;"></i> Homework Log</h2>
            <p>Compile and download homework summaries within any custom range.</p>
        </div>
        <form action="homework_generator" method="GET">
            <div class="form-group">
                <label>Class</label>
                <select name="student_class" class="form-control" required>
                    <option value="ALL">All Classes</option>
                    <?php foreach ($classes_list as $c): ?>
                        <option value="<?= htmlspecialchars($c['student_class']) ?>"><?= htmlspecialchars($c['student_class']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Section</label>
                <select name="student_section" class="form-control" required>
                    <option value="ALL">All Sections</option>
                    <?php foreach ($sections_list as $s): ?>
                        <option value="<?= htmlspecialchars($s['student_section']) ?>"><?= htmlspecialchars($s['student_section']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="homework_date" value="<?= date('Y-m-d') ?>" class="form-control" required />
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn-submit" style="background: #0d9488;">
                    <i class="fa fa-file-text-o"></i> Fetch Homework Sheet
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = "block";
    }

    function closeModal(id) {
        document.getElementById(id).style.display = "none";
    }

    // Close modal when clicking outside content area
    window.onclick = function(event) {
        if (event.target.classList.contains('rep-modal')) {
            event.target.style.display = "none";
        }
    }

    // Dynamic Student Dropdown Loader for Student Progress Card
    function loadStudentsForSPC() {
        const cls = document.getElementById('spc_class').value;
        const sec = document.getElementById('spc_section').value;
        const studentDropdown = document.getElementById('spc_student');

        if (!cls || !sec) {
            studentDropdown.innerHTML = '<option value="ALL">All Students (Bulk Report)</option>';
            return;
        }

        // Fetch students dynamically using AJAX
        fetch(`system/master_process.php?task=get_students_by_class&class_name=${encodeURIComponent(cls)}&section_name=${encodeURIComponent(sec)}`)
            .then(res => res.json())
            .then(data => {
                let options = '<option value="ALL">All Students (Bulk Report)</option>';
                if (data && data.status === 'success' && data.data) {
                    data.data.forEach(std => {
                        options += `<option value="${std.id}">${std.roll_no ? '#' + std.roll_no + ' - ' : ''}${std.name}</option>`;
                    });
                }
                studentDropdown.innerHTML = options;
            })
            .catch(err => {
                console.error("Error loading students:", err);
                studentDropdown.innerHTML = '<option value="ALL">All Students (Bulk Report)</option>';
            });
    }
</script>

<?php require_once('system/footer2.php'); ?>
