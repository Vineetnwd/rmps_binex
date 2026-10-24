<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
$item_id = $_GET['item_id'];
$item = get_data('inventory_item', $item_id)['data'];
$ostock = $item['opening_stock'];
$cstock = $item['current_stock'];
$pstock  = total_purchase_count($item_id);
$sstock  = total_sale_count($item_id);
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Item History
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active">Item History</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?= $item['name'] ?> 
                        
                        </h3>
                        <div class='float-right'>
                        <span class='btn bg-primary'> Opening: <?= $ostock ?></span>
                        <span class='btn bg-success'> Purchase: <?= $pstock ?> </span>
                        <span class='btn bg-danger'>Sale: <?= $sstock ?> </span>
                        <span class='btn bg-dark text-light'>Current Stock: <?= $cstock ?></span> 
                        </div>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class='row'>
                            <div class='col-md-4'>
                               <h3> Purchase History </h3>
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr class='op-thead'>
                                            <th>Date </th>
                                            <th>Quantity</th>
                                            <th>Amount</th>
                                         
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $sql ="select * from purchase_item where name='$item_id'";
                                    $res = direct_sql($sql);
                                    $total =0;
                                    foreach((array)$res['data'] as $row)
                                    {
                                         $total += ($row['rate']*$row['quantity']);
                                        echo "<tr>";
                                        echo "<td>". date('d-M-Y', strtotime($row['doi'])) ."</td>";
                                        echo "<td>". $row['quantity'] ."</td>";
                                        echo "<td>". $row['rate'] ."</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                    </tbody>
                                      <tfoot>
                                        <tr bgcolor='orange'>
                                            <th colspan='2'> Total </th>
                                            <th><?= $total ?> </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class='col-md-8'>
                                 <h3>Distribution History </h3>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr class='op-thead'>
                                            <th>Date </th>
                                            <th>Admission No. </th>
                                            <th>Name </th>
                                            <th>Quantity</th>
                                            <th>Rate</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                     $sql ="select * from distribute_item where item_id='$item_id' and status ='ACTIVE' ";
                                    $res = direct_sql($sql);
                                    $total =0;
                                    foreach((array)$res['data'] as $row)
                                    {
                                        $total +=$row['amount'];
                                        $student = get_data('student', $row['student_id'])['data'];
                                        echo "<tr>";
                                        echo "<td>". date('d-M-Y', strtotime($row['date'])) ."</td>";
                                        echo "<td>". $student['student_admission'] ."</td>";
                                        echo "<td>". $student['student_name'] ."</td>";
                                        echo "<td>". $row['qty'] ."</td>";
                                        echo "<td>". $row['rate'] ."</td>";
                                        echo "<td>". $row['amount'] ."</td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                   
                                    </tbody>
                                    <tfoot>
                                        <tr bgcolor='orange'>
                                            <th colspan='5'> Total </th>
                                            <th><?= $total ?> </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
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