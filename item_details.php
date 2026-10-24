<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Stock Details
        </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item">Inventory</li>
            <li class="breadcrumb-item active">Item Stock</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-12">

                <div class="box">
                    <div class="box-header with-border">
                        <h3 class="box-title">Items Stock</h3>
                    </div>
                    <!-- /.box-header -->
                    <div class="box-body">
                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr class='op-thead'>
                                        <th>#</th>
                                        <th>Item Detail</th>
                                        <th>Opening Stock</th>
                                        <th>Total Purchase</th>
                                        <th>Total Sale </th>
                                        <th>Current Stock</th>
                                        <!--<th>Used/Sale</th>-->
                                        <th>Status</th>
                                        <th class='text-right'>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $sql = "SELECT * from inventory_item where status not in('AUTO','DELETED','REMOVED') ORDER BY name";
                                    $res = direct_sql($sql);
                                    // $res = get_all('inventory_item');
                                    if ($res['count'] > 0) {
                                        foreach ($res['data'] as $row) {
                                            $id = $row['id'];
                                            $ostock = $row['opening_stock'];
                                            $cstock = $row['current_stock'];
                                            $pstock  = total_purchase_count($id);
                                            $sstock  = total_sale_count($id);
                                            
                                            $astock = ($ostock+ $pstock) -$sstock;
                                           
                                            if($cstock == 0){
                                                update_data('inventory_item',array('status'=>'OUT OF STOCK', 'current_stock' =>intval($astock) ),$id);
                                            }else{
                                                update_data('inventory_item',array('status'=>'IN STOCK', 'current_stock' =>intval($astock) ),$id);
                                            }
                                    ?>
                                            <tr>
                                                <td><?php echo $i; ?></td>
                                                <td><?php echo $row['name']; ?></td>
                                                <td><?php echo $ostock; ?></td>
                                                <td><?php echo $pstock; ?></td>
                                                <td><?php echo $sstock; ?></td>
                                                
                                                <td> <?= ($cstock>0) ?"<span class='badge bg-success'>":"";?> <?php echo $cstock; ?></span></td>
                                                 <td><?php echo $row['status']; ?></td>
                                                <td class='text-right'>
                                                    <?php
                                                    echo btn_view('inventory_item', $id, $row['name']);
                                                    ?>
                                                    <a href="item_history?item_id=<?=$id?>" class='btn btn-danger btn-sm'>View </a>
                                                </td>
                                            </tr>

                                    <?php $i++;
                                        }
                                    } ?>
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