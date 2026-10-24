<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if (isset($_GET['link']) and $_GET['link'] != '') {
	$student = decode($_GET['link']);
	$id = $student['id'];
	$isedit ='yes';
} else {
	$student = insert_row('scholorship');
	$isedit ='no';
	$id = $student['id'];
}
if ($id != '') {
	$res = get_data('scholorship', $id);
	if ($res['count'] > 0 and $res['status'] == 'success') {
		extract($res['data']);
		if ($student_admission == '') {
			$student_admission = $student['id'];
		}
	}
}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> New Echolorship</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#">Scholorship</a></li>
			<li class="breadcrumb-item active">New Scholorship</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Student Details </h3>

				<div class="box-tools pull-right">
					<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form id='update_frm' action='update_scholorship_ad'>
					<div class="row">
						<div class="col-lg-6">
                            <div class="form-group row">
                                <label for="category"  class="col-sm-4 col-form-label">Category</label>
								<div class="col-sm-8">
                                    <select class="form-control" id="category" name="category" required>
                                        <option value="" disabled selected>Select Category</option>
                                        <option value="Junior" <?php if ($category == "Junior") echo 'selected'; ?>>Junior</option>
                                        <option value="Senior" <?php if ($category == "Senior") echo 'selected'; ?>>Senior</option>
                                    </select>
                                </div>
                            </div>
							<div class="form-group row">

								<label for="example-text-input" class="col-sm-4 col-form-label">Name <?= imp() ?></label>
								<div class="col-sm-8">
									<input type='hidden' name='id' value='<?php echo $id; ?>' />
									<input type='hidden' name='isedit' value='<?= $isedit; ?>' />
									<input class="form-control border-warning" type="text" value='<?php echo $name; ?>' name="name" required>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Father's Name <?= imp() ?></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $father_name; ?>' name='father_name' required>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Class <?= imp() ?></label>
								<div class="col-sm-8">
									<select name='student_class' class='form-control' required>
										 <option value="">Select Class</option>
                                            <option value="I" <?php if ($student_class == "I") echo 'selected'; ?>>I</option>
                                            <option value="II" <?php if ($student_class == "II") echo 'selected'; ?>>II</option>
                                            <option value="III" <?php if ($student_class == "III") echo 'selected'; ?>>III</option>
                                            <option value="IV" <?php if ($student_class == "IV") echo 'selected'; ?>>IV</option>
                                            <option value="V" <?php if ($student_class == "V") echo 'selected'; ?>>V</option>
                                            <option value="VI" <?php if ($student_class == "VI") echo 'selected'; ?>>VI</option>
                                            <option value="VII" <?php if ($student_class == "VII") echo 'selected'; ?>>VII</option>
                                            <option value="VIII" <?php if ($student_class == "VIII") echo 'selected'; ?>>VIII</option>
                                            <option value="IX" <?php if ($student_class == "IX") echo 'selected'; ?>>IX</option>
    
									</select>
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Date Of Birth <?= imp() ?></label>
								<div class="col-sm-8">
									<input class="form-control" type="date" value='<?php echo $date_of_birth; ?>' name="date_of_birth" >
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Mobile No. <?= imp() ?></label>
								<div class="col-sm-8">
									<input class="form-control" type="tel" value='<?php echo $mobile; ?>' pattern="[6789][0-9]{9}" name="mobile" required maxlength="10" minlength="10">
								</div>
							</div>
							<div class="form-group row">
								<label for="example-text-input" class="col-sm-4 col-form-label">Email <?= imp() ?></label>
								<div class="col-sm-8">
									<input class="form-control" type="email" value='<?php echo $email; ?>'name="email" >
								</div>
							</div>
							<div class="form-group row">
								<label for="current_school" class="col-sm-4 col-form-label">Current School <?= imp() ?></label>
								<div class="col-sm-8">
									<input class="form-control" type="text" value='<?php echo $current_school; ?>' name="current_school" >
								</div>
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group row">
								<label class="col-sm-4 col-form-label"> Address</label>
								<div class="col-sm-8">
									<textarea class="form-control" rows="3" name='address'><?php echo $address ?></textarea>
								</div>
							</div>
							<div class="form-group row">
    <label class="col-sm-4 col-form-label">Payment Status</label>
    <div class="col-sm-8">
        <select name='payment_status' id="payment_status" class='form-control'>
            <option value="" disabled selected>Select Status</option>
            <option value="PAID" <?php if ($payment_status == "PAID") echo 'selected'; ?>>PAID</option>
            <option value="UNPAID" <?php if ($payment_status == "UNPAID") echo 'selected'; ?>>UNPAID</option>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-4 col-form-label">Payment Amount</label>
    <div class="col-sm-8">
        <input type='number' name='amount' class='form-control' value='<?= isset($amount) ? $amount : 1000; ?>'>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-4 col-form-label">Payment Date</label>
    <div class="col-sm-8">
        <input type='date' name='pay_date' id="pay_date" value='<?=$pay_date?>' class='form-control'>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-4 col-form-label">Payment Mode</label>
    <div class="col-sm-8">
        <select name='pay_mode' id="pay_mode" class='form-control'>
            <?= dropdown($payment_mode_list, $pay_mode); ?>
        </select>
    </div>
</div>

<div class="form-group row">
    <label class="col-sm-4 col-form-label">Remarks / Txn Id</label>
    <div class="col-sm-8">
        <input type='text' name='payment_details' id="payment_details" class='form-control' value ='<?=$payment_details?>'>
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
<?php require_once('system/footer.php'); ?><script>
    // Function to disable/enable fields based on payment status
    function toggleFieldsBasedOnStatus() {
        var paymentStatus = document.getElementById('payment_status').value;

        // If status is "UNPAID", disable the related fields
        if (paymentStatus == "UNPAID") {
            document.getElementById('pay_date').disabled = true;
            document.getElementById('pay_mode').disabled = true;
            document.getElementById('payment_details').disabled = true;
        } else {
            // If status is "PAID" or anything else, enable the fields
            document.getElementById('pay_date').disabled = false;
            document.getElementById('pay_mode').disabled = false;
            document.getElementById('payment_details').disabled = false;
        }
    }

    // Call the function on page load to check the current payment status
    document.addEventListener('DOMContentLoaded', function() {
        toggleFieldsBasedOnStatus();
        
        // Add event listener to check when payment status changes
        document.getElementById('payment_status').addEventListener('change', toggleFieldsBasedOnStatus);
    });
</script>