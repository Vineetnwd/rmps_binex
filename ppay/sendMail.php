<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require './vendor/autoload.php';

class Mail
{
function sendMail($to='',$msg='')
{
	try {

	$mail = new PHPMailer(true);
	$mail->SMTPDebug = 0;
	$mail->isSMTP();
	$mail->Host	 = 'smtp.hostinger.com';
	$mail->SMTPAuth = true;
	$mail->Username = 'info@idealwin.org';
	$mail->Password = 'Info@@2024';
	$mail->SMTPSecure = 'ssl';
	$mail->Port	 = 465;
	$mail->setFrom('info@idealwin.org', 'Ideal Win');
	$mail->addAddress($to);
	$mail->isHTML(true);
	$mail->Subject = 'Payment Successfully Completed';
	$mail->Body = $msg;
	// $mail->AltBody = 'Body in plain text for non-HTML mail clients';
	return $mail->send();

} catch (Exception $e) {
	echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
}

}
?>