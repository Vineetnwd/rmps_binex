<?php 
require_once("system/function.php");	?>
<title> <?php echo $inst_name; ?></title>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Roboto&display=swap');

	body {
		color: #000;
		margin: 10px !important;
		padding: 0px;
		font-family: 'Roboto', sans-serif;
	}

	td {
		font-size: 16px;
		padding: 2px;
		line-height: 25px;
		font-weight: 800;
		padding-left: 10px;
	}

	.header {
		letter-spacing: 2px;
	}

	.idcard {
		/*
	background:url('assets/img/idcard_back.png') no-repeat; 
	background-size:345px 202px;
   float:left; */
		page-break-before: always;
		width: 900px;
		border: solid 0px #ddd;
		margin: 4px 15px;
		position: relative;
	}

	.adm {
		position: absolute;
		width: 70px;
		height: 15px;
		top: 165px;
		left: 10px;
		z-index: 0;
		border: solid 0px #000;
		border-radius: 2px;
		background: #fff;
		text-align: center;
		color: maroon;
		font-weight: 600;
		font-size: 12px;
		opacity: 0.75;
	}

	.session {
		position: absolute;
		width: 45px;
		height: 10px;
		margin: auto;
		top: 32px;
		right: 3px;
		z-index: 0;
		border: solid 0px #000;
		padding: 2px 6px;
	}

	.content {
		margin-left: 100px;
		font-size: 22px;
		text-align: justified;
		line-height: 40px;
	}

	.cc {
		/*font-family: Old English Text MT;*/
		font-size: 32px;
		color: #fff;
		background:#4c4b4b;
	}

	.fdata {
		border-bottom: dotted 1px #313140;
		float: right;
		text-align: left;
		font-weight: 800;
		color: #222;
		padding-left: 10px;
	}

	.fill {
		width: 300px;
		border-bottom: dotted 1px #333;
		text-align: center;
		display: inline;
		padding: 0px 60px;
	}

	@media print {
		#printbtn {
			display: none;
		}

		.idcard {
			page-break-inside: avoid;
		}

		@page {
			size: portrait;
		}
	}
</style>
<?php
$data = decode($_GET['link']);
// $res = get_data('student', $data['student_admission'], null, 'student_admission');
$res = get_all('student', '*',['student_admission'=>$data['student_admission'],'branch_id'=>$_SESSION['branch_id']]);
// print_r($res);
if ($res['count'] > 0) {
	$student = $res['data']['0'];
	$student_admission = $student['student_admission'];
	$tcdata = get_data('tbl_tc', $student_admission, null, 'student_admission')['data'];
?>
	<table border='1' class='idcard' rules='none'>
		<tr height='50px'>
			<td colspan='4' align='center' class='header'>
				<img src='assets/images/header.jpeg' width='100%' align='left'>
					<!--<br>UDISE Code : -->
			</td>
		</tr>
		<tr valign='top' style='margin-top:-20px'>
			<td> Admission Number : <?php echo $tcdata['cbse_admission_no']; ?> </td>
			<td align='right'> Serial Number :<?php echo $tcdata['tc_no']??'..............................'; ?> </td>
		</tr>
		<tr height='60px'>
			<td bgcolor='#ddd' align='center' colspan='2' valign='middle' class='cc'>School Leaving Certificate / Transfer Certificate </td>
		</tr>
        <tr>
			<td colspan='2'> School UDISE Code: 10151100402 </td>
		</tr>
        <tr>
			<td colspan='2'>Student’s PEN:<span class='fill'> <?php echo $tcdata['pen_no']; ?></span>
				<div style='float:right'></span>Student APAR ID: <span class='fill'><?php echo $tcdata['apaar_id']; ?></div> </span>
			</td>
		</tr>
        <tr>
			<td> <div style='width:450px'>Registration No. of the candidate (in case of class IX to XII): </div></td>
			<td>
				<div class='fdata' style='width:400px'><?php echo $student['cbsereg_no']??$tcdata['cbsereg_no']; ?></div>
			</td>
		</tr>
        <tr>
			<td>Board Issued Roll Number (in case of class IX to XII): </td>
			<td>
				<div class='fdata' style='width:400px'><?php echo $student['cbseroll_no']??$tcdata['cbseroll_no']; ?></div>
			</td>
		</tr>
		<tr>
			<td> 1. Name of the Student </td>
			<td>
				<div class='fdata' style='width:400px'><?php echo strtoupper(strtolower($student['student_name'])); ?></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'> 2. Mother's Name <div class='fdata' style='width:400px'><?php echo strtoupper(strtolower($student['student_mother'])); ?> </div>
			</td>
		</tr>
		<tr>
			<td colspan='2'> 3. Father's /Guardian's Name <div class='fdata' style='width:400px'><?php echo strtoupper(strtolower($student['student_father'])); ?> </div>
			</td>
		</tr>
		<tr>
			<td colspan='2'> 4. Date of Birth (in Christian era) according to Admission & Withdrawal Register	: <div class='fdata' style='width:260px'><?php echo date('d-M-Y', strtotime($student['date_of_birth'])); ?> 
			(<?php echo $tcdata['dob_text']; ?>)
			</div></td>
		</tr>
		<tr>
			<td colspan='2'> 5. Nationality <div class='fdata' style='width:560px'> Indian <?php //echo $tcdata['nationality']; 
																							?></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'>
				6. Whether the candidate belongs to Scheduled caste or Schedule Tribe or OBC<!-- 6. Category --> <div class='fdata' style='width:300px'><?php echo $tcdata['sc_st']; ?></div>
		</tr>
		<tr>
			<td colspan='2'> 7. Date of first admission in the School with class<div class='fdata' style='width:490px'><?php echo date('d-M-Y', strtotime($tcdata['d_first_date'])) . " " . $tcdata['d_first']; ?></div>
		</tr>
		<tr>
			<td colspan='2'> 8. Class in which pupil last studied <div class='fdata' style='width:490px'> <?php echo $tcdata['last_class']; ?> </div>
		</tr>
		<tr>
			<td colspan='2'> 9. School/Board Annual Examination last taken with result <div class='fdata' style='width:460px'> <?php echo $tcdata['last_result']; ?> </div>
		</tr>
		<tr>
			<td colspan='2'> 10. Whether failed, If so once / twice in the same class <div class='fdata' style='width:460px'><?php echo $tcdata['higher_class']; ?> </div>
		</tr>
		<tr>
			<td colspan='2'> 11. Subjects studied <div class='fdata' style='width:720px'><?php echo $tcdata['subject_studies']; ?></div>
		</tr>
		<tr>
			<td colspan='2'> 12. Whether qualified for promotion in higher class <div class='fdata' style='width:490px'><?php echo $tcdata['promotion_higher_class']; ?></div>
		</tr>
        <tr>
			<td colspan='2'> <span style='margin-left:45px'>If so, to which class (in fig) :<span class='fill' style='width:525px'> <?php echo $tcdata['higher_class_fig']; ?> </span>
			</span>	(in words) <span class='fill'><?php echo $tcdata['higher_class_word']; ?></span></span>
			</td>
		</tr>
		<tr>
			<td colspan='2'> 13. Total No. of working days in the academic session <div class='fdata' style='width:490px'> <?php echo $tcdata['working_day']; ?> </div>
		</tr>
		<tr>
			<td colspan='2'> 14. Total No. of presence in the academic session: <div class='fdata' style='width:300px'><?php echo $tcdata['total_present']; ?></div> 
		</tr>
		<tr>
			<td colspan='2'> 15. Whether the pupil has paid all dues of the school:  <div class='fdata' style='width:450px'> <?php echo $tcdata['dues_paid']; ?> </div>
		</tr>
		<tr>
			<td colspan='2'> 16. Whether the pupil was in receipt of any fee concession, is so the nature of such concession: <div class='fdata' style='width:160px'><?php echo $tcdata['consession']; ?></div> 
		</tr>
		<!--<tr>-->
		<!--	<td colspan='2'> 15. Total numbers of working days <span class='fill'> <?php echo $tcdata['working_day']; ?> </span>-->
		<!--		<div style='float:right'></span>Total numbers of working days present <span class='fill'> <?php echo $tcdata['total_present']; ?> </span></div>-->
		<!--</tr>-->
		<tr>
			<td colspan='2'> 17. Where NCC Cadet/Boy Scout/Girl Guide (details may be given) <div class='fdata' style='width:320px'> <?php echo $tcdata['ncc']; ?></div>
		</tr>
		<tr>
			<td colspan='2'> 18. Game played or extracurricular activities in which the pupil usually took part (mention achievement level there in)
		</tr>

		<tr>
			<td colspan='2'>
				<div class='fdata' style='width:850px'> <?php echo $tcdata['game']; ?></div>
		</tr>
		<tr>
		<tr>
			<td colspan='2'> 19. General Conduct <div class='fdata' style='width:660px'> <?php echo $tcdata['conduct']; ?></div>
		</tr>
		<tr>

			<td colspan='2'> 20.Date of application of certificate <div class='fdata' style='width:600px'> <?php echo date('d-M-Y', strtotime($tcdata['doa_certificate'])); ?></div>
			</td>
		</tr>
		<tr>

			<td colspan='2'> 21.Date of issue of certificate <div class='fdata' style='width:600px'><?php echo date('d-M-Y', strtotime($tcdata['doi_certificate'])); ?></div>
			</td>
		</tr>
		<tr>
			<td colspan='2'> 22. Reason of leaving the school <div class='fdata' style='width:610px'> <?php echo $tcdata['reason_leaving']; ?></div>
		</tr>
		<tr>
			<td colspan='2'> 23. Any other remarks <div class='fdata' style='width:710px'> <?php echo $tcdata['other_remarks']; ?> </div>
		</tr>
		<tr style="height:60px"></tr>
		<tr>
			<td colspan='2' valign='bottom' height='150px'>
				<table border='0' width='100%'>
					<tr>
						<td> Prepared by <br>(Name & Designation) </td>
						<td> Checked By<br>(Name & Designation)  </td>
						<td align='center'> Sign. of Principal  <br> <span style='padding-right:5px'> with official seal </span> </td>
					</tr>
					<tr>
						<td colspan='3' align='center'>
							<input type='button' id='printbtn' onclick='window.print()' value='Print'>
						</td>
					</tr>
				</table>
			</td>
		</tr>

	</table>

<?php } ?>