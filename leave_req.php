<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Leave Details</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item ">Employee Mgmt.</li>
            <li class="breadcrumb-item active">Leave Details</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Leave Details </h3>
                <div class="box-tools pull-right">
                    <!-- <a class='btn btn-success btn-sm' title='New Employee' href='add_employee'> <i class='fa fa-plus'>  </i> Add New</a> -->
                </div>
            </div>
            <!-- /.box-header -->

            <div class="box-body">

                <div class="table-responsive">
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr class='op-thead'>
                                <th> # </th>
                                <th> Name </th>
                                <th> From</th>
                                <th> To</th>
                                <th> Status</th>
                                <th> Operation </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $i = 1;
                            $sql = "SELECT * from leave_details where status NOT IN('AUTO','BLOCK')";
                            $res = direct_sql($sql);
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $emp_id = $row['emp_id'];
                                    $id = $row['id'];
                                    $emp = get_data('employee',$emp_id,null)['data'];
                                    echo "<tr>";
                                    echo "<td>" . $i . "</td>";
                                    echo "<td>" . $emp['name'] . "</td>";
                                    echo "<td>" .$row['from_date']. "</td>";
                                    echo "<td>" .$row['to_date']. "</td>";
                                    echo "<td>" . show_status($row['status']) . "</td>";
                            ?>
                                    <td>
                                        <?//= btn_view('leave_details', $id, $row['name']) ?>
                                        <!-- <a href='leave_details.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a> -->
                                        <span class='delete_btn btn btn-danger btn-sm' data-table='leave_details' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
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