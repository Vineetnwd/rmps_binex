<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");

require_once('system/function.php');

// print_r($_POST);
extract($_POST);
$db_name = 'u443617320_rmps_26';
$db_user = 'u443617320_rmps_26';
$con = mysqli_connect('localhost',$db_user,$db_password,$db_name);
if(isset($_FILES['student_photo']['name']) and $_FILES['student_photo']['name']!='')
{
  $_POST['student_photo'] = upload_img('student_photo')['id'];
}

if(isset($_FILES['student_tc']['name']) and $_FILES['student_tc']['name']!='')
{
  $_POST['student_tc'] = upload_img('student_tc')['id'];
}

$res = insert_data($db_name.'.admission',$_POST); //u443617320_rmps_25
// print_r($_POST);

$app_id = date('ymd').sprintf("%04d", $res['id']);
$res2= update_data($db_name.'.admission',array('app_no' => $app_id), $res['id']);



$link = encode('student_name=' . $_POST['student_name'] . '&id=' . $res['id']);

$full_link = $base_url.'application_print.php?link='.$link;

// $wa_sms ="Dear *$student_name*,
// Your application for admission in class *$student_class* is recevied. 
// Your application No. is $app_id Kindly pay amount *₹ 500* to complete the your application.

// Click on link to Print Application :  
// $full_link";
//$st = wa_send($student_whatsapp, $wa_sms);

$res2['exam_date'] = $exam_date= get_phase($app_id)['exam_date'];
$res2['phase'] = $phase= get_phase($app_id)['phase'];
$res2['application_no'] = $app_id;

$res3= update_data($db_name.'.admission',array('exam_date' => $exam_date, 'phase'=>$phase,'branch_id'=>$_POST['branch_id']), $res['id']);

json_encode($res2);
echo "<script> window.location ='$full_link' </script>";
?>
