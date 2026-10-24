<?php
//ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
//https://idealwin.org/ppay/success.php?tid=T2410130709301765636392&amount=100&txn_id=IWIN-12592030895
include_once('../opex/function.php');
require_once "config.php";
require_once "sendMail.php";
 $utr_id = $_GET['tid'];
 //$application_no = (isset($_GET['app_no']))?$_GET['app_no']:$_SESSION['app_no'];
 $txn_id = $_GET['txn_id'];
 $amount = $_GET['amount'];
 $res  = get_data('application',$txn_id,null,'txn_id');
// print_r($res);
 extract($res['data']);
 
$pass = substr($name, 0, 3). date("Y", strtotime($date_of_birth));
$loginUrl = 'https://idealwin.org/opex/system/op_login.php';

?>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:400,400i,700,900&display=swap" rel="stylesheet">
  </head>
    <style>
      body {
        text-align: center;
        padding: 40px 0;
        background: #EBF0F5;
      }
        h1 {
          color: #88B04B;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-weight: 900;
          font-size: 40px;
          margin-bottom: 10px;
        }
        p {
          color: #404F5E;
          font-family: "Nunito Sans", "Helvetica Neue", sans-serif;
          font-size:20px;
          margin: 0;
        }
      i {
        color: #9ABC66;
        font-size: 100px;
        line-height: 200px;
        margin-left:-15px;
      }
      .card {
        background: white;
        padding: 60px;
        border-radius: 4px;
        box-shadow: 0 2px 3px #C8D0D8;
        display: inline-block;
        margin: 0 auto;
      }
    </style>
    <body>
      <div class="card">
      <!--<div style="border-radius:200px; height:200px; width:200px; background: #F8FAF5; margin:0 auto;">-->
      <!--  <i class="checkmark">✓</i>-->
      <!--</div>-->

        <h1>Success</h1>
       
        <?php 

        
        // Email content with login details
        $msg2 = '<html><body>';
        $msg2 .= '<p>Dear <strong>' . $name . '</strong>,</p>';
        $msg2 .= '<p>Your account has been successfully created. Below are your login credentials:</p>';
        $msg2 .= '<table style="border: 1px solid #ddd; width: 100%; border-collapse: collapse;">';
        $msg2 .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Username:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $app_no . '</td></tr>';
        $msg2 .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Password:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $pass . '</td></tr>';
         $msg2 .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Email:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $email . '</td></tr>';
       
        $msg2 .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Transaction ID:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $_GET['tid'] . '</td></tr>';
        $msg2 .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Amount:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $amount . '</td></tr>';
        $msg2 .= '</table>';
        $msg2 .= '<p>You can log in using the following link:</p>';
        $msg2 .= '<a href="' . $loginUrl . '" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-align: center; text-decoration: none; display: inline-block;">Login Here</a>';
        $msg2 .= '<p>We recommend changing your password after logging in. We already send this on your mail . Save this for future.</p>';
      //  $msg2 .= '<p>Best Regards,<br/><strong>IDEALWIN</strong></p>';
        $msg2 .= '</body></html>';
        
        // Send user password and login email
        $ob = new Mail();
        $ob->sendMail($email, $msg2);
        echo $msg2;
        
        //Updating Wallet With Amount 
       
        //update_data('application',['status'=>'ACTIVE','payment_status'=>'PAID','utr_id'=>$utr_id],$app_no,'app_no');
        ?>
      </div>
    </body>
</html>