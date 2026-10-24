<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$is_admin = in_array($user_type, ['ADMIN', 'DBA', 'DEV']);
$is_class_teacher = false;
$emp_cls = '';

if ($user_type == 'TEACHER') {
    $emp = get_data('employee', $_SESSION['user_name'], null, 'emp_code')['data'];
    if (!empty($emp['class_teacher'])) {
        $is_class_teacher = true;
        $emp_cls = $emp['class_teacher'];
    }
}

$emp_class = '';
$emp_section = '';
if (!empty($emp_cls)) {
    $parts = explode('-', $emp_cls);
    $emp_class = trim($parts[0] ?? '');
    $emp_section = trim($parts[1] ?? '');
}

// Block access for teachers who are not class teachers
if (!$is_admin && !$is_class_teacher) {
    echo "<div class='content-wrapper'><section class='content'><div class='alert alert-danger'>Access Denied: Only Assigned Class Teachers can access this page.</div></section></div>";
    require_once('system/footer2.php');
    exit;
}

$exam_name = $student_class = $student_section = null;

if (isset($_REQUEST['exam_id'])) {
    $exam_id = $_REQUEST['exam_id'];
    $exam_name = get_data('exam_list', $exam_id, 'exam_name')['data'];
}

if (isset($_REQUEST['student_class'])) {
    $student_class = $_REQUEST['student_class'];
}

if (isset($_REQUEST['student_section'])) {
    $student_section = $_REQUEST['student_section'];
}

$subject_list = [];
if ($student_class) {
    $subject_list = co_subject_list($student_class);
}

?>
<style>
    @media print {
        body * {
            visibility: hidden;
        }

        #tbl_a,
        #tbl_a * {
            visibility: visible;
        }

        #tbl_a {
            page-break-inside: avoid;
            position: absolute;
            left: 0;
            top: 20;
        }

        input[type='text'] {
            border: solid 1px #ddd;
        }
    }
</style>
<title> <?php echo $inst_name . " " . ($exam_name ?? '') . " " . ($student_class ?? ''); ?> </title>

<div class="content-wrapper">
    <section class="content-header">
        <h1> Co-Scholastic Area Grade Updates </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="breadcrumb-item"><a href="#student">Student Management</a></li>
            <li class="breadcrumb-item active">Co-Scholastic Grades</li>
        </ol>
    </section>

    <section class="content">
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Manage Grades</h3>
                <div class="box-tools pull-right">
                    <!-- <?php if ($is_admin): ?>
                        <a href="subject_setting" target="_blank" class="btn btn-primary btn-md mr-2">Manage Subjects</a>
                    <?php endif; ?> -->
                    <button id='save_grades_btn' class='btn btn-success btn-md' onclick="submitGradesForm()">Save
                        Grades</button>
                </div>
            </div>
            <div class="box-body">
                <form action='' method='get'>
                    <div class="row">
                        <div class="col-lg-3 col-offset-lg-2">
                            <div class="form-group">
                                <label>Select Class</label>
                                <select class="form-control" name='student_class' required
                                    onChange='selsub(this.value)'>
                                    <?php if ($is_admin): ?>
                                        <?php dropdown($class_list, $student_class); ?>
                                    <?php elseif ($is_class_teacher && !empty($emp_class)): ?>
                                        <option value="<?= htmlspecialchars($emp_class) ?>" <?= ($student_class == $emp_class) ? 'selected' : '' ?>><?= htmlspecialchars($emp_class) ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Section</label>
                                <select class="form-control" name='student_section' required>
                                    <?php if ($is_admin): ?>
                                        <?php dropdown($section_list, $student_section); ?>
                                    <?php elseif ($is_class_teacher && !empty($emp_section)): ?>
                                        <option value="<?= htmlspecialchars($emp_section) ?>" <?= ($student_section == $emp_section) ? 'selected' : '' ?>><?= htmlspecialchars($emp_section) ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="form-group">
                                <label>Select Exam</label>
                                <select class="form-control" name='exam_id' required>
                                    <option value=''> Choose An Exam </option>
                                    <?php dropdown_list('exam_list', 'id', 'exam_name', @$exam_id); ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-dark btn-block">Show List</button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="table-responsive">
                <?php if (isset($_REQUEST['exam_id']) && $student_class): ?>
                    <form action='save_co_scholastic_grades' method='post' id='grades_frm'>
                        <input type='hidden' name='exam_id' value='<?php echo $exam_id; ?>'>
                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <td colspan='<?= count($subject_list) + 3 ?>' align='center'
                                        class='bg-yellow text-dark text-bold'>
                                        Enter Grades for
                                        <?= htmlspecialchars($exam_name) . ' of ' . htmlspecialchars($student_class) . '-' . htmlspecialchars($student_section); ?>
                                    </td>
                                </tr>
                                <tr class='op-thead'>
                                    <th> Adm No. </th>
                                    <th> Roll </th>
                                    <th> Name </th>
                                    <?php
                                    foreach ((array) $subject_list as $subject) {
                                        echo "<th>" . htmlspecialchars($subject['subject_name']) . "</th>";
                                    }
                                    ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = get_all('student', '*', ['student_class' => $student_class, 'student_section' => $student_section, 'status' => 'ACTIVE'], 'student_roll');

                                if ($res['count'] > 0) {
                                    foreach ($res['data'] as $row) {
                                        $sid = $row['id'];

                                        echo "<tr>";
                                        echo "<td>
                                            <input type='hidden' name='student_ids[]' value='$sid'>
                                            " . htmlspecialchars($row['student_admission']) . "
                                          </td>";
                                        echo "<td>" . htmlspecialchars($row['student_roll']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['student_name']) . "</td>";

                                        foreach ((array) $subject_list as $subject) {
                                            $sub_id = $subject['id'];

                                            // Fetch existing grade for this student, exam, and subject
                                            $grade_sql = "SELECT grade FROM co_scholastic_grades WHERE exam_id='$exam_id' AND student_id='$sid' AND subject_id='$sub_id'";
                                            $grade_res = direct_sql($grade_sql);
                                            $existing_grade = ($grade_res['count'] > 0) ? $grade_res['data'][0]['grade'] : '';

                                            echo "<td><select name='grades[$sid][$sub_id]' class='form-control'>";
                                            dropdown($grade_list, $existing_grade);
                                            echo "</select></td>";
                                        }
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='" . (count($subject_list) + 3) . "' align='center'>No students found.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<script>
    const classSections = <?php echo json_encode(explode(',', $emp_cls ?? '')); ?>;
    const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;

    function populateClasses() {
        if (isAdmin) return;
        const classDropdown = document.querySelector("select[name='student_class']");
        classDropdown.innerHTML = "<option value='' disabled selected>Select Class</option>";
        classSections.forEach(item => {
            const parts = item.split("-");
            if (parts.length < 2) return;
            const className = parts[0];
            if (!Array.from(classDropdown.options).some(opt => opt.value === className)) {
                const option = document.createElement("option");
                option.value = className;
                option.textContent = className;
                if (className === "<?php echo $student_class ?? ''; ?>") option.selected = true;
                classDropdown.appendChild(option);
            }
        });
    }

    function selsub(selectedClass) {
        if (isAdmin) return;
        const sectionDropdown = document.querySelector("select[name='student_section']");
        sectionDropdown.innerHTML = "<option value='' disabled selected>Select Section</option>";
        classSections.forEach(item => {
            const parts = item.split("-");
            if (parts.length < 2) return;
            const className = parts[0];
            const section = parts[1];
            if (className === selectedClass) {
                const option = document.createElement("option");
                option.value = section;
                option.textContent = section;
                if (section === "<?php echo $student_section ?? ''; ?>") option.selected = true;
                sectionDropdown.appendChild(option);
            }
        });
    }

    window.onload = function () {
        populateClasses();
        <?php if (!empty($student_class)): ?>
            selsub("<?php echo $student_class; ?>");
        <?php endif; ?>
    };

    function submitGradesForm() {
        if (!$('#grades_frm').length) {
            alert("Please filter and show the list first.");
            return;
        }

        var formData = new FormData(document.getElementById('grades_frm'));
        $.ajax({
            url: $('#grades_frm').attr('action'),
            type: 'POST',
            data: formData,
            success: function (data) {
                var res = JSON.parse(data);
                if (res.status == 'success') {
                    alert('Grades saved successfully!');
                } else {
                    alert('Error: ' + res.msg);
                }
            },
            cache: false,
            contentType: false,
            processData: false
        });
    }
</script>

<?php require_once('system/footer2.php'); ?>