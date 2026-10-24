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
		<h1> Student Payment Ledger</h1>
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
            <th>Adm No.</th>
            <th>Student Name</th>
            <th>Father Name</th>
            <th>Mother Name</th>
            <th>Class/Section</th>
            <th>Mobile</th>
            <th>Prev Session Dues</th>
            <th>Current Dues</th>
            <?php 
            $month_totals = array_fill_keys($month_list, 0); // Initialize monthly totals
            foreach($month_list as $month) {
                $color = ($month == date('M')) ? 'red' : 'black';
                echo "<th bgcolor='$color'>" . add_space($month) . "</th>";
            } ?>
            <th>Total</th>
            <th>Paid</th>
            <th>Dues</th>
            <th>Paid (%)</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $grand_total = 0;
        $grand_paid = 0;
        if ($res['count'] > 0) {
            foreach ($res['data'] as $row) {
                $id = $row['id'];
                $total = 0;
                $paid = student_paid($id);
                $student_fee =get_data('student_fee', $id, null, 'student_id')['data'];
                echo "<tr class='odd gradeX'>";

                echo "<td>" . $row['student_admission'] . "</td>";
                echo "<td>" . $row['student_name'] . "</td>";
                echo "<td>" . $row['student_father'] . "</td>";
                echo "<td>" . $row['student_mother'] . "</td>";
                echo "<td class='text-dark'>" . $row['student_class']. "-". $row['student_section'] . "</td>";
                echo "<td>" . $row['student_mobile'] . "</td>";
                echo "<td>" . $student_fee['prev_session_dues'] . "</td>";
                echo "<td>" . $student_fee['current_dues'] . "</td>";
                
                foreach($month_list as $month) {
                    $mfee = monthly_fee($id, $month)['fee']['total'];
                    $total += $mfee;
                    $month_totals[$month] += $mfee; // Add to monthly total
                    
                    $data[$month] = $mfee;
                    echo "<td>" . $mfee . "</td>";
                }

                $dues = $total - $paid;
                $per = ($total > 0) ? round(($paid / $total) * 100) : 0;
                
                $grand_total += $total;
                $grand_paid += $paid;
                
                $data['total'] =$total;
                $data['paid'] =$paid;
                $data['dues'] =$dues;
                $search  = get_all('student_target',$id,null, 'student_id');
                if($search['count']>0)
                {
                    update_data('student_target' ,$data, $id, 'student_id');
                }else{
                    
                    $data['student_id'] = $id;
                    $data['student_admission'] = $row['student_admission'];
                    $data['branch_id'] = $row['branch_id'];
                    $data['student_class'] = $row['student_class'];
                    $data['student_section'] = $row['student_section'];
                    insert_data('student_target' ,$data);
                }
                
                echo "<td bgcolor='pink'>" . $total . "</td>";
                echo "<td bgcolor='lightgreen'>" . $paid . "</td>";
                echo "<td bgcolor='lightblue'>" . $dues . "</td>";
                echo "<td style='background:lightyellow;color:#222;'>" . $per . "% </td>";
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
            <td bgcolor='pink'><?php echo $grand_total; ?></td>
            <td bgcolor='lightgreen'><?php echo $grand_paid; ?></td>
            <td bgcolor='lightblue'><?php echo ($grand_total - $grand_paid); ?></td>
            <td style='background:lightyellow;color:#222;'><?php echo ($grand_total > 0) ? round(($grand_paid / $grand_total) * 100) : 0; ?>%</td>
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