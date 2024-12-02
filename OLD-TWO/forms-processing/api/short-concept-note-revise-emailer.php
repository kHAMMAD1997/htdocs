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
$requiredFields = ['recipient_email', 'applicant_name', 'project_title'];
foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["status" => "error", "message" => "Missing required field: $field"]);
        exit;
    }
}

// Assign variables from the request
$recipientEmail = $data['recipient_email'];
$applicantName = $data['applicant_name'];
$projectTitle = $data['project_title'];


// Set the email subject
$subject = "Short concept Note Application Submission   – $projectTitle – Revise";

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
                                                <h2 style="font-family:arial, 'Roboto', sans-serif;font-size:26px;color:#D12E0B;">
                                                    <strong>Short Concept Note Revision</strong>
                                                </h2>
                                                <p>Dear <strong>$applicantName</strong>,</p>
                                                <p>Thank you for submitting your short  concept note application for <strong>$projectTitle</strong>. We appreciate your effort and commitment to your project. After our initial review, we would like to request some revisions and additional information to proceed with your application.</p>

                                                <p>The specific revisions and additional information needed are available in our portal. Please <a href="https://test.hammad-khan.org/">Click Here</a> to access the portal for details.</p>

                                                <p><strong>Next Steps:</strong></p>
                                                <p>Once you’ve reviewed the requested revisions, please provide the updated information directly in the portal. Once we receive your updates, we will resume our review process and get back to you as soon as possible.</p>

                                                <p>If you have any questions or need clarification on the requested changes, please feel free to reach out to us at <a href="mailto:programs@mercywithoutlimits.org">programs@mercywithoutlimits.org</a>.</p>

                                                <p>Thank you for your understanding and cooperation. We look forward to your response.</p>

                                                <p>Best regards,</p>
                                                <p>Programs Department</p>
                                                <p>Mercy Without Limits</p>
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
                        <p style="font-size:10.0pt;font-family:'Roboto', sans-serif;">© 2024 Mercy Without Limits. All rights reserved.</p>
                    </td>
                </tr>
            </tbody>
        </table>
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
$sent = mail($recipientEmail, $subject, $emailContent, $headers);

if (!$sent) {
    echo json_encode(["status" => "error", "message" => "Failed to send email. Please try again"]);
} else {
    echo json_encode(["status" => "success", "message" => "Email sent successfully"]);
}
?>
