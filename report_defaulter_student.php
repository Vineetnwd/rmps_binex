<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');


$search_text = $_POST['search_text']??10000;
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Defaulter Student List</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Fee</a></li>
			<li class="breadcrumb-item active">Defaulter Student</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Defaulter fee</h3>

				<div class="box-tools pull-right">
					<a class='fa fa-table btn btn-info btn-sm' href='manage_student' title='Show Student'> </a>

				</div>
			</div>
			<!-- /.box-header -->
            <form action='' method='post' >
			<div class="box-body">
				
					<div class='row'>
						<div class="col-lg-2 col-offset-lg-2"></div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Select Class</label>
								<select class="form-control" name='student_class'>
									<?php dropdown($class_list, @$_POST['student_class']); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Search Via</label>
								<select class="form-control" name='search_by' required>
									<option value='student_admission'>Previous Dues</option>
									<!--<option value='student_roll'>Roll No.</option>-->
									<!--<option value='student_name'>Name </option>-->
									<!--<option value='student_father'>Father's Name</option>-->
									<!--<option value='student_mobile'>Mobile No</option>-->
								</select>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">

								<div class="form-group has-success">
									<label class="control-label" for="inputSuccess">Enter Dues (>)</label>
									<input type="text" name='search_text' style='border:solid 1px green; padding:5px;width:120px;' value='<?= $search_text ?>' autofocus required>
								</div>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">
								<label class="control-label">&nbsp; Enter to Search</label>
								<input type="submit" class='btn btn-success btn-md' value='Search Student' id='update_btn' name='search' >
							</div>
						</div>
					</div>
				</form>

				<div class='row'>
					<div class="col-lg-12">
						<hr>
						<!-- Advanced Tables -->
						<?php
						if (isset($_REQUEST['student_class']) and isset($_REQUEST['search_text'])) {
						    
							//$sql = "select student.*, student_fee.current_dues from student, student_fee where student.status not in ('BLOCK') and student.id =student_fee.student_id  and student.branch_id ='{$_SESSION['branch_id']}' and (student_fee.current_dues > '{$_REQUEST['search_text']}'  or student_fee.prev_session_dues>'{$_REQUEST['search_text']}') ";
							
							$sql = "select student.*, student_fee.current_dues from student, student_fee where student.status not in ('BLOCK') and student.id =student_fee.student_id  and student.branch_id ='{$_SESSION['branch_id']}' and student.status ='ACTIVE' ";
							
							$student_class = xss_clean(trim($_REQUEST['student_class']));
						
							if ($student_class <> "") {
								$sql .= " and student_class = '$student_class' ";
							}
					
						?>

							<div class="table-responsive">
								<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
								   
									<thead>
										<tr>
											<th>Adm No.</th>
											<th>Student Name</th>
											<th>Father Name</th>
											<th>Area/Fare</th>
											<th>Class </th>
											<th>Roll No.</th>
											<th>Student Type </th>
											<th>Mobile No </th>
											<!--<th class='bg-warning'>Session Dues </th>-->
											<th class='bg-warning'>Curent Sesion Dues </th>
											<th class='bg-danger text-light'>Prev Session Dues </th>
											<th class='bg-primary text-light'>Total </th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
									<?php
									$res = direct_sql($sql);
									if ($res['count'] > 0) {
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$cdues  = finaldues($id);
											
											$student_admission = $row['student_admission'];
											$sdues  = get_data('student_fee', $student_admission, 'prev_session_dues', 'student_admission')['data'];
											$status = $row['status'];
											if($cdues >= intval($_REQUEST['search_text']))
											{
											echo "<tr class='odd gradeX'>";
											echo "<td>" . $row['student_admission'] . "</td>";
											echo "<td>" . $row['student_name'] . "</td>";
											echo "<td>" . $row['student_father'] . "</td>";
											echo "<td>" . $row['student_address1'];
											if (get_data('transport_area', $row['area_id'], 'area_fee')['data'] > 0) {
												echo " (<b>" . get_data('transport_area', $row['area_id'], 'area_fee')['data'] . ")";
											}
											echo "</td>";
											echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
											echo "<td>" . $row['student_roll'] . "</td>";
											echo "<td>" . $row['student_type'] . "</td>";
											echo "<td>" . $row['student_mobile'] . "</td>";
										//	echo "<td class='bg-warning text-dark'>" . get_data('student_fee', $student_admission, 'current_dues', 'student_admission')['data'] . "</td>";
											echo "<td class='bg-warning text-dark'>" . $cdues . "</td>";
											echo "<td class='bg-danger text-light'>" . $sdues . "</td>";
										    echo "<td class='bg-primary text-light'>" . ($cdues + $sdues) . "</td>";
											echo "<td>" .show_status($row['status']) . "</td>";
							 				echo "</tr>";
											}
										}
									}
								}
									?>
									</tbody>
								</table>
							</div>
					</div>
				</div>
	</section>
</div>
<?php require_once('system/footer2.php'); ?>
