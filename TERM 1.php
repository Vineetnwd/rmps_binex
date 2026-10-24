<?php ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// session_start();
$date = date_default_timezone_set('Asia/Kolkata');
require_once('system/function.php');
//$sel_id =array(1,2,3,5);

// 	if(!is_array($_REQUEST['sel_id']))
// 	{
// 		$sel_id[] = $_REQUEST['sel_id']; 
// 	}
// 	else{
// 		$sel_id = $_REQUEST['sel_id']; 
// 	}
extract($_POST);
$student_type = "'" . implode("','", $student_type) . "'";
$sql = "select student_admission from student where student_class ='$student_class'  and student_section = '$student_section' and student_type in ($student_type) and status in ('ACTIVE','PROMOTED') order by student_roll ";
$res = direct_sql($sql);
foreach ($res['data'] as $row) {
	$student_list[] = $row['student_admission'];
}


foreach ($student_list as $student) {
	$student = get_data('student', $student, null, 'student_admission')['data'];
	$sid = $student['id'];
	//$gtotal =0;
	$graph = null;
?>
	<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow&display=swap" rel="stylesheet">
	<style>
		body {
			font-family: 'PT Sans Narrow', sans-serif;
		}

		.h2 {
			display: inline;
			font-family: arial black;
			font-size: 36px;
			margin: 0px;
			padding: 0px;
		}

		td {
			padding: 1.5px;
		}

		p {
			font-size: 13px;
		}

		.data {
			color: maroon;
			font-family: time new roman;
		}

		.photo {
			border-radius: 50%;
		}

		.reportcard {
			margin: 4px auto;
		}

		@media print {
			#printbtn {
				display: none;
			}

			.reportcard {
				page-break-inside: avoid;
			}

			@page {
				size: portrait;
			}
		}
	</style>
	<table align='center' rules='all' border='1' width='780px' class='reportcard'>
		<tr>
			<td align='center' colspan='12'>
				<table>
					<td>
						<?php if ($db_name == 'u443617320_rmps_2425') {
							echo "<img src='./images/b1.JPG' height='150px'>";
						} else if ($db_name == 'u443617320_sch2_2425') {
							echo "<img src='images/b2.JPG' height='150px'>";
						} else if ($db_name == 'u443617320_sch3_2425') {
							echo "<img src='images/b3.JPG' height='150px'>";
						}
						?>

					</td>

					<!--	<td align='center' > 
		<img src='images/cbse.png' height='100px'>
		</td> -->
				</table>
		</tr>
		<tr>
			<td colspan='12' align='center' style='background:#525252;color:#fff;height:30px;'>
				<?php // echo  "REPORT CARD - ".strtoupper( $exam_name) ." EXAMINATION 2023-2024" ; 
				?>
				ANNUAL EXAMINATION 2024-25
			</td>
		</tr>
		<tr>
			<td colspan='12' align='center'>
				<b><?= get_data('student', $sid, 'student_name')['data']; ?> </b>
			</td>
		</tr>
		<tr>
			<td colspan='12'>
				<table rules='none' width='100%' border='0'>
					<td> Roll No. </td>
					<td colspan='3' width='110px'><?= get_data('student', $sid, 'student_roll')['data']; ?></td>
					<td rowspan='3' align='center'> <img src='required/upload/<?= get_data('student', $sid, 'student_photo')['data']; ?>' width='60' height='60' class='photo'></td>
					<td align='right'>Father's Name : </td>
					<td colspan='3'><?= get_data('student', $sid, 'student_father')['data']; ?></td>
		</tr>
		<tr>
			<td> Class </td>
			<td colspan='3'><?= $st_class = get_data('student', $sid, 'student_class')['data']; ?>-<?= get_data('student', $sid, 'student_section')['data']; ?></td>
			<td align='right'>Mother's Name : </td>
			<td colspan='3'><?= get_data('student', $sid, 'student_mother')['data']; ?></td>
		</tr>
		<tr>
			<td> Student ID </td>
			<td colspan='3'><?= $admission = get_data('student', $sid, 'student_admission')['data'] . ' - ' . $sid; ?></td>
			<td align='right'>Date of Birth : </td>
			<td colspan='3'>
				<?php
				//echo get_data('student',$sid,'date_of_birth');
				if (get_data('student', $sid, 'date_of_birth') <> '0000-00-00') {
					echo date('d-M-Y', strtotime(get_data('student', $sid, 'date_of_birth')['data']));
				} ?></td>
		</tr>
	</table>
	</td>
	</tr>
	<tr>
		<td colspan='12' align='center' style='background:#525252;color:#fff;height:30px;'>
			SCHOLASTIC AREAS
		</td>
	</tr>
	<tr>
		<td colspan='12' align='center' height='245px' valign='top'>
			<table rules='all' width='100%' border='0' bordercolor='#c5c5c5'>
				<tr align='center' bgcolor='lightyellow'>
					<th rowspan='2'> Subjects </th>
					<th colspan='10'> TERM 1 RESULT </th>
					<!--<th colspan='5'> TERM 2 RESULT </th>-->
					<!--<th colspan='2'> ANNUAL RESULT </th>-->
				</tr>
				<?php if ($student_class == 'I' || $student_class == 'II') { ?>
					<tr bgcolor='lightyellow'>
						<th>UT1</th>
						<th>UT2</th>
						<th>Best 30</th>
						<th>GA</th>
						<th>PA</th>
						<th>NB</th>
						<th>SEA</th>
						<th>Mid Term</th>
						<th>Term 1</th>
						<th>Grade</th>
					</tr>
				<?php } else { ?>
					<tr bgcolor='lightyellow'>
						<th> PT1(10)</th>
						<th> NB (5)</th>
						<th> SE(5) </th>
						<th> MO (80)</th>
						<th> Total </th>
						<th> PT2 (10)</th>
						<th> NB (5)</th>
						<th> SE (5) </th>
						<th> MO (80)</th>
						<th> Total </th>
					</tr>
				<?php } ?>
				<?php
				$sub_list = subject_list($student['student_class']); //.'_subject';
				//$extra_list =extra_list($st_class); //.'_extra';
				//$subject_list = array_diff($sub_list,$extra_list);
				//print_r($extra_list);
				$graph[] = array('Subject', 'TERM-1', 'TERM-2');
				$gtotal = 0;
				foreach ($sub_list as $subject_id) {
					$garr = array();
					$subject = get_data('subject', $subject_id)['data'];
					$subject_name = $subject['subject_name'];
					$subject_col = $subject['subject_column'];

					if ($subject_name == 'SST') {
						$subject_name = "Social Science";
					}

					// 	$marks1  = get_marks($student['student_admission'], 'TERM-1', $subject_col);
					// 	$marks2  = get_marks($student['student_admission'], 'TERM-2', $subject_col);
					//     $pt1  = get_marks($student['student_admission'], 'PT1', $subject_col)['total'];
					//     $pt2  = get_marks($student['student_admission'], 'PT2', $subject_col)['total'];


					$marks1  = get_marks_by_sid($student['id'], 'TERM 1', $subject_id, $student_class);
					$marks2  = get_marks_by_sid($student['id'], 'TERM 2', $subject_id, $student_class);
					// $pt1  = get_marks_by_sid($student['id'], 'PT1', $subject_id)['total'];
					// $pt2  = get_marks_by_sid($student['id'], 'PT2', $subject_id)['total'];

					$a = $pt1 * 2 / 10;
					$b = $pt2 * 2 / 10;
					$stotal = ($marks1['total'] + $marks2['total'] + $a + $b) / 2;

					$garr[] = $subject_name;
					$garr[] = intval($marks1['total'] + $a);
					$garr[] = intval($marks2['total'] + $b);
					$gtotal += $stotal;
					$grade = grade($stotal);
				?>
					<tr align='center'>
						<td align='left'> <?= $subject_name; ?> </td>
						<td> <?= intval($marks1['ut1']); ?> </td>
						<td> <?= intval($marks1['ut2']); ?> </td>
						<td> <?= intval($marks1['ut2']); ?> </td>
						<td> <?= intval($marks1['ga']); ?> </td>
						<td> <?= intval($marks1['pa']); ?> </td>
						<td> <?= intval($marks1['nb']); ?> </td>
						<td> <?= intval($marks1['sea']); ?> </td>
						<td> <?= intval($marks1['mid_term']); ?> </td>
						<td> <?= intval($marks1['total']); ?> </td>
						<td> <?= intval($marks1['grade']); ?> </td>

					</tr>
				<?php
					//$gtotal = $gtotal + $stotal+$a+$b;
					$graph[] = $garr;
					// $rem =get_remarks($student_class, $subject_id, $grade);
					if ($rem <> '') {
						$remarks[$subject_name] =  get_remarks($student_class, $subject_id, $grade);
					}
				}
				?>
			</table>

		</td>
	</tr>
	<tr>
		<td colspan='12' align='center' valign='top'>
			<table rules='all' width='100%' border='0' bordercolor='#c5c5c5'>
				<tr align='center' bgcolor='lightyellow'>
					<th rowspan='2'> Additional Subject </th>
					<th colspan='5'> TERM 1 RESULT </th>
					<th colspan='5'> TERM 2 RESULT </th>
					<th colspan='2'> ANNUAL RESULT </th>
				</tr>
				<th> PT1(10)</th>
				<th> NB (5)</th>
				<th> SE(5) </th>
				<th> MO (80)</th>
				<th> Total </th>
				<th> PT2(10)</th>
				<th> NB (5)</th>
				<th> SE (5) </th>
				<th> MO (80)</th>
				<th> Total </th>
				<th> G Total </th>
				<th> Grade</th>
	</tr>

	<?php
	$extra_list = extra_list($student['student_class']);
	foreach ($extra_list as $extra_id) {


		$subject_name = get_data('subject', $extra_id, 'subject_name')['data'];
		$subject_col = get_data('subject', $extra_id, 'subject_column')['data'];

		if ($st_class == 'IX' && $subject_col == 'artificial_intelligence_417' && $exam_name == 'TERM-2') {
			$subject_col = 'artificial_intelligence';
		}


		$extra1  = get_marks_by_sid($student['id'], 'TERM-1', $subject_col);
		$extra2  = get_marks_by_sid($student['id'], 'TERM-2', $subject_col);
		$ept1  = get_marks($student['student_admission'], 'PT1', $subject_col)['total'];
		$ept2  = get_marks($student['student_admission'], 'PT2', $subject_col)['total'];


		$estotal = $extra1['total'] + $extra2['total'];
		//$gtotal = $gtotal + $estotal;
	?>

		<tr align='center'>
			<td align='left'> <?= $subject_name; ?> </td>
			<td> <?= $c = $ept1 * 2 / 10; ?> </td>
			<td> <?= intval($extra1['nb']); ?> </td>
			<td> <?= intval($extra1['se']); ?> </td>
			<td> <?= intval($extra1['mo']); ?> </td>
			<td> <?= $c + $extra1['total']; ?> </td>
			<td> <?= $d = $ept2 * 2 / 10; ?> </td>
			<td> <?= intval($extra2['nb']); ?> </td>
			<td> <?= intval($extra2['se']); ?> </td>
			<td> <?= intval($extra2['mo']); ?> </td>
			<td> <?= $d + $extra2['total']; ?> </td>
			<td> <?= (($estotal + $c + $d) / 2); ?> </td>
			<td> <?= grade(($estotal + $c + $d) / 2); ?> </td>
		</tr>
	<?php } ?>

	</table>
	</td>
	</tr>
	<tr>
		<td colspan='12' align='center' height='60px'>
			8 Point Grading Scale : A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32) <br>
			MO-Marks Obtained | NB - Note Book | SEA - Subject Enrichment Activity | PT1 - Periodic Test 1 | PT2 - Periodic Test 2
		</td>
	</tr>

	<tr style='background:#525252;color:#fff;height:30px;'>
		<th> OVERALL ANNUAL RESULT </th>
		<th colspan='2'><?= $gtotal; ?></th>
		<th>Percentage </th>
		<th><?php
			//	echo $gper = ($gtotal)/count($subject_list); 
			echo $gper = number_format(($gtotal) / count($sub_list), 2);
			?> % </th>
		<th>Grade</th>
		<th colspan='4'><?= grade($gper); ?></th>
	</tr>

	<?php
	// $co_scholastic_list= co_subject_list($student['student_class']);
	$rcount  = 0; //count($co_scholastic_list)+2;
	?>
	<tr>
		<td colspan='3' align='center' valign='middle'>
			<b>Co-Scholastic Areas </b>
		</td>
		<td rowspan='<?= $rcount ?>' colspan='4' width='60%' height='160px'>

			<div id="<?= 'graph_' . $sid; ?>" style="width: 450px; height: 150px"></div>
		</td>
	</tr>
	<?php

	foreach ($co_scholastic_list as $co) {

	?>
		<tr>
			<td colspan='2'><?= $co['subject_name']; ?></td>
			<th colspan='1'><?= get_co_marks_by_sid($sid, 'TERM-2', remove_space($co['subject_name'])); ?></th>

		</tr>
	<?php } ?>



	<tr align='center'>
		<th colspan='3'>A - Outstanding | B - Very Good | C - Fair </th>
	</tr>

	<tr style='height:60px'>
		<td colspan='4' rowspan="2" valign='top' height='50px'>

			<div style='font-size:10px'> <b>Remarks:</b>
				<?php
				foreach ($remarks as $key => $value) {
					echo "<br><b>" . $key . ": </b>" . $value;
				}
				?>
			</div>

		</td>
		<td rowspan='2' valign='top' align='center'><b>Attendance</b><br>
			<? //= get_co_marks_by_sid($sid,'TERM-2', 'attendance'); 
			?> / 222

		<th colspan='2' valign='top'>Promoted</th>
	<tr>
		<th> Yes </th>
		<th> No</th>
	</tr>
	</td>
	</tr>


	<tr align='center' valign='bottom' height='80px'>
		<th colspan='4' align='left'>Class Teacher</th>

		<th colspan='3' align='right'>
			<img src='images/new_sign.png' height='60px'><br>
			Principal
		</th>
	</tr>
	</table>
	<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	<script type="text/javascript">
		google.charts.load('current', {
			'packages': ['corechart']
		});
		google.charts.setOnLoadCallback(drawChart);

		function drawChart() {
			var data = google.visualization.arrayToDataTable(<?= json_encode($graph); ?>);

			var options = {
				title: 'Performance Graph',
				legend: {
					position: 'top',
					textStyle: {
						color: 'green',
						fontSize: 16
					}
				},
				bar: {
					groupWidth: "60%"
				},
				isStacked: false // Set to true for stacked bars
			};

			var chart = new google.visualization.ColumnChart(document.getElementById('<?= 'graph_' . $sid; ?>'));
			chart.draw(data, options);
		}
	</script>

<?php
}
?>