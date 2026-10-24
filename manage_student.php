<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
extract(post_clean($_GET));
$table_name = 'student';
$filters = array();

// Decode from link if exists
if (isset($_GET['link']) && $_GET['link'] != '') {
    $data = decode($_GET['link']);
    $student_class = $data['student_class'] ?? '';
    $student_section = $data['student_section'] ?? '';
    if ($student_class != '')
        $filters['student_class'] = $student_class;
    if ($student_section != '')
        $filters['student_section'] = $student_section;
}

// Direct GET filters 
if (isset($_GET['status']) && $_GET['status'] != '') {
    $filters['status'] = $_GET['status'];
} else {
    $filters['status'] = 'ACTIVE';
}

if (isset($_GET['student_class']) && $_GET['student_class'] != '') {
    $filters['student_class'] = $_GET['student_class'];
}
if (isset($_GET['student_section']) && $_GET['student_section'] != '') {
    $filters['student_section'] = $_GET['student_section'];
}
if (isset($_GET['academic_type']) && $_GET['academic_type'] != '') {
    $filters['academic_type'] = $_GET['academic_type'];
} else {
    $filters['academic_type'] = $academic_type = 'REGULAR';
}

// Final result
if (!empty($filters)) {
    $res = get_all('student', '*', $filters);
} else {
    $branch_id = $_SESSION['branch_id'];
    $sql = "SELECT * FROM student WHERE academic_type = 'REGULAR' AND status NOT IN ('AUTO','DELETED','BLOCKED') AND branch_id = '{$branch_id}'";
    $res = direct_sql($sql);
}
if ($_SESSION['branch_id'] == 1) {
    $img_url = $base_url . 'student_photo/Bishambharpur/Photo/';
} else if ($_SESSION['branch_id'] == 2) {
    $img_url = $base_url . 'student_photo/Barauli/Photo/';
} else {
    $img_url = $base_url . 'student_photo/Barharia/Photo/';
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Manage Student
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Student</li>
            <li class="breadcrumb-item active">Manage Student</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">
                <div class="box">
                    <div class="box-header with-border">

                        <div class="row">

                            <!-- LEFT SIDE (Actions) -->
                            <div class="col-md-6">

                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="task" value="new_roll">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        ASSIGN NEW ROLL
                                    </button>
                                </form>

                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="distribute" value="yes">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        Distribute Student
                                    </button>
                                </form>

                                <div style="margin-top:8px;">
                                    <?php
                                    if (isset($_POST['task']) && $_POST['task'] == 'new_roll') {
                                        $ct = new_roll_no($student_class, $student_section, 'student_name');
                                        echo "<span class='text-success'>$ct Student Roll Updated</span>";
                                    }

                                    if (isset($_POST['distribute']) && $_POST['distribute'] == 'yes') {
                                        distribute_student($student_class);
                                        echo "<span class='text-primary'>Students Distributed Successfully</span>";
                                    }
                                    ?>
                                </div>

                            </div>

                            <!-- RIGHT SIDE (Filters) -->
                            <div class="col-md-6 text-right">

                                <form method="get" class="form-inline">

                                    <select name="student_class" class="form-control input-sm">
                                        <?php dropdown($class_list, $student_class); ?>
                                    </select>

                                    <select name="student_section" class="form-control input-sm">
                                        <?php dropdown($section_list, $student_section); ?>
                                    </select>

                                    <select name="status" class="form-control input-sm">
                                        <?php dropdown($status_list, $status); ?>
                                    </select>

                                    <select name="academic_type" class="form-control input-sm">
                                        <?php dropdown($academic_list, $academic_type); ?>
                                    </select>

                                    <button class="btn btn-warning btn-sm p-2">
                                        Show
                                    </button>

                                </form>

                            </div>

                        </div>

                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example" class="table table-bordered table-striped">
                                <thead>
                                    <tr class='op-thead'>
                                        <th>Photo</th>
                                        <th>Adm. No.</th>
                                        <th>Name</th>
                                        <th>Father's Name</th>
                                        <th>Class -Section </th>
                                        <th>Roll</th>
                                        <th>Mobile No</th>
                                        <th>Student Type</th>
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            $link = encode('student_name=' . $row['student_name'] . '&id=' . $row['id']);
                                            ?>
                                            <tr>
                                                <td><img src='<?= $base_url . '/upload/' . $row['student_photo'] ?>'
                                                        style="width:40px;height:40px;border-radius:50%;"></td>
                                                <td><?php echo $row['student_admission'] ?></td>
                                                <td><?php echo $row['student_name'] ?></td>
                                                <td><?php echo $row['student_father'] ?></td>
                                                <td><?php echo $row['student_class'] . "-" . $row['student_section'] ?></td>
                                                <td><?php echo $row['student_roll']; ?></td>
                                                <td><span class='mobile'><?php echo $row['student_mobile'] ?></span></td>
                                                <td><?php echo $row['student_type'] ?></td>
                                                <td><?= show_status($row['status']) ?></td>
                                                <td class='text-right'>
                                                    <?php echo btn_view('student', $id, $row['student_name']); ?>
                                                    <a class='fa fa-print btn btn-dark btn-xs'
                                                        href='student_slip?link=<?php echo $link; ?>' target='_op'></a>
                                                    <?php if ($row['status'] == 'ACTIVE') { ?>
                                                        <?php echo btn_edit('add_student', $id); ?>

                                                    <?php } ?>

                                                    <?php if (($user_type = 'ADMIN' or $user_type == 'DEV') and $row['status'] != 'ACTIVE') { ?>
                                                        <?php echo btn_edit('add_student', $id); ?>
                                                        <?php echo btn_delete($table_name, $id); ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php }
                                    } ?>
                                </tbody>
                                <!--<tfoot>
                        <tr>
                            <th>Name</th>
                            <th>Position</th>
                            <th>Office</th>
                            <th>Age</th>
                            <th>Start date</th>
                            <th>Salary</th>
                        </tr>
                    </tfoot>-->
                            </table>
                        </div>
                    </div>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->
<?php require_once('system/footer2.php'); ?>
<script>
    $(document).ready(function () {

        if ($.fn.DataTable.isDataTable('#example')) {
            $('#example').DataTable().destroy();
        }

        $('#example').DataTable({
            dom: 'lBfrtip',   // ✅ shows BOTH dropdown + buttons

            pageLength: 10,

            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],

            buttons: [
                {
                    extend: 'excelHtml5',
                    text: 'Export Excel',
                    className: 'btn btn-success ml-2'
                },
                {
                    extend: 'print',
                    text: 'Print',
                    className: 'btn btn-primary'
                }
            ]
        });

    });
</script>