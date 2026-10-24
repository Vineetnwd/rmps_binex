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
	         
	        echo simple_widget (get_all('student','*',['student_type'=>'HOSTEL','status'=>'ACTIVE'])['count'], 'Hostel', 'fa fa-building' , 'warning');
	        echo simple_widget (get_all('student','*',['student_type'=>'TRANSPORT','status'=>'ACTIVE'])['count'], 'Transport', 'fa fa-bus' , 'danger');
	        echo simple_widget (get_all('student','*',['student_sex'=>'MALE','status'=>'ACTIVE'])['count'], 'Boys', 'fa fa-male' , 'info');
	        echo simple_widget (get_all('student','*',['student_sex'=>'FEMALE','status'=>'ACTIVE'])['count'], 'Girls', 'fa fa-female' , 'success');
	       
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
								<select name='status'>
									<?php dropdown($status_list, $conditions['status']); ?>
								</select>
								<select name='student_type'>
									<?php dropdown($student_type_list, $conditions['student_type']); ?>
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
										<th>#</th>
										<th>Adm No.</th>
										<th>Class </th>
										<th>Roll No.</th>
										<th>Student Name</th>
										<th>Father Name</th>
										<th>Primary Mobile </th>
										<th>Secondary Mobile</th>
										<th>Student Type </th>
										<th>Remark </th>
									</tr>
								</thead>
								<tbody>
									<?php
									
								
										
									if ($res['count'] > 0) {
									    $i=1;
										foreach ($res['data'] as $row) {
											$id = $row['id'];
											$status = $row['status'];
											echo "<tr class='odd gradeX'>";

											echo "<td>" . $i . "</td>";
											echo "<td>" . $row['student_admission'] . "</td>";
											echo "<td>" . $row['student_class'] . "-" . $row['student_section'] . "</td>";
											echo "<td>" . $row['student_roll'] . "</td>";
											echo "<td>" . $row['student_name'] . "</td>";
											echo "<td>" . $row['student_father'] . "</td>";
											echo "<td>" . $row['student_mobile']. "</td>";
											echo "<td>" . $row['student_whatsapp']. "</td>";
											echo "<td>" . $row['student_type'] . "</td>";
											echo "<td></td>";
											echo "</tr>";
											$i++;
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