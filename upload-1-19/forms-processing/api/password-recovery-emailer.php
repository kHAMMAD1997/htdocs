<?php
include 'email-system-credentials.php'; // Include email credentials

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
            <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;border-spacing:0px;padding:0;Margin:0;width:100%;height:100%;background-repeat:repeat;background-position:center top">
                <tr>
                    <td valign="top" style="padding:0;Margin:0">
                        <table cellspacing="0" cellpadding="0" align="center" style="border-collapse:collapse;border-spacing:0px;table-layout:fixed!important;width:100%">
                            <tr>
                                <td align="center" style="padding:0;Margin:0">
                                    <table width="645" cellspacing="0" cellpadding="0" bgcolor="#ffffff" align="center" style="border-collapse:collapse;border-spacing:0px;background-color:#ffffff">
                                        <tr>
                                            <td align="center" style="padding:20px 20px 0px 20px">
                                                <img src="https://test.hammad-khan.org/forms-processing/assets/images/logo.svg" alt="Logo" style="display:block;border:0;outline:none;text-decoration:none;width:100%;max-width:200px;">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td align="left" style="Margin:0;padding-top:20px;padding-bottom:20px;padding-left:20px;padding-right:20px">
                                                <h2 style="font-family:arial, 'Roboto', sans-serif;font-size:26px;color:#00984a;">
                                                    <strong>Password Recovery Request</strong>
                                                </h2>
                                                <p>Dear <strong>$username</strong>,</p>
                                                <p>You recently requested to reset your password. Please click the button below to proceed with your password recovery:</p>
                                                <p><a href="$recoveryUrl" style="display:inline-block;padding:10px 20px;background-color:#B5CB16;color:#ffffff;text-decoration:none;border-radius:5px;font-size:16px;">Recover Password</a></p>
                                                <p>If you did not request this change or believe this is an error, please ignore this email.</p>
                                                <p>For any questions, feel free to contact us at <a href="mailto:info@mercywithoutlimits.org">info@mercywithoutlimits.org</a>.</p>
                                                <p>Best regards,</p>
                                                <p>Support Team<br>Mercy Without Limits</p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <!-- Footer starts here -->
    <div style="background-color:#ffffff;padding:20px;text-align:center;">
        <p style="font-size:10pt;font-family:'Roboto', sans-serif;color:black;">
            Mercy Without Limits<br>
            11661 College Blvd Suite 105<br>
            Overland Park, KS 66210<br>
            1-855-633-3695<br>
            <a href="mailto:info@mercywithoutlimits.org" style="color:#000000;text-decoration:none;">info@mercywithoutlimits.org</a>
        </p>
        <p style="font-size:10pt;font-family:'Roboto', sans-serif;">
            Follow us on social media:<br>
            <a href="https://www.facebook.com/MWLimits/" target="_blank">Facebook</a> |
            <a href="https://twitter.com/mwlimits" target="_blank">Twitter</a> |
            <a href="https://www.instagram.com/mwlimits/" target="_blank">Instagram</a> |
            <a href="https://www.linkedin.com/company/mwlimits-org/" target="_blank">LinkedIn</a> |
            <a href="https://www.youtube.com/@MWLimits" target="_blank">YouTube</a>
        </p>
        <p style="font-size:10pt;font-family:'Roboto', sans-serif;">© 2024 Mercy Without Limits. All rights reserved.</p>
    </div>
    <!-- Footer ends here -->
</body>
</html>
HTML;

// Email headers as a properly formatted string
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=UTF-8\r\n";
$headers .= "From: " . EMAIL_USERNAME . "\r\n";
$headers .= "Reply-To: " . EMAIL_USERNAME . "\r\n";

// Send the email
$sent = mail($email, $subject, $emailContent, $headers);

if (!$sent) {
    echo json_encode(["status" => "error", "message" => "Failed to send email. Please try again"]);
} else {
    echo json_encode(["status" => "success", "message" => "Password recovery email sent successfully"]);
}
?>
