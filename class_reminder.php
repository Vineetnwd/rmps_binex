<?php
require_once('system/function.php');
extract($_REQUEST);

$branch = get_data('branch_list',$_SESSION['branch_id'])['data'];
?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
  $("#btn1").click(function(){
	//$( ".total:contains('0')" ).closest( "table").hide(500);
	$( ".zero" ).closest( "table").hide(500);
  });

});
</script>
<input type='button' id='btn1' class='btn' Value=' Remove all 0'>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Open+Sans&display=swap');

	body {
		font-family: 'Open Sans', sans-serif;
		font-size: 12px;
		padding: 0px;
		margin: 0px;

	}

	td {
		font-weight: 300;
		font-size: 12px;
	}

	.outer {
		float: left;
		margin: 12px 14px;
		height: 400px;
		width: 330px;
		border: solid 1px #ddd;
	}

	.name {
		display: inline;
		font-size: 18px;
		line-height: 22px;
		font-weight: 500;
	}

	.btn {
		font-size: 15px;
		padding: 3px;
		text-align: center;
		background: #c0e;
		color: #fff;
		border-radius: 10px;
	}

	@media print {
		.btn {
			display: none;
		}

		.outer {
			page-break-inside: avoid;
			border: solid 1px #999;
		}
	}
</style>
<?php
//extract($_POST);

/*
foreach($area_id as $area)
{
	$data = get_data('transport_area',$area,null,'area_name')['data'];
	$id[] = $data['id'];
}
$area_id = "'" . implode("','", $id) . "'";
$sql ="select * from student where area_id in($area_id) and status ='ACTIVE'";
$res =direct_sql($sql);
*/
$res = get_all('student', '*', array('student_class' => $student_class, 'student_section' => $student_section, 'student_section' => $student_section, 'finance_type' => 'NORMAL', 'status' => 'ACTIVE') ,' student_roll');

if ($student_admission <> '') {
	$sql = " select * from student where finance_type='NORMAL' and status ='ACTIVE' and student_admission in ($student_admission) order by student_roll";
	$res = direct_sql($sql);
} 

else if ($student_type <> '' and $student_class =='') {
    $student_type = "'" . implode("','", $student_type) . "'";
	$sql = " select * from student where finance_type='NORMAL' and status ='ACTIVE' and student_type in ($student_type) order by student_roll";
	$res = direct_sql($sql);
} 
else {

	$res = get_all('student', '*', array('student_class' => $student_class, 'student_section' => $student_section, 'student_section' => $student_section, 'finance_type' => 'NORMAL', 'status' => 'ACTIVE'), 'student_roll');
}

if ($res['count'] > 0) {
	foreach ($res['data'] as $student) {
	    //print_r($student);
		$student_id = $student['id'];
		$prev_dues = 0.00;
		$dues_month = duesmonthcount($student_id);
		//print_r($dues_month);
?>
		<title> Demand Bill of <?php echo $student_class . " - " . $student_section; ?> </title>

		<table border='0' align='center' rules='all' cellpadding='2px' class='outer'>
			<thead>
				<tr>
					<td colspan='5' align='center'>
						<span class='name'><?php echo $full_name; ?></span><br>
						<?php echo $branch['inst_address1']; ?>,<?php echo $branch['inst_address2']; ?> <br>
						<?php echo $branch['inst_contact']; ?> | <?php echo $inst_email; ?> <br>
						<?php echo $inst_url; ?>
					</td>
				</tr>

				<tr>
					<td colspan='5' style='text-align:center; overflow-wrap: break-word;width:320px;height:25px;'>
						<b> Demand Bill : Up to <?php echo end($_REQUEST['fee_month']);	?>
						</b>
					
					</td>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td> Name </td>
					<td colspan='4'><?php echo strtoupper($student['student_name']); ?></td>
				</tr>
				<tr>
					<td> Father's Name </td>
					<td colspan='4'><?php echo strtoupper($student['student_father']); ?></td>

				</tr>
				<tr>
					<td> Adm No.</td>
					<td colspan='4'><?php echo strtoupper($student['student_admission']); ?></td>

				</tr>
				<tr>
					<td> Class/Sec. </td>
					<td colspan='2'><?php echo strtoupper($student['student_class']); ?>-<?php echo strtoupper($student['student_section']); ?> <b> (<?php echo strtoupper($student['student_type']); ?>) </b></td>
					<td> Roll No. </td>
					<td><?php echo strtoupper($student['student_roll']); ?></td>
				</tr>
				<tr>
					<td colspan='5' height='150px' valign='top'>
						<table width='320px' border='0' rules='none'>

							<?php
						
							$prev_dues = intval(get_data('student_fee', $student_id, 'current_dues', 'student_id')['data'])??0;
							$prev_session_dues = intval(get_data('student_fee', $student_id, 'prev_session_dues', 'student_id')['data'])??0;
							
							
							if ($prev_session_dues != 0) { ?>
								<tr>
									<td> Prev Session Dues </td>
									<td align='right'><?php echo $prev_session_dues; ?></td>
								</tr>
							<?php } ?>
							
							<?php 
							if ( $prev_dues != 0) { ?>
								<tr>
									<td> Previous Dues </td>
									<td align='right'><?php echo $prev_dues; ?></td>
								</tr>
							<?php } ?>

							<?php $all_fee = nmonth_fee($student_id, $_REQUEST['fee_month']);
							//print_r($all_fee);
							foreach ($all_fee as $key => $value) {
								if ($value <> 0) {
			
									if ($key <> 'total') {
										echo "<tr><td>"
											. add_space($key)
											. "<b> @" . get_fee_by_name($student_id, $key)
											//. " X " . $dues_month['count']
											. "</b></td>
						<td align='right'>" . intval($value) . "</td></tr>";
									}
								}
							}

							?>
						</table>
					</td>
				</tr>
				<tr bgcolor='lightyellow'>
					<td colspan='5'>
						<center> Please Pay Before : <?php echo date('d-M-Y', strtotime($collection_date)); ?> </center>
					</td>
				</tr>
				<tr>
					<td colspan='4' align='right'><b> Total </b> </td>
					<td align='right' class='total'>
					<b>
					    <?php echo $total = $prev_session_dues+$prev_dues + $all_fee['total']; ?>
					</b>
					<?php if ($total ==0)
					{
					    echo "<div class='zero'></div>";
					}
					?>
					</td>
				</tr>


				<tr>
					<td colspan='5' style='text-transform:capitalize;overflow:hidden;font-size:10px;'>
					In Words :<?php echo amount_in_word($total); ?>
					</td>
				</tr>

				<tr>
					<td colspan='5'>

						<small>
							<b>Instructions :-</b> </br>
							Late fine (Rs. 10/day) will be charged if pay after 10th of every month.<br>
							Always update your mobile no. to receive notification. <br>
							This receipt is auto generated only for information purpose. </br>

					</td>
				</tr>

		</table>

<?php }
} ?>