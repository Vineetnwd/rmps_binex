<?php
include('function.php');
$dt = date('d');
$mt = date('m');
		$sql ="select * from student where day(date_of_birth) = '$dt' and month(date_of_birth) ='$mt'";
		
		$res = direct_sql($sql);
		
	    foreach($res['data'] as $row)
		{
		$stu_id =$row['student_id'];
		$stu_name =$row['student_name'];
		$stu_mobile =$row['student_mobile'];
		
		echo $sms = "Dear " .$stu_name. ", \nwishing you a day filled with happiness and a year filled with joy. Happy birthday.  \nRegards  $inst_name Via Bine";
		$te_id='1507163688607495378';
		send_msg($stu_mobile,$sms, $te_id);
		}
?>

