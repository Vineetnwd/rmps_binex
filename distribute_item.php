<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

$table_name = 'distribute_item';
if (isset($_SESSION['inv_id'])) {
    $inv_id = $_SESSION['inv_id'];
} else {
    $invoice = insert_row('invoice');
    $inv_id = $invoice['id'];
}

if (isset($_GET['link']) and $_GET['link'] != '') {
    $item = decode($_GET['link']);
    $id = $item['id'];
} else {
    $item = insert_row($table_name);
    $id = $item['id'];
}

if (isset($_SESSION['txn_date'])) {
    $date = $_SESSION['txn_date'];
} else {
    $date = date("Y-m-d");
}

if (isset($_REQUEST['student_id'])) {
    $student_id =  $_REQUEST['student_id'];
}
if (isset($_SESSION['inv_no'])) {
  $inv_no = $_SESSION['inv_no'];
}

?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>Sell / Distribute Item</h1>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i>Home</a></li>
            <li class="breadcrumb-item"><a href="#">Inventory</a></li>
            <li class="breadcrumb-item active">New Item</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">

        <!-- Basic Forms -->
        <div class="box box-default">
            <div class="box-header with-border">
                <h3 class="box-title">Sell Entry</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
                <form id='item_frm' action='update_distribute_item'>
                        
                            <div class="form-group row m-2" style='background:lightgreen;padding-bottom:20px;'>
                                <div class="col-md-2">
                                    <input type='hidden' name='id' value='<?php echo $id; ?>' required />
                                    <input type='hidden' name='inv_id' value='<?php echo $inv_id; ?>' required />
                                    <label for="invoice" class="col-form-label">Invoice No. </label>
                                    <?php if (isset($_SESSION['inv_no'])) { ?>
                                        <input type="invoice" name="inv_no" id="inv_no" class="form-control" value="<?php echo $inv_no; ?>" readonly >
                                    <?php } else { ?>
                                        <input type="invoice" name="inv_no" id="inv_no" class="form-control" value="<?php echo $inv_no; ?>"  >
                                    <?php } ?>
                                  
                                </div>
                              
                                <div class="col-md-3">
                                    <label for="example-text-input" class="col-form-label">Admisson No. </label>

                                        <?php
                                        if (isset($student_id)) {
                                            $student_admission = get_data('student', $student_id, 'student_admission')['data'];
                                            echo "<input type='text' value='$student_admission' readonly class='form-control'>";
                                        }
                                        ?>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="example-text-input" class="col-form-label">Student Name</label>

                                        <?php
                                        if (isset($student_id)) {
                                            $student_name = get_data('student', $student_id, 'student_name')['data'];
                                            echo "<input type='hidden' value='$student_id' name='student_id' required>";
                                            echo "<input type='text' value='$student_name' readonly class='form-control'>";
                                        }
                                        ?>
                                </div>
                                
                                <div class="col-md-3">
                                    <label for="date" class="col-form-label">Date</label>
                                    <?php
                                    if (isset($_SESSION['txn_date'])) {
                                        $inv_date = date("Y-m-d", strtotime($date));
                                        echo "<input type='date' value='$inv_date' name='date' readonly required class='form-control' >";
                                    } else {
                                    ?>
                                        <input type="date" name="date" id="date" class="form-control" value="<?php echo date("Y-m-d", strtotime($date)); ?>" required>
                                    <?php } ?>
                                </div>
                       
                                <div class="col-md-3">
                                    <label for="item_id" class="col-form-label">Item</label>
                                    <div>
                                        <select name="item_id" id="item_id" class="form-control " onchange="get_item()" required>
                                            <?php dropdown_list('inventory_item', 'id', 'name', $item_id, 'current_stock'); ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="qty" class="col-form-label">Qty</label>
                                    <div>
                                        <input type="number" name="qty" id="qty" class="form-control" min="1" onblur="checkqty(this)" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="rate" class="col-form-label">Rate</label>
                                    <div>
                                        <input type="number" name="rate" id="rate" class="form-control" onkeyup="cal_amt()" onblur="cal_amt()" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label for="amount" class="col-form-label">Amount</label>
                                    <div>
                                        <input name="amount" id="amount" class="form-control" required readonly>
                                    </div>
                                </div>
                </form>
                <div class="col-md-2 pt-5">
                        <br>
                        <button  id="add_item_btn" class="btn btn-dark btn-block mt-2" onmouseover="cal_amt()" >Add Item</button>
                    
                </div>
            </div>
    
            </div>
<!-- /.box-body -->
<div class="container">
   
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-stripped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Quantity</th>
                                    <th>Rate</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $i = 1;
                                $q = 0;
                                $t = 0;
                                if (isset($_SESSION['student_id'])) {
                                    $student_id = $_SESSION['student_id'];
                                    $res = get_all('distribute_item', '*', array("student_id" => $student_id, "inv_id" => $inv_id));
                                     if ($res['count'] > 0) {
                                    foreach ($res['data'] as $row) {
                                        $id = $row['id'];
                                        $item_id = $row['item_id'];
                                        $q += $row['qty'];
                                        $t += $row['amount'];
                                        echo "<tr>";
                                        echo "<td>" . $i . "</td>";
                                        echo "<td>" . get_data('inventory_item', $item_id, 'name')['data'] . "</td>";
                                        echo "<td class='qty'>" . $row['qty'] . "</td>";
                                        echo "<td>" . $row['rate'] . "</td>";
                                        echo "<td class='amt'>" . $row['amount'] . "</td>";
                                        echo "<td align='right'>" . btn_delete('distribute_item', $id) . "</td>";
                                        echo "</tr>";
                                        $i++;
                                    }
                                }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr style='background:#222;color:#fff;font-size:16px;'>
                                    <td colspan='2'> Total </td>
                                    <td> <?= $q ?></td>
                                    <td> </td>
                                    <td> <span id='total_amt'><?= $t ?></span> </td>
                                    <td> </td>
                                </tr>
                            </tfoot>
                           
                        </table>
                    </div>
                </div>
            </div>
       
</div>
<div class="container" >
    <div class="card">
        <div class="card-body p-2 mb-4" style='background:pink'>
            <form action="close_invoice" id="invoice_frm">
                <div class="form-group row">
                   
                    <div class="col-md-2">
                        <label for="dues" class="form-label">Previous Dues</label>
                        <?php
                        if (isset($_SESSION['student_id'])) {
                            $student_id = $_SESSION['student_id'];
                            $get_dues = get_data('student', $student_id, 'item_dues')['data'];
                        }
                        ?>
                        <input type="number" class="form-control" name="prev_dues" id="dues" value="<?php echo $get_dues; ?>" readonly required>
                    </div>
                    <div class="col-md-2">
                        <label for="paid" class="form-label">Paid Amount</label>
                        <input type="number" class="form-control" name="payment" id="paid" onkeyup="cal_dues()" required>
                    </div>
                    <div class="col-md-2">
                        <label for="cur_dues" class="form-label">Current Dues</label>
                        <input type="text" class="form-control" name="dues" id="cur_dues" readonly value='<?=$get_dues?>' required>
                    </div>
                    <div class="col-md-2">
                        <label for="paid" class="form-label">Payment Mode</label>
                        <select name="payment_mode" id="payment_mode" class="form-control" required>
                            <?php dropdown($payment_mode_list, $payment_mode); ?>
                        </select>
                    </div>
                     <div class="col-md-2">
                        <label for="remarks" class="form-label">Remarks</label>
                        <input type="hidden" class="form-control" name="id" value="<?php echo $inv_id; ?>" required>
                        <input type="hidden" class="form-control" name="inv_no" value="<?php echo $inv_no; ?>">
                        <input type="hidden" class="form-control" name="student_id" value="<?php echo $student_id; ?>">
                        <input type="hidden" class="form-control" name="txn_date" value="<?php echo date('Y-m-d', strtotime($date)); ?>">
                        <input type="hidden" class="form-control" name="total" id="total">
                        <input type="text" class="form-control" name="remarks" id="remarks">
                    </div>
            </form>
            <div class="col-md-2">
                <label for=""></label>
                <input type="button" value="CLOSE INVOICE" id="close_invoice" class="my-2 btn btn-danger btn-block">
            </div>
        </div>
    </div>
</div>
</div>
</section>

<section>
</section>
</div>
<!-- /.content-wrapper -->
<?php require_once('system/footer2.php'); ?>
<script>

    $(document).on('change blur',"#stu_list", function(){
        let student_id = $("#stu_list").val();
        $.ajax({
            'url': "master_process?task=get_item_dues",
            'type': "POST",
            'data': {
                'student_id': student_id
            },
            'success': function(res) {
                var obj = JSON.parse(res);
                $("#dues").val(obj.data);
            }
        })
    });
    
    function get_student() {
        let stu_class = $("#student_class").find(":selected").text();
        let stu_section = $("#student_section").find(":selected").text();
        $.ajax({
            'url': "master_process?task=get_student",
            'type': "POST",
            'data': {
                'stu_class': stu_class,
                'stu_section': stu_section
            },
            'success': function(data) {
                $("#stu_list").html(data);
            }
        })
    }
    
    function checkqty(ele) {
        const qty = $(ele).val();
        const id = $('#item_id').val();
        $.ajax({
            'url': 'master_process?task=check_qty',
            'type': 'post',
            'data': {
                'qty': qty,
                'item_id': id
            },
            'success': function(data) {
                console.log(data);
                var obj = JSON.parse(data);
                $.notify(obj.msg, obj.status);
                $("#rate").val(obj.rate);
                if (obj.status == "error") {
                    $('#add_item_btn').attr('disabled',true);
                }else if(obj.status == "success"){
                    $("#add_item_btn").attr('disabled',false);
                }
            }
        });
    }

    function cal_amt() {
        let q = $("#qty").val();
        let r = $("#rate").val();
        let amt = parseFloat(q) * parseFloat(r);
        document.getElementById("amount").value = parseFloat(amt).toFixed(2);
    }

    function cal_dues() {
        let total = $("#total_amt").text();
        let paid = $("#paid").val();
        let due = $("#dues").val();
        let dues = parseFloat(total) + parseFloat(due) - parseFloat(paid);
        // alert(parseFloat(dues));
        document.getElementById("cur_dues").value = parseFloat(dues).toFixed(2);
    }
    let tot_amt = $('#total_amt').text();
    document.getElementById("total").value = parseFloat(tot_amt);
    
    
        
    $(document).on('change blur','#item_id',function() {
        let pid = $("#item_id").val();
        $.ajax({
            url: 'master_process.php?task=product_info', // Replace with your server endpoint
            method: 'post',
            data :{'product_id':pid},
            dataType:'json',
            success: function(response) {
                // Assuming response is a string or JSON with a key 'data'
                $('#rate').val(response.rate);
               // $('#mrp').val(response.mrp);
                
            },
            error: function(xhr, status, error) {
                console.error('Error: ' + error);
            }
        });
    });
    
</script>