<?php 
session_start();
$date = date_default_timezone_set('Asia/Kolkata');
require_once('system/function.php');
//$sel_id =array(1,2,3,5);

	if(isset($_REQUEST['student_admission']) and $_REQUEST['student_admission'] !='')
	{
		$student_list = explode(",",$_REQUEST['student_admission']); 
	}
	else{
	
    extract($_POST);
    $student_type = "'" . implode("','", $student_type) . "'";
    $sql = "select student_admission from student where student_class ='$student_class'  and student_section = '$student_section' and student_type in ($student_type) and status in ('ACTIVE','PROMOTED') order by student_roll ";
    $res = direct_sql($sql);
    
    foreach ($res['data'] as $row) {
    	$student_list[] = $row['student_admission'];
    }

}

foreach ($student_list as $student)
	{
    $student = get_data('student', $student, null, 'student_admission')['data'];
    $sid = $student['id'];
    //$gtotal =0;
    $graph =null;
?>
<link href="https://fonts.googleapis.com/css?family=PT+Sans+Narrow&display=swap" rel="stylesheet">
<style>
	body{
		font-family: 'PT Sans Narrow', sans-serif;
	}
	.h2{
		display:inline;
		font-family:arial black;
		font-size:30px;
		margin:0px;
		padding:0px;
	}
	td{
		padding:2px;
	}
	p{
		font-size:13px;
	}
	.data{
		color:maroon;
		font-family:time new roman;
	}
	.photo{
		border-radius:50%;
	}
.reportcard{
	margin:4px auto;
}
@media print {
  #printbtn {
    display: none;
  }
  .reportcard{
        page-break-inside: avoid;
    }
  @page {size:portrait;}
}
</style>
<table align='center' rules='all' border='1' width='780px' class='reportcard'>
<tr>
	<td align='center' colspan='7'> 
	<table>
		<td>
		<img src='images/cbse.png' height='100px'>
		</td>
		<td align='center' width='500px'>
			<div class='h2'> <?php echo $full_name; ?> </div><br>
			<?php echo $inst_address1; ?>, <?php echo $inst_address2; ?> <br>
			Managed By: <?php echo $inst_managed_by; ?> <br>
			AFFILIATED TO CBSE, NEW DELHI UPTO 10+2 | AFFILIATION NO. <?php echo $aff_no; ?> <br>
			<?php echo $inst_contact; ?> | <?php echo $inst_email; ?> | <?php echo $inst_url; ?>
		</td>
		<td align='center' > 
		<img src='images/logo.png' height='100px'>
		</td>
	</table>
</tr>
<tr>
	<td colspan='7' align='center' style='background:#525252;color:#fff;height:30px;'>
		<?php echo  "REPORT CARD - ANNUAL EXAMINATION 2023-2024" ; ?>
	</td>
</tr>
<tr>
	<td colspan='7' align='center'>
		<b><?php echo get_data('student',$sid,'student_name')['data']; ?> </b>
	</td>
</tr>
<tr>
<td colspan='7'>
	<table rules='none' width='100%' border='0' >
			<td> Roll No. </td>
				<td colspan='3' width='110px'><?php echo get_data('student',$sid,'student_roll')['data']; ?></td> 
			<td rowspan='3' align='center'>	<img src='upload/<?php echo get_data('student',$sid,'student_photo')['data']; ?>' width='60' height='60' class='photo'></td>
				<td align='right'>Father's Name : </td><td colspan='3'><?php echo get_data('student',$sid,'student_father')['data']; ?></td>
		</tr>
		<tr>
			<td> Class  </td>
				<td colspan='3'><?php echo $st_class= get_data('student',$sid,'student_class')['data']; ?>-<?php echo get_data('student',$sid,'student_section')['data']; ?></td>
			<td align='right'>Mother's Name : </td>
				<td colspan='3'><?php echo get_data('student',$sid,'student_mother')['data']; ?></td >
		</tr>
		<tr>
			<td> Student ID </td>
				<td colspan='3'><?php echo $admission = get_data('student',$sid,'student_admission')['data']; ?></td> 
			<td align='right'>Date of Birth : </td>
				<td colspan='3'>
				<?php 
				//echo get_data('student',$sid,'date_of_birth');
				if(get_data('student',$sid,'date_of_birth')<>'0000-00-00'){
				echo date('d-M-Y',strtotime(get_data('student',$sid,'date_of_birth')['data']));
				}?></td >
		</tr>
	</table>
	</td>
</tr>
<tr>
	<td colspan='7' align='center' style='background:#525252;color:#fff;height:30px;'>
		SCHOLASTIC AREAS
	</td>
</tr>
<tr>
	<td colspan='7' align='center' height='245px' valign='top'>
		<table rules='all' width='100%'  border='0' bordercolor='#c5c5c5' height='200px' align='center'>
			<tr align='center'>
				
				<th colspan='5'> Term 1 (100 Marks) </th>
				<th colspan='4'> Term 2 (100 Marks) </th>
				<th colspan='4'> OverAll </th>
			</tr>
			<tr>
				<th> Subject </th>
				<!--<th> PT1 (10)</th>-->
				<th> NB (10)</th>
				<th> SE (10) </th>
				<th> HY (80)</th>
				<th> Total (100)</th>
				<!--<th> Grade</th>-->
				<!--<th> PT2 (10)</th>-->
				<th> NB (10)</th>
				<th> SE (10) </th>
				<th> AN (80)</th>
				<th> Total (100)</th>
				<th> Percent</th>
				<th> Grade</th>
			</tr>
           
				<?php
				$sub_list = subject_list($student['student_class']); //.'_subject';
				//$extra_list =extra_list($st_class); //.'_extra';
				//$subject_list = array_diff($sub_list,$extra_list);
				//print_r($extra_list);
				$graph[] =array('Subject','Marks');
				$gtotal =0;
				foreach ($sub_list as $subject_id) {
					$garr = array();
					$subject_name = get_data('subject', $subject_id, 'subject_name')['data'];
					$subject_col = get_data('subject', $subject_id, 'subject_column')['data'];
					
				
					$marks1  = get_marks($student['student_admission'], 'HY', $subject_col);
					$marks2  = get_marks($student['student_admission'], 'AN', $subject_col);
				    $pt1  = get_marks($student['student_admission'], 'PT1', $subject_col)['total'];
				    $pt2  = get_marks($student['student_admission'], 'PT2', $subject_col)['total'];
				    
				    
				//     $marks1  = get_marks_by_sid($student['id'], 'TERM-1', $subject_col);
				// 	$marks2  = get_marks_by_sid($student['id'], 'TERM-2', $subject_col);
				//     $pt1  = get_marks_by_sid($student['id'], 'PT1', $subject_col2)['total'];
				//     $pt2  = get_marks_by_sid($student['id'], 'PT2', $subject_col)['total'];
				    
				    
				    $a = $pt1*2/10;
				    $b = $pt2*2/10;
					$stotal = ($marks1['total'] + $marks2['total'])/2;
					
					$garr[] =$subject_name; 
				    $garr[] =$stotal; 
				    $gtotal += $stotal;
				?>
					<tr align='center'>
						<td align='left'> <?php echo $subject_name; ?> </td>
						<!--<td> <?php echo $a; ?> </td>-->
						<td> <?php echo $marks1['nb']; ?> </td>
						<td> <?php echo $marks1['se']; ?> </td>
						<td> <?php echo $marks1['mo']; ?> </td>
						<td> <?php echo $marks1['total']; ?> </td>
						<!--<td> <?php echo $b; ?> </td>-->
						<td> <?php echo $marks2['nb']; ?> </td>
						<td> <?php echo $marks2['se']; ?> </td>
						<td> <?php echo $marks2['mo']; ?> </td>
						<td> <?php echo $marks2['total']; ?> </td>
						<td> <?php echo $stotal; ?> </td>
						<td> <?php echo grade($stotal); ?> </td>
						
					</tr>
				<?php 
				    //$gtotal = $gtotal + $stotal+$a+$b;
				    $graph[] = $garr;
				} 
				?>
		</table>
		
	</td>
</tr>
<tr>
    <td colspan='7' align='center' valign='top'>
    <table rules='all' width='100%'  border='0' bordercolor='#c5c5c5' >
    <tr align='center' bgcolor='lightyellow'>
				<th rowspan='2'> Additional Subject </th>
				<th colspan='4'> TERM 1 RESULT </th>
				<th colspan='4'> TERM 2 RESULT </th>
				<th colspan='2'> ANNUAL RESULT </th>
			</tr>
	    <!--<th> PT1(10)</th>-->
	    <th> NB (10)</th>
		<th> SE(10) </th>
		<th> MO (80)</th>
		<th> Total </th>
		 <!--<th> PT2(10)</th>-->
		<th> NB (10)</th>
		<th> SE (10) </th>
		<th> MO (80)</th>
		<th> Total </th>
		<th> G Total </th>
		<th> Grade</th>
    </tr>

<?php 
$extra_list = extra_list($student['student_class']); 
foreach($extra_list as $extra_id) { 
   

	$subject_name = get_data('subject', $extra_id, 'subject_name')['data'];
	$subject_col = get_data('subject', $extra_id, 'subject_column')['data'];
	
    $subject_col2 =remove_space($subject_col);
	
	$extra1  = get_marks_by_sid($student['id'], 'HY', $subject_col);
	$extra2  = get_marks_by_sid($student['id'], 'AN', $subject_col2);
	$ept1  = get_marks($student['student_admission'], 'PT1', $subject_col2)['total'];
	$ept2  = get_marks($student['student_admission'], 'PT2', $subject_col)['total'];
	
	
    $estotal = $extra1['total'] + $extra2['total'];
	//$gtotal = $gtotal + $estotal;
?>

    <tr align='center'>
		<td align='left'> <?php echo $subject_name; ?> </td>
	    <!--<td> <?php echo $c = $ept1*2/10; ?> </td>-->
		<td> <?php echo $extra1['nb']; ?> </td>
		<td> <?php echo $extra1['se']; ?> </td>
		<td> <?php echo $extra1['mo']; ?> </td>
		<td> <?php echo $extra1['total']; ?> </td>
		<!--<td> <?php echo $d = $ept2*2/10; ?> </td>-->
		<td> <?php echo $extra2['nb']; ?> </td>
		<td> <?php echo $extra2['se']; ?> </td>
		<td> <?php echo $extra2['mo']; ?> </td>
		<td> <?php echo $extra2['total']; ?> </td>
		<td> <?php echo (($estotal)/2); ?> </td>
		<td> <?php echo grade(($estotal)/2); ?> </td>
	</tr>
<?php } ?>

</table>
</td>
</tr>
<tr>
	<td colspan='7' align='center' height='60px'>
	8 Point Grading Scale : A1(91-100), A2(81-90), B1(71-80), B2(61-70), C1(51-60), C2(41-50), D(33-40), E(0-32) <br>
	MO-Marks Obtained | NB - Note Book | SEA - Subject Enrichment Activity | PT1 - Periodic Test 1 | PT2 - Periodic Test 2
	</td>
</tr>

<tr style='background:#525252;color:#fff;height:30px;'>
	<th> OVERALL ANNUAL RESULT </th>
	<th><?php echo $gtotal; ?></th>
	<th>Percentage </th>
	<th><?php 
//	echo $gper = ($gtotal)/count($subject_list); 
	echo $gper = number_format(($gtotal)/count($sub_list),2); 
	?> % </th>
	<th>Grade</th>
	<th colspan='2'><?php echo grade($gper); ?></th>
</tr>

<!--
<tr>
	<td colspan='4' align='center' valign='middle' bgcolor='lightyellow'>
		<b>Co-Scholastic Areas </b><br>
		<small> (3 Points Grading Scale A,B,C)</small>
		<table width='100%' border='0'  rules='all' bgcolor='#fff'>
		 <tr bgcolor='cyan'>
		     <th> Activity </th>
		     <th> T1 </th>
		 </tr>
		<?php foreach($co_scholastic_list as $co) {?>
        <tr>
        	<td><?php echo $co; ?></td>
        	<th width='70px'><?php // echo get_co_scholastic( $student_admission, 'Half Yearly' ,remove_space($co)); ?></th>
        	
        </tr>
        <?php } ?>
		</table>
	</td>
	<td colspan='3'  align='center' valign='middle' bgcolor='lightyellow'>
        <b>Discipline </b><br>
		<small> (3 Points Grading Scale A,B,C)</small>
		
		<table width='100%' border='0' rules='all' bgcolor='#fff'>
		    <tr bgcolor='cyan'>
		     <th> Element </th>
		     <th> T1 </th>
		 </tr>
		<?php foreach($discipline_list as $dis) {?>
        <tr>
        	<td ><?php echo $dis; ?></td>
        	<th width='70px'><?php // echo get_co_scholastic( $student_admission, 'Half Yearly' ,remove_space($co)); ?></th>
        	
        </tr>
        <?php } ?>
		</table>
        
	</td>
</tr>


<tr>
	<td colspan='4' valign='top' height='20px'>
		
		<b>Remarks: </b>
	
	</td>
	<td colspan='3' valign='top' align='center'><b>Attendance</b> </td>
</tr>

<tr align='center' valign='bottom' height='50px'>
	<th colspan='4' align='left'>Class Teacher</th>

	<th colspan='3' align='right'>
	<img src='images/sign.png' height='42px'><br>
	<small>Principal </small></th>
</tr>
-->
<tr>
	<td colspan='3' align='center' valign='middle'>
		<b>Co-Scholastic Areas </b>
	</td>
	<td rowspan='5' colspan='4' width='60%' height='160px'>
	
	<div id="<?php echo 'graph_'.$sid; ?>" style="width: 450px; height: 150px"></div>
	</td>
</tr>
<?php foreach($co_scholastic_list as $co) {?>
<tr>
	<td colspan='2'><?php echo $co; ?></td>
	<th colspan='1'><?php // echo get_co_scholastic( $student_admission, 'Half Yearly' ,remove_space($co)); ?></th>
	
</tr>
<?php } ?>


<tr align='center'>
	<th colspan='3'>A - Outstanding | B - Very Good | C - Fair </th>
</tr>

<tr>
	<td colspan='5' valign='top' height='80px'>
		
		<b>Remarks: </b>
	
	</td>
	<td colspan='150px' valign='top' align='center'><b>Attendance</b> </td>
</tr>

<tr align='center' valign='bottom' height='80px'>
	<th colspan='4' align='left'>Class Teacher</th>

	<th colspan='3' align='right'>
	<img src='images/sign.jpg' height='60px'><br>
	<small>Principal <br> Seal  </small></th>
</tr>
</table>

<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable(<?php echo json_encode($graph); ?>);

        var options = {
          title: 'Performance Graph',
          legend: {position: 'top', textStyle: {color: 'green', fontSize: 16}},
		  bar: {groupWidth: "65%"},
        };

        var chart = new google.visualization.ColumnChart(document.getElementById('<?php echo 'graph_'.$sid; ?>'));

        chart.draw(data, options);
      }
    </script>
<?php
}
?>

