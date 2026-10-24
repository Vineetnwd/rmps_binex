<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Manage Employee</h1>
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
                <h3 class="box-title">Employee Recruitment </h3>
                <div class="box-tools pull-right">
                    <a class='btn btn-success btn-sm' title='New Employee' href='add_recruitment'> <i class='fa fa-plus'>  </i> Add New</a>
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class='op-thead'>
                                <th> # </th>
                                <th> Emp Code </th>
                                <th> Name </th>
                                <th> Mobile</th>
                                <th> Allowed Class</th>
                                <th> Designation</th>
                                <th> Preferred Std.</th>
                                <th> Operation </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $i = 1;
                            $sql = "SELECT * from recruitment where status NOT IN('AUTO','BLOCK')";
                            $res = direct_sql($sql);
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    echo "<tr>";
                                    echo "<td>" . $i . "</td>";
                                    echo "<td>" . $row['app_no'] . "</td>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td>" . $row['mobile'] . "</td>";
                                    echo "<td>" . $row['qualification'] . "</td>";
                                    echo "<td>" . $row['subject'] . "</td>";
                                    echo "<td>" . $row['class_preferred'] . "</td>";
                            ?>
                                    <td>
                                        <?= btn_view('recruitment', $id, $row['name']) ?>
                                        <a href='add_recruitment.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                        <span class='delete_btn btn btn-danger btn-xs' data-table='recruitment' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
                                        <a href='<?= $base_url.'system/upload/'.$row['resume'] ?>' class='fa fa-download btn btn-dark btn-xs' download></i></a>
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