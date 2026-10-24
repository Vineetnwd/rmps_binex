<?php
include_once('function.php');
$txn_date = (isset($_GET['txn_date']) and $_GET['txn_date'] !='')?$_GET['txn_date'] :$today;

$date_str = date('d-M-Y', strtotime($txn_date));
add_to_whatsapp($wa_owner, $inst_name, $inst_email = '', $base_url);
send_wa_template($wa_owner, 'daily_report', $full_name, $date_str , $base_url."summary.php?txn_date=$txn_date", 'Report of '.$date_str) ;
//send_wa_template('9431426600', 'daily_report', $full_name, $date_str , $base_url."summary.php?txn_date=$txn_date", 'Report of '.$date_str) ;

?>