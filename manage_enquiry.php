<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
extract(post_clean($_GET));
$res = get_all('enquiry', '*', array('status' => 'ACTIVE'), 'id ');
?>
 <title> Enquiry Report : <?= isset($inst_name)?$inst_name:'  Bine Digital School' ?></title>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Manage Enquiry
		</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item">Enquiry</li>
			<li class="breadcrumb-item active">Manage Enquiry</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
	    <div class="row">
	        <?php 
	        $icons  =['fa-plus-circle','fa-heartbeat','fa-address-book-o','fa-times'];
	        $colors  =['danger','info','success','primary'];
	        $i=0;
	        foreach($lead_status as $lead)
	        {
	            
	            $ct = get_all('enquiry', '*',['status'=>$lead])['count'];
	            echo simple_widget ($ct, $lead, 'fa '. $icons[$i] , $colors[$i]);
	            $i++;
	        }
	        ?>
			
		</div>
		<div class="row">
			<div class="col-12">

				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title">Manage Enquiry </h3>
						<div class="box-tools pull-right">
							<a class='btn btn-success' title='New Enquiry'  href='add_enquiry'><i class='fa fa-plus'></i>  Add New </a> 
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-striped">
								<thead>
									<tr class='op-thead'>
										<th>Sl. No.</th>
										<th>Name</th>
										<th>Lead Source </th>
										<th>Date</th>
										<th>Class </th>
										<th>Mobile No</th>
										<th>Whatsapp</th>
										<th>status</th>
										<th class='text-right'>Action</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$i=1;
									if ($res['count'] > 0) {
										$fee = 0;
										foreach ($res['data'] as $row) {
											$link = encode('student_name=' . $row['student_name'] . '&id=' . $row['id']);
											$fee = $fee + $row['form_fee'];
									?>
											<tr>
											    	<td><?= $i; ?></td>
												<td><?php echo $row['student_name'] ?></td>
												<td><?php echo $row['source']; ?></td>
												<td><?php echo $row['enquiry_date']; ?></td>
												<td><?php echo $row['student_class']; ?></td>
												<td><?php echo $row['student_mobile'] ?></td>
												<td><?php echo $row['student_whatsapp'] ?></td>
												<td><?php echo show_status($row['status']) ?></td>
												<td class='text-right'>
													<?= btn_view('enquiry', $row['id'])?>
													<a class='fa fa-edit btn btn-info btn-xs' href='add_enquiry?link=<?php echo $link; ?>'></a>
													<span class='delete_btn btn btn-danger btn-xs fa fa-trash' data-table='enquiry' data-id='<?php echo $row['id']; ?>' data-pkey='id'></span>
												</td>
											</tr>
									<?php $i++;
										    
										}
									} ?>
								</tbody>
								<!--<tfoot>
						<tr>
							<th>Name</th>
							<th>Position</th>
							<th>Office</th>
							<th>Age</th>
							<th>Start date</th>
							<th>Salary</th>
						</tr>
					</tfoot>-->
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