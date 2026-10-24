<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$student_class = $student_section = $att_date = null;
if (isset($_REQUEST['student_class'])) {
    $student_class = $_REQUEST['student_class'];
}
if (isset($_REQUEST['student_section'])) {
    $student_section = $_REQUEST['student_section'];
}
if (isset($_REQUEST['att_date'])) {
    $att_date = $_REQUEST['att_date'];
} else {
    $att_date = date('Y-m-d');
}

$m_value = remove_space(date('M_Y', strtotime($att_date)));
$day_num = date('j', strtotime($att_date));

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
?>
<title> <?php echo $inst_name; ?> - Daily Attendance </title>
<style>
    .att-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #ccc;
        background: #fff;
        cursor: pointer;
        transition: 0.3s;
        font-weight: bold;
    }

    .att-btn.active-P {
        background: #00a65a;
        color: #fff;
        border-color: #008d4c;
    }

    .att-btn.active-A {
        background: #dd4b39;
        color: #fff;
        border-color: #d73925;
    }

    .att-btn.active-L {
        background: #f39c12;
        color: #fff;
        border-color: #e08e0b;
    }

    .att-btn.active-C {
        background: #9e9e9e;
        color: #fff;
        border-color: #757575;
    }

    .att-btn:hover {
        opacity: 0.8;
    }

    .student-row:hover {
        background-color: #f9f9f9;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1> Daily Attendance <small>Module 2</small> </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Daily Attendance</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Mark Daily Attendance</h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-default btn-sm" id="copy-prev-btn"><i class="fa fa-copy"></i> Copy from
                        Previous Day</button>
                    <button class="btn btn-success btn-sm" id="mark-all-p"><i class="fa fa-check-all"></i> Mark All
                        Present</button>
                    <button class="btn btn-danger btn-sm" id="clear-all-att"><i class="fa fa-eraser"></i> Clear All</button>
                </div>
            </div>
            <div class="box-body">
                <form action="" method="get" id="filter-form">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Class</label>
                                <select class="form-control" name="student_class" required>
                                    <?php dropdown($class_list, $student_class); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Section</label>
                                <select class="form-control" name="student_section" required>
                                    <?php dropdown($section_list, $student_section); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="att_date" class="form-control" value="<?= $att_date ?>"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">Show Students</button>
                        </div>
                    </div>
                </form>

                <?php if ($student_class && $student_section && $att_date): ?>
                    <hr>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th width="10%">Roll No</th>
                                    <th width="40%">Student Name</th>
                                    <th width="15%">Admission No</th>
                                    <th width="35%" class="text-center">Status (P / A / L)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = get_all('student', '*', ['student_class' => $student_class, 'student_section' => $student_section, 'status' => 'ACTIVE'], 'student_roll');

                                foreach ($res['data'] as $student) {
                                    $stu_id = $student['id'];
                                    $att_sql = "SELECT d_$day_num as status FROM student_att WHERE student_id = '$stu_id' AND att_month = '$m_value'";
                                    $att_res = direct_sql($att_sql);

                                    if ($att_res['count'] == 0) {
                                        // Initialize record for this month if missing
                                        insert_data('student_att', ['student_id' => $stu_id, 'att_month' => $m_value, 'branch_id' => $_SESSION['branch_id']]);
                                        $status = '';
                                    } else {
                                        $status = $att_res['data'][0]['status'];
                                    }

                                    echo "<tr class='student-row' id='row_{$stu_id}'>";
                                    echo "<td>{$student['student_roll']}</td>";
                                    echo "<td>{$student['student_name']}</td>";
                                    echo "<td>{$student['student_admission']}</td>";
                                    echo "<td class='text-center'>";
                                    ?>
                                    <div class="btn-group">
                                        <button class="att-btn <?= ($status == 'P') ? 'active-P' : '' ?>"
                                            data-id="<?= $stu_id ?>" data-status="P">P</button>
                                        <button class="att-btn <?= ($status == 'A') ? 'active-A' : '' ?>"
                                            data-id="<?= $stu_id ?>" data-status="A">A</button>
                                        <button class="att-btn <?= ($status == 'L') ? 'active-L' : '' ?>"
                                            data-id="<?= $stu_id ?>" data-status="L">L</button>
                                    </div>
                                    <?php
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
    $(document).ready(function () {
        $('.att-btn').on('click', function () {
            let btn = $(this);
            let stu_id = btn.data('id');
            let status = btn.data('status');
            let att_date = '<?= $att_date ?>';

            $.ajax({
                url: 'system/master_process.php?task=save_single_attendance',
                type: 'POST',
                data: { student_id: stu_id, att_date: att_date, status: status },
                success: function (res) {
                    let data = JSON.parse(res);
                    if (data.status === 'success') {
                        btn.parent().find('.att-btn').removeClass('active-P active-A active-L active-C');
                        btn.addClass('active-' + status);
                    }
                }
            });
        });

        $('#mark-all-p').on('click', function () {
            if (confirm("Mark all students as Present?")) {
                let stu_ids = [];
                $('.att-btn[data-status="P"]').each(function () {
                    stu_ids.push($(this).data('id'));
                });

                $.ajax({
                    url: 'system/master_process.php?task=mark_all_present',
                    type: 'POST',
                    data: { student_ids: stu_ids, att_date: '<?= $att_date ?>' },
                    success: function (res) {
                        location.reload();
                    }
                });
            }
        });

        $('#clear-all-att').on('click', function () {
            if (confirm("Clear attendance for all students for this day?")) {
                let stu_ids = [];
                $('.att-btn[data-status="P"]').each(function () {
                    stu_ids.push($(this).data('id')); // send all student IDs
                });

                $.ajax({
                    url: 'system/master_process.php?task=clear_all_attendance',
                    type: 'POST',
                    data: { student_ids: stu_ids, att_date: '<?= $att_date ?>' },
                    success: function (res) {
                        location.reload();
                    }
                });
            }
        });

        $('#copy-prev-btn').on('click', function () {
            if (confirm("Copy attendance from the previous working day?")) {
                $.ajax({
                    url: 'system/master_process.php?task=copy_prev_attendance',
                    type: 'POST',
                    data: {
                        student_class: '<?= $student_class ?>',
                        student_section: '<?= $student_section ?>',
                        att_date: '<?= $att_date ?>'
                    },
                    success: function (res) {
                        let data = JSON.parse(res);
                        if (data.status === 'success') {
                            location.reload();
                        } else {
                            alert(data.msg);
                        }
                    }
                });
            }
        });
    });
</script>