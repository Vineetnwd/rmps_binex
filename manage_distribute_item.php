<?php require_once('system//header.php'); ?>
<?php require_once('system/menu.php');
$from_date = (isset($_REQUEST['from_date']) and $_REQUEST['from_date'] !='')?$_REQUEST['from_date']:$today;
$to_date = (isset($_REQUEST['to_date']) and $_REQUEST['to_date'] !='')?$_REQUEST['to_date']:$today;
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Sell Report
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active">Sell Item</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                       
                        <form>
                             <h3 class="box-title">Details of Sell Items </h3>
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
                                        <th>Invoice Date</th>
                                        <th>Invoice No.</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Total</th>
                                        <th>Paid Amount</th>
                                        <th>Payment Mode</th>
                                        <!--<th>Remarks</th>-->
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if(isset($_REQUEST['from_date']) and isset($_REQUEST['to_date']))
                                    {
                                        $sql="select * from invoice where txn_date between '$from_date' and '$to_date' and status ='CLOSED'";
                                        $res = direct_sql($sql);
                                    }
                                    else{
                                        $res = get_all('invoice', "*", array('status' => 'CLOSED'));
                                    }
                                    if ($res['count'] > 0) {
                                        $cash =0;
                                        $bank =0;
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            $inv_no = $row['inv_no'];
                                            
                                            if ($row['payment_mode']=='CASH')
                                            {
                                               $cash +=$row['payment'];
                                            }
                                            else{
                                               $bank +=$row['payment'];
                                            }
                                            $student_name = get_data('student', $row['student_id'], 'student_name')['data'];
                                            $student_class = get_data('student', $row['student_id'], 'student_class')['data'];
                                            $student_section = get_data('student', $row['student_id'], 'student_section')['data'];
                                    ?>
                                            <tr>
                                                <td><?php echo $row['txn_date']; ?></td>
                                                <td><?php echo $inv_no; ?></td>
                                                <td><?php echo $student_name; ?></td>
                                                <td><?php echo $student_class . "-" . $student_section; ?></td>
                                                <td><?php echo $row['total']; ?></td>
                                                <td><?php echo $row['payment']; ?></td>
                                                <td><?php echo $row['payment_mode']; ?></td>
                                                <!--<td><?php echo $row['remarks']; ?></td>-->
                                                <td><?php echo $row['status']; ?></td>
                                                <td class='text-right'>
                                                    <a href="<?php echo "distribute_item_receipt?link=" . encode('student_id=' . $row['student_id'] . '&inv_id=' . $id) ?>" class="btn btn-warning btn-sm" target='_blank'><i class='fa fa-print'></i></i></a>
                                                    <a class="cancel_invoice_btn btn btn-danger btn-sm text-light" title="Cancel/Reverse Receipt" data-id="<?php echo $row['student_id']; ?>" data-inv="<?php echo $id; ?>"><i class="fa fa-ban"></i></a>
                                                </td>
                                            </tr>
                                    <?php }
                                    } ?>
                                </tbody>
                                <tfoot>
                                    <tr class='bg-gray'>
                                        <td colspan='3'>
                                            Total : ₹<?=$cash + $bank ?>
                                        </td>
                                        <td colspan='2'>
                                            Cash : ₹<?= $cash ?>
                                        </td>
                                        <td colspan='2'>
                                           Bank : ₹<?= $bank ?>
                                        </td>
                                        <td colspan='2'>
                                            
                                        </td>
                                       
                                    </tr>
                                    
                                </tfoot>
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