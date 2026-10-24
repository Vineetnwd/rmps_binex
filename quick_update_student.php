<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>

<?php
extract(post_clean($_GET));
$student_class = $_GET['student_class'] ?? '';
$student_section = $_GET['student_section'] ?? '';
?>

<div class="content-wrapper">
    <section class="content-header">
        <h1>Manage Student</h1>
        <ol class="breadcrumb">
            <li><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li>Student</li>
            <li class="active">Manage Student</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">

                    <div class="box-header with-border">
                        <form method="get" class="form-inline pull-right">
                            <select name="student_class" class="form-control" required>
                                <?php dropdown($class_list, $student_class); ?>
                            </select>
                            <select name="student_section" class="form-control">
                                <?php dropdown($section_list, $student_section); ?>
                            </select>
                            <button type="submit" class="btn btn-warning">Show</button>
                        </form>

                        <form method="post">
                            <input type="hidden" name="task" value="new_roll">
                            <input type="hidden" name="student_class" value="<?= $student_class ?>">
                            <input type="hidden" name="student_section" value="<?= $student_section ?>">
                            <h3 class="box-title">
                                Manage Student
                                <button type="submit" class="btn btn-success btn-sm">ASSIGN NEW ROLL</button>
                                <?php
                                if (isset($_POST['task']) && $_POST['task'] == 'new_roll') {
                                    $ct = new_roll_no($_POST['student_class'], $_POST['student_section']);
                                    echo "<span class='text-success'> $ct Student Roll Updated</span>";
                                }
                                ?>
                            </h3>
                        </form>
                    </div>

                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="studentTable" class="display nowrap" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Father</th>
                                        <th>Mobile</th>
                                        <th>Class</th>
                                        <th>Section</th>
                                        <th>Roll</th>
                                        <th>Email</th>
                                        <th>DOB</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</div>

<?php require_once('system/footer2.php'); ?>

<script>
$(document).ready(function () {
    const studentClass = '<?= $student_class ?>';
    const studentSection = '<?= $student_section ?>';

    $('#studentTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: 'student_data.php',
            type: 'GET',
            data: function (d) {
                d.student_class = studentClass;
                d.student_section = studentSection;
            }
        },
        dom: 'Bfrtip',
        buttons: ['colvis'],
        scrollX: true,
        columns: [
            { data: 'id' },
            { data: 'student_name' },
            { data: 'student_father' },
            { data: 'student_mobile' },
            { data: 'student_class' },
            { data: 'student_section' },
            { data: 'student_roll' },
            { data: 'student_email' },
            { data: 'date_of_birth' },
            { data: 'status' }
        ]
    });
});
</script>
