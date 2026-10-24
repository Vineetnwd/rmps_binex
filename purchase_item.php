<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
$table_name = 'purchase_item';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $item = decode($_GET['link']);
    $id = $item['id'];
} else {
    $item = insert_row($table_name);
    $id = $item['id'];
}
if ($id != '') {
    $res = get_data($table_name, $name);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}
$doi  = (isset($doi) and $doi !='')?$doi:$today;

?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Purchase Item Details </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Purchase Item</a></li>
            <li class="breadcrumb-item active">New Item</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Purchase Item Details </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn' name='save'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form id='update_frm' action='purchase_item'>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Item Name</label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <select name="name" id="product_id" required class="form-control " type='text'>
                                        <?php dropdown_list('inventory_item', 'id', 'name') ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Select Vendor</label>
                                <div class="col-sm-8">
                                    <select name="vendor" id="cat_id" required class="form-control ">
                                        <?php dropdown_list('vendor', 'id', 'name') ?>
                                    </select>
                                </div>
                            </div>
                            <!--<div class="form-group row">-->
                            <!--    <label for="example-text-input" class="col-sm-4 col-form-label">Select Vendor Cat.</label>-->
                            <!--    <div class="col-sm-8">-->
                            <!--        <select name="vendor_cat" id="id" required class="form-control ">-->
                            <!--            <?php dropdown_list('vendor_cat', 'id', 'cat_name') ?>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Quantity</label>
                                <div class="col-sm-8">
                                    <input type="number" min="1" name="quantity" required class="form-control " value="quantity">
                                </div>
                            </div>
                           
                        </div>
                        <div class="col-lg-6">
                             <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Purchase Rate</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number" name="rate" id='rate' required>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Invoice Date</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="date"  name="doi" id="doi"  value='<?= $doi ?>' required>
                                </div>
                            </div>
                            <!--<div class="form-group row">-->
                            <!--    <label for="example-text-input" class="col-sm-4 col-form-label">GST</label>-->
                            <!--    <div class="col-sm-8">-->
                            <!--        <select name="gst" id="gst" class="form-control " required>-->
                            <!--            <?php dropdown($gst_percent_list, $gst); ?>-->
                            <!--        </select>-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">MRP</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number"  value='mrp' name="mrp" id="mrp" required>
                                </div>
                            </div>
                            <!--<div class="form-group row">-->
                            <!--    <label for="example-text-input" class="col-sm-4 col-form-label">Barcode </label>-->
                            <!--    <div class="col-sm-8">-->
                            <!--        <input class="form-control " type="text" value='<?php echo $barcode; ?>' name="barcode">-->
                            <!--    </div>-->
                            <!--</div>-->
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Status </label>
                                <div class="col-sm-8">
                                    <select name='status' class='form-control ' required>
                                        <?php dropdown($item_status, $status); ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->

        </div>
        <!-- /.box-body -->
        </form>
    </section>
</div>
<!-- /.content-wrapper -->
<?php require_once('system/footer2.php'); ?>
<script>
    function sum_gst() {
        let rate = $("#rate").val();
        let gst = $("#gst").find(":selected").val();
        let mrp = (parseFloat(gst) * parseFloat(rate) / 100) + parseFloat(rate);
        $("#mrp").val(parseFloat(mrp, 2));
    }
    
    
    $(document).on('change blur','#product_id',function() {
        let pid = $("#product_id").val();
        $.ajax({
            url: 'master_process.php?task=product_info', // Replace with your server endpoint
            method: 'post',
            data :{'product_id':pid},
            dataType:'json',
            success: function(response) {
                // Assuming response is a string or JSON with a key 'data'
                $('#rate').val(response.rate);
                $('#mrp').val(response.mrp);
                
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + error);
            }
        });
    });
    
    
</script>








