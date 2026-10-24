<?php
require_once('system/function.php');

$ct = rand(30,60);

$sql ="select * from wa_sms where status ='PENDING' order by id limit $ct";
$res4 = direct_sql($sql);

foreach($res4['data'] as $row)
{
   
   $mobile  = $row['mobile'];
   $msg  = $row['msg'];
   echo wa_send($mobile,$msg);
   // echo wa_send('9431426600',$msg);
   $re1=  update_data('wa_sms', ['status'=>'SENT'], $row['id']);
}

?>
