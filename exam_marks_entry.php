<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
// Initialize parameters
$student_class = $_REQUEST['student_class'] ?? '';
$student_section = $_REQUEST['student_section'] ?? '';
$exam_id = $_REQUEST['exam_id'] ?? '';
$subject_id = $_REQUEST['subject_id'] ?? '';

$exam_name_lower = '';
$is_periodic = false;
if ($exam_id != '') {
    $exam_name_lower = strtolower(get_data('exam_list', $exam_id, 'exam_name')['data'] ?? '');
    $is_periodic = (strpos($exam_name_lower, 'periodic') !== false || strpos($exam_name_lower, 'pt') !== false);
}

// Determine permission (DEV, ADMIN, DBA can edit, teachers need employee config setup)
$can_edit = 'NO';
$is_teacher = (strtoupper($user_type ?? '') == 'TEACHER');

if (strtoupper($user_type ?? '') == 'ADMIN' || strtoupper($user_type ?? '') == 'DEV' || strtoupper($user_type ?? '') == 'DBA') {
    $can_edit = 'YES';
} elseif ($is_teacher) {
    $can_edit = 'YES'; // Teachers can edit their own classes/subjects
} else {
    $emp_code = strtoupper(remove_space($_SESSION['user_name']));
    $can_edit = get_data('employee', $emp_code, 'marks_entry', 'emp_code')['data'] ?? 'NO';
}

if ($is_teacher) {
    $emp = get_teacher_employee();
    $teacher_id = $emp['id'] ?? null;
    
    // Fetch unique classes taught by this teacher from timetable table
    $assigned_classes = direct_sql("SELECT DISTINCT student_class as class_name FROM timetable WHERE teacher_id = '$teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
    $class_list = array_column($assigned_classes['data'] ?? [], 'class_name');
    
    // Fetch unique sections taught by this teacher from timetable table
    $assigned_sections = direct_sql("SELECT DISTINCT student_section as section_name FROM timetable WHERE teacher_id = '$teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
    $section_list = array_column($assigned_sections['data'] ?? [], 'section_name');
} else {
    // For Admin/Dev/DBA, fetch all classes and sections
    $assigned_classes = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
    $class_list = array_column($assigned_classes['data'] ?? [], 'student_class');

    $assigned_sections = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
    $section_list = array_column($assigned_sections['data'] ?? [], 'student_section');
}
?>

<style>
    /* Premium font and layout overrides */
    .content-wrapper {
        font-family: 'Outfit', sans-serif;
    }

    .marks-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        border: none;
        margin-bottom: 30px;
        transition: all 0.3s ease;
    }

    .marks-card:hover {
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08);
    }

    .card-header-gradient {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #ffffff;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 20px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title-premium {
        font-size: 20px;
        font-weight: 600;
        margin: 0;
        letter-spacing: 0.5px;
    }

    .form-group label {
        font-weight: 500;
        color: #475569;
        font-size: 13px;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 14px;
        height: auto;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .btn-search-premium {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        color: #ffffff;
        font-weight: 600;
        border-radius: 8px;
        padding: 11px 24px;
        font-size: 14px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.2);
        transition: all 0.2s;
        cursor: pointer;
        height: auto;
    }

    .btn-search-premium:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
        color: #ffffff;
    }

    /* Premium Table styling */
    .table-premium {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
        margin-bottom: 0;
    }

    .table-premium th {
        background-color: #f8fafc;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 0.5px;
        padding: 16px;
        border-bottom: 2px solid #e2e8f0;
    }

    .table-premium td {
        padding: 14px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    .table-premium tr:hover td {
        background-color: #f8fafc;
    }

    .input-marks {
        width: 90px;
        text-align: center;
        font-weight: 600;
        font-size: 15px;
        border-radius: 6px;
        border: 1px solid #cbd5e1;
        padding: 6px;
        transition: all 0.2s ease;
    }

    .input-marks:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }

    .total-badge {
        background: #f1f5f9;
        color: #334155;
        font-weight: 700;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 14px;
        display: inline-block;
        min-width: 45px;
        text-align: center;
        border: 1px solid #e2e8f0;
    }

    /* Micro-animation transitions on save */
    .input-saving {
        background-color: #fff3cd !important;
        border-color: #ffeba2 !important;
    }

    .input-saved-ok {
        background-color: #d4edda !important;
        border-color: #c3e6cb !important;
    }

    .input-saved-err {
        background-color: #f8d7da !important;
        border-color: #f5c6cb !important;
    }

    .badge-permission {
        font-size: 12px;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
    }
</style>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Exam Marks Entry</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item active">Exam Marks Entry</li>
        </ol>
    </section>

    <section class="content">
        <!-- Configuration Card -->
        <div class="marks-card box box-default">
            <div class="card-header-gradient">
                <h3 class="card-title-premium"><i class="fa fa-sliders"></i> Filter Criteria</h3>
                <div>
                    <?php if ($can_edit == 'YES') { ?>
                        <span class="badge badge-success bg-success badge-permission"><i class="fa fa-pencil"></i> Marks
                            Editable</span>
                    <?php } else { ?>
                        <span class="badge badge-warning bg-warning badge-permission"><i class="fa fa-lock"></i> View Only
                            Mode</span>
                    <?php } ?>
                </div>
            </div>

            <div class="box-body" style="padding: 25px;">
                <form action="" method="post" id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="student_class">Select Class</label>
                                <select class="form-control" name="student_class" id="student_class" required>
                                    <?php dropdown($class_list, $student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="student_section">Select Section</label>
                                <select class="form-control" name="student_section" id="student_section" required>
                                    <?php dropdown($section_list, $student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exam_id">Select Exam</label>
                                <select class="form-control" name="exam_id" id="exam_id" required>
                                    <option value="">Select Exam</option>
                                    <?php dropdown_list('exam_list', 'id', 'exam_name', $exam_id); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="subject_id">Select Subject</label>
                                <select class="form-control" name="subject_id" id="subject_id" required>
                                    <option value="">Select Subject</option>
                                    <?php
                                    if ($student_class != '') {
                                        if ($is_teacher) {
                                            $sql = "SELECT DISTINCT s.id, s.subject_name FROM timetable t 
                                                    JOIN subject s ON (t.subject = s.subject_name OR t.subject = CONVERT(s.id, CHAR)) 
                                                    WHERE t.teacher_id = '$teacher_id' 
                                                    AND t.student_class = '$student_class' 
                                                    AND t.student_section = '$student_section' 
                                                    AND t.status = 'ACTIVE' 
                                                    AND s.status = 'ACTIVE'
                                                    AND FIND_IN_SET('$student_class', s.student_class)
                                                    AND t.branch_id = '$branch_id' 
                                                    AND s.branch_id = '$branch_id' 
                                                    ORDER BY s.subject_name ASC";
                                        } else {
                                            $sql = "select * from subject where find_in_set('$student_class', student_class) and status='ACTIVE' and branch_id='$branch_id' and ifnull(show_in_report, 'YES') = 'YES' order by subject_order, subject_name";
                                        }
                                        $sub_res = direct_sql($sql, 'get', false);
                                        foreach ($sub_res['data'] as $sub) {
                                            $sel = ($sub['id'] == $subject_id) ? 'selected' : '';
                                            echo "<option value='" . $sub['id'] . "' $sel>" . $sub['subject_name'] . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-search-premium btn-block"><i class="fa fa-eye"></i>
                                    Show Details</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Student List Entry Grid -->
        <?php if ($student_class != '' && $student_section != '' && $exam_id != '' && $subject_id != '') { 
            $selected_subject_name = get_data('subject', $subject_id, 'subject_name')['data'] ?? '';
            $is_both_lang = (stripos($selected_subject_name, 'urdu') !== false && stripos($selected_subject_name, 'sanskrit') !== false);
            $is_urdu_subject = (!$is_both_lang && stripos($selected_subject_name, 'urdu') !== false);
            $is_sanskrit_subject = (!$is_both_lang && stripos($selected_subject_name, 'sanskrit') !== false);

            $res_students = get_all('student', '*', [
                'student_class' => $student_class,
                'student_section' => $student_section,
                'status' => 'ACTIVE'
            ], 'student_roll ASC');

            $total_students = $res_students['count'] ?? 0;
            $urdu_students_count = 0;
            $sanskrit_students_count = 0;
            if ($total_students > 0) {
                foreach ($res_students['data'] as $s) {
                    $u = $s['urdu'] ?? 0;
                    if (!empty($u) && ($u == '1' || $u === 1)) {
                        $urdu_students_count++;
                    } else {
                        $sanskrit_students_count++;
                    }
                }
            }
            $eligible_count = $is_urdu_subject ? $urdu_students_count : ($is_sanskrit_subject ? $sanskrit_students_count : $total_students);
            $non_eligible_count = $total_students - $eligible_count;
        ?>
            <div class="marks-card box box-default">
                <div class="card-header-gradient" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);">
                    <h3 class="card-title-premium" style="color: #fff;"><i class="fa fa-users"></i> Student Entry Sheet</h3>
                    <div style="font-size: 13px; font-weight: 500; color: rgba(255,255,255,0.9);">
                        Class:
                        <strong><?= htmlspecialchars($student_class) ?>-<?= htmlspecialchars($student_section) ?></strong>
                        &nbsp;|&nbsp;
                        Subject:
                        <strong><?= htmlspecialchars($selected_subject_name) ?></strong>
                    </div>
                </div>

                <?php if ($is_urdu_subject || $is_sanskrit_subject) { ?>
                    <div class="lang-filter-bar" style="background: #f0fdf4; border-bottom: 1px solid #bbf7d0; padding: 12px 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                        <div style="font-size: 13px; color: #166534; font-weight: 500;">
                            <i class="fa fa-language fa-lg" style="margin-right: 5px;"></i>
                            Language Filter: <strong><?= htmlspecialchars($selected_subject_name) ?></strong>
                            &nbsp;|&nbsp;
                            <span class="badge" style="background: #16a34a; color: #fff; font-weight: 600; padding: 5px 10px; border-radius: 12px; font-size: 12px;">
                                <?= $eligible_count ?> <?= $is_urdu_subject ? 'Urdu' : 'Sanskrit' ?> Students (Editable)
                            </span>
                            <span class="badge" style="background: #94a3b8; color: #fff; font-weight: 600; padding: 5px 10px; border-radius: 12px; font-size: 12px;">
                                <?= $non_eligible_count ?> <?= $is_urdu_subject ? 'Sanskrit' : 'Urdu' ?> Students (Not Applicable)
                            </span>
                        </div>
                        <div>
                            <button type="button" id="btnToggleHideNonEligible" class="btn btn-sm" 
                                data-lang="<?= $is_urdu_subject ? 'Sanskrit' : 'Urdu' ?>"
                                style="background: #ffffff; border: 1px solid #2563eb; color: #2563eb; font-weight: 600; font-size: 13px; padding: 6px 16px; border-radius: 8px; box-shadow: 0 1px 3px rgba(37,99,235,0.1); cursor: pointer; display: inline-flex; align-items: center; gap: 7px; transition: all 0.2s;">
                                <i class="fa fa-eye" id="toggleHideIcon"></i>
                                <span id="toggleHideText">Unhide <?= $is_urdu_subject ? 'Sanskrit' : 'Urdu' ?> Students</span>
                            </button>
                        </div>
                    </div>
                <?php } ?>

                <div class="box-body" style="padding: 0;">
                    <div class="table-responsive">
                        <table class="table-premium">
                            <thead>
                                <tr>
                                    <th style="width: 80px; text-align: center;">Roll No</th>
                                    <th style="width: 120px;">Admission No</th>
                                    <th>Student Name</th>
                                    <?php if ($is_urdu_subject || $is_sanskrit_subject) { ?>
                                    <th style="width: 110px; text-align: center;">Opted Lang</th>
                                    <?php } ?>
                                    <?php
                                    $is_extra_subj = get_data('subject', $subject_id, 'is_extra')['data'] ?? 'NO';
                                    
                                        if ($is_periodic) {
                                            $sea_max = get_pt_max_mark($student_class, $subject_id, 'sea', $branch_id);
                                            $ma_max = get_pt_max_mark($student_class, $subject_id, 'ma', $branch_id);
                                            $pt_max = get_pt_max_mark($student_class, $subject_id, 'pt', $branch_id);
                                    ?>
                                        <th style="width: 130px; text-align: center;">SEA (<?= $sea_max ?>)</th>
                                        <th style="width: 130px; text-align: center;">MA (<?= $ma_max ?>)</th>
                                        <th style="width: 130px; text-align: center;">PT (<?= $pt_max ?>)</th>
                                    <?php 
                                        } else { 
                                            $nb_max = '-';
                                            $ma_max = '-';
                                            $mo_max = '-';
                                            $mm_res = direct_sql("SELECT nb, ma, mo FROM max_marks WHERE class='$student_class' AND exam_id='$exam_id' AND (subject_id='$subject_id' OR subject_id='' OR subject_id IS NULL) AND branch_id='$branch_id' ORDER BY subject_id DESC LIMIT 1");
                                            if ($mm_res['count'] > 0) {
                                                $nb_max = $mm_res['data'][0]['nb'];
                                                $ma_max = $mm_res['data'][0]['ma'];
                                                $mo_max = $mm_res['data'][0]['mo'];
                                            }
                                    ?>
                                        <th style="width: 130px; text-align: center;">NB (<?= $nb_max ?>)</th>
                                        <th style="width: 130px; text-align: center;">MA (<?= $ma_max ?>)</th>
                                        <th style="width: 130px; text-align: center;">MO (<?= $mo_max ?>)</th>
                                    <?php 
                                        } 
                                    ?>
                                    <th style="width: 130px; text-align: center;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($total_students > 0) {
                                    foreach ($res_students['data'] as $srow) {
                                        $sid = $srow['id'];
                                        $student_admission = $srow['student_admission'];

                                        // Urdu vs Sanskrit determination
                                        $student_urdu = $srow['urdu'] ?? 0;
                                        $is_urdu_student = (!empty($student_urdu) && ($student_urdu == '1' || $student_urdu === 1));
                                        $student_lang = $is_urdu_student ? 'Urdu' : 'Sanskrit';

                                        // Eligibility check based on selected subject
                                        $is_eligible = true;
                                        $eligibility_reason = '';
                                        if ($is_urdu_subject && !$is_urdu_student) {
                                            $is_eligible = false;
                                            $eligibility_reason = 'Opted for Sanskrit';
                                        } elseif ($is_sanskrit_subject && $is_urdu_student) {
                                            $is_eligible = false;
                                            $eligibility_reason = 'Opted for Urdu';
                                        }

                                        $effective_can_edit = ($can_edit == 'YES' && $is_eligible);

                                        // Query existing marks
                                        $tabulation = get_all('tabulation', '*', [
                                            'student_id' => $sid,
                                            'student_admission' => $student_admission,
                                            'exam_id' => $exam_id,
                                            'subject' => $subject_id,
                                            'branch_id' => $branch_id
                                        ])['data'][0] ?? [];

                                        $input_style = (!$is_eligible) ? 'style="background-color: #f1f5f9; cursor: not-allowed; border-color: #e2e8f0; color: #94a3b8;"' : '';
                                        $title_attr = (!$is_eligible) ? 'title="Student opted for ' . htmlspecialchars($student_lang) . '"' : '';

                                            if ($is_periodic) {
                                                $sea_val = $tabulation['sea'] ?? '';
                                                $ma_val = $tabulation['ma'] ?? '';
                                                $pt_val = $tabulation['pt'] ?? '';
    
                                                // Calculate total
                                                $total_val = 0;
                                                if (is_numeric($sea_val)) $total_val += $sea_val;
                                                if (is_numeric($ma_val)) $total_val += $ma_val;
                                                if (is_numeric($pt_val)) $total_val += $pt_val;
    
                                                $disabled_attr = $effective_can_edit ? '' : 'disabled';
                                                
                                                $disabled_sea = ($disabled_attr != '' || $sea_max == '-') ? 'disabled' : '';
                                                $placeholder_sea = (!$is_eligible) ? 'N/A' : (($sea_max == '-') ? '-' : "Max $sea_max");
                                                $max_attr_sea = ($is_eligible && is_numeric($sea_max)) ? "max='$sea_max'" : "";
                                                
                                                $disabled_ma = ($disabled_attr != '' || $ma_max == '-') ? 'disabled' : '';
                                                $placeholder_ma = (!$is_eligible) ? 'N/A' : (($ma_max == '-') ? '-' : "Max $ma_max");
                                                $max_attr_ma = ($is_eligible && is_numeric($ma_max)) ? "max='$ma_max'" : "";
                                                
                                                $disabled_pt = ($disabled_attr != '' || $pt_max == '-') ? 'disabled' : '';
                                                $placeholder_pt = (!$is_eligible) ? 'N/A' : (($pt_max == '-') ? '-' : "Max $pt_max");
                                                $max_attr_pt = ($is_eligible && is_numeric($pt_max)) ? "max='$pt_max'" : "";
                                                ?>
                                                <tr class="student-row <?= !$is_eligible ? 'not-eligible-row' : 'eligible-row' ?>"
                                                    data-lang="<?= strtolower($student_lang) ?>"
                                                    data-eligible="<?= $is_eligible ? '1' : '0' ?>"
                                                    <?= !$is_eligible ? 'style="background-color: #fafbfc; color: #94a3b8;"' : '' ?>>
                                                    <td style="text-align: center; font-weight: 600;"><?= htmlspecialchars($srow['student_roll']) ?></td>
                                                    <td><code style="font-size: 13px; color: <?= $is_eligible ? '#4f46e5' : '#94a3b8' ?>;"><?= htmlspecialchars($student_admission) ?></code></td>
                                                    <td style="font-weight: 500;">
                                                        <?= htmlspecialchars($srow['student_name']) ?>
                                                        <?php if (!$is_eligible) { ?>
                                                            <span class="badge badge-secondary" style="font-size: 10px; background: #e2e8f0; color: #64748b; margin-left: 5px; font-weight: 500;">
                                                                <i class="fa fa-ban"></i> <?= htmlspecialchars($eligibility_reason) ?>
                                                            </span>
                                                        <?php } ?>
                                                    </td>
                                                    <?php if ($is_urdu_subject || $is_sanskrit_subject) { ?>
                                                    <td style="text-align: center;">
                                                        <?php if ($is_urdu_student) { ?>
                                                            <span class="badge" style="background-color: #059669; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600;">
                                                                <i class="fa fa-book"></i> Urdu
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="badge" style="background-color: #d97706; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600;">
                                                                <i class="fa fa-book"></i> Sanskrit
                                                            </span>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td style="text-align: center;">
                                                        <input type="number" class="input-marks marks-input" data-student-id="<?= $sid ?>"
                                                            data-student-admission="<?= $student_admission ?>" data-col="sea"
                                                            value="<?= htmlspecialchars($sea_val) ?>" min="0" <?= $max_attr_sea ?> placeholder="<?= $placeholder_sea ?>" <?= $disabled_sea ?> <?= $input_style ?> <?= $title_attr ?>>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" class="input-marks marks-input" data-student-id="<?= $sid ?>"
                                                            data-student-admission="<?= $student_admission ?>" data-col="ma"
                                                            value="<?= htmlspecialchars($ma_val) ?>" min="0" <?= $max_attr_ma ?> placeholder="<?= $placeholder_ma ?>" <?= $disabled_ma ?> <?= $input_style ?> <?= $title_attr ?>>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" class="input-marks marks-input" data-student-id="<?= $sid ?>"
                                                            data-student-admission="<?= $student_admission ?>" data-col="pt"
                                                            value="<?= htmlspecialchars($pt_val) ?>" min="0" <?= $max_attr_pt ?> placeholder="<?= $placeholder_pt ?>" <?= $disabled_pt ?> <?= $input_style ?> <?= $title_attr ?>>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="total-badge row-total" <?= (!$is_eligible) ? 'style="opacity: 0.5; background: #f1f5f9; color: #94a3b8;"' : '' ?>><?= $is_eligible ? $total_val : '-' ?></span>
                                                    </td>
                                                </tr>
                                            <?php } else { 
                                                $nb_val = $tabulation['nb'] ?? '';
                                                $ma_val = $tabulation['ma'] ?? '';
                                                $mo_val = $tabulation['mo'] ?? '';
                                                
                                                // Calculate total
                                                $total_val = 0;
                                                if (is_numeric($nb_val)) $total_val += $nb_val;
                                                if (is_numeric($ma_val)) $total_val += $ma_val;
                                                if (is_numeric($mo_val)) $total_val += $mo_val;

                                                $disabled_attr = $effective_can_edit ? '' : 'disabled';
                                                
                                                $disabled_nb = ($disabled_attr != '' || $nb_max == '-') ? 'disabled' : '';
                                                $placeholder_nb = (!$is_eligible) ? 'N/A' : (($nb_max == '-') ? '-' : "Max $nb_max");
                                                $max_attr_nb = ($is_eligible && is_numeric($nb_max)) ? "max='$nb_max'" : "";
                                                
                                                $disabled_ma = ($disabled_attr != '' || $ma_max == '-') ? 'disabled' : '';
                                                $placeholder_ma = (!$is_eligible) ? 'N/A' : (($ma_max == '-') ? '-' : "Max $ma_max");
                                                $max_attr_ma = ($is_eligible && is_numeric($ma_max)) ? "max='$ma_max'" : "";
                                                
                                                $disabled_mo = ($disabled_attr != '' || $mo_max == '-') ? 'disabled' : '';
                                                $placeholder_mo = (!$is_eligible) ? 'N/A' : (($mo_max == '-') ? '-' : "Max $mo_max");
                                                $max_attr_mo = ($is_eligible && is_numeric($mo_max)) ? "max='$mo_max'" : "";
                                            ?>
                                                <tr class="student-row <?= !$is_eligible ? 'not-eligible-row' : 'eligible-row' ?>"
                                                    data-lang="<?= strtolower($student_lang) ?>"
                                                    data-eligible="<?= $is_eligible ? '1' : '0' ?>"
                                                    <?= !$is_eligible ? 'style="background-color: #fafbfc; color: #94a3b8;"' : '' ?>>
                                                    <td style="text-align: center; font-weight: 600;"><?= htmlspecialchars($srow['student_roll']) ?></td>
                                                    <td><code style="font-size: 13px; color: <?= $is_eligible ? '#4f46e5' : '#94a3b8' ?>;"><?= htmlspecialchars($student_admission) ?></code></td>
                                                    <td style="font-weight: 500;">
                                                        <?= htmlspecialchars($srow['student_name']) ?>
                                                        <?php if (!$is_eligible) { ?>
                                                            <span class="badge badge-secondary" style="font-size: 10px; background: #e2e8f0; color: #64748b; margin-left: 5px; font-weight: 500;">
                                                                <i class="fa fa-ban"></i> <?= htmlspecialchars($eligibility_reason) ?>
                                                            </span>
                                                        <?php } ?>
                                                    </td>
                                                    <?php if ($is_urdu_subject || $is_sanskrit_subject) { ?>
                                                    <td style="text-align: center;">
                                                        <?php if ($is_urdu_student) { ?>
                                                            <span class="badge" style="background-color: #059669; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600;">
                                                                <i class="fa fa-book"></i> Urdu
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="badge" style="background-color: #d97706; color: #ffffff; font-size: 11px; padding: 4px 8px; border-radius: 12px; font-weight: 600;">
                                                                <i class="fa fa-book"></i> Sanskrit
                                                            </span>
                                                        <?php } ?>
                                                    </td>
                                                    <?php } ?>
                                                    <td style="text-align: center;">
                                                        <input type="number" class="input-marks marks-input" data-student-id="<?= $sid ?>"
                                                            data-student-admission="<?= $student_admission ?>" data-col="nb"
                                                            value="<?= htmlspecialchars($nb_val) ?>" min="0" <?= $max_attr_nb ?> placeholder="<?= $placeholder_nb ?>" <?= $disabled_nb ?> <?= $input_style ?> <?= $title_attr ?>>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" class="input-marks marks-input" data-student-id="<?= $sid ?>"
                                                            data-student-admission="<?= $student_admission ?>" data-col="ma"
                                                            value="<?= htmlspecialchars($ma_val) ?>" min="0" <?= $max_attr_ma ?> placeholder="<?= $placeholder_ma ?>" <?= $disabled_ma ?> <?= $input_style ?> <?= $title_attr ?>>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <input type="number" class="input-marks marks-input" data-student-id="<?= $sid ?>"
                                                            data-student-admission="<?= $student_admission ?>" data-col="mo"
                                                            value="<?= htmlspecialchars($mo_val) ?>" min="0" <?= $max_attr_mo ?> placeholder="<?= $placeholder_mo ?>" <?= $disabled_mo ?> <?= $input_style ?> <?= $title_attr ?>>
                                                    </td>
                                                    <td style="text-align: center;">
                                                        <span class="total-badge row-total" <?= (!$is_eligible) ? 'style="opacity: 0.5; background: #f1f5f9; color: #94a3b8;"' : '' ?>><?= $is_eligible ? $total_val : '-' ?></span>
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        <?php }
                                    if ($is_urdu_subject || $is_sanskrit_subject) { ?>
                                        <tr id="noEligibleRow" style="display: none;">
                                            <td colspan="<?= ($is_urdu_subject || $is_sanskrit_subject) ? 8 : 7 ?>" class="text-center" style="padding: 35px; color: #64748b; background-color: #f8fafc;">
                                                <i class="fa fa-filter fa-2x" style="margin-bottom: 10px; color: #94a3b8;"></i><br>
                                                <strong>No <?= $is_urdu_subject ? 'Urdu' : 'Sanskrit' ?> students to display.</strong><br>
                                                <span style="font-size: 13px;">All students in this class have opted for <?= $is_urdu_subject ? 'Sanskrit' : 'Urdu' ?>. Click <a href="javascript:void(0);" onclick="$('#btnToggleHideNonEligible').click();" style="font-weight: 600; text-decoration: underline; color: #2563eb;">Unhide <?= $is_urdu_subject ? 'Sanskrit' : 'Urdu' ?> Students</a> to view all students.</span>
                                            </td>
                                        </tr>
                                    <?php }
                                } else { ?>
                                    <tr>
                                        <td colspan="<?= ($is_urdu_subject || $is_sanskrit_subject) ? 8 : 7 ?>" class="text-center" style="padding: 30px; color: #64748b;">
                                            <i class="fa fa-info-circle fa-2x"></i><br>
                                            No active students found in this class and section.
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php } ?>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    // AJAX load sections for selected class
    function loadSections() {
        let stu_class = $('#student_class').val();
        let current_section = "<?= $student_section ?>";

        if (stu_class) {
            $.ajax({
                type: 'POST',
                url: 'system/master_process.php?task=get_exam_entry_sections',
                data: { 'student_class': stu_class },
                success: function (data) {
                    $('#student_section').html(data);
                    if (current_section) {
                        $('#student_section').val(current_section);
                    }
                    // After loading sections, load subjects
                    loadSubjects();
                }
            });
        } else {
            $('#student_section').html('<option value="">Select Section</option>');
            $('#subject_id').html('<option value="">Select Subject</option>');
        }
    }

    // AJAX load subjects for selected class and section
    function loadSubjects() {
        let stu_class = $('#student_class').val();
        let stu_section = $('#student_section').val();
        let current_subject = "<?= $subject_id ?>";

        if (stu_class && stu_section) {
            $.ajax({
                type: 'POST',
                url: 'system/master_process.php?task=get_exam_entry_subjects',
                data: { 
                    'student_class': stu_class, 
                    'student_section': stu_section 
                },
                success: function (data) {
                    $('#subject_id').html(data);
                    if (current_subject) {
                        $('#subject_id').val(current_subject);
                    }
                }
            });
        } else {
            $('#subject_id').html('<option value="">Select Subject</option>');
        }
    }

    $(document).ready(function () {
        $('#student_class').on('change', loadSections);
        $('#student_section').on('change', loadSubjects);
        // State for hiding non-eligible students (default: true = hidden)
        let areNonEligibleHidden = true;

        function updateTableVisibility() {
            let $btn = $('#btnToggleHideNonEligible');
            if ($btn.length > 0) {
                let lang = $btn.data('lang') || '';
                if (areNonEligibleHidden) {
                    $('.not-eligible-row').hide();
                    $btn.html('<i class="fa fa-eye"></i> <span>Unhide ' + lang + ' Students</span>');
                    $btn.css({
                        'background': '#ffffff',
                        'border-color': '#2563eb',
                        'color': '#2563eb'
                    });
                } else {
                    $('.not-eligible-row').show();
                    $btn.html('<i class="fa fa-eye-slash"></i> <span>Hide ' + lang + ' Students</span>');
                    $btn.css({
                        'background': '#f8fafc',
                        'border-color': '#94a3b8',
                        'color': '#475569'
                    });
                }
                let visibleRows = $('tbody tr.student-row:visible').length;
                if (visibleRows === 0 && areNonEligibleHidden) {
                    $('#noEligibleRow').show();
                } else {
                    $('#noEligibleRow').hide();
                }
            }
        }

        $(document).on('click', '#btnToggleHideNonEligible', function () {
            areNonEligibleHidden = !areNonEligibleHidden;
            updateTableVisibility();
        });
        updateTableVisibility();

        // Cache initial value on focus
        $(document).on('focus', '.marks-input', function () {
            $(this).data('prev-val', $(this).val().trim());
        });

        // Auto-save logic on blur
        $(document).on('blur', '.marks-input', function () {
            saveMark(this);
        });

        // Handle Enter key for fast vertical entry and instant saving
        $(document).on('keypress', '.marks-input', function (e) {
            if (e.which === 13) { // Enter key
                e.preventDefault();
                $(this).blur(); // Trigger blur to save

                // Move focus down to the same column input for the next eligible student
                let col = $(this).data('col');
                let nextRow = $(this).closest('tr').nextAll('tr.student-row:visible').filter(function () {
                    return $(this).find('.marks-input[data-col="' + col + '"]:not(:disabled)').length > 0;
                }).first();

                if (nextRow.length) {
                    let nextInput = nextRow.find('.marks-input[data-col="' + col + '"]:not(:disabled)');
                    nextInput.focus().select();
                }
            }
        });

        // Function to perform the save via AJAX
        function saveMark(input) {
            let $input = $(input);
            let studentId = $input.data('student-id');
            let studentAdmission = $input.data('student-admission');
            let colName = $input.data('col');
            let value = $input.val().trim();

            let examId = $('#exam_id').val();
            let subject = $('#subject_id').val();

            let prevVal = $input.data('prev-val') || '';
            if (value === prevVal) {
                return; // Nothing changed
            }

            // Frontend validation of max marks
            let maxVal = parseFloat($input.attr('max'));
            if (!isNaN(maxVal) && value !== '') {
                let parsedVal = parseFloat(value);
                if (parsedVal > maxVal) {
                    $input.addClass('input-saved-err');
                    alert('Invalid input! Maximum allowed marks for this column is ' + maxVal);
                    $input.val(prevVal);
                    setTimeout(function () {
                        $input.removeClass('input-saved-err');
                    }, 1500);
                    return;
                }
            }

            // Put input in saving state
            $input.removeClass('input-saved-ok input-saved-err').addClass('input-saving');

            $.ajax({
                url: 'system/master_process.php?task=save_exam_tabulation_marks',
                type: 'POST',
                dataType: 'json',
                data: {
                    student_id: studentId,
                    student_admission: studentAdmission,
                    exam_id: examId,
                    subject: subject,
                    col_name: colName,
                    value: value
                },
                success: function (response) {
                    $input.removeClass('input-saving');
                    if (response.status === 'success') {
                        $input.data('prev-val', value);
                        $input.addClass('input-saved-ok');
                        setTimeout(function () {
                            $input.removeClass('input-saved-ok');
                        }, 1000);

                        // Recalculate row total
                        recalculateRowTotal($input.closest('tr'));
                    } else {
                        $input.addClass('input-saved-err');
                        alert('Failed to save: ' + (response.msg || 'Database error'));
                        $input.val(prevVal);
                        setTimeout(function () {
                            $input.removeClass('input-saved-err');
                        }, 1500);
                    }
                },
                error: function (xhr, status, error) {
                    $input.removeClass('input-saving').addClass('input-saved-err');
                    alert('Server or network connection error. Try again!');
                    $input.val(prevVal);
                    setTimeout(function () {
                        $input.removeClass('input-saved-err');
                    }, 1500);
                }
            });
        }

        // Real-time UI total recalculation helper
        function recalculateRowTotal($row) {
            let total = 0;
            $row.find('.marks-input').each(function () {
                let v = $(this).val().trim();
                if (v !== '' && !isNaN(v)) {
                    total += parseFloat(v);
                }
            });
            $row.find('.row-total').text(total);
        }
    });
</script>