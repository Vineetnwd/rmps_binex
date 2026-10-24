<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once('system/function.php');  

echo "<pre>";
$res = direct_sql("SELECT id, class_cat, subject, subject_name, branch_id, A1, A2, B1, B2, status FROM remark");
print_r($res);
echo "</pre>";
?>
