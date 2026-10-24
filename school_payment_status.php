<?php require_once('system/header.php'); ?>
<?php require_once('system/menu.php');
// Initialize an array to hold the query conditions
$conditions = [];
//$conditions['status'] = $_GET['status']??'ACTIVE';
// Check the conditions and populate the conditions array
if (!empty($_GET['status'])) {
    $conditions['status'] = $_GET['status'];
}

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

// Execute the query with the conditions array, or fetch all students if no conditions are set
$res = empty($conditions) ?null: get_all('student', '*', $conditions);

?>

<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1> School Payment Status</h1>
		<ol class="breadcrumb">
			<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
			<li class="breadcrumb-item"><a href="#transport">Student</a></li>
			<li class="breadcrumb-item active">Student Payment Ledger</li>
		</ol>
	</section>
	<section class="content">
	    <div class="row">
	        <?php 
	         
	       // echo simple_widget (get_all('student','*',['student_type'=>'HOSTEL'])['count'], 'Hostel', 'fa fa-building' , 'warning');
	       // echo simple_widget (get_all('student','*',['student_type'=>'TRANSPORT'])['count'], 'Transport', 'fa fa-bus' , 'danger');
	       // echo simple_widget (get_all('student','*',['student_sex'=>'MALE'])['count'], 'Boys', 'fa fa-male' , 'info');
	       // echo simple_widget (get_all('student','*',['student_sex'=>'FEMALE'])['count'], 'Girls', 'fa fa-female' , 'success');
	       
	        ?>
			
		</div>
		<div class="row">
			<div class="col-12">
				<div class="box">
					<div class="box-header with-border">
						<h3 class="box-title"> Apply Filters</h3>
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
								<button class='btn btn-orange'> Generate </button>
							</form>
						</div>
					</div>
					<!-- /.box-header -->
					<div class="box-body">
						<div class="table-responsive">
						    <?php if(!empty($res)){ ?>
						<table id="example" class="table table-bordered table-hover display nowrap margin-top-10">
    <thead>
        <tr class='op-thead'>
            <th>Class </th>
            <th>Section</th>
            <th>Strength</th>
            <?php 
            $month_totals = array_fill_keys($month_list, 0); // Initialize monthly totals
            foreach($month_list as $month) {
                $color = ($month == date('M')) ? 'red' : 'black';
                echo "<th bgcolor='$color'>" . add_space($month) . "</th>";
            } ?>
         
        </tr>
    </thead>
    <tbody>
        <?php
        $grand_total = 0;
        $grand_paid = 0;
        $sql ="select student_class, student_section, count(id) as total from student where status ='ACTIVE' GROUP by student_class, student_section";
        $res  = direct_sql($sql);
        
        if ($res['count'] > 0) {
            foreach ((array)$res['data'] as $row) {
               
                echo "<tr class='odd gradeX'>";

                echo "<td>" . $row['student_class'] . "</td>";
                echo "<td>" . $row['student_section'] . "</td>";
                echo "<td>" . $row['total'] . "</td>";
              
                foreach($month_list as $month) {
                    $mfee = monthly_target($month, $row['student_class'], $row['student_section']);
                    $total += $mfee;
                    $month_totals[$month] += $mfee; // Add to monthly total
                    echo "<td>" . $mfee . "</td>";
                }

                echo "</tr>";
            }
        }
        ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background: #ddd;">
            <td colspan="3" align="center">Total</td>
            <?php
            foreach ($month_list as $month) {
                echo "<td>" . $month_totals[$month] . "</td>"; // Show sum of each month
            }
            ?>
           
        </tr>
    </tfoot>
</table>
<?php }?>
						</div>
					</div>
				</div>
			</div>
	</section>
</div>
<?php require_once('system/footer2.php'); ?>