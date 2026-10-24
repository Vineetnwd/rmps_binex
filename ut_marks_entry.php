<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $student_section = $subject = $ut_number = null;
if (isset($_REQUEST['student_class']))
    $student_class = $_REQUEST['student_class'];
if (isset($_REQUEST['student_section']))
    $student_section = $_REQUEST['student_section'];
if (isset($_REQUEST['subject']))
    $subject = $_REQUEST['subject'];
if (isset($_REQUEST['ut_number']))
    $ut_number = $_REQUEST['ut_number'];

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

// Fetch UT details from global master schedule if selected
$max_marks = 20; // Default
if ($ut_number) {
    $ut_esc = mysqli_real_escape_string($con, $ut_number);
    $admin_schedule = direct_sql("SELECT * FROM ut_tests WHERE ut_number = '$ut_esc' AND status = 'ACTIVE' AND (student_class = '' OR student_class IS NULL) AND (student_section = '' OR student_section IS NULL) AND (subject = '' OR subject IS NULL) AND branch_id = '$branch_id' LIMIT 1");
    if ($admin_schedule['count'] > 0) {
        $max_marks = $admin_schedule['data'][0]['max_marks'];
    }
}
?>

<title> <?php echo $inst_name; ?> - UT Marks Entry </title>
<style>
    .marks-input {
        border: 1px solid #ccc;
        border-radius: 4px;
        text-align: center;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1> UT Marks Entry <small>Module 1</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">UT Marks Entry</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Select Class & Test</h3>
                <div class="box-tools pull-right">
                    <span class="text-primary font-weight-bold">Unit Test Marks Entry Grid</span>
                </div>
            </div>
            <div class="box-body">
                <div class="row align-items-center mb-4">
                    <div class="col-md-6">
                        <h4 class="m-0 font-weight-bold text-dark">UT Marks Entry</h4>
                        <p class="text-muted mb-0">Record and manage student performance</p>
                    </div>
                    <div class="col-md-6 text-right">
                        <button class="btn btn-premium btn-default mr-2"><i class="fa fa-copy"></i> Copy Previous
                            UT</button>
                        <button class="btn btn-premium btn-warning" id="save-draft"><i class="fa fa-save"></i> Save
                            Draft</button>
                    </div>
                </div>

                <div class="box box-primary premium-card">
                    <div class="box-body">
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
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Section</label>
                                        <select class="form-control form-control-premium" name="student_section"
                                            required>
                                            <?php dropdown($section_list, $student_section); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Subject</label>
                                        <select name="subject" id="subject_dropdown"
                                            class="form-control form-control-premium" required>
                                            <option value="">Select Subject</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label>&nbsp;</label>
                                    <button type="submit"
                                        class="btn btn-premium btn-premium-primary btn-block">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <?php if ($student_class && $student_section && $subject): ?>
                    <div class="premium-card">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th width="60">Roll</th>
                                        <th>Student Name</th>
                                        <th>Father's Name</th>
                                        <?php
                                        $master_uts_hd = direct_sql("SELECT ut_number FROM ut_tests WHERE (student_class = '' OR student_class IS NULL) AND (student_section = '' OR student_section IS NULL) AND (subject = '' OR subject IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY id ASC");
                                        $cols_count = $master_uts_hd['count'];
                                        if ($cols_count > 0) {
                                            foreach ($master_uts_hd['data'] as $master) {
                                                $is_selected = ($master['ut_number'] == $ut_number);
                                                $th_style = $is_selected ? "style='color: #3b82f6; font-weight: 800; text-decoration: underline;'" : "";
                                                echo "<th class='text-center' {$th_style}>" . htmlspecialchars($master['ut_number']) . "</th>";
                                            }
                                        }
                                        ?>
                                        <th width="100" class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Fetch all active master UT tests scheduled by admin
                                    $master_uts = direct_sql("SELECT * FROM ut_tests WHERE (student_class = '' OR student_class IS NULL) AND (student_section = '' OR student_section IS NULL) AND (subject = '' OR subject IS NULL) AND status = 'ACTIVE' AND branch_id = '$branch_id' ORDER BY id ASC");

                                    if ($master_uts['count'] == 0) {
                                        echo "<tr><td colspan='5' class='text-center text-danger font-weight-bold p-4'>No Unit Tests have been scheduled by the Admin. Please contact the administrator.</td></tr>";
                                    } else {
                                        $ut_ids = [];
                                        $ut_max_marks = [];

                                        foreach ($master_uts['data'] as $master) {
                                            $ut_num = $master['ut_number'];
                                            $master_max = $master['max_marks'];
                                            $master_date = $master['date'];

                                            $ut_max_marks[$ut_num] = $master_max;

                                            // Query class-wise scheduled subject for this class under this UT number
                                            $ut_test = direct_sql("SELECT id, max_marks, date FROM ut_tests WHERE student_class = '$student_class' AND (student_section = '' OR student_section IS NULL) AND subject = '$subject' AND ut_number = '$ut_num' AND status = 'ACTIVE' AND branch_id = '$branch_id' LIMIT 1");

                                            if ($ut_test['count'] > 0) {
                                                $ut_id = $ut_test['data'][0]['id'];
                                                $ut_ids[$ut_num] = $ut_id;

                                                // Sync max_marks and date if they differ from the master configuration
                                                if ($ut_test['data'][0]['max_marks'] != $master_max || $ut_test['data'][0]['date'] != $master_date) {
                                                    update_data('ut_tests', [
                                                        'max_marks' => $master_max,
                                                        'date' => $master_date
                                                    ], $ut_id);
                                                }
                                            } else {
                                                $ut_ids[$ut_num] = null;
                                            }
                                        }

                                        // Fetch active students
                                        $res = get_all('student', '*', ['student_class' => $student_class, 'student_section' => $student_section, 'status' => 'ACTIVE']);

                                        foreach ($res['data'] as $student) {
                                            echo "<tr>";
                                            echo "<td class='font-weight-bold'>{$student['student_roll']}</td>";
                                            echo "<td class='font-weight-bold text-dark'>{$student['student_name']}</td>";
                                            echo "<td class='text-muted'>{$student['student_father']}</td>";

                                            foreach ($master_uts['data'] as $master) {
                                                $ut_num = $master['ut_number'];
                                                $ut_id = $ut_ids[$ut_num];
                                                $max_m = $ut_max_marks[$ut_num];

                                                echo "<td class='text-center'>";
                                                if ($ut_id !== null) {
                                                     // Fetch marks
                                                     $marks_res = direct_sql("SELECT marks FROM ut_marks WHERE ut_id = '$ut_id' AND student_id = '{$student['id']}' LIMIT 1");
                                                     $mark_val = ($marks_res['count'] > 0) ? $marks_res['data'][0]['marks'] : '';

                                                     // Check if this column is the selected UT test to highlight it
                                                     $highlight_style = ($ut_num == $ut_number) ? "border: 2px solid #3b82f6; background-color: #eff6ff;" : "border: 1px solid #cbd5e1;";

                                                     echo "<input type='number' class='form-control form-control-premium marks-input mx-auto text-center' data-ut-id='{$ut_id}' data-student-id='{$student['id']}' value='{$mark_val}' placeholder='-' min='0' max='{$max_m}' step='0.5' style='width: 55px; padding: 4px; font-size: 12px; border-radius: 6px; {$highlight_style} height: 32px;'>";
                                                } else {
                                                     // This subject is not scheduled for this class in this UT
                                                     echo "<span class='text-muted' style='font-size: 11px;' title='Not scheduled for this class'>-</span>";
                                                }
                                                echo "</td>";
                                            }

                                            echo "<td class='text-center'><span class='badge-premium status-done'>Draft</span></td>";
                                            echo "</tr>";
                                        }
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

<?php require_once('system/footer2.php'); ?>

<script>
    $(document).ready(function () {
        function loadSubjects() {
            let stu_class = $('select[name="student_class"]').val();
            let stu_section = $('select[name="student_section"]').val();
            let current_subject = "<?= $subject ?>";

            if (stu_class && stu_section) {
                $.ajax({
                    url: 'system/master_process.php?task=get_teacher_subjects',
                    type: 'POST',
                    data: { student_class: stu_class, student_section: stu_section },
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
        loadSubjects();

        $('.marks-input').on('change', function () {
            let input = $(this);
            let marks = input.val();
            let ut_id = input.data('ut-id');
            let student_id = input.data('student-id');
            let max_marks = parseFloat(input.attr('max')) || 20;

            if (parseFloat(marks) > max_marks) {
                alert("Marks cannot exceed " + max_marks + ".");
                input.val('');
                return;
            }

            $.ajax({
                url: 'system/master_process.php?task=save_ut_marks',
                type: 'POST',
                data: { ut_id: ut_id, student_id: student_id, marks: marks },
                success: function (res) {
                    let data = JSON.parse(res);
                    if (data.status === 'success') {
                        input.css('border-color', '#10b981');
                        setTimeout(() => input.css('border-color', ''), 1000);
                    } else {
                        alert('Failed to save marks');
                    }
                }
            });
        });

        $('#save-draft').on('click', function () {
            alert("Draft saved successfully!");
        });
    });
</script>