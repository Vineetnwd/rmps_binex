<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $student_section = $log_date = null;
if (isset($_REQUEST['student_class']))
    $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section']))
    $student_section = $_REQUEST['student_section'];
if (isset($_REQUEST['log_date']))
    $log_date = $_REQUEST['log_date'];
else
    $log_date = date('Y-m-d');

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

$day_name = date('l', strtotime($log_date));

$teachers_list = [];
$teach_res = direct_sql("SELECT id, name FROM employee WHERE emp_type='TEACHER' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY name ASC");
if ($teach_res['status'] == 'success') {
    $teachers_list = $teach_res['data'] ?? [];
}
?>
<title> <?php echo $inst_name; ?> - Daily Log Book </title>
<style>
    .log-input {
        width: 100%;
        border: 1px solid #ccc;
        border-radius: 4px;
        padding: 5px;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-weight: bold;
        color: #fff;
    }

    .status-pending {
        background-color: #f39c12;
    }

    .status-done {
        background-color: #00a65a;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1> Daily Log Book <small>Module 3</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Daily Log Book</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Search Log Book</h3>
            </div>
            <div class="box-body">
                <div class="premium-card">
                    <form action="" method="get">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Class</label>
                                    <select class="form-control form-control-premium" name="student_class" required>
                                        <?php dropdown($class_list, $student_class); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Section</label>
                                    <select class="form-control form-control-premium" name="student_section" required>
                                        <?php dropdown($section_list, $student_section); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Date</label>
                                    <input type="date" name="log_date" class="form-control form-control-premium"
                                        value="<?= $log_date ?>" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-premium btn-premium-primary btn-block">
                                    <i class="fa fa-search"></i> Search Log Book
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <?php if ($student_class && $student_section): ?>
                    <?php
                    // Get Logbook
                    $log_sql = "SELECT * FROM logbook WHERE date='$log_date' AND student_class='$student_class' AND student_section='$student_section'";
                    $log_res = direct_sql($log_sql);
                    $log_id = 0;

                    if ($log_res['count'] > 0) {
                        $log_id = $log_res['data'][0]['id'];
                    } else {
                        // Create logbook entry
                        $insert = insert_data('logbook', [
                            'date' => $log_date,
                            'student_class' => $student_class,
                            'student_section' => $student_section,
                            'class_teacher_id' => $_SESSION['user_id']
                        ]);
                        $log_id = $insert['id'];
                    }

                    // Determine which periods to show (DYNAMIC LOGIC)
                    $active_periods = [];
                    $tt_sql = "SELECT DISTINCT period FROM timetable WHERE student_class='$student_class' AND student_section='$student_section' AND day='$day_name' AND status='ACTIVE' ORDER BY period ASC";
                    $tt_res = direct_sql($tt_sql);
                    if ($tt_res['count'] > 0) {
                        foreach ($tt_res['data'] as $ttr) { $active_periods[] = intval($ttr['period']); }
                    }
                    $lp_sql = "SELECT DISTINCT period_no FROM logbook_period WHERE logbook_id='$log_id' ORDER BY period_no ASC";
                    $lp_res = direct_sql($lp_sql);
                    if ($lp_res['count'] > 0) {
                        foreach ($lp_res['data'] as $lpr) { $active_periods[] = intval($lpr['period_no']); }
                    }
                    $active_periods = array_unique($active_periods);
                    sort($active_periods);
                    $total_active = count($active_periods);
                    ?>

                    <div class="box mb-4" style="background: #fff; padding: 30px; border-radius: 24px;">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <h2 class="m-0 font-weight-bold text-dark" style="font-size: 28px; letter-spacing: -0.5px;"><?= date('d M, Y', strtotime($log_date)) ?></h2>
                                <div class="d-flex align-items-center mt-3">
                                    <div class="mr-4">
                                        <small class="text-muted d-block font-weight-bold mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em;">Day</small>
                                        <span class="text-primary font-weight-700" style="font-size: 15px;"><i class="fa fa-calendar-check-o mr-1"></i> <?= $day_name ?></span>
                                    </div>
                                    <div style="border-left: 1px solid #eee; padding-left: 20px;">
                                        <small class="text-muted d-block font-weight-bold mb-1" style="font-size: 10px; text-transform: uppercase; letter-spacing: 0.1em;">Class & Section</small>
                                        <span class="text-dark font-weight-700" style="font-size: 15px;"><i class="fa fa-users mr-1"></i> <?= $student_class ?> - <?= $student_section ?></span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="px-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted font-weight-bold" style="font-size: 11px;">COMPLETION PROGRESS</small>
                                        <?php
                                        $done_count = get_all('logbook_period', 'id', ['logbook_id' => $log_id, 'status' => 'DONE'])['count'];
                                        $prog_perc = ($total_active > 0) ? ($done_count / $total_active) * 100 : 0;
                                        ?>
                                        <small class="text-primary font-weight-bold" style="font-size: 12px;"><?= $done_count ?> OF <?= $total_active ?></small>
                                    </div>
                                    <div class="progress progress-premium" style="height: 14px !important; margin: 0 !important; background: #f1f5f9 !important;">
                                        <div class="progress-bar progress-bar-premium" style="width: <?= $prog_perc ?>%; background: linear-gradient(90deg, #4f46e5, #6366f1) !important;"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-right d-flex flex-column" style="gap: 10px;">
                                <button class="btn btn-primary btn-block py-3" id="main-signoff-all" 
                                    style="border-radius: 16px; font-weight: 800; font-size: 13px; letter-spacing: 0.5px; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); margin-bottom: 0;">
                                    <i class="fa fa-check-circle mr-2"></i> SIGN OFF ALL
                                </button>
                                <button class="btn btn-success btn-block py-3" id="add-log-period-btn" 
                                    style="border-radius: 16px; font-weight: 800; font-size: 13px; letter-spacing: 0.5px; box-shadow: 0 10px 15px -3px rgba(16, 185, 129, 0.3); margin-top: 0; background: #10b981; border: none;">
                                    <i class="fa fa-plus-circle mr-2"></i> ADD PERIOD
                                </button>
                                <a href="print_daily_log.php?student_class=<?= urlencode($student_class) ?>&student_section=<?= urlencode($student_section) ?>&log_date=<?= urlencode($log_date) ?>" target="_blank" class="btn btn-warning btn-block py-3" 
                                   style="border-radius: 16px; font-weight: 800; font-size: 13px; letter-spacing: 0.5px; box-shadow: 0 10px 15px -3px rgba(245, 158, 11, 0.3); margin-top: 0; background: #f59e0b; border: none; color: #fff; text-align: center; text-decoration: none;">
                                    <i class="fa fa-print mr-2"></i> PRINT DAILY LOG
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="logbook-timeline">
                        <?php
                        $assigned_subjects = [];
                        if ($user_type == 'TEACHER') {
                            // Fetch from timetable (daily routine) — the teacher-filled source of truth
                            $sub_query = "SELECT DISTINCT subject FROM timetable 
                                          WHERE teacher_id = $teacher_id 
                                          AND student_class = '$student_class' 
                                          AND student_section = '$student_section' 
                                          AND status = 'ACTIVE'
                                          AND branch_id = $branch_id
                                          ORDER BY subject ASC";
                        } else {
                            $sub_query = "SELECT subject_name FROM subject WHERE branch_id = $branch_id ORDER BY subject_name ASC";
                        }
                        $sub_res = direct_sql($sub_query);
                        if ($sub_res['status'] == 'success') {
                            foreach ($sub_res['data'] as $sr) {
                                // timetable uses `subject`; subject table uses `subject_name`
                                $assigned_subjects[] = $sr['subject'] ?? $sr['subject_name'] ?? '';
                            }
                        }

                        $subject_badges = [
                            'Hindi' => 'badge-hindi',
                            'English' => 'badge-english',
                            'Mathematics' => 'badge-maths',
                            'Science' => 'badge-science',
                            'Social Science' => 'badge-social'
                        ];

                        if (empty($active_periods)) {
                            echo '<div class="premium-card text-center py-5">
                                    <i class="fa fa-calendar-o mb-3 text-muted" style="font-size: 48px;"></i>
                                    <h4 class="text-dark">No Schedule Found</h4>
                                    <p class="text-muted">There are no periods scheduled in the timetable for ' . $day_name . '.</p>
                                    <button class="btn btn-primary px-4" onclick="location.reload()"><i class="fa fa-refresh mr-2"></i> Refresh Schedule</button>
                                  </div>';
                        } else {
                            foreach ($active_periods as $p) {
                                $p_res = get_all('logbook_period', '*', ['logbook_id' => $log_id, 'period_no' => $p]);

                                $subject = "";
                                $teacher_id_period = 0;
                                $topics = "";
                                $homework = "";
                                $status = "PENDING";
                                $period_id = 0;
                                $is_from_timetable = false;

                                // Check timetable for this period (source of truth)
                                $tt_data = direct_sql("SELECT t.subject, t.teacher_id, e.name as teacher_name 
                                                       FROM timetable t 
                                                       LEFT JOIN employee e ON t.teacher_id = e.id 
                                                       WHERE t.student_class='$student_class' 
                                                       AND t.student_section='$student_section' 
                                                       AND t.day='$day_name' 
                                                       AND t.period='$p' 
                                                       AND t.status='ACTIVE' 
                                                       AND t.branch_id='$branch_id' LIMIT 1", 'get', false);
                                $tt_subject = '';
                                $tt_teacher_id = 0;
                                $tt_teacher_name = null;
                                if ($tt_data['count'] > 0) {
                                    $is_from_timetable = true;
                                    $tt_subject = $tt_data['data'][0]['subject'];
                                    $tt_teacher_id = $tt_data['data'][0]['teacher_id'] ?: 0;
                                    $tt_teacher_name = $tt_data['data'][0]['teacher_name'] ?: null;
                                }

                                if ($p_res['count'] > 0) {
                                    $pd = $p_res['data'][0];
                                    $period_id        = $pd['id'];
                                    $subject          = $pd['subject'];
                                    $teacher_id_period= $pd['teacher_id'];
                                    $topics           = $pd['topics'];
                                    $homework         = $pd['homework'];
                                    $status           = $pd['status'];

                                    // Override with timetable data if available
                                    if ($is_from_timetable) {
                                        if (empty($subject))           $subject           = $tt_subject;
                                        if (empty($teacher_id_period)) $teacher_id_period = $tt_teacher_id;
                                        if ($period_id && ($pd['subject'] != $subject || $pd['teacher_id'] != $teacher_id_period)) {
                                            update_data('logbook_period', ['subject' => $subject, 'teacher_id' => $teacher_id_period], $period_id);
                                        }
                                    }
                                } else {
                                    // Use timetable data for new period entry
                                    $subject           = $tt_subject;
                                    $teacher_id_period = $tt_teacher_id;
                                    $insert_p = insert_data('logbook_period', [
                                        'logbook_id'  => $log_id,
                                        'period_no'   => $p,
                                        'subject'     => $subject,
                                        'teacher_id'  => $teacher_id_period,
                                        'status'      => 'PENDING'
                                    ]);
                                    $period_id = $insert_p['id'];
                                }

                                // Resolve teacher name
                                $teacher_name = null;
                                
                                // Use timetable teacher name if available (from LEFT JOIN above)
                                if ($is_from_timetable && !empty($tt_teacher_name)) {
                                    $teacher_name = $tt_teacher_name;
                                }
                                
                                // Otherwise try stored teacher_id
                                if (empty($teacher_name) && !empty($teacher_id_period)) {
                                    $teacher_name = get_data('employee', $teacher_id_period, 'name')['data'] ?? null;
                                }
                                
                                // Final fallback: search timetable for ANY teacher who teaches this subject to this class
                                if (empty($teacher_name) && !empty($subject)) {
                                    $tt_any = direct_sql("SELECT e.name as teacher_name, t.teacher_id 
                                                          FROM timetable t 
                                                          JOIN employee e ON t.teacher_id = e.id 
                                                          WHERE t.subject='$subject' 
                                                          AND t.student_class='$student_class' 
                                                          AND t.student_section='$student_section' 
                                                          AND t.status='ACTIVE' 
                                                          AND t.teacher_id > 0
                                                          AND t.branch_id='$branch_id' LIMIT 1", 'get', false);
                                    if ($tt_any['count'] > 0) {
                                        $teacher_name = $tt_any['data'][0]['teacher_name'];
                                        $teacher_id_period = $tt_any['data'][0]['teacher_id'];
                                    }
                                }
                                
                                // Patch stored record if we resolved a new teacher
                                if (!empty($teacher_id_period) && $period_id) {
                                    $stored_tid = $p_res['data'][0]['teacher_id'] ?? 0;
                                    if ($stored_tid != $teacher_id_period) {
                                        update_data('logbook_period', ['teacher_id' => $teacher_id_period], $period_id);
                                    }
                                }
                                $badge_class = $subject_badges[$subject] ?? 'badge-default';
                                $status_pill_class = ($status == 'DONE') ? 'done' : 'pending';
                                $card_done_class = ($status == 'DONE') ? 'done' : '';
                                ?>
                                <div class="logbook-period-card <?= $card_done_class ?>" id="period_row_<?= $period_id ?>">
                                    <div class="period-number"><?= $p ?></div>
                                    <div class="period-content">
                                        <div class="period-header">
                                            <div class="d-flex align-items-center">
                                                <div class="expert-badge <?= $badge_class ?> mr-4 period-subject-badge">
                                                    <i class="fa fa-bookmark"></i>
                                                    <span class="subject-text"><?= $subject ?: 'Not Assigned' ?></span>
                                                </div>
                                                <div class="teacher-info">
                                                    <i class="fa fa-user-circle text-primary" style="font-size: 18px;"></i>
                                                    <span class="teacher-name-text"><?= $teacher_name ?: '<span class="text-muted">No Teacher Assigned</span>' ?></span>
                                                    <span class="badge badge-info ml-2" style="font-size: 11px; padding: 3px 8px; border-radius: 6px; background: #e0f2fe; color: #0369a1; font-weight: 600;">Period <?= $p ?></span>
                                                    <?php if ($is_from_timetable): ?>
                                                        <span class="badge ml-2" style="font-size: 10px; padding: 3px 8px; border-radius: 6px; background: #dcfce7; color: #16a34a; font-weight: 600;"><i class="fa fa-lock mr-1"></i>Scheduled</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <div class="status-pill <?= $status_pill_class ?> mr-3"><?= $status ?></div>
                                                <?php if ($status == 'PENDING'): ?>
                                                    <button class="btn btn-success signoff-btn" data-id="<?= $period_id ?>"
                                                        style="border-radius: 12px; font-weight: 700; padding: 8px 24px; font-size: 12px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);">
                                                        SIGN OFF
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="period-body">
                                            <div class="form-group mb-0">
                                                <label><i class="fa fa-align-left"></i> Topics Taught</label>
                                                <?php if ($status == 'PENDING'): ?>
                                                    <textarea class="form-control form-control-premium auto-save"
                                                        data-id="<?= $period_id ?>" data-field="topics" rows="3"
                                                        placeholder="Detailed record of what was covered..."><?= $topics ?></textarea>
                                                <?php else: ?>
                                                    <div class="p-3 bg-light rounded text-dark" style="border: 1px solid #f1f5f9; min-height: 80px;"><?= nl2br($topics) ?: '<em class="text-muted">No topics recorded</em>' ?></div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="form-group mb-0">
                                                <label><i class="fa fa-edit"></i> Homework Assigned</label>
                                                <?php if ($status == 'PENDING'): ?>
                                                    <textarea class="form-control form-control-premium auto-save"
                                                        data-id="<?= $period_id ?>" data-field="homework" rows="3"
                                                        placeholder="Specific homework or task details..."><?= $homework ?></textarea>
                                                <?php else: ?>
                                                    <div class="p-3 bg-light rounded text-dark" style="border: 1px solid #f1f5f9; min-height: 80px;"><?= nl2br($homework) ?: '<em class="text-muted">No homework assigned</em>' ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if ($status == 'PENDING'): ?>
                                            <?php if ($is_from_timetable): ?>
                                                <div class="quick-switch-area" style="background: #f0fdf4; border-top: 1px solid #bbf7d0;">
                                                    <span style="color: #16a34a;"><i class="fa fa-lock mr-1"></i> ASSIGNED VIA CLASS ROUTINE</span>
                                                    <span class="text-muted" style="font-size: 12px;">Subject & teacher are locked from timetable</span>
                                                </div>
                                            <?php else: ?>
                                                <div class="quick-switch-area">
                                                    <span><i class="fa fa-exchange"></i> SWITCH SUBJECT</span>
                                                    <select class="form-control-premium change-subject" data-id="<?= $period_id ?>"
                                                        data-field="subject"
                                                        style="width: 240px; font-size: 13px; height: 42px; padding: 0 15px;">
                                                        <option value="">Select Subject</option>
                                                        <?php foreach ($assigned_subjects as $asub): ?>
                                                            <option value="<?= $asub ?>" <?= ($asub == $subject) ? 'selected' : '' ?>><?= $asub ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- Add Period Modal -->
<div class="modal fade" id="addPeriodModal" tabindex="-1" role="dialog" aria-labelledby="addPeriodModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content" style="border-radius: 20px; overflow: hidden; border: none; box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
            <div class="modal-header bg-primary py-3" style="background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important; color: white;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 0.8; font-size: 28px;">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title font-weight-bold" id="addPeriodModalLabel" style="font-size: 16px;"><i class="fa fa-plus-circle mr-2"></i> Add Manual Logbook Period</h5>
            </div>
            <form id="addPeriodForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="logbook_id" value="<?= $log_id ?>">

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Period Number <span class="text-danger">*</span></label>
                        <select name="period_no" class="form-control" style="border-radius: 10px; padding: 10px; border: 1.5px solid #cbd5e1;" required>
                            <option value="">-- Select Period --</option>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <option value="<?= $i ?>">Period <?= $i ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Subject <span class="text-danger">*</span></label>
                        <select name="subject" class="form-control" style="border-radius: 10px; padding: 10px; border: 1.5px solid #cbd5e1;" required>
                            <option value="">-- Select Subject --</option>
                            <?php foreach ($assigned_subjects as $sub): ?>
                                <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Teacher <span class="text-danger">*</span></label>
                        <select name="teacher_id" class="form-control" style="border-radius: 10px; padding: 10px; border: 1.5px solid #cbd5e1;" required>
                            <option value="">-- Select Teacher --</option>
                            <?php foreach ($teachers_list as $teach): ?>
                                <option value="<?= $teach['id'] ?>" <?= (isset($teacher_id) && $teach['id'] == $teacher_id) ? 'selected' : '' ?>><?= htmlspecialchars($teach['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Topics Taught</label>
                        <textarea class="form-control" name="topics" rows="3" style="border-radius: 10px; padding: 10px; border: 1.5px solid #cbd5e1;" placeholder="What was covered in this class..."></textarea>
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-muted font-weight-bold" style="font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em;">Homework Assigned</label>
                        <textarea class="form-control" name="homework" rows="3" style="border-radius: 10px; padding: 10px; border: 1.5px solid #cbd5e1;" placeholder="Homework or task instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light d-flex justify-content-end" style="gap: 10px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary py-2 px-4" data-dismiss="modal" style="border-radius: 10px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-primary py-2 px-4" style="border-radius: 10px; font-weight: 700; background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%); border: none;">Add Entry</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    $(document).ready(function () {
        $('#add-log-period-btn').on('click', function () {
            $('#addPeriodModal').modal('show');
        });

        $('#addPeriodForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: 'system/master_process.php?task=add_logbook_period',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#addPeriodModal').modal('hide');
                        location.reload();
                    } else {
                        alert(res.msg || 'Error adding period');
                    }
                },
                error: function () {
                    alert('Connection error. Failed to add period.');
                }
            });
        });
        function updateProgress() {
            let total = 8;
            let done = $('.done').length;
            let percent = (done / total) * 100;
            $('.progress-bar-premium').css('width', percent + '%');
            $('.text-primary.font-weight-bold').first().text(done + '/8');
        }

        const subjectBadges = <?= json_encode($subject_badges) ?>;

        $('.auto-save, .change-subject').on('change', function () {
            let val = $(this).val();
            let field = $(this).data('field');
            let id = $(this).data('id');
            let $card = $(this).closest('.logbook-period-card');

            $.ajax({
                url: 'system/master_process.php?task=update_logbook',
                type: 'POST',
                data: { id: id, field: field, value: val },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        if (field === 'subject') {
                            // Update Subject Badge
                            let $badge = $card.find('.period-subject-badge');
                            $badge.find('.subject-text').text(val || 'No Subject');

                            // Clear old badge classes
                            Object.values(subjectBadges).forEach(cls => $badge.removeClass(cls));
                            $badge.removeClass('bg-gray');

                            // Add new badge class
                            if (subjectBadges[val]) {
                                $badge.addClass(subjectBadges[val]);
                            } else {
                                $badge.addClass('bg-gray');
                            }

                            // Update Teacher Name
                            $card.find('.teacher-name-text').html(res.teacher_name || '<span class="text-muted">No Teacher Assigned</span>');
                        }

                        // Show subtle save indicator
                        console.log('Saved successfully');
                    }
                }
            });
        });

        $('.signoff-btn').on('click', function () {
            let btn = $(this);
            let id = btn.data('id');

            if (confirm("Are you sure you want to sign off this period? This action is irreversible.")) {
                $.ajax({
                    url: 'system/master_process.php?task=signoff_logbook',
                    type: 'POST',
                    data: { id: id },
                    success: function (res) {
                        location.reload();
                    }
                });
            }
        });

        $('#main-signoff-all').on('click', function () {
            if (confirm("Sign off all periods for this day? This action is irreversible.")) {
                let ids = [];
                $('.signoff-btn').each(function () {
                    ids.push($(this).data('id'));
                });

                if (ids.length === 0) {
                    alert("No pending periods found.");
                    return;
                }

                let completed = 0;
                ids.forEach(function (id) {
                    $.ajax({
                        url: 'system/master_process.php?task=signoff_logbook',
                        type: 'POST',
                        data: { id: id },
                        success: function () {
                            completed++;
                            if (completed === ids.length) {
                                location.reload();
                            }
                        }
                    });
                });
            }
        });
    });
</script>