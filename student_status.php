<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
// Initialize an array to hold the query conditions
$conditions = [];
$conditions['status'] = $_GET['status']??'ACTIVE';
$conditions['branch_id'] = $branch_id ?? $_SESSION['branch_id'];
//Check the conditions and populate the conditions array
// if (!empty($_GET['status'])) {
//     $conditions['status'] = $_GET['status']??'ACTIVE';
// }


if (!empty($_GET['student_class'])) {
    $conditions['student_class'] = $_GET['student_class']; // Fixed: Used $_GET to get the value
}

if (!empty($_GET['student_section'])) {
    $conditions['student_section'] = $_GET['student_section'];
}

if (!empty($_GET['student_type'])) {
    $conditions['student_type'] = $_GET['student_type'];
}

if (!empty($_GET['student_sex'])) {
    $conditions['student_sex'] = $_GET['student_sex'];
}

if (!empty($_GET['admission_type'])) {
    $conditions['admission_type'] = $_GET['admission_type'];
}
if (!empty($_GET['house_name'])) {
    $conditions['house_name'] = $_GET['house_name'];
}

// Execute the query with the conditions array, or fetch all students if no conditions are set
$res = empty($conditions) ? get_all('student') : get_all('student', '*', $conditions);

?>

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> Student Report</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Student</a></li>
			<li class="breadcrumb-item active">Student Report</li>
		</ol>
	</section>
	<section class="content">
	    <div class="row">
	        <?php 
	         
	        echo simple_widget (get_all('student','*',['student_type'=>'HOSTEL'])['count'], 'Hostel', 'fa fa-building' , 'warning');
	        echo simple_widget (get_all('student','*',['student_type'=>'TRANSPORT'])['count'], 'Transport', 'fa fa-bus' , 'danger');
	        echo simple_widget (get_all('student','*',['student_sex'=>'MALE'])['count'], 'Boys', 'fa fa-male' , 'info');
	        echo simple_widget (get_all('student','*',['student_sex'=>'FEMALE'])['count'], 'Girls', 'fa fa-female' , 'success');
	       
	        ?>
			
		</div>
		<div class="row">
			<div class="col-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title"> Filters</h3>
						<div class="box-tools pull-right">
							<form>
							    <select name='student_class'>
									<?php dropdown($class_list, $conditions['student_class']); ?>
								</select>
								<select name='student_section'>
									<?php dropdown($section_list, $conditions['student_section']); ?>
								</select>
								<select name='student_sex'>
									<?php dropdown($gender_list, $conditions['student_sex']); ?>
								</select>
								<select name='status'>
									<?php dropdown($status_list, $conditions['status']); ?>
								</select>
								<select name='student_type'>
									<?php dropdown($student_type_list, $conditions['student_type']); ?>
								</select>
								<select name='admission_type'>
									<?php dropdown($admission_list, $conditions['admission_type']); ?>
								</select>
								<select name='house_name'>
									<?php dropdown($house_list, $conditions['house_name']); ?>
								</select>
								<button class='btn btn-orange'> Generate </button>
							</form>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
							<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
								<thead>
									<tr class='op-thead'>
										<th>Adm No.</th>
										<th>Class </th>
										<th>Roll No.</th>
										<th>Student Name</th>
										<th>Father Name</th>
										<th>Mother Name</th>
										<th>DOB</th>
										<th>Addres</th>
										<th>Photo Name</th>
										<th>House</th>
										<th>Bus Stop</th>
										<th>Fare</th>
										<th>Bus No/ Trip</th>
										<th>Student Type </th>
										<th>Mobile No </th>
									</tr>
								</thead>
								<tbody>
									<?php
									
								
										
									if ($res['count'] > 0) {
									    
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$status = $row['status'];
											echo "<tr class='odd gradeX'>";

											echo "<td>" . $row['student_admission'] . "</td>";
											echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
											echo "<td>" . $row['student_roll'] . "</td>";
											echo "<td>" . $row['student_name'] . "</td>";
											echo "<td>" . $row['student_father'] . "</td>";
												echo "<td>" . $row['student_mother'] . "</td>";
													echo "<td>" . date('d-M-Y',strtotime($row['date_of_birth'])) . "</td>";
											echo "<td>" . $row['student_address1'] . "</td>";
											echo "<td>" . $row['student_photo'] . "</td>";
											echo "<td>" . $row['house_name'] . "</td>";
											echo "<td>" . get_transport($id,'area_name') . "</td>";
											echo "<td>" . get_transport($id,'area_fee') . "</td>";
											echo "<td>" . get_data('trip_details', $row['trip_id'], 'trip_name')['data'] . "</td>";
											echo "<td>" . $row['student_type'] . "</td>";
											echo "<td>" . $row['student_mobile']. "</td>";
											echo "</tr>";
										}
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
	</section>
</div>
<?php require_once('system/footer2.php'); ?>