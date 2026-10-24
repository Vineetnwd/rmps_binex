<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Teacher Subject</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item active">Employee</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Teacher Subject Details </h3>
                <div class="box-tools pull-right">
                    <a class='btn btn-success btn-sm' title='New Employee' href='add_teacher_subject'> <i class='fa fa-plus'>  </i> Add New</a>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class='op-thead'>
                                <th> # </th>
                                <th> Teacher </th>
                                <th> Class </th>
                                <th> Section</th>
                                <th> Subject</th>
                                <th> Status</th>
                                <th> Operation </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $i = 1;
                            $sql = "SELECT * from teacher_subject where branch_id='{$_SESSION['branch_id']}' and status NOT IN('AUTO','BLOCK')";
                            $res = direct_sql($sql);
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    echo "<tr>";
                                    echo "<td>" . $i . "</td>";
                                    echo "<td>" . get_data('employee',$row['teacher_id'],'name')['data'] . "</td>";
                                    echo "<td>" . $row['class_name'] . "</td>";
                                    echo "<td>" . $row['section_name'] . "</td>";
                                    echo "<td>" . get_data('subject',$row['subject_name'],'subject_name')['data'] . "</td>";
                                    echo "<td>" . show_status($row['status']) . "</td>";
                            ?>
                                    <td>
                                        <?= btn_view('teacher_subject', $id, $row['name']) ?>
                                        <a href='add_teacher_subject.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                        <span class='delete_btn btn btn-danger btn-sm' data-table='teacher_subject' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
                                    </td>
                            <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

            </div>
    </section>
</div>
<?php require_once('system/footer2.php'); ?>