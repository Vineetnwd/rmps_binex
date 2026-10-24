<?php

define("BASE_URL", "https://rmpublicschool.org/binex/ppay/");
define("API_STATUS", "LIVE"); //LIVE OR UAT
define("MERCHANTIDLIVE", "M224348DPWV8Q"); //IDEAL WIN
define("MERCHANTIDUAT", "PGTESTPAYUAT");  //Sandbox testing
define("SALTKEYLIVE", "0ad63d80-ff94-4239-bb6f-d46170256f7d");
define("SALTKEYUAT", "099eb0cd-02cf-4e2a-8aca-3e6c6aff0399");
define("SALTINDEX", "1");
define("REDIRECTURL", "paymentstatus.php");
define("SUCCESSURL", "success.php");
define("FAILUREURL", "failure.php");
define("UATURLPAY", "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/pay");
define("LIVEURLPAY", "https://api.phonepe.com/apis/hermes/pg/v1/pay");
define("STATUSCHECKURL", "https://api-preprod.phonepe.com/apis/pg-sandbox/pg/v1/status/");
define("LIVESTATUSCHECKURL", "https://api.phonepe.com/apis/hermes/pg/v1/status/");


function getTransactionID(){

    return rand(1111111111,99999999999);
}

?>