<style>
	.student-photo {
		width: 40px;
		height: 40px;
		border-radius: 50%;
		object-fit: cover;
		border: 2px solid #3b82f6;
	}
</style>
<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');

if ($user_type == 'DBA') {
	echo "<script> window.location='dashboard.php' </script>";
}
$sel_branch = ($_POST['sel_branch']) ?? $branch_id;
$search_by = ($_POST['search_by']) ?? 'student_admission';
$student_class = ($_POST['student_class']) ?? '';
$search_text = ($_POST['search_text']) ?? '';
?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Collect Fee</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="dashboard"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#fee">Fee</a></li>
			<li class="breadcrumb-item active">Collect fee</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">

		<!-- Basic Forms -->
		<div class="box box-default">
			<div class="box-header with-border">
				<h3 class="box-title">Collect fee</h3>

				<div class="box-tools pull-right">
					<a class='fa fa-table btn btn-info btn-sm' href='add_area' title='Show Student'> </a>
				</div>
			</div>
			<!-- /.box-header -->

			<div class="box-body">
				<form action='' method='post'>
					<div class='row'>
						<div class="col-lg-2 col-offset-lg-2"></div>
						<div class="col-lg-2 col-offset-lg-2 d-none">
							<div class="form-group">
								<label>Select Branch</label>
								<select class="form-control" name='sel_branch'>
									<?= dropdown_with_key(branch_list(), $sel_branch); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Select Class</label>
								<select class="form-control" name='student_class'>
									<?php dropdown($class_list, $student_class); ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2 col-offset-lg-2">
							<div class="form-group">
								<label>Search Via</label>

								<select class="form-control" name='search_by' required>
									<?= dropdown_with_key($search_by_list, $search_by) ?>
								</select>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">

								<div class="form-group has-success">
									<label class="control-label" for="inputSuccess">Enter value</label>
									<input type="text" class='form-control' name='search_text' value='' required
										autofocus>
								</div>
							</div>
						</div>
						<div class="col-lg-2">
							<div class="form-group">
								<label class="control-label">&nbsp; Alt +S to Search </label>
								<input type="submit" id='st_search' class='btn btn-success btn-md'
									value='Search Student' name='search' accesskey='s'>
							</div>
						</div>
					</div>
				</form>

				<div class='row'>
					<div class="col-lg-12">
						<hr>
						<!-- Advanced Tables -->
						<?php
						if (isset($_REQUEST['search_text'])) {

							$search_by = isset($_REQUEST['search_by']) ? xss_clean(trim($_REQUEST['search_by'])) : '';
							$search_text = xss_clean(trim($_REQUEST['search_text']));
							$student_class = isset($_REQUEST['student_class']) ? xss_clean(trim($_REQUEST['student_class'])) : '';

							if ($search_by == "student_all") {
								$sql = "SELECT * FROM student 
                WHERE status NOT IN ('AUTO','BLOCK') 
                AND branch_id = '{$_SESSION['branch_id']}' 
                AND (
                    student_admission LIKE '%$search_text%' OR  
                    student_name LIKE '%$search_text%' OR 
                    student_mobile = '$search_text'
                )";
							} else {
								$sql = "SELECT * FROM student 
                WHERE status NOT IN ('AUTO','BLOCK') 
                AND branch_id = '{$_SESSION['branch_id']}'";

								if ($student_class != '') {
									$sql .= " AND student_class = '$student_class'";
								}

								if (in_array($search_by, ['student_roll', 'student_admission', 'student_mobile'])) {
									$sql .= " AND $search_by = '$search_text'";
								} elseif (in_array($search_by, ['student_father', 'student_name'])) {
									$sql .= " AND $search_by LIKE '%$search_text%'";
								}
							}

							// Debug or run your query here
							// echo $sql;
							?>


							<div class="table-responsive">
								<table rules='all' border='1' width='100%' cellpadding='5'>
									<thead>
										<tr class='op-thead'>
											<th>Adm No.</th>
											<th>Photo</th>
											<th>Student Name</th>
											<th>Father Name</th>
											<th>Area/Fare</th>
											<th>Class </th>
											<th>Roll No.</th>
											<th>Student Type </th>
											<th>Mobile No </th>
											<!--<th>Prev. Dues </th>-->
											<th>Till Date </th>
											<th>Operation</th>
										</tr>
									</thead>
									<tbody>
										<?php
										//	echo $sql;
										$res = direct_sql($sql);
										if ($res['count'] > 0) {
											foreach ($res['data'] as $row) {

												$photo = $row['student_photo'];

												// if($photo==''){
												if ($_SESSION['branch_id'] == 1) {
													$folder_path = $base_url . 'student_photo/Bishambharpur/Photo/';
												} else if ($_SESSION['branch_id'] == 2) {
													$folder_path = $base_url . 'student_photo/Barauli/Photo/';
												} else {
													$folder_path = $base_url . 'student_photo/Barharia/Photo/';
												}
												// echo $base_url;
												$st_photo = $folder_path . $row['student_admission'] . '.jpg';
												// }else{
												//     $st_photo = $base_url.'upload/'.$row['student_photo'];
												// }
												if (file_exists($st_photo)) {
													$st_photo = "assets/images/user.jpg";
												}

												$id = $row['id'];
												$student_admission = $row['student_admission'];
												$status = $row['status'];
												echo "<tr class='odd gradeX'>";
												//echo"<td><a href='print_application.php?student_id=$stu_id' target='_blank'>".$row['student_name']."</a></td>";
												echo "<td>" . $row['student_admission'] . "</td>";
												echo "<td><img src='$st_photo' class='student-photo'></td>";
												echo "<td>" . $row['student_name'] . "</td>";
												echo "<td>" . $row['student_father'] . "</td>";
												echo "<td>" . $row['student_address1'];

												if (get_transport($id, 'area_fee') > 0) {
													echo "<br> (<b>" . get_transport($id, 'area_name') . "-" . get_transport($id, 'area_fee') . ")";
												}
												echo "</td>";
												echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
												echo "<td>" . $row['student_roll'] . "</td>";
												echo "<td>" . $row['student_type'] . "</td>";
												echo "<td>" . $row['student_mobile'] . "</td>";
												//		echo"<td>".get_data('student_fee',$student_admission,'current_dues','student_admission')['data']."</td>";
									
												echo "<td class='text-danger'><big>" . finaldues($id) . "</big></td>";
												echo "<td width='145'>";
												echo "<a href='pay_fee.php?student_id=$id&action=pay' title='Pay Fee ' class='btn btn-success btn-sm' name='Pay_fee'>Pay </a>";
												echo "<a href='student_ledger.php?student_id=$id&action=show' title='Pay Fee ' class='btn btn-info btn-sm' name='Pay_fee'>Ledger </a>";
												echo "<a href='distribute_item.php?student_id=$id&action=sale' title='Sale Item ' class='btn btn-danger btn-sm' >Sale </a>";
												echo "</td></tr>";
											}

										} else {
											echo "<tr><td colspan='10'> 
										<div class='text-danger text-center'>No Record Found </div> </td></tr>";
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
<?php require_once('system/footer.php'); ?>
<script>
	$(window).keydown(function (event) {
		if (event.keyCode === 13) {
			event.preventDefault();
			$("#st_search").trigger("click");
		}
	});
</script>