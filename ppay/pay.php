<?php
include_once('../opex/function.php');
require_once "config.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// print_r($_POST);
if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['mobile']) && isset($_POST['amount']) && isset( $_POST['app_no'])) {
    
    $app_no = $_POST['app_no'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $amount = $_POST['amount'];

    $_SESSION['name']  = $name;
    $_SESSION['email']  = $email;
    $_SESSION['mobile']  = $mobile;
    $_SESSION['app_no']  = $app_no;

// session_start();

if (API_STATUS == "LIVE") {
    $merchantid  = MERCHANTIDLIVE;
    $saltkey = SALTKEYLIVE;
    $saltindex = SALTINDEX;
    $pay_url = LIVEURLPAY;
} else {
      
    $merchantid  = MERCHANTIDUAT;
    $saltkey = SALTKEYUAT;
    $saltindex = SALTINDEX;
    $pay_url = UATURLPAY;
    
}


    $txn_id = "txn-".rand(1000,9999).'-'.$app_no;// getTransactionID();
    $mid = 'M-'.$_SESSION['user_name'].uniqid();
    $mid = 'M-'.$app_no.uniqid();
    
    $resp = update_data('application',['amount'=>$amount, 'txn_id'=>$txn_id,'payment_status'=>'PENDING'], $app_no, 'app_no');
   // print_r($resp);
    
    $payLoad = array(
        'merchantId' => $merchantid,
        'merchantTransactionId' => $txn_id, // test transactionID
        "merchantUserId" => $mid,
        'amount' => intval($amount) * 100, // phone pe works on paise
        'redirectUrl' => BASE_URL . REDIRECTURL,
        'redirectMode' => "POST",
        'callbackUrl' => BASE_URL . REDIRECTURL,
        "mobileNumber" => $mobile,
        "email" => $email,
        // "param1"=>$email,
        "paymentInstrument" => array(
            "type" => "PAY_PAGE",
        )
    );


    $jsonencode = json_encode($payLoad);

    $payloadbase64 = base64_encode($jsonencode);

    $payloaddata = $payloadbase64 . "/pg/v1/pay" . $saltkey;


    $sha256 = hash("sha256", $payloaddata);

    $checksum = $sha256 . '###' . $saltindex;

    echo $checksum;


    $request = json_encode(array('request' => $payloadbase64));

    $curl = curl_init(); // This extention should be installed

    curl_setopt_array($curl, [
        CURLOPT_URL => $pay_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $request,
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json",
            "X-VERIFY:" . $checksum,
            "accept: application/json"
        ],
    ]);

    $response = curl_exec($curl);

    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
      echo "cURL Error #:" . $err;
    } else {
      $res = json_decode($response);

      echo "<br/>response===";
      print_r($res);

      if (isset($res->success) && $res->success == '1') {
        // $paymentCode=$res->code;
        // $paymentMsg=$res->message;
        $payUrl = $res->data->instrumentResponse->redirectInfo->url;
        header('Location:' . $payUrl);
      }

    }
}
