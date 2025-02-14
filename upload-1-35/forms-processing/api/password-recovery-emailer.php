<?php
include 'email-system-credentials.php'; // Include email credentials
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header("Content-Type: application/json");

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["status" => "error", "message" => "Invalid request method. Only POST is allowed."]);
    exit;
}

// Get request payload
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$requiredFields = ['user_id', 'email', 'username'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["status" => "error", "message" => "Missing required field: $field"]);
        exit;
    }
}

// Assign variables from the request
$userId = $data['user_id'];
$email = $data['email'];
$username = $data['username'];

// Generate the recovery URL
$recoveryUrl = "https://test.hammad-khan.org/forms-processing/password-recovery.html?user_id=$userId";

// Generate the email subject
$subject = "Password Recovery Request";

// Prepare the email content
$emailContent = <<<HTML
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>
    <div style="width:100%;font-family:'Roboto', sans-serif;padding:0;Margin:0">
        <div style="background-color:#f6f6f6">
            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;">
                <tr>
                    <td align="center" style="padding:20px 20px 0px 20px">
                        <img src="https://test.hammad-khan.org/forms-processing/assets/images/logo.svg" alt="Logo" style="width:100%;max-width:200px;">
                    </td>
                </tr>
                <tr>
                    <td align="left" style="padding:20px">
                        <h2 style="font-family:arial, 'Roboto', sans-serif;font-size:26px;color:#00984a;">
                            <strong>Password Recovery Request</strong>
                        </h2>
                        <p>Dear <strong>$username</strong>,</p>
                        <p>You recently requested to reset your password. Please click the button below:</p>
                        <p><a href="$recoveryUrl" style="display:inline-block;padding:10px 20px;background-color:#B5CB16;color:#ffffff;text-decoration:none;border-radius:5px;font-size:16px;">Recover Password</a></p>
                        <p>If you did not request this change, please ignore this email.</p>
                        <p>For any questions, feel free to contact us at <a href="mailto:hkhan@mercywithoutlimits.org">hkhan@mercywithoutlimits.org</a>.</p>
                        <p>Best regards,<br>Support Team<br>Mercy Without Limits</p>
                    </td>
                </tr>
            </table>
            <!-- Footer starts here -->
        <table width="645" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="border-collapse:collapse;border-spacing:0px;background-color:#ffffff">
            <tbody>
                <tr style="height:22.7pt">
                    <td colspan="3" valign="top" style="width:50%;background:#B5CB16;padding:0.15in 5.4pt 0in 5.4pt;height:22.7pt">
                        <p class="MsoNormal" align="center" style="text-align:center">
                            <span style="font-size:10.0pt;font-family:'Roboto', sans-serif;color:#0E0E0D">Follow us on social media:</span>
                        </p>
                        <p class="MsoNormal" align="center" style="text-align:center">
                            <span style="font-size:10.0pt;font-family:'Roboto', sans-serif;color:white">
                                <a href="https://www.facebook.com/MWLimits/" target="_blank"><img border="0" width="29" height="29" src="https://mwlimits.org/wp-content/uploads/2024/11/Facebook.png" alt="Facebook"></a>
                                <a href="https://twitter.com/mwlimits" target="_blank"><img border="0" width="29" height="29" src="https://mwlimits.org/wp-content/uploads/2024/11/X.png" alt="Twitter"></a>
                                <a href="https://www.instagram.com/mwlimits/" target="_blank"><img border="0" width="29" height="29" src="https://mwlimits.org/wp-content/uploads/2024/11/Instagram.png" alt="Instagram"></a>
                                <a href="https://www.youtube.com/@MWLimits" target="_blank"><img border="0" width="29" height="29" src="https://mwlimits.org/wp-content/uploads/2024/11/YouTube.png" alt="YouTube"></a>
                                <a href="https://www.linkedin.com/company/mwlimits-org/" target="_blank"><img border="0" width="29" height="29" src="https://mwlimits.org/wp-content/uploads/2024/11/LinkedIn.png" alt="LinkedIn"></a>
                            </span>
                        </p>
                    </td>
                    <td valign="top" style="width:50%;background:#B5CB16;padding:0.15in 5.4pt 10.4pt 5.4pt;height:22.7pt">
                        <p class="MsoNormal" align="center" style="text-align:center">
                            <span style="font-size:10.0pt;font-family:'Roboto', sans-serif;color:black">Mercy Without Limits<br>
                                11661 College Blvd Suite 105<br>
                                Overland Park, KS 66210<br>
                                1-855-633-3695<br>
                                <a href="mailto:info@mercywithoutlimits.org" style="font-size:10.0pt;color:#000000;text-decoration:none;">info@mercywithoutlimits.org</a>
                            </span>
                        </p>
                    </td>
                </tr>
                <tr style="height:22.7pt">
                    <td colspan="4" valign="top" style="padding:20px;height:22.7pt;text-align:center;">
                        <img border="0" width="204" height="65" src="https://mwlimits.org/wp-content/uploads/2024/11/thanks-img-new.jpg" alt="Thank you image">
                        <p style="font-size:10.0pt;font-family:'Roboto', sans-serif;">© 2025 Mercy Without Limits. All rights reserved.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Footer ends here -->

        </div>
    </div>
</body>
</html>
HTML;

try {
    // Initialize PHPMailer
    $mail = new PHPMailer(true);

    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = EMAIL_OUTGOING_SERVER; // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME; // SMTP username
    $mail->Password = EMAIL_PASSWORD; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Encryption
    $mail->Port = EMAIL_SMTP_PORT; // SMTP port

    // Email Metadata
    $mail->setFrom(EMAIL_USERNAME, 'Support Team');
    $mail->addAddress($email, $username);
    $mail->Subject = $subject;
    $mail->isHTML(true);
    $mail->Body = $emailContent;

    // Send Email
    $mail->send();
    echo json_encode(["status" => "success", "message" => "Password recovery email sent successfully"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Email could not be sent. Error: {$mail->ErrorInfo}"]);
}
?>
