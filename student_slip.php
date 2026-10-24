<?php
error_reporting( E_ALL );
require_once('system/function.php');
verify_request();
$data = decode($_GET['link']);

$student_id = $data['id'];
$student = get_data('student', $student_id)['data'];
?>
<title> Admission Slip  <?php echo $rid; ?> </title>
<script type="text/javascript" src="js/towords.js"></script>
<style>
body{
	font-family:calibri,arial, time new roman;
	font-size:10px;
	padding:0px;
	margin:0px;
}
.cancel, .CANCEL{
    text-decoration: line-through;
    color:red;
}
.tbl{
    margin-left:15px;
    width:360px;
    float:left;
	margin-bottom:10px;
}
td{
    font-weight:300; 
    font-size:14px;
	padding:2px;
}
td .head{
	font-size:11px;
}
.btn{
	border:solid 1px #ddd;
	padding:4px;
	margin:10px;
	background:#f5f5f5;
	text-decoration:none;
	color:#222;
	text-transform:uppercase;
	font-weight:800;
}
.no-print{
    padding:15px 50px;
    width:450px;
}
.name{
	display:inline;
	font-size:16px;
	line-height:24px;
	font-weight:800;
}
@media print {
         .btn{display:none;}
      }	
@media print{@page {size: portrait}}
</style>
<?php
$slip_list = ['School','Parent'];
foreach($slip_list as $receipt_copy)
{
    ?>

<!--<table  border='1'  rules ='all' cellpadding='5px' width='70%' align='center'>-->
	
	<table class='tbl'  border='1'  rules ='all' align='right' style='margin-right:10px'>

	<thead>
		<tr>
			<td colspan='5' align='center' class='head'> 
				<img align='left' src='assets/images/logo.png' height='80px'>
				<span style='font-size:18px;font-weight:600;'> <?php echo $full_name; ?> </span><br>
				<?php echo $aff_to; ?> <br>
				<b>Aff. No. : <?php echo $aff_no;?>  School Code :  <?php echo $school_code;?></b><br>
				<?php 
				$binfo = get_data('branch_list',$student['branch_id'])['data'];
				
				echo $binfo['inst_address1'] . $binfo['inst_address2'] ?> <br>
				 📱 +91 <?php echo  $binfo['inst_contact'] ?> 🌐 <?php echo $inst_url; ?>
				 <!--<br> 📧 <?php echo  $branch['inst_email']; ?>-->
			</td>
		</tr>
		<tr bgcolor='#f5f5f5'>
			<td colspan='3'> Adm No. : <b><?= strtoupper($student['student_admission']); ?></b></td>
			<td align='right' colspan='2'><?php echo date('d-M-Y h:i A', strtotime($student['created_at'])); ?></td>
		</tr>
		<tr>
			<td colspan='5' style='text-align:center'>
			<strong style='font-weight:700'> Admission Slip (<?=$current_session?>) - <?= $receipt_copy ?> Copy</strong>
			</td>
		</tr>
	</thead>
	<tbody >
		<tr>
			<td > Name </td> <td colspan='3'><?php echo strtoupper($student['student_name']); ?></td>
			<td rowspan='6' style="text-align: center; vertical-align: middle;width:100px">
              <img src='<?= $base_url.'/upload/'. $student['student_photo']; ?>' style="display: inline-block;width:100px">
            </td>

		</tr>
		
		<tr>
			<td> Mother's Name </td> <td colspan='3'><?php echo strtoupper($student['student_mother']); ?></td>
		</tr>
		<tr>
			<td> Father's Name </td> <td colspan='3'><?php echo strtoupper($student['student_father']); ?></td>
		</tr>
		<tr>
			<td> Gender </td><td colspan='3'><?php echo strtoupper($student['student_sex']); ?></td>
		</tr>
		<tr>
			<td> Class </td> <td colspan='3'><?php echo $student['student_class']; ?>-<?php echo $student['student_section']; ?> (<?php echo $student['student_type']; ?>)</td>
		</tr>
		<tr>
			<td>Roll No. </td><td colspan='3'> <?php echo $student['student_roll']; ?></td>
		</tr>
		<tr>
			<td>Mobile No. </td><td colspan='4'> <?php echo $student['student_mobile']; ?></td>
		</tr>
		<tr>
			<td>Whatsapp No </td><td colspan='4'> <?php echo $student['student_whatsapp']; ?></td>
		</tr>
		<tr>
			<td>Student Type </td><td> <?php echo $student['student_type']; ?></td>
			<td colspan='2'>Fin. Type </td><td> <?php echo $student['finance_type']; ?></td>
		</tr>
		<?php if($student['student_type']=='TRANSPORT'){ ?>
		<tr>
			<td>Bus Stop </td><td colspan='4'> <?php echo get_data('transport_area',$student['area_id'],'area_name')['data'] ?></td>
		</tr>
		<?php } ?>
		<tr>
			<td>Branch </td><td colspan='4'> <?php echo get_data('branch_list',$student['branch_id'],'inst_name')['data'] ?></td>
		</tr>
		<tr>
			<td>Adm Type </td><td colspan='4'> <?php echo $student['admission_type'] ?></td>
		</tr>
		<?php if($receipt['remarks'] !='') { ?>
		<tr><td colspan='5' align='center'>Remarks: <?php echo $receipt['remarks']; ?>
		<?php }?>
		</td></tr>
		<?php if($receipt['discount'] <>0) { ?>
		<tr>
			<td colspan='4' align='right'> Discount (-) </td> <td align='right'><?php echo $receipt['discount']; ?></td>
		</tr>
		<?php } ?>
		<!--<tr>-->
		<!--	<td colspan='4' align='right'> Total </td> <td align='right'><?php echo $receipt['total']; ?></td>-->
		<!--</tr>-->
		
		<!--<tr bgcolor='#d6d6d6'>-->
		<!--	<td colspan='4' align='right' ><b> Paid Amount (In <?php echo $paid =$receipt['payment_mode']; ?>) </b> </td> <td align='right'><b><big><?php echo $paid = $receipt['paid_amount']; ?></big></b></td>-->
		<!--</tr>-->
		
		<!--<tr>-->
		<!--	<td colspan='5' style='text-transform:capitalize;' >-->
		<!--	    <b> Amount in Words : </b><?php echo amount_in_word($paid);?> Only -->
		<!--	</td>-->
		<!--</tr>-->
		<!--<tr style='color:red'>-->
		<!--	<td colspan='4'> Current Dues </td> <td align='right'><?php echo $receipt['current_dues']; ?></td>-->
		<!--</tr>-->
		
	<!--<tr>-->
	<!--	<td colspan='5' >-->
		
	<!--		<small>-->
	<!--		<b>Declaration :-</b><br>-->
	<!--	        I agree to abide by the rules and regulations of the institution and understand that any violation of these rules may result in disciplinary actions as deemed necessary by the school authorities.-->
			
	<!--	</td>-->
	<!--</tr>-->
	<tr>
		<td colspan='2' style='height:60px'> Issued by : <?php echo get_data('user',$_SESSION['user_id'],'user_name')['data']; ?>
		</td >
		<td colspan='3' align='right'>
		Authorised Signatory 
		</td>
	</tr>
	
	
	</table>


<?php } ?>


	<div class='no-print'>
			<!--<a href='collect_fee' class='btn' accesskey ='n' style='margin-top:20px'> New Payment (Use Alt+N) </a>-->
			<a href='' class='btn' onClick='window.print()'>  PRINT (Use Ctrl +P) </a>
	</div>