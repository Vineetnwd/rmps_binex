<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php'); 
$from_date = (isset($_REQUEST['from_date']) and $_REQUEST['from_date'] !='')?$_REQUEST['from_date']:$today;
$to_date = (isset($_REQUEST['to_date']) and $_REQUEST['to_date'] !='')?$_REQUEST['to_date']:$today;
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Purchase Report
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Purchase</li>
            <li class="breadcrumb-item active">Purchase Report </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <form>
                             <h3 class="box-title">Details of Purchase Items </h3>
                        <span  class='float-right'>     
                        <input type='date' name='from_date' value='<?= $from_date ?>'>
                        <input type='date' name='to_date' value='<?= $to_date ?>' >
                        <button class='btn btn-sm btn-danger'> Show </button>
                        </span>
                        </form>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr class='op-thead'>
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Item Name</th>
                                        <th>Vendor Name</th>
                                        <th>Quantity</th>
                                        <th>Rate</th>
                                        <th>MRP</th>
                                        <th>D.O.P</th>
                                        <th>STATUS</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Retrieve data from the database
                                    $sql = "SELECT pi.*, ii.name AS item_name, vt.name
                                            FROM purchase_item pi
                                            JOIN inventory_item ii ON pi.name = ii.id
                                            JOIN vendor vt ON pi. vendor = vt.id
                                            WHERE pi.status NOT IN ('AUTO','DELETED','REMOVED')
                                            and pi.doi between '$from_date' and '$to_date'
                                            and pi.branch_id ='{$_SESSION['branch_id']}' ORDER BY pi.name";
                                    $res = direct_sql($sql,'get',false);
                                    
                                    // Check if there are any rows returned
                                    if ($res['count'] > 0) {
                                        // Loop through each row
                                        $i = 1;
                                        foreach ($res['data'] as $row) {
                                    ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $row['id']; ?></td>
                                                <td><?php echo $row['item_name']; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $row['quantity']; ?></td>
                                                <td><?php echo $row['rate']; ?></td>
                                                <td><?php echo $row['mrp']; ?></td>
                                                <td><?php echo $row['doi']; ?></td>
                                                <td><?php echo show_status($row['status']); ?></td>
                                                <td class='text-right'>
                                                    <?php
                                                    // Display action buttons
                                                    echo btn_view('purchase_item', $row['id'], $row['name']);
                                                    echo ($row['status'] !=='CANCELLED')?'<a class="cancel_entry_btn btn btn-danger btn-sm text-light" title="Cancel/Reverse Receipt" data-name="'. $row['id'].'" data-id="'.$row['id'].'"><i class="fa fa-ban"></i></a>':'';
                                                    
                                                    
                                                    ?>
                                                </td>
                                            </tr>
                                    <?php 
                                            $i++;
                                        }
                                    } else {
                                        // No records found
                                        echo "<tr><td colspan='8'>No records found</td></tr>";
                                    }
                                    ?>
                                </tbody>
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


