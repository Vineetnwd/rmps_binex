<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $student_section = $subject = $selected_teacher_id = null;
if (isset($_REQUEST['student_class']))
    $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section']))
    $student_section = $_REQUEST['student_section'];
if (isset($_REQUEST['subject']))
    $subject = $_REQUEST['subject'];
if (isset($_REQUEST['teacher_id']))
    $selected_teacher_id = $_REQUEST['teacher_id'];

// Get all active teachers for Admin dropdown
$teachers_list = [];
$teacher_name = '';
if ($user_type !== 'TEACHER') {
    $teachers_res = direct_sql("SELECT id, name, emp_code FROM employee WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY name ASC");
    $teachers_list = $teachers_res['data'] ?? [];
    
    if ($selected_teacher_id) {
        $t_info = get_data('employee', $selected_teacher_id);
        if ($t_info['count'] > 0) {
            $teacher_name = ' (Teacher: ' . htmlspecialchars($t_info['data']['name']) . ')';
        }
    }
}

// Fetch assigned classes/sections
if ($user_type == 'TEACHER') {
    $emp = get_teacher_employee();
    $teacher_id = $emp['id'] ?? null;

    // Pull classes/sections from timetable (daily routine) — the source of truth
    $assigned_classes = direct_sql("SELECT DISTINCT student_class as class_name FROM timetable WHERE teacher_id = '$teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
    $class_list = array_column($assigned_classes['data'] ?? [], 'class_name');

    $assigned_sections = direct_sql("SELECT DISTINCT student_section as section_name FROM timetable WHERE teacher_id = '$teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
    $section_list = array_column($assigned_sections['data'] ?? [], 'section_name');
} else {
    // For Admin/Dev
    if ($selected_teacher_id) {
        // Pull classes/sections from timetable for this selected teacher
        $assigned_classes = direct_sql("SELECT DISTINCT student_class as class_name FROM timetable WHERE teacher_id = '$selected_teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
        $class_list = array_column($assigned_classes['data'] ?? [], 'class_name');

        $assigned_sections = direct_sql("SELECT DISTINCT student_section as section_name FROM timetable WHERE teacher_id = '$selected_teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
        $section_list = array_column($assigned_sections['data'] ?? [], 'section_name');
    } else {
        // If no teacher is selected, fetch all active student classes/sections
        $assigned_classes = direct_sql("SELECT DISTINCT student_class FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class ASC");
        $class_list = array_column($assigned_classes['data'] ?? [], 'student_class');

        $assigned_sections = direct_sql("SELECT DISTINCT student_section FROM student WHERE status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_section ASC");
        $section_list = array_column($assigned_sections['data'] ?? [], 'student_section');
    }
}

?>
<title> <?php echo $inst_name; ?> - Syllabus Tracker </title>
<style>
    .status-pending {
        color: #d9534f;
        font-weight: bold;
    }

    .status-taught {
        color: #5cb85c;
        font-weight: bold;
    }

    .status-revision {
        color: #f0ad4e;
        font-weight: bold;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1> Syllabus Tracker <small>Module 5</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Syllabus Tracker</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Filter Syllabus</h3>
            </div>
            <div class="box-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <h4 class="m-0 font-weight-bold text-dark">Syllabus Tracker</h4>
                        <p class="text-muted mb-0">Monitor and manage academic progress</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group">
                            <button type="button" id="btn-by-chapter" class="btn btn-premium btn-default" style="font-weight: bold;">By Chapter</button>
                            <button type="button" id="btn-by-month" class="btn btn-premium btn-primary active" style="font-weight: bold;">By Month</button>
                        </div>
                    </div>
                </div>

                <div class="box box-primary premium-card">
                    <div class="box-body">
                        <form action="" method="get">
                            <div class="row">
                                <?php if ($user_type !== 'TEACHER'): ?>
                                    <div class="col-md-9">
                                        <div class="form-group">
                                            <label>Teacher</label>
                                            <select class="form-control form-control-premium" name="teacher_id" id="teacher_select" required>
                                                <option value="">Select Teacher</option>
                                                <?php foreach ($teachers_list as $t): ?>
                                                    <option value="<?= $t['id'] ?>" <?= ($selected_teacher_id == $t['id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['emp_code']) ?>)
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-premium btn-premium-primary btn-block">View Progress</button>
                                    </div>
                                <?php else: ?>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Class</label>
                                            <select class="form-control form-control-premium" name="student_class" required>
                                                <?php dropdown($class_list, $student_class); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Section</label>
                                            <select class="form-control form-control-premium" name="student_section" required>
                                                <?php dropdown($section_list, $student_section); ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Subject</label>
                                            <select name="subject" id="subject_dropdown" class="form-control form-control-premium" required>
                                                <option value="">Select Subject</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <label>&nbsp;</label>
                                        <button type="submit" class="btn btn-premium btn-premium-primary btn-block">View Syllabus</button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <?php if ($user_type !== 'TEACHER' && $selected_teacher_id && !$student_class): ?>
                    <!-- Teacher Overview Dashboard -->
                    <?php
                    $courses = direct_sql("SELECT DISTINCT student_class, student_section, subject FROM timetable WHERE teacher_id = '$selected_teacher_id' AND status='ACTIVE' AND branch_id='$branch_id' ORDER BY student_class, student_section, subject ASC");
                    ?>
                    <div class="row" style="margin-top: 20px;">
                        <?php
                        if ($courses['count'] > 0) {
                            foreach ($courses['data'] as $course) {
                                $c_class = $course['student_class'];
                                $c_section = $course['student_section'];
                                $c_subj = $course['subject'];
                                
                                // Resolve subject name if it's numeric ID
                                $c_subj_name = $c_subj;
                                if (is_numeric($c_subj)) {
                                    $sub_info = get_data('subject', $c_subj);
                                    if ($sub_info['count'] > 0) {
                                        $c_subj_name = $sub_info['data']['subject_name'];
                                    }
                                }
                                
                                // Query progress
                                $progress_sql = "SELECT COUNT(*) as total, SUM(CASE WHEN id IN (SELECT syllabus_id FROM syllabus_progress WHERE student_section='$c_section' AND status='TAUGHT') THEN 1 ELSE 0 END) as taught FROM syllabus WHERE student_class='$c_class' AND subject='$c_subj'";
                                $prog_res = direct_sql($progress_sql);
                                $total_topics = $prog_res['data'][0]['total'] ?? 0;
                                $taught_topics = $prog_res['data'][0]['taught'] ?? 0;
                                
                                $perc = ($total_topics > 0) ? round(($taught_topics / $total_topics) * 100) : 0;
                                $status_label = ($perc == 100) ? "Completed" : (($perc > 0) ? "On Track" : "Not Started");
                                $status_class = ($perc == 100) ? "status-done" : "status-pending";
                                ?>
                                <div class="col-md-4" style="margin-bottom: 20px;">
                                    <div class="premium-card" style="background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); padding: 20px; transition: transform 0.2s;">
                                        <div class="d-flex justify-content-between align-items-center mb-3" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                                            <h5 class="m-0 font-weight-bold" style="font-size: 16px; font-weight: bold; margin: 0; color: #1e293b;"><?= htmlspecialchars($c_class) ?> - <?= htmlspecialchars($c_section) ?>: <?= htmlspecialchars($c_subj_name) ?></h5>
                                            <span class="badge-premium <?= $status_class ?>"><?= $status_label ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                            <small class="text-muted"><?= $taught_topics ?> of <?= $total_topics ?> topics taught</small>
                                            <small class="text-dark font-weight-bold"><?= $perc ?>%</small>
                                        </div>
                                        <div class="progress progress-premium" style="height: 8px; background-color: #f1f5f9; border-radius: 4px; overflow: hidden; margin-bottom: 15px;">
                                            <div class="progress-bar progress-bar-premium" style="width: <?= $perc ?>%; height: 100%; border-radius: 4px;"></div>
                                        </div>
                                        <div class="text-right" style="text-align: right;">
                                            <a href="syllabus_tracker.php?teacher_id=<?= $selected_teacher_id ?>&student_class=<?= urlencode($c_class) ?>&student_section=<?= urlencode($c_section) ?>&subject=<?= urlencode($c_subj) ?>" class="btn btn-premium btn-premium-primary btn-xs" style="padding: 4px 12px; font-size: 12px; border-radius: 6px;">
                                                <i class="fa fa-eye"></i> View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '<div class="col-md-12 text-center text-muted" style="padding: 40px; background: #fff; border-radius: 12px; border: 2px dashed #cbd5e1;">No active classes found for this teacher in the timetable.</div>';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <?php if ($student_class && $student_section && $subject): ?>
                    <?php if ($user_type !== 'TEACHER'): ?>
                        <div style="margin-bottom: 15px;">
                            <a href="syllabus_tracker.php?teacher_id=<?= $selected_teacher_id ?>" class="btn btn-premium btn-default btn-sm">
                                <i class="fa fa-arrow-left"></i> Back to Teacher Overview
                            </a>
                        </div>
                    <?php endif; ?>
                    <?php
                    // Dynamic Alert Logic: Find topics not taught for upcoming UT
                    $pending_topics_sql = "SELECT topic FROM syllabus WHERE student_class='$student_class' AND subject='$subject' AND id NOT IN (SELECT syllabus_id FROM syllabus_progress WHERE student_section='$student_section' AND status='TAUGHT') LIMIT 1";
                    $pending_topics = direct_sql($pending_topics_sql);

                    if ($pending_topics['count'] > 0):
                        ?>
                        <div class="alert alert-danger premium-card d-flex align-items-center"
                            style="background: #fff5f5; border-left: 5px solid var(--danger); color: #991b1b; padding: 20px;">
                            <div class="mr-4" style="font-size: 24px;"><i class="fa fa-exclamation-triangle"></i></div>
                            <div>
                                <strong style="font-size: 16px; display: block; margin-bottom: 4px;">Syllabus Gap
                                    Detected</strong>
                                <p class="m-0">The topic <strong>"<?= $pending_topics['data'][0]['topic'] ?>"</strong> has not
                                    been started yet. Please prioritize this to stay on track for the upcoming assessments.</p>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Chapter View Container -->
                    <div id="chapter-view-container" class="row" style="display: none;">
                        <?php
                        // Group by chapter to show cards
                        $chapter_sql = "SELECT chapter, COUNT(*) as total, SUM(CASE WHEN id IN (SELECT syllabus_id FROM syllabus_progress WHERE student_section='$student_section' AND status='TAUGHT') THEN 1 ELSE 0 END) as taught FROM syllabus WHERE student_class='$student_class' AND subject='$subject' GROUP BY chapter";
                        $chapters = direct_sql($chapter_sql);

                        foreach ($chapters['data'] as $chap) {
                            $perc = ($chap['total'] > 0) ? round(($chap['taught'] / $chap['total']) * 100) : 0;
                            $status_label = ($perc == 100) ? "Completed" : (($perc > 0) ? "On Track" : "Not Started");
                            $status_class = ($perc == 100) ? "status-done" : "status-pending";
                            ?>
                            <div class="col-md-4">
                                <div class="premium-card">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="m-0 font-weight-bold"><?= $chap['chapter'] ?></h5>
                                        <span class="badge-premium <?= $status_class ?>"><?= $status_label ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <small class="text-muted"><?= $chap['taught'] ?> of <?= $chap['total'] ?> topics</small>
                                        <small class="text-dark font-weight-bold"><?= $perc ?>%</small>
                                    </div>
                                    <div class="progress progress-premium">
                                        <div class="progress-bar progress-bar-premium" style="width: <?= $perc ?>%"></div>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                    </div>

                    <!-- Month View Container (Hidden by default) -->
                    <div id="month-view-container" class="row">
                        <?php
                        // Month-wise grouping
                        $month_data = [];
                        $all_syl_sql = "SELECT s.*, sp.status as progress_status FROM syllabus s LEFT JOIN syllabus_progress sp ON s.id = sp.syllabus_id AND sp.student_section = '$student_section' WHERE s.student_class='$student_class' AND s.subject='$subject' AND s.branch_id='$branch_id' ORDER BY s.planned_date ASC";
                        $all_syl_res = direct_sql($all_syl_sql, 'get', false);
                        $all_syl = $all_syl_res['data'] ?? [];

                        foreach ($all_syl as $item) {
                            $month_key = date('Y-m-d', strtotime($item['planned_date']));
                            $month_display = date('F Y', strtotime($item['planned_date']));
                            $chap = $item['chapter'];
                            $is_taught = ($item['progress_status'] === 'TAUGHT');

                            if (!isset($month_data[$month_key])) {
                                $month_data[$month_key] = [
                                    'display' => $month_display,
                                    'topics_total' => 0,
                                    'topics_taught' => 0,
                                    'chapters' => []
                                ];
                            }

                            $month_data[$month_key]['topics_total']++;
                            if ($is_taught) {
                                $month_data[$month_key]['topics_taught']++;
                            }

                            if (!isset($month_data[$month_key]['chapters'][$chap])) {
                                $month_data[$month_key]['chapters'][$chap] = [
                                    'total' => 0,
                                    'taught' => 0
                                ];
                            }
                            $month_data[$month_key]['chapters'][$chap]['total']++;
                            if ($is_taught) {
                                $month_data[$month_key]['chapters'][$chap]['taught']++;
                            }
                        }

                        // Calculate chapter counts per month
                        foreach ($month_data as $m_key => &$data) {
                            $data['chapters_total'] = count($data['chapters']);
                            $data['chapters_completed'] = 0;
                            foreach ($data['chapters'] as $c_name => $c_stats) {
                                if ($c_stats['taught'] === $c_stats['total']) {
                                    $data['chapters_completed']++;
                                }
                            }
                        }
                        unset($data);

                        if (empty($month_data)) {
                            echo '<div class="col-md-12 text-center text-muted">No monthly syllabus data found.</div>';
                        } else {
                            foreach ($month_data as $m_key => $data) {
                                $perc = ($data['topics_total'] > 0) ? round(($data['topics_taught'] / $data['topics_total']) * 100) : 0;
                                $status_label = ($perc == 100) ? "Completed" : (($perc > 0) ? "On Track" : "Not Started");
                                $status_class = ($perc == 100) ? "status-done" : "status-pending";
                                ?>
                                <div class="col-md-4">
                                    <div class="premium-card">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="m-0 font-weight-bold"><?= htmlspecialchars($data['display']) ?></h5>
                                            <span class="badge-premium <?= $status_class ?>"><?= $status_label ?></span>
                                        </div>
                                        <div class="d-flex justify-content-between mb-2">
                                            <small class="text-muted">
                                                <strong><?= $data['chapters_completed'] ?>/<?= $data['chapters_total'] ?></strong> chapters, 
                                                <strong><?= $data['topics_taught'] ?>/<?= $data['topics_total'] ?></strong> topics
                                            </small>
                                            <small class="text-dark font-weight-bold"><?= $perc ?>%</small>
                                        </div>
                                        <div class="progress progress-premium">
                                            <div class="progress-bar progress-bar-premium" style="width: <?= $perc ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>

                    <div class="premium-card mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h4 class="m-0 font-weight-bold text-dark"><?= $subject ?> - Topic Details<?= $teacher_name ?></h4>
                            <button class="btn btn-premium btn-success" data-toggle="modal" data-target="#addTopicModal"><i
                                    class="fa fa-plus"></i> Add New Topic</button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Chapter</th>
                                        <th>Topic</th>
                                        <th>Planned Date</th>
                                        <th class="text-center">Status</th>
                                        <th>Date Taught</th>
                                        <th>UT Covered</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                     $syl_sql = "SELECT * FROM syllabus WHERE student_class='$student_class' AND subject='$subject' ORDER BY planned_date ASC";
                                     $syl_res = direct_sql($syl_sql);
 
                                     if ($syl_res['count'] > 0) {
                                         foreach ($syl_res['data'] as $syl) {
                                             $prog = get_all('syllabus_progress', '*', ['syllabus_id' => $syl['id'], 'student_section' => $student_section]);
 
                                             $status = "PENDING";
                                             $date_taught = "-";
                                             $ut_covered = "-";
                                             if ($prog['count'] > 0) {
                                                 $status = $prog['data'][0]['status'];
                                                 $date_taught = $prog['data'][0]['date_taught'] ? date('d M Y', strtotime($prog['data'][0]['date_taught'])) : "-";
                                                 $ut_covered = $prog['data'][0]['ut_covered'] ? "UT " . $prog['data'][0]['ut_covered'] : "-";
                                             }
 
                                             $status_class = ($status == 'TAUGHT') ? 'status-done' : (($status == 'REVISION') ? 'badge-hindi' : 'status-pending');
 
                                             echo "<tr>";
                                             echo "<td>{$syl['chapter']}</td>";
                                             echo "<td>{$syl['topic']}</td>";
                                             echo "<td>" . date('d M Y', strtotime($syl['planned_date'])) . "</td>";
                                            echo "<td class='text-center'><span class='badge-premium {$status_class}'>{$status}</span></td>";
                                            echo "<td>{$date_taught}</td>";
                                            echo "<td>{$ut_covered}</td>";
                                            echo "<td class='text-center'><button class='btn btn-premium btn-default btn-xs update-btn' data-id='{$syl['id']}' data-status='{$status}'><i class='fa fa-edit'></i> Update</button></td>";
                                            echo "</tr>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='7' class='text-center'>No syllabus topics found for this subject. Please add topics.</td></tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- Add Topic Modal -->
<div class="modal fade" id="addTopicModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="system/master_process.php?task=add_syllabus" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Add Syllabus Topic</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="student_class" value="<?= $student_class ?>">
                    <input type="hidden" name="subject" value="<?= $subject ?>">

                    <div class="form-group">
                        <label>Chapter Name</label>
                        <input type="text" name="chapter" class="form-control" placeholder="e.g. Chapter 1: Real Numbers" required>
                    </div>
                    <div class="form-group">
                        <label>Topics & Planned Dates</label>
                        <div id="topics-container">
                            <div class="row mb-2 topic-input-row" style="margin-bottom: 10px;">
                                <div class="col-md-6" style="padding-right: 5px;">
                                    <input type="text" name="topics[]" class="form-control" placeholder="Topic Description" required>
                                </div>
                                <div class="col-md-4" style="padding-left: 5px; padding-right: 5px;">
                                    <input type="date" name="planned_dates[]" class="form-control" required>
                                </div>
                                <div class="col-md-2" style="padding-left: 5px;">
                                    <button class="btn btn-success add-topic-row-btn" type="button" style="width: 100%;"><i class="fa fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Save Topic</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Update Progress Modal -->
<div class="modal fade" id="updateProgressModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="system/master_process.php?task=update_syllabus_progress" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Update Syllabus Progress</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="syllabus_id" id="upd_syllabus_id">
                    <input type="hidden" name="student_section" value="<?= $student_section ?>">
                    <input type="hidden" name="url" value="<?= $_SERVER['REQUEST_URI'] ?>">

                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" id="upd_status" class="form-control" required>
                            <option value="PENDING">Pending</option>
                            <option value="TAUGHT">Taught</option>
                            <option value="REVISION">Revision Done</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Date Taught</label>
                        <input type="date" name="date_taught" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="form-group">
                        <label>Covered in UT (Number)</label>
                        <input type="number" name="ut_covered" class="form-control" min="1" max="27"
                            placeholder="e.g. 1">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    $(document).ready(function () {
        function loadSubjects() {
            let stu_class = $('select[name="student_class"]').val();
            let stu_section = $('select[name="student_section"]').val();
            let teacher_id = $('#teacher_select').val() || '';
            let current_subject = "<?= $subject ?>";

            if (stu_class && stu_section) {
                $.ajax({
                    url: 'system/master_process.php?task=get_teacher_subjects',
                    type: 'POST',
                    data: { 
                        student_class: stu_class, 
                        student_section: stu_section,
                        teacher_id: teacher_id
                    },
                    success: function (res) {
                        $('#subject_dropdown').html(res);
                        if (current_subject) {
                            $('#subject_dropdown').val(current_subject);
                        }
                    }
                });
            }
        }

        $('select[name="student_class"], select[name="student_section"]').on('change', loadSubjects);
        $('#teacher_select').on('change', function() {
            $(this).closest('form').submit();
        });
        loadSubjects();

        // Tab switching between By Chapter and By Month
        $('#btn-by-chapter').on('click', function () {
            $(this).removeClass('btn-default').addClass('btn-primary active');
            $('#btn-by-month').removeClass('btn-primary active').addClass('btn-default');
            $('#chapter-view-container').show();
            $('#month-view-container').hide();
        });

        $('#btn-by-month').on('click', function () {
            $(this).removeClass('btn-default').addClass('btn-primary active');
            $('#btn-by-chapter').removeClass('btn-primary active').addClass('btn-default');
            $('#chapter-view-container').hide();
            $('#month-view-container').show();
        });

        $('.update-btn').on('click', function () {
            let id = $(this).data('id');
            let status = $(this).data('status');

            $('#upd_syllabus_id').val(id);
            $('#upd_status').val(status);
            $('#updateProgressModal').modal('show');
        });

        // Add topic input row inside the modal
        $(document).on('click', '.add-topic-row-btn', function () {
            let lastDate = $('#topics-container .topic-input-row').last().find('input[type="date"]').val() || '';
            let row = `
                <div class="row mb-2 topic-input-row" style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="col-md-6" style="padding-right: 5px;">
                        <input type="text" name="topics[]" class="form-control" placeholder="Topic Description" required>
                    </div>
                    <div class="col-md-4" style="padding-left: 5px; padding-right: 5px;">
                        <input type="date" name="planned_dates[]" class="form-control" value="${lastDate}" required>
                    </div>
                    <div class="col-md-2" style="padding-left: 5px;">
                        <button class="btn btn-danger remove-topic-row-btn" type="button" style="width: 100%;"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
            `;
            $('#topics-container').append(row);
        });

        // Remove topic input row inside the modal
        $(document).on('click', '.remove-topic-row-btn', function () {
            $(this).closest('.topic-input-row').remove();
        });
    });
</script>