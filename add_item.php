<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
$table_name = 'inventory_item';
if (isset($_GET['link']) and $_GET['link'] != '') {
    $item = decode($_GET['link']);
    $id = $item['id'];
    $isedit ='yes';
} else {
    $item = insert_row($table_name);
    $id = $item['id'];
    $isedit ='no';
}
if ($id != '') {
    $res = get_data($table_name, $id);
    if ($res['count'] > 0 and $res['status'] == 'success') {
        extract($res['data']);
    }
}


// Fetch existing item names from the database
$itemname = array();
$sql = "SELECT name FROM inventory_item";
$result = direct_sql($sql);
if ($result['count']>0) {
    foreach ($result['data'] as $row) {
        $itemname[] = $row["name"];
    }
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get item name from form submission
    $item = $_POST["name"];
    
    // Check if item name already exists
    $openingst = '';
    if (in_array($name, $itemname)) {
        // If item name already exists, leave opening stock value empty
        $openingst = '';
    } else {
        // If item name doesn't exist, set opening stock value from form submission
        $openingst = $_POST["opening_stock"];
    }
} else {
    // If form is not submitted, set opening stock input to empty
    $openingst = '';
}
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1> Item Details </h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li class="breadcrumb-item"><a href="#">Inventory</a></li>
            <li class="breadcrumb-item active">New Item</li>
        </ol>
    </section>
    <!-- Main content -->
    <section class="content">
        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Add /Update Item Details </h3>
                <div class="box-tools pull-right">
                    <button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
                </div>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form id='update_frm' action='update_item'>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Item Name</label>
                                <div class="col-sm-8">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' />
                                    <input type='hidden' name='isedit' value='<?php echo $isedit; ?>' />
                                    <input class="form-control " type="text" value='<?php echo $name; ?>' name="name" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Select Category</label>
                                <div class="col-sm-8">
                                    <select name="cat_id" id="cat_id" required class="form-control ">
                                        <?php dropdown_list('item_cat', 'id', 'cat_name', $cat_id) ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Opening Stock</label>
                                <div class="col-sm-8">
                                    <input type="number" min="0" name="opening_stock" required class="form-control " value="<?php echo $opening_stock; ?>">
                                </div>
                            </div>
                            <!--<div class="form-group row">-->
                            <!--    <label for="example-text-input" class="col-sm-4 col-form-label">Current Stock</label>-->
                            <!--    <div class="col-sm-8">-->
                            <!--        <input type="number" min="0" name="current_stock" required class="form-control " value="<?php echo $current_stock; ?>">-->
                            <!--    </div>-->
                            <!--</div>-->
                       
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Unit Name</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="text" value='<?php echo $unit; ?>' name="unit">
                                </div>
                            </div>
                          </div>
                        <div class="col-lg-6">
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Min Alert Limit</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number" value='<?php echo $min_alert_limit; ?>' name="min_alert_limit" min="1">
                                </div>
                            </div>
                       
                            <div class="form-group row">
                                <label for="example-text-input" class="col-sm-4 col-form-label">Rate</label>
                                <div class="col-sm-8">
                                    <input class="form-control " type="number" min="0" value='<?php echo $rate; ?>' name="rate" id="rate">
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
                                    <input class="form-control " type="number" min="0" value='<?php echo $mrp; ?>' name="mrp" id="mrp" required>
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
</script>
<?php

// MySQL database connection parameters
$servername = "localhost";
$username = "username";
$password = "password";
$database = "your_database";

// Function to add stock to a specific location
function addStock($product_id, $quantity, $location_id) {
    global $servername, $username, $password, $database;

    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare SQL statement to update stock
    $sql = "UPDATE stock SET quantity = quantity + ? WHERE product_id = ? AND location_id = ?";
    $stmt = $conn->prepare($sql);

    // Bind parameters
    $stmt->bind_param("iii", $quantity, $product_id, $location_id);

    // Execute SQL statement
    if ($stmt->execute() === TRUE) {
        echo "Stock added successfully.";
    } else {
        echo "Error adding stock: " . $conn->error;
    }

    // Close statement and connection
    $stmt->close();
}



?>





