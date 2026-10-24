<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
extract(post_clean($_GET));
$table_name = 'admission';


$from_date  = (isset($_GET['from_date']))?$_GET['from_date']:$today;
$to_date  = (isset($_GET['to_date']))?$_GET['to_date']:$today;

if(isset($_GET['from_date']))
{
    $sql ="select * from scholorship where date(pay_date) between '$from_date' and '$to_date' and payment_status = 'PAID' ";
    $res = direct_sql($sql);
}else{
    $sql = "SELECT * FROM scholorship where payment_status = 'PAID' ORDER BY updated_at DESC";
    $res = direct_sql($sql);
}


?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Scholorship Collection Report
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Student</li>
			<li class="breadcrumb-item active">Manage Scholorship Collection</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<form action='send_sms.php' method='post'>
							<h3 class="box-title">Manage Scholorship Collection
								<input type='hidden' name='mobiles' id='mobiles'>
								<!--<input type='submit' class='btn btn-success btn-xs' value='Send SMS'>-->
							</h3>
						</form>
						<div class="box-tools pull-right">
						    <form>
							<input type='date' name='from_date' value='<?= $from_date ?>'>
							<input type='date' name='to_date' value='<?= $to_date ?>' onchange='submit()' onblur='submit()'>
							</form>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
						    
							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr class='op-thead'>
										<th>Sl.</th>
										<th>App. No.</th>
										<th>Name</th>
										<th>Class </th>
										<th>Amount </th>
										<th>Mode </th>
										<th>Payment Status </th>
										<th>Pay Date</th>
									    <th>Received By </th>
									    <th>Remark </th>
									</tr>
								</thead>
								<tbody>
									<?php
								
									$i=1;
									$total =0;
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$link = encode('name=' . $row['name'] . '&id=' . $row['id']);
											$created_at = $row['created_at'];
											$updated_by = get_data('user', $row['updated_by'],'full_name')['data'];
											$total +=  $row['amount'];
											$branch_name =  get_data('branch_list', $row['branch_id'],'inst_name')['data'];
									?>
											<tr>
											    <td><?php echo $i; ?></td>
											    <td>
											        <?php echo $row['app_no'] ?>
											    </td>
												<td><?php echo $row['name'] ?></td>
												<td><?php echo $row['student_class'] ?></td>
												<td><?php echo $row['amount'] ?></td>
												<td><?php echo $row['pay_mode'] ?></td>
												<td><?php echo show_status($row['payment_status']) ?></td>
												<td><?php echo date('d-M-Y',strtotime($row['pay_date'])) ?></td>
												<td><?php echo $updated_by ?></td>
												<td><?php echo $row['payment_details'] ?></td>
											</tr>
									<?php 
										    $i++;
										}
									} ?>
								</tbody>
							    <tfoot>
							        <tr class='bg-gray'>
							            <th colspan='6'> Total </th>
							            <th> <?= $total ?> </th>
							            <th colspan='6' class='bg-gray'>  </th>
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

<script>
$(document).on('click',".pay_btn",function(){
    var id = $(this).data('id');
    var app_no = $(this).data('app_no');
    var name = $(this).data('name');
   
   
  $("#app_id").val(id);
  $("#student_name").val(name);
  $("#app_no").val(app_no);
  $("#exampleModal").modal('show');
});

</script>

<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Update Payment Details </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
       	<form id='update_frm' action='update_adm_payment'>
        <label> Application No. </label>
        <input type='text' id='app_no' class='form-control' readonly >
        
        <label> Student Name </label>
        <input type='hidden' id='app_id' name='id' class='form-control' readonly >
        <input type='text' id='student_name' name='student_name' class='form-control' readonly >
        
        <label> Payment Date </label>
        <input type='date' name='pay_date' class='form-control'>
        
        <label> Payment Amount </label>
        <input type='number' name='pay_amount' class='form-control' value='500'>
        
        <label> Payment Mode </label>
        <select name='pay_mode' class='form-control' >
                <?php dropdown($payment_mode_list); ?>
        </select>
        
        <label> Payment Remarks / Txn No.</label>
        <input type='text' name='pay_txn_no' class='form-control' >
        </form>
      </div>
      <div class="modal-footer">
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
        	<button class="btn btn-success" id='update_btn'><i class='fa fa-save'></i> Save</button>
      </div>
    </div>
  </div>
</div>

