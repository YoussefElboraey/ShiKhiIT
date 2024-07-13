<?php

require "modules/PHPMailer/autoload.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mailer = new PHPMailer(true);

$mailer->isSMTP();

$mailer->Host = "mail.gym.c1.is";
$mailer->SMTPAuth = true;
$mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$mailer->Port = 587;
$mailer->Username = "test@gym.c1.is";
$mailer->Password = 12345678;

$mailer->SMTPOptions = array("ssl" => array(

	"verify_peer" => false,
	"verify_peer_name" => false,
	"allow_self_signed" => true

));

$mailer->setFrom("test@gym.c1.is" , "ShiKhiIT");
$mailer->addAddress($email , $first_name);

$mailer->isHTML(true);
$mailer->Subject = "E-Mail Verification";
$mailer->Body = '
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
        }
        .content {
            line-height: 1.6;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            background-color: #24004f;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Email Verification</h1>
        </div>
        <div class="content">
            <p>Hi ' . $first_name . ',</p>
            <p>Thank you for registering with us. Please click the button below to verify your email address and complete your registration:</p>
            <a href="' . $_SERVER["SERVER_NAME"] . "/verify?identifier=$identifier" . '" class="button">Verify Email</a>
            <p>If you did not create an account, no further action is required.</p>
            <p>Thank you,<br>The ShiKhiIT Team</p>
        </div>
        <div class="footer">
            <p>If you’re having trouble clicking the "Verify Email" button, copy and paste the URL below into your web browser:</p>
            <p><a href="' . $_SERVER["SERVER_NAME"] . "/verify?identifier=$identifier" . '">' . $_SERVER["SERVER_NAME"] . "/verify?identifier=$identifier" . '</a></p>
        </div>
    </div>
</body>
</html>
';

$mailer->send();

?>
