<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$branch_id = $_SESSION['branch_id'] ?? 1;
$user_id = $_SESSION['user_id'] ?? 0;

$emp = null;
$teacher_id = null;
$is_class_teacher = false;
$ct_class = null;
$ct_section = null;

if ($user_type == 'TEACHER') {
    $emp = get_teacher_employee();
    $teacher_id = $emp['id'] ?? null;
    if (!empty($emp['class_teacher'])) {
        $is_class_teacher = true;
        $parts = explode('-', $emp['class_teacher']);
        $ct_class = trim($parts[0]);
        $ct_section = isset($parts[1]) ? trim($parts[1]) : '';
    }
}

// Determine active class and section to manage
$student_class = $ct_class;
$student_section = $ct_section;

if ($user_type == 'ADMIN' || $user_type == 'DEV' || $user_type == 'TEACHER') {
    if (isset($_GET['student_class'])) {
        $student_class = $_GET['student_class'];
    }
    if (isset($_GET['student_section'])) {
        $student_section = $_GET['student_section'];
    }
}

// Fetch lists for Admin filters
$all_classes = [];
$all_sections = [];

$class_res = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
$all_classes = array_column($class_res['data'] ?? [], 'student_class');

$section_res = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
$all_sections = array_column($section_res['data'] ?? [], 'student_section');


// Fetch weekly timetable for the selected class-section
$timetable_map = [];
$is_editable = false;

if (($user_type == 'ADMIN' || $user_type == 'DEV' || $user_type == 'TEACHER') && !empty($student_class) && !empty($student_section)) {
    $is_editable = true;
    $tt_res = direct_sql("SELECT * FROM timetable WHERE student_class='$student_class' AND student_section='$student_section' AND status='ACTIVE' AND branch_id='$branch_id'");
    if ($tt_res['status'] == 'success') {
        foreach ($tt_res['data'] as $row) {
            $timetable_map[$row['day']][$row['period']][] = $row;
        }
    }
}

// Fetch teacher's own teaching schedule if not managing a class routine
$my_schedule_map = [];
if ($user_type == 'TEACHER') {
    $my_tt = direct_sql("SELECT * FROM timetable WHERE teacher_id='$teacher_id' AND status='ACTIVE' AND branch_id='$branch_id'");
    if ($my_tt['status'] == 'success') {
        foreach ($my_tt['data'] as $row) {
            $my_schedule_map[$row['day']][$row['period']][] = $row;
        }
    }
}

// Fetch all subjects and teachers for the modals
$subjects_list = [];
$sub_res = direct_sql("SELECT DISTINCT subject_name FROM subject WHERE branch_id = '$branch_id' ORDER BY subject_name ASC");
if ($sub_res['status'] == 'success' && !empty($sub_res['data'])) {
    $subjects_list = array_column($sub_res['data'], 'subject_name');
}

// Fetch active teachers
$teachers_list = [];
$teach_res = direct_sql("SELECT id, name FROM employee WHERE emp_type='TEACHER' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY name ASC");
if ($teach_res['status'] == 'success') {
    $teachers_list = $teach_res['data'] ?? [];
}
?>

<title> <?php echo $inst_name; ?> - Weekly Routine Management </title>

<style>
    .routine-container {
        padding: 25px;
        background: #f8fafc;
        min-height: calc(100vh - 50px);
    }

    .banner-premium {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        border-radius: 20px;
        padding: 30px;
        color: white;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px -5px rgba(79, 70, 229, 0.2);
    }

    .table-routine {
        border-collapse: separate !important;
        border-spacing: 6px !important;
        background: transparent !important;
    }

    .table-routine th {
        background: #0f172a !important;
        color: #f8fafc !important;
        font-weight: 700 !important;
        text-transform: uppercase !important;
        font-size: 12px !important;
        padding: 15px !important;
        text-align: center !important;
        border: none !important;
        border-radius: 8px;
    }

    .day-header {
        background: #e2e8f0 !important;
        color: #1e293b !important;
        font-weight: 700 !important;
        font-size: 13px !important;
        text-align: left !important;
        vertical-align: middle !important;
        border: none !important;
        border-radius: 8px;
        padding: 15px !important;
        width: 120px;
    }

    .period-cell {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 12px !important;
        padding: 10px !important;
        min-width: 130px;
        height: 85px;
        vertical-align: middle !important;
        position: relative;
        transition: all 0.2s ease;
    }

    .period-cell:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #4f46e5 !important;
    }

    .scheduled-period {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        text-align: center;
    }

    .scheduled-period.my-class {
        background: #e0f2fe;
        border-radius: 8px;
        padding: 5px;
    }

    .subject-title {
        font-weight: 700;
        font-size: 13px;
        color: #4f46e5;
        margin-bottom: 4px;
        line-height: 1.2;
    }

    .teacher-subtitle,
    .class-subtitle {
        font-size: 11px;
        color: #64748b;
        font-weight: 500;
        line-height: 1.1;
    }

    .action-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 11px;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .period-cell:hover .action-overlay {
        opacity: 1;
    }

    .empty-period {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        height: 100%;
        color: #94a3b8;
        border: 2px dashed #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .empty-period:hover {
        background: #f8fafc;
        color: #4f46e5;
        border-color: #4f46e5;
    }

    .empty-period i {
        font-size: 20px;
        margin-bottom: 4px;
    }

    .empty-period span {
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
    }

    .free-period {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }

    .free-badge {
        background: #f1f5f9;
        color: #64748b;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
    }

    .form-control-routine {
        border-radius: 10px;
        padding: 10px 15px;
        border: 1.5px solid #cbd5e1;
        font-size: 14px;
        background-color: #f8fafc;
    }

    .form-control-routine:focus {
        border-color: #4f46e5;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
    }
</style>

<div class="content-wrapper">
    <div class="routine-container">

        <!-- Header / Banner -->
        <div class="banner-premium">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="font-weight-bold text-white mb-2" style="font-size: 30px;">
                        <i class="fa fa-calendar-plus-o mr-2"></i> Weekly Class Routine
                    </h1>
                    <p class="mb-0 text-white-50">
                        <?php if ($user_type == 'TEACHER' && !$is_class_teacher): ?>
                            Set your class-section assignment below or construct your weekly teaching schedule.
                        <?php else: ?>
                            Insert, update, and manage period schedules for class sections.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="col-md-4 text-right">
                    <span class="badge badge-premium bg-white text-primary px-3 py-2"
                        style="border-radius: 30px; font-size: 13px; font-weight: 700;">
                        <?= date('l, d M Y') ?>
                    </span>
                </div>
            </div>
        </div>

        <!-- Filter bar for Admin/Dev -->
        <?php if ($user_type == 'ADMIN' || $user_type == 'DEV'): ?>
            <div class="box p-4 mb-4">
                <form method="get" action="">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark mb-2">Select Class</label>
                                <select name="student_class" class="form-control form-control-routine" required>
                                    <option value="" disabled selected>-- Select Class --</option>
                                    <?php dropdown($all_classes, $student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="font-weight-bold text-dark mb-2">Select Section</label>
                                <select name="student_section" class="form-control form-control-routine" required>
                                    <option value="" disabled selected>-- Select Section --</option>
                                    <?php dropdown($all_sections, $student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-block py-2"
                                style="border-radius: 10px; font-weight: 700; height: 45px;">
                                <i class="fa fa-search mr-2"></i> Load Routine
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Class Teacher status banner with Edit & Delete options -->
        <?php if ($user_type == 'TEACHER' && $is_class_teacher): ?>
            <div class="box p-4 mb-4" style="border-left: 5px solid #4f46e5 !important;">
                <div class="d-flex align-items-center justify-content-between flex-wrap" style="gap: 15px;">
                    <div>
                        <h4 class="font-weight-bold text-dark m-0"><i class="fa fa-user-circle mr-2 text-primary"></i> Class
                            Teacher Assignment</h4>
                        <p class="text-muted mb-0 mt-1">You are assigned as the Class Teacher for Class <strong
                                class="text-primary"><?= $ct_class ?> - <?= $ct_section ?></strong>.</p>
                    </div>
                    <div class="d-flex align-items-center" style="gap: 10px;">
                        <button type="button" class="btn btn-outline-primary btn-sm px-3 py-2 btn-edit-ct"
                            style="border-radius: 8px; font-weight: 700; border: 1.5px solid #4f46e5; background: transparent; color: #4f46e5;">
                            <i class="fa fa-edit mr-1"></i> Edit Assignment
                        </button>
                        <button type="button" class="btn btn-outline-danger btn-sm px-3 py-2 btn-delete-ct"
                            style="border-radius: 8px; font-weight: 700; border: 1.5px solid #ef4444; background: transparent; color: #ef4444;"
                            data-teacher-id="<?= $teacher_id ?>">
                            <i class="fa fa-trash mr-1"></i> Delete Assignment
                        </button>
                    </div>
                </div>

                <!-- Hidden inline edit form (toggles on click) -->
                <div id="ctEditFormContainer" class="mt-3 p-3 bg-light rounded"
                    style="display: none; border: 1px dashed #4f46e5;">
                    <form id="ctEditForm" class="d-flex align-items-center flex-wrap" style="gap: 15px;">
                        <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <label class="font-weight-bold text-dark m-0">New Class:</label>
                            <select name="class_name" id="edit_ct_class" class="form-control form-control-routine"
                                style="height: 40px; width: 120px;" required>
                                <option value="">-- Class --</option>
                                <?php foreach ($all_classes as $c): ?>
                                    <option value="<?= htmlspecialchars($c) ?>" <?= $c == $ct_class ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-flex align-items-center" style="gap: 10px;">
                            <label class="font-weight-bold text-dark m-0">Section:</label>
                            <select name="section_name" id="edit_ct_section" class="form-control form-control-routine"
                                style="height: 40px; width: 100px;" required>
                                <option value="">-- Sec --</option>
                                <?php foreach ($all_sections as $s): ?>
                                    <option value="<?= htmlspecialchars($s) ?>" <?= $s == $ct_section ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="d-flex" style="gap: 10px;">
                            <button type="submit" class="btn btn-primary btn-sm px-3"
                                style="border-radius: 8px; font-weight: 700; height: 40px; background: #4f46e5; border: none; color: white;">
                                Update
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm px-3 btn-cancel-ct-edit"
                                style="border-radius: 8px; font-weight: 600; height: 40px;">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Assignment Option for General Teacher -->
        <?php if ($user_type == 'TEACHER' && !$is_class_teacher): ?>
            <div class="box p-4 mb-4" style="border-left: 5px solid #10b981 !important;">
                <div class="row align-items-center">
                    <div class="col-md-7">
                        <h4 class="font-weight-bold text-dark m-0"><i class="fa fa-user-plus mr-2 text-success"></i> Self
                            Class Teacher Assignment</h4>
                        <p class="text-muted mb-0 mt-1">You are not designated as a Class Teacher. Select a class-section
                            below to assign yourself as its Class Teacher and manage its entire routine.</p>
                    </div>
                    <div class="col-md-5">
                        <form id="selfAssignForm" class="d-flex align-items-center" style="gap: 10px;">
                            <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                            <select name="class_name" id="assign_class" class="form-control form-control-routine"
                                style="height: 45px;" required>
                                <option value="">-- Class --</option>
                                <?php foreach ($all_classes as $c): ?>
                                    <option value="<?= htmlspecialchars($c) ?>"><?= htmlspecialchars($c) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select name="section_name" id="assign_section" class="form-control form-control-routine"
                                style="height: 45px;" required>
                                <option value="">-- Sec --</option>
                                <?php foreach ($all_sections as $s): ?>
                                    <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-success py-2 px-3"
                                style="border-radius: 10px; font-weight: 700; height: 45px; white-space: nowrap; background: #10b981; border: none; color: white;">
                                <i class="fa fa-check mr-1"></i> Assign Me
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Routine Grid Card -->
        <?php if (($user_type == 'ADMIN' || $user_type == 'DEV') && !empty($student_class) && !empty($student_section)): ?>
            <div class="box p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold text-dark m-0">
                        Weekly Routine: <span
                            class="text-primary"><?= htmlspecialchars($student_class) ?>-<?= htmlspecialchars($student_section) ?></span>
                    </h3>
                    <?php if ($is_editable): ?>
                        <small class="text-muted"><i class="fa fa-info-circle mr-1"></i> Hover on cells to edit or clear
                            periods.</small>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table table-routine">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Day</th>
                                <?php for ($p = 1; $p <= 8; $p++): ?>
                                    <th>Period <?= $p ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach ($days as $day):
                                ?>
                                <tr>
                                    <td class="day-header">
                                        <strong><?= $day ?></strong>
                                    </td>
                                    <?php for ($p = 1; $p <= 8; $p++): ?>
                                        <td class="period-cell">
                                            <?php if (isset($timetable_map[$day][$p])): ?>
                                                <?php
                                                $grouped = [];
                                                foreach ($timetable_map[$day][$p] as $record) {
                                                    $subj = $record['subject'];
                                                    $subj_lower = strtolower($subj);
                                                    if (strpos($subj_lower, 'sanskrit') !== false || strpos($subj_lower, 'urdu') !== false) {
                                                        $subj_key = 'SANSKRIT/URDU';
                                                    } else {
                                                        $subj_key = $subj;
                                                    }
                                                    $grouped[$subj_key][] = $record;
                                                }
                                                ?>
                                                <?php foreach ($grouped as $subj_key => $records): ?>
                                                    <div class="scheduled-period mb-2 w-100 d-flex flex-column align-items-center">
                                                        <div class="subject-title"><?= htmlspecialchars($subj_key) ?></div>
                                                        <?php foreach ($records as $record): ?>
                                                            <?php
                                                            $t_id = $record['teacher_id'];
                                                            $t_name = get_data('employee', $t_id, 'name')['data'] ?? 'No Teacher';
                                                            $tt_id = $record['id'] ?? '';
                                                            ?>
                                                            <div class="teacher-subtitle position-relative w-100 p-1 mt-1" style="border-radius: 4px; background: rgba(0,0,0,0.02);">
                                                                <i class="fa fa-user-o mr-1"></i> <?= htmlspecialchars($t_name) ?>
                                                                
                                                                <?php if ($is_editable && ($user_type != 'TEACHER' || $t_id == $teacher_id)): ?>
                                                                    <div class="action-overlay" style="border-radius: 4px; padding: 2px;">
                                                                        <button class="btn btn-xs btn-primary edit-cell-btn" data-day="<?= $day ?>"
                                                                            data-period="<?= $p ?>" data-subject="<?= htmlspecialchars($record['subject']) ?>"
                                                                            data-teacher-id="<?= $t_id ?>"
                                                                            data-id="<?= htmlspecialchars($tt_id) ?>"
                                                                            data-class="<?= htmlspecialchars($student_class) ?>"
                                                                            data-section="<?= htmlspecialchars($student_section) ?>"
                                                                            title="Edit Period">
                                                                            <i class="fa fa-pencil"></i>
                                                                        </button>
                                                                        <button class="btn btn-xs btn-danger delete-cell-btn" data-day="<?= $day ?>"
                                                                            data-period="<?= $p ?>" data-class="<?= htmlspecialchars($student_class) ?>"
                                                                            data-id="<?= htmlspecialchars($tt_id) ?>"
                                                                            data-section="<?= htmlspecialchars($student_section) ?>"
                                                                            title="Clear Period">
                                                                            <i class="fa fa-trash"></i>
                                                                        </button>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                                <?php 
                                                $can_add_more = false;
                                                if (count($timetable_map[$day][$p]) == 1) {
                                                $first_subj = strtolower(trim($timetable_map[$day][$p][0]['subject']));
                                                $f_subj = $first_subj;
                                                $first_subj_name_q = mysqli_query($con, "SELECT subject_name FROM subject WHERE id = '" . mysqli_real_escape_string($con, $first_subj) . "' OR LOWER(subject_name) = '" . mysqli_real_escape_string($con, $first_subj) . "' LIMIT 1");
                                                if ($first_subj_name_q && mysqli_num_rows($first_subj_name_q) > 0) {
                                                    $sub_row = mysqli_fetch_assoc($first_subj_name_q);
                                                    $f_subj = strtolower(trim($sub_row['subject_name']));
                                                }
                                                if (strpos($f_subj, 'sanskrit') !== false || strpos($f_subj, 'urdu') !== false) {
                                                    $can_add_more = true;
                                                }
                                                // Hardcode a fallback just in case the ID or name somehow bypasses the query
                                                if ($first_subj == 'urdu' || $first_subj == 'sanskrit' || $first_subj == '14' || $first_subj == '15') { // assuming 14/15 might be IDs
                                                    $can_add_more = true;
                                                }
                                                }
                                                ?>
                                                <?php if ($can_add_more && $is_editable && $user_type != 'TEACHER'): ?>
                                                    <div class="empty-period add-cell-btn" data-day="<?= $day ?>"
                                                        data-period="<?= $p ?>" data-class="<?= htmlspecialchars($student_class) ?>"
                                                        data-section="<?= htmlspecialchars($student_section) ?>">
                                                        <i class="fa fa-plus-circle text-success"></i>
                                                        <span>Add 2nd Teacher</span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <?php if ($is_editable && $user_type != 'TEACHER'): ?>
                                                    <div class="empty-period add-cell-btn" data-day="<?= $day ?>"
                                                        data-period="<?= $p ?>" data-class="<?= htmlspecialchars($student_class) ?>"
                                                        data-section="<?= htmlspecialchars($student_section) ?>">
                                                        <i class="fa fa-plus-circle text-primary"></i>
                                                        <span>Add Subject</span>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="free-period">
                                                        <span class="free-badge">Free</span>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php elseif ($user_type == 'ADMIN' || $user_type == 'DEV'): ?>
            <!-- Admin load prompt -->
            <div class="text-center py-5 box bg-white">
                <i class="fa fa-calendar-o mb-3 text-muted" style="font-size: 60px; opacity: 0.3;"></i>
                <h4 class="text-dark font-weight-bold">Select Class & Section</h4>
                <p class="text-muted">Please select a class and section from the filters above to load the weekly routine.
                </p>
            </div>
        <?php endif; ?>

        <?php if ($user_type == 'TEACHER'): ?>
            <!-- Timetable display for teacher with Add/Edit self-schedule options -->
            <div class="box p-4" style="border-top: 5px solid #10b981 !important;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="font-weight-bold text-dark m-0"><i class="fa fa-calendar text-success mr-2"></i>Weekly
                        Routine: <span
                            class="text-primary"><?= htmlspecialchars($emp['name'] ?? 'Teacher') ?></span><?php if ($is_class_teacher && !empty($emp['class_teacher'])): ?><span
                                class="text-muted font-weight-normal" style="font-size: 16px;"> (Class Teacher:
                                <?= htmlspecialchars($emp['class_teacher']) ?>)</span><?php endif; ?></h3>
                    <small class="text-muted"><i class="fa fa-info-circle mr-1"></i> You can insert, edit, or clear your
                        classes directly in this grid.</small>
                </div>
                <div class="table-responsive">
                    <table class="table table-routine">
                        <thead>
                            <tr>
                                <th style="width: 120px;">Day</th>
                                <?php for ($p = 1; $p <= 8; $p++): ?>
                                    <th>Period <?= $p ?></th>
                                <?php endfor; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach ($days as $day):
                                ?>
                                <tr>
                                    <td class="day-header">
                                        <strong><?= $day ?></strong>
                                    </td>
                                    <?php for ($p = 1; $p <= 8; $p++): ?>
                                        <td class="period-cell">
                                            <?php if (isset($my_schedule_map[$day][$p])): ?>
                                                <?php foreach ($my_schedule_map[$day][$p] as $record): ?>
                                                    <?php
                                                    $subj = $record['subject'];
                                                    $cls = $record['student_class'];
                                                    $sec = $record['student_section'];
                                                    $tt_id = $record['id'] ?? '';
                                                    ?>
                                                    <div class="scheduled-period my-class mb-2">
                                                        <div class="subject-title"><?= htmlspecialchars($subj) ?></div>
                                                        <div class="class-subtitle">
                                                            <i class="fa fa-users mr-1"></i> Class
                                                            <?= htmlspecialchars($cls) ?>-<?= htmlspecialchars($sec) ?>
                                                        </div>

                                                        <div class="action-overlay">
                                                            <button class="btn btn-xs btn-primary edit-my-cell-btn" data-day="<?= $day ?>"
                                                                data-period="<?= $p ?>" data-subject="<?= htmlspecialchars($subj) ?>"
                                                                data-id="<?= htmlspecialchars($tt_id) ?>"
                                                                data-class="<?= htmlspecialchars($cls) ?>"
                                                                data-section="<?= htmlspecialchars($sec) ?>" title="Edit Period">
                                                                <i class="fa fa-pencil"></i>
                                                            </button>
                                                            <button class="btn btn-xs btn-danger delete-my-cell-btn" data-day="<?= $day ?>"
                                                                data-period="<?= $p ?>" data-class="<?= htmlspecialchars($cls) ?>"
                                                                data-id="<?= htmlspecialchars($tt_id) ?>"
                                                                data-section="<?= htmlspecialchars($sec) ?>" title="Clear Period">
                                                                <i class="fa fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="empty-period add-my-cell-btn" data-day="<?= $day ?>"
                                                    data-period="<?= $p ?>">
                                                    <i class="fa fa-plus-circle text-success"></i>
                                                    <span>Add Class</span>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endfor; ?>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>

    </div>
</div>

<!-- Add / Edit Period Modal (For Class Timetable) -->
<div class="modal fade" id="periodModal" tabindex="-1" role="dialog" aria-labelledby="periodModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"
            style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
            <div class="modal-header bg-primary py-3"
                style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important;">
                <h5 class="modal-title font-weight-bold text-white" id="periodModalLabel">Schedule Period</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="periodForm">
                <div class="modal-body p-4">
                    <input type="hidden" name="day" id="modal_day">
                    <input type="hidden" name="period" id="modal_period">
                    <input type="hidden" name="timetable_id" id="p_timetable_id">

                    <!-- Conflict Warning Container -->
                    <div id="period_conflict_alert" class="alert alert-danger"
                        style="display: none; border-radius: 12px; font-weight: 600; font-size: 13px; padding: 12px 15px;">
                        <i class="fa fa-exclamation-triangle mr-2"></i> <span class="alert-text"></span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Class <span
                                    class="text-danger">*</span></label>
                            <select name="student_class" id="modal_class" class="form-control form-control-routine"
                                required>
                                <option value="">-- Select --</option>
                                <?php foreach ($all_classes as $cls): ?>
                                    <option value="<?= htmlspecialchars($cls) ?>"><?= htmlspecialchars($cls) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Section <span
                                    class="text-danger">*</span></label>
                            <select name="student_section" id="modal_section" class="form-control form-control-routine"
                                required>
                                <option value="">-- Select --</option>
                                <?php foreach ($all_sections as $sec): ?>
                                    <option value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Day</label>
                            <div class="form-control" id="display_day"
                                style="background: #f1f5f9; border-radius: 10px; font-weight: 700; border: none;">Monday
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Period</label>
                            <div class="form-control" id="display_period"
                                style="background: #f1f5f9; border-radius: 10px; font-weight: 700; border: none;">Period
                                1</div>
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold"
                            style="font-size: 11px; text-transform: uppercase;">Subject <span
                                class="text-danger">*</span></label>
                        <select name="subject" id="modal_subject" class="form-control form-control-routine" required>
                            <option value="">-- Select Subject --</option>
                            <?php foreach ($subjects_list as $sub): ?>
                                <option value="<?= htmlspecialchars($sub) ?>"><?= htmlspecialchars($sub) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <?php if ($user_type == 'TEACHER'): ?>
                        <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                        <div class="form-group mb-0">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Teacher</label>
                            <div class="form-control"
                                style="background: #f1f5f9; border-radius: 10px; font-weight: 700; border: none; height: auto; padding: 10px 15px;">
                                <?= htmlspecialchars($emp['name'] ?? '') ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="form-group mb-0">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Teacher <span
                                    class="text-danger">*</span></label>
                            <select name="teacher_id" id="modal_teacher_id" class="form-control form-control-routine"
                                required>
                                <option value="">-- Select Teacher --</option>
                                <?php foreach ($teachers_list as $teach): ?>
                                    <option value="<?= $teach['id'] ?>"><?= htmlspecialchars($teach['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer p-3 bg-light d-flex justify-content-end"
                    style="gap: 10px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary py-2 px-4" data-dismiss="modal"
                        style="border-radius: 10px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-primary py-2 px-4"
                        style="border-radius: 10px; font-weight: 700;">Save Period</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add / Edit Self Period Modal (For General Teacher schedule builder) -->
<div class="modal fade" id="myPeriodModal" tabindex="-1" role="dialog" aria-labelledby="myPeriodModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content"
            style="border-radius: 16px; overflow: hidden; border: none; box-shadow: 0 15px 30px rgba(0,0,0,0.1);">
            <div class="modal-header bg-success py-3"
                style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;">
                <h5 class="modal-title font-weight-bold text-white" id="myPeriodModalLabel">Schedule Your Class</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close"
                    style="opacity: 0.8;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="myPeriodForm">
                <div class="modal-body p-4">
                    <!-- Hidden inputs -->
                    <input type="hidden" name="teacher_id" value="<?= $teacher_id ?>">
                    <input type="hidden" name="day" id="my_modal_day">
                    <input type="hidden" name="period" id="my_modal_period">
                    <input type="hidden" name="timetable_id" id="my_p_timetable_id">

                    <!-- Conflict Warning Container -->
                    <div id="my_period_conflict_alert" class="alert alert-danger"
                        style="display: none; border-radius: 12px; font-weight: 600; font-size: 13px; padding: 12px 15px;">
                        <i class="fa fa-exclamation-triangle mr-2"></i> <span class="alert-text"></span>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Day</label>
                            <div class="form-control" id="my_display_day"
                                style="background: #f1f5f9; border-radius: 10px; font-weight: 700; border: none;">Monday
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Period</label>
                            <div class="form-control" id="my_display_period"
                                style="background: #f1f5f9; border-radius: 10px; font-weight: 700; border: none;">Period
                                1</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Class <span
                                    class="text-danger">*</span></label>
                            <select name="student_class" id="my_modal_class" class="form-control form-control-routine"
                                required>
                                <option value="">-- Select --</option>
                                <?php foreach ($all_classes as $cls): ?>
                                    <option value="<?= htmlspecialchars($cls) ?>"><?= htmlspecialchars($cls) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="text-muted font-weight-bold"
                                style="font-size: 11px; text-transform: uppercase;">Section <span
                                    class="text-danger">*</span></label>
                            <select name="student_section" id="my_modal_section"
                                class="form-control form-control-routine" required>
                                <option value="">-- Select --</option>
                                <?php foreach ($all_sections as $sec): ?>
                                    <option value="<?= htmlspecialchars($sec) ?>"><?= htmlspecialchars($sec) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group mb-0">
                        <label class="text-muted font-weight-bold"
                            style="font-size: 11px; text-transform: uppercase;">Subject <span
                                class="text-danger">*</span></label>
                        <select name="subject" id="my_modal_subject" class="form-control form-control-routine" required>
                            <option value="">-- Select Subject --</option>
                            <?php
                            $all_sub_res = direct_sql("SELECT DISTINCT subject_name FROM subject WHERE branch_id = '$branch_id' ORDER BY subject_name ASC");
                            foreach ($all_sub_res['data'] ?? [] as $sub):
                                ?>
                                <option value="<?= htmlspecialchars($sub['subject_name']) ?>">
                                    <?= htmlspecialchars($sub['subject_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer p-3 bg-light d-flex justify-content-end"
                    style="gap: 10px; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-secondary py-2 px-4" data-dismiss="modal"
                        style="border-radius: 10px; font-weight: 600;">Cancel</button>
                    <button type="submit" class="btn btn-success py-2 px-4"
                        style="border-radius: 10px; font-weight: 700; background: #10b981; border: none; color: white;">Save
                        Period</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    $(document).ready(function () {

        // Self-Assign Class Teacher Submission
        $('#selfAssignForm').on('submit', function (e) {
            e.preventDefault();
            let teacherId = $('input[name="teacher_id"]').val();
            let className = $('#assign_class').val();
            let sectionName = $('#assign_section').val();
            let classTeacherString = className + '-' + sectionName;

            $.ajax({
                url: 'system/master_process?task=save_class_teacher',
                type: 'POST',
                data: {
                    teacher_id: teacherId,
                    class_teacher: classTeacherString
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alert('You have assigned yourself as the Class Teacher for Class ' + classTeacherString + '!');
                        location.reload();
                    } else {
                        alert('Error assigning class teacher: ' + (res.msg || 'Unknown error.'));
                    }
                },
                error: function () {
                    alert('Connection error. Failed to save assignment.');
                }
            });
        });

        // Toggle Edit Class Teacher Form
        $('.btn-edit-ct').on('click', function () {
            $('#ctEditFormContainer').slideToggle();
        });

        $('.btn-cancel-ct-edit').on('click', function () {
            $('#ctEditFormContainer').slideUp();
        });

        // Edit Class Teacher Form Submission
        $('#ctEditForm').on('submit', function (e) {
            e.preventDefault();
            let teacherId = $(this).find('input[name="teacher_id"]').val();
            let className = $('#edit_ct_class').val();
            let sectionName = $('#edit_ct_section').val();
            let classTeacherString = className + '-' + sectionName;

            $.ajax({
                url: 'system/master_process?task=save_class_teacher',
                type: 'POST',
                data: {
                    teacher_id: teacherId,
                    class_teacher: classTeacherString
                },
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        alert('Your Class Teacher assignment has been updated successfully to Class ' + classTeacherString + '!');
                        location.reload();
                    } else {
                        alert('Error updating class teacher: ' + (res.msg || 'Unknown error.'));
                    }
                },
                error: function () {
                    alert('Connection error. Failed to update assignment.');
                }
            });
        });

        // Delete Class Teacher Assignment
        $('.btn-delete-ct').on('click', function () {
            if (confirm('Are you sure you want to delete your Class Teacher assignment? This will clear your Class Teacher role.')) {
                let teacherId = $(this).data('teacher-id');
                $.ajax({
                    url: 'system/master_process?task=save_class_teacher',
                    type: 'POST',
                    data: {
                        teacher_id: teacherId,
                        class_teacher: ''
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            alert('Your Class Teacher assignment has been deleted.');
                            location.reload();
                        } else {
                            alert('Error deleting class teacher assignment: ' + (res.msg || 'Unknown error.'));
                        }
                    },
                    error: function () {
                        alert('Connection error. Failed to delete assignment.');
                    }
                });
            }
        });

        // Timetable slot conflict checking
        let bookedClasses = [];
        let ignoreClass = '';
        let ignoreSection = '';
        let ignoreTeacher = '';

        function fetchBookedClasses(day, period, alertEl, submitBtnEl, callback) {
            $.ajax({
                url: 'system/master_process?task=get_booked_class_sections',
                type: 'POST',
                data: {
                    day: day,
                    period: period
                },
                dataType: 'json',
                success: function (res) {
                    bookedClasses = res;
                    if (callback) {
                        callback();
                    }
                },
                error: function () {
                    bookedClasses = [];
                    if (callback) {
                        callback();
                    }
                }
            });
        }

        function checkConflict(classVal, sectionVal, teacherVal, subjectVal, alertEl, submitBtnEl) {
            let classConflict = null;
            let teacherConflict = null;

            if (classVal && sectionVal) {
                // Only look for class/section conflict if it's not the one we are editing
                if (!(classVal === ignoreClass && sectionVal === ignoreSection)) {
                    let isOptionalLang = (subjectVal && (subjectVal.toLowerCase().includes('sanskrit') || subjectVal.toLowerCase().includes('urdu')));
                    let classConflicts = bookedClasses.filter(function (item) {
                        return item.class.toLowerCase() === classVal.toLowerCase() &&
                            item.section.toLowerCase() === sectionVal.toLowerCase();
                    });
                    
                    if (classConflicts.length > 0) {
                        if (isOptionalLang && classConflicts.length === 1) {
                            let existingSubject = classConflicts[0].subject.toLowerCase();
                            let existingIsOptionalLang = (existingSubject.includes('sanskrit') || existingSubject.includes('urdu'));
                            if (existingIsOptionalLang) {
                                if (teacherVal && classConflicts[0].teacher_id == teacherVal) {
                                    classConflict = classConflicts[0];
                                }
                            } else {
                                classConflict = classConflicts[0];
                            }
                        } else {
                            classConflict = classConflicts[0];
                        }
                    }
                }
            }

            if (teacherVal) {
                // Only look for teacher conflict if it's not the teacher we are editing
                if (teacherVal !== ignoreTeacher) {
                    teacherConflict = bookedClasses.find(function (item) {
                        return item.teacher_id == teacherVal;
                    });
                }
            }

            if (classConflict) {
                $(alertEl).removeClass('alert-warning').addClass('alert-danger')
                    .html('<i class="fa fa-exclamation-triangle mr-2"></i> Class <strong>' + classVal + '-' + sectionVal + '</strong> is already scheduled for <strong>' + classConflict.subject + '</strong> with <strong>' + classConflict.teacher + '</strong>.')
                    .show();
                $(submitBtnEl).prop('disabled', true);
            } else if (teacherConflict) {
                $(alertEl).removeClass('alert-warning').addClass('alert-danger')
                    .html('<i class="fa fa-exclamation-triangle mr-2"></i> Teacher <strong>' + teacherConflict.teacher + '</strong> is already scheduled to teach Class <strong>' + teacherConflict.class + '-' + teacherConflict.section + '</strong> (' + teacherConflict.subject + ').')
                    .show();
                $(submitBtnEl).prop('disabled', true);
            } else {
                $(alertEl).hide().html('');
                $(submitBtnEl).prop('disabled', false);
            }
        }

        // Live inputs change handlers
        $('#modal_class, #modal_section, #modal_teacher_id, #modal_subject').on('change', function () {
            let classVal = $('#modal_class').val();
            let sectionVal = $('#modal_section').val();
            let teacherVal = $('#modal_teacher_id').val();
            let subjectVal = $('#modal_subject').val();
            checkConflict(classVal, sectionVal, teacherVal, subjectVal, '#period_conflict_alert', '#periodForm button[type="submit"]');
        });

        $('#my_modal_class, #my_modal_section, #my_modal_subject').on('change', function () {
            let classVal = $('#my_modal_class').val();
            let sectionVal = $('#my_modal_section').val();
            let teacherVal = '<?= $teacher_id ?? "" ?>';
            let subjectVal = $('#my_modal_subject').val();
            checkConflict(classVal, sectionVal, teacherVal, subjectVal, '#my_period_conflict_alert', '#myPeriodForm button[type="submit"]');
        });

        // Add Period trigger (Class timetable)
        $('.add-cell-btn').on('click', function () {
            let day = $(this).data('day');
            let period = $(this).data('period');
            let clickedClass = $(this).data('class') || '<?= htmlspecialchars($student_class ?? '') ?>';
            let clickedSection = $(this).data('section') || '<?= htmlspecialchars($student_section ?? '') ?>';

            $('#modal_day').val(day);
            $('#modal_period').val(period);
            $('#p_timetable_id').val(''); // Clear ID for new assignment
            $('#display_day').text(day);
            $('#display_period').text('Period ' + period);

            $('#modal_class').val(clickedClass);
            $('#modal_section').val(clickedSection);

            $('#modal_subject').val('');
            $('#modal_teacher_id').val('');

            $('#periodModalLabel').text('Schedule Routine Period');

            // Initialize conflict check states
            ignoreClass = '';
            ignoreSection = '';
            ignoreTeacher = '';

            $('#period_conflict_alert').hide().html('');
            $('#periodForm button[type="submit"]').prop('disabled', false);

            $('#periodModal').modal('show');

            fetchBookedClasses(day, period, '#period_conflict_alert', '#periodForm button[type="submit"]', function () {
                let classVal = $('#modal_class').val();
                let sectionVal = $('#modal_section').val();
                let teacherVal = $('#modal_teacher_id').val();
                let subjectVal = $('#modal_subject').val();
                checkConflict(classVal, sectionVal, teacherVal, subjectVal, '#period_conflict_alert', '#periodForm button[type="submit"]');
            });
        });

        // Edit Period trigger (Class timetable)
        $('.edit-cell-btn').on('click', function (e) {
            e.stopPropagation();
            let day = $(this).data('day');
            let period = $(this).data('period');
            let subject = $(this).data('subject');
            let teacherId = $(this).data('teacher-id');
            let timetableId = $(this).data('id') || '';
            let clickedClass = $(this).data('class') || '<?= htmlspecialchars($student_class ?? '') ?>';
            let clickedSection = $(this).data('section') || '<?= htmlspecialchars($student_section ?? '') ?>';

            $('#modal_day').val(day);
            $('#modal_period').val(period);
            $('#p_timetable_id').val(timetableId);
            $('#display_day').text(day);
            $('#display_period').text('Period ' + period);

            $('#modal_class').val(clickedClass);
            $('#modal_section').val(clickedSection);

            $('#modal_subject').val(subject);
            $('#modal_teacher_id').val(teacherId);

            $('#periodModalLabel').text('Edit Routine Period');

            // Initialize conflict check states (ignoring current record)
            ignoreClass = clickedClass;
            ignoreSection = clickedSection;
            ignoreTeacher = teacherId;

            $('#period_conflict_alert').hide().html('');
            $('#periodForm button[type="submit"]').prop('disabled', false);

            $('#periodModal').modal('show');

            fetchBookedClasses(day, period, '#period_conflict_alert', '#periodForm button[type="submit"]', function () {
                checkConflict(clickedClass, clickedSection, teacherId, subject, '#period_conflict_alert', '#periodForm button[type="submit"]');
            });
        });

        // Submit Period Form (Class timetable)
        $('#periodForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: 'system/master_process?task=update_timetable',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#periodModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error saving timetable: ' + (res.msg || 'Unknown error occurred.'));
                    }
                },
                error: function () {
                    alert('Connection error. Failed to save period.');
                }
            });
        });

        // Delete Period (Class timetable)
        $('.delete-cell-btn').on('click', function (e) {
            e.stopPropagation();
            let day = $(this).data('day');
            let period = $(this).data('period');
            let studentClass = $(this).data('class');
            let studentSection = $(this).data('section');
            let timetableId = $(this).data('id') || '';

            if (confirm('Are you sure you want to clear Period ' + period + ' for ' + day + '?')) {
                $.ajax({
                    url: 'system/master_process?task=clear_timetable_period',
                    type: 'POST',
                    data: {
                        student_class: studentClass,
                        student_section: studentSection,
                        day: day,
                        period: period,
                        timetable_id: timetableId
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            location.reload();
                        } else {
                            alert('Error clearing period: ' + (res.msg || 'Unknown error.'));
                        }
                    },
                    error: function () {
                        alert('Connection error. Failed to clear period.');
                    }
                });
            }
        });

        // Add Period trigger (Personal teacher timetable)
        $('.add-my-cell-btn').on('click', function () {
            let day = $(this).data('day');
            let period = $(this).data('period');

            $('#my_modal_day').val(day);
            $('#my_modal_period').val(period);
            $('#my_p_timetable_id').val(''); // Clear ID for new assignment
            $('#my_display_day').text(day);
            $('#my_display_period').text('Period ' + period);

            $('#my_modal_class').val('');
            $('#my_modal_section').val('');
            $('#my_modal_subject').val('');

            $('#myPeriodModalLabel').text('Schedule Your Class');

            // Initialize conflict check states
            ignoreClass = '';
            ignoreSection = '';
            ignoreTeacher = '';

            $('#my_period_conflict_alert').hide().html('');
            $('#myPeriodForm button[type="submit"]').prop('disabled', false);

            $('#myPeriodModal').modal('show');

            fetchBookedClasses(day, period, '#my_period_conflict_alert', '#myPeriodForm button[type="submit"]', function () {
                let classVal = $('#my_modal_class').val();
                let sectionVal = $('#my_modal_section').val();
                let teacherVal = '<?= $teacher_id ?? "" ?>';
                let subjectVal = $('#my_modal_subject').val();
                checkConflict(classVal, sectionVal, teacherVal, subjectVal, '#my_period_conflict_alert', '#myPeriodForm button[type="submit"]');
            });
        });

        // Edit Period trigger (Personal teacher timetable)
        $('.edit-my-cell-btn').on('click', function (e) {
            e.stopPropagation();
            let day = $(this).data('day');
            let period = $(this).data('period');
            let subject = $(this).data('subject');
            let timetableId = $(this).data('id') || '';
            let studentClass = $(this).data('class');
            let studentSection = $(this).data('section');

            $('#my_modal_day').val(day);
            $('#my_modal_period').val(period);
            $('#my_p_timetable_id').val(timetableId);
            $('#my_display_day').text(day);
            $('#my_display_period').text('Period ' + period);

            $('#my_modal_class').val(studentClass);
            $('#my_modal_section').val(studentSection);
            $('#my_modal_subject').val(subject);

            $('#myPeriodModalLabel').text('Edit Your Scheduled Class');

            // Initialize conflict check states (ignoring current record)
            ignoreClass = studentClass;
            ignoreSection = studentSection;
            ignoreTeacher = '<?= $teacher_id ?? "" ?>';

            $('#my_period_conflict_alert').hide().html('');
            $('#myPeriodForm button[type="submit"]').prop('disabled', false);

            $('#myPeriodModal').modal('show');

            fetchBookedClasses(day, period, '#my_period_conflict_alert', '#myPeriodForm button[type="submit"]', function () {
                checkConflict(studentClass, studentSection, '<?= $teacher_id ?? "" ?>', subject, '#my_period_conflict_alert', '#myPeriodForm button[type="submit"]');
            });
        });

        // Submit Period Form (Personal teacher timetable)
        $('#myPeriodForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: 'system/master_process?task=update_timetable',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (res.status === 'success') {
                        $('#myPeriodModal').modal('hide');
                        location.reload();
                    } else {
                        alert('Error saving timetable: ' + (res.msg || 'Unknown error occurred.'));
                    }
                },
                error: function () {
                    alert('Connection error. Failed to save period.');
                }
            });
        });

        // Delete Period (Personal teacher timetable)
        $('.delete-my-cell-btn').on('click', function (e) {
            e.stopPropagation();
            let day = $(this).data('day');
            let period = $(this).data('period');
            let studentClass = $(this).data('class');
            let studentSection = $(this).data('section');
            let timetableId = $(this).data('id') || '';

            if (confirm('Are you sure you want to clear Period ' + period + ' for ' + day + '?')) {
                $.ajax({
                    url: 'system/master_process?task=clear_timetable_period',
                    type: 'POST',
                    data: {
                        student_class: studentClass,
                        student_section: studentSection,
                        day: day,
                        period: period,
                        timetable_id: timetableId
                    },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status === 'success') {
                            location.reload();
                        } else {
                            alert('Error clearing period: ' + (res.msg || 'Unknown error.'));
                        }
                    },
                    error: function () {
                        alert('Connection error. Failed to clear period.');
                    }
                });
            }
        });

    });
</script>