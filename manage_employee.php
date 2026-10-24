<style>
.emp_photo {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #3b82f6;
}    
.dt-buttons {
    margin-bottom: 10px;
}
</style>
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
                <h3 class="box-title">Employee Details </h3>
                <div class="box-tools pull-right">
                    <a class='btn btn-success btn-sm' title='New Employee' href='add_employee'> <i class='fa fa-plus'>  </i> Add New</a>
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
                                <th> Photo </th>
                                <th> Mobile</th>
                                <th> Allowed Marks Entry</th>
                                <th> Designation</th>
                                <th> Status</th>
                                <th> Operation </th>
                            </tr>
                        </thead>
                        <tbody>

                            <?php
                            $i = 1;
                            $sql = "SELECT * from employee where branch_id='{$_SESSION['branch_id']}' and status NOT IN('AUTO','BLOCK')";
                            $res = direct_sql($sql);
                            if ($res['count'] > 0) {
                                foreach ($res['data'] as $row) {
                                    $id = $row['id'];
                                    echo "<tr>";
                                    echo "<td>" . $i . "</td>";
                                    echo "<td>" . $row['emp_code'] . "</td>";
                                    echo "<td>" . $row['name'] . "</td>";
                                    echo "<td><img src='".$base_url.'upload/'.$row['photo']."' class='emp_photo'></td>";
                                    echo "<td>" . $row['mobile'] . "</td>";
                                    echo "<td>" . $row['marks_entry'] . "</td>";
                                    echo "<td>" . $row['designation'] . "</td>";
                                    echo "<td>" . show_status($row['status']) . "</td>";
                            ?>
                                    <td>
                                        <?= btn_view('employee', $id, $row['name']) ?>
                                        <a href='add_employee.php?link=<?php echo encode('id=' . $id); ?>' class='fa fa-edit btn btn-info btn-xs'></a>
                                        <span class='delete_btn btn btn-danger btn-sm' data-table='employee' data-id='<?php echo $id; ?>' data-pkey='id'><i class='fa fa-trash'></i></span>
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
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<?php require_once('system/footer2.php'); ?>
<script>
 $(document).ready(function() {

    if ($.fn.DataTable.isDataTable('#example1')) {
        $('#example1').DataTable().destroy();
    }

    $('#example1').DataTable({
        dom: 'lBfrtip',
        pageLength: 50,
        buttons: [
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                className: 'btn btn-success ml-2',
                exportOptions: {
                    columns: [1,2,4,5,6,7]
                }
            }
        ]
    });

});
</script>
