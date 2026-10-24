<?php
//ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once('../opex/function.php');
require_once "config.php";
require_once "sendMail.php";

if(isset($_POST['merchantId']) && isset($_POST['transactionId']) && isset($_POST['amount']) )
    {
        $merchantId=$_POST['merchantId'];
        $transactionId=$_POST['transactionId'];
        // $amount=$_POST['amount'];
        //  $name = $_SESSION['name'] ;
        //  $email = $_SESSION['email'] ;
        //  $mobile = $_SESSION['mobile'] ;
        //  $app_no = $_SESSION['app_no'] ;




if (API_STATUS == "LIVE") {
    $url = LIVESTATUSCHECKURL . $merchantId . "/" . $transactionId;
    $saltkey = SALTKEYLIVE;
    $saltindex = SALTINDEX;
} else {
    $url = STATUSCHECKURL . $merchantId . "/" . $transactionId;
    $saltkey = SALTKEYUAT;
    $saltindex = SALTINDEX;
}



$st = "/pg/v1/status/" . $merchantId . "/" . $transactionId . $saltkey;

$dataSha256 = hash("sha256", $st);

$checksum = $dataSha256 . "###" . $saltindex;


//GET API CALLING
$headers = array(
    "Content-Type: application/json",
    "accept: application/json",
    "X-VERIFY: " . $checksum,
    "X-MERCHANT-ID:" . $merchantId
);



$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, '0');
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, '0');
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

$resp = curl_exec($curl);

curl_close($curl);

$responsePayment = json_decode($resp, true);

// echo "<pre>";
// print_r($responsePayment);
// echo "</pre>";


$tran_id = $responsePayment['data']['transactionId'];
$amount = $responsePayment['data']['amount'];
//$app_no = $responsePayment['data']['app_no'];


if ($responsePayment['success'] && $responsePayment['code'] == "PAYMENT_SUCCESS")
{
    // Update payment status in the database
    update_data('application', ['status'=>'ACTIVE','payment_status'=>'PAID','utr_id'=>$tran_id,'payment_details' =>json_encode($responsePayment['data'])], $transactionId, 'txn_id');
    
    
    $app_data = get_data('application',$transactionId,null,'txn_id')['data'];
    
    // ref_points($app_data['app_no']);
    
    $now = new DateTime();
    $timestring = $now->format('d-M-Y h:i:s');

    // Email content for payment success
    $msg = '<html><body>';
    $msg .= '<h2 style="color: #1c87c9;">Payment Confirmation</h2>';
    $msg .= '<p>Dear <strong>' . $name . '</strong>,</p>';
    $msg .= '<p>We have successfully received your payment. Please find your payment details below:</p>';
    $msg .= '<table style="border: 1px solid #ddd; width: 100%; border-collapse: collapse;">';
    $msg .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Name:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $app_data['name'] . '</td></tr>';
    $msg .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Email:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $app_data['email'] . '</td></tr>';
    $msg .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Mobile:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $app_data['mobile'] . '</td></tr>';
    $msg .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Amount:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">Rs.' . ($app_data['amount']) . '</td></tr>';
    $msg .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Transaction ID:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $tran_id . '</td></tr>';
    $msg .= '<tr><td style="border: 1px solid #ddd; padding: 8px;"><strong>Date:</strong></td><td style="border: 1px solid #ddd; padding: 8px;">' . $timestring . '</td></tr>';
    $msg .= '</table>';
    $msg .= '<p>Thank you for your payment!</p>';
    $msg .= '<p>Best Regards,<br/><strong>IDEALWIN</strong></p>';
    $msg .= '</body></html>';

    // Send Payment confirmation email
    $ob = new Mail();
    $ob->sendMail($email, $msg);
    $ob->sendMail('myofferplant@gmail.com', $msg);
    $ob->sendMail('idealwinning@gmail.com', $msg);


    // Redirect to success page
    header('Location:' . BASE_URL . "success.php?tid=" . $tran_id . "&amount=" . $amount."&txn_id=".$transactionId."&app_no=".$app_no);
}

else {

    header('Location:' . BASE_URL . "failure.php?tid=" . $tran_id . "&amount=" . $amount."&txn_id=".$transactionId);

    }

}


?>