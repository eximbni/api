   <?php
if (isset($_SERVER['HTTP_ORIGIN']))
{
    header("Access-Control-Allow-Origin:*");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400'); // cache for 1 day
    
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS')
{

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
}

$postdata = file_get_contents("php://input");
$request = json_decode($postdata);
include ("config.php");
//include("fcmpush.php");
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
//require 'PHPMailer/src/PHPMailerAutoload.php';
require '../PHPMailer/src/SMTP.php';

$fullname ="Testing mail by Ganesh Y";
$fmobile = "917744966820";
$address ="Hyderabad";
$company = "Ganesh Export Pvt Ltd";
$sponcer_id ="hgjhg";
$platform = "platform";
$manufacturer = "manufacturer";

            $message = "New Registrtion in EXIM BNI. <br> User name : <strong>" . $fullname . " </strong>,<br>  Moblie No :  <strong>+" . $fmobile . " </strong>, <br> Address : <strong>" . $address . " </strong>, <br> Business Name : <strong>" . $company . " </strong>, <br> Sponcer ID :  <strong>" . $sponcer_id . " </strong>, <br> Operating System :  <strong>" . $platform . " </strong>,<br>  Device Model :  <strong>" . $manufacturer." </strong>.";

            $email = 'info@eximbin.com';
            $password = 'EximBni.2020';
            $to_email = 'miioslimited@gmail.com';
            $to_cc = 'logins@eximbni.com';
            $to_bcc = 'muralimiios@gmail.com';
            $to_bcc1 = 'ganesh.vab@gmail.com';
            $message = $message;
            $subject = "New registration ";

            $mail = new PHPMailer(); // create a new object
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth = true; // authentication enabled
            $mail->SMTPSecure = 'none'; // secure transfer enabled REQUIRED for Gmail
            $mail->Host = "mail.eximbin.com";
            $mail->Port = 587; // or 587
            $mail->IsHTML(true);
            $mail->Username = $email;
            $mail->Password = $password;
            $mail->SetFrom($email);
            $mail->Subject = $subject;
            $mail->Body = $message;
            $mail->AddAddress($to_email);
            $mail->AddCC($to_cc);
            $mail->AddBCC($to_bcc);
            $mail->AddBCC($to_bcc1);
            if($mail->Send()){
                  echo "Sent Mail Success";
            }else{
                  echo "Not Sent";
            }


?>
