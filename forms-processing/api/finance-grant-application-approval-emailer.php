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
$requiredFields = [
    'recipientEmail',
    'grant_application_id',
    'projectTitle',
    'projectLocation',
    'countryProject',
    'organization',
    'contactName',
    'contactEmail',
    'contactPhone',
    'contactTitle',
    'taxStatus',
    'budgetData'
];

foreach ($requiredFields as $field) {
    if (empty($data[$field])) {
        echo json_encode(["status" => "error", "message" => "Missing required field: $field"]);
        exit;
    }
}

// Assign variables from the request
$recipientEmails = $data['recipientEmail'];
$conceptNoteId = $data['grant_application_id'];
$projectTitle = $data['projectTitle'];
$projectLocation = $data['projectLocation'];
$countryProject = $data['countryProject'];
$organization = $data['organization'];
$contactName = $data['contactName'];
$contactEmail = $data['contactEmail'];
$contactPhone = $data['contactPhone'];
$contactTitle = $data['contactTitle'];
$tax = $data['taxStatus'];
$budgetData = $data['budgetData'];

// Ensure recipientEmail is an array
if (!is_array($recipientEmails)) {
    $recipientEmails = [$recipientEmails];
}

// Calculate the total budget
$totalBudget = 0;
$budgetBreakdown = '';
foreach ($budgetData as $budgetItem) {
    $totalBudget += $budgetItem['budget'];
    $budgetBreakdown .= '<li style="color:#00984a;">Budget: <strong>' . $budgetItem['budget'] . '</strong> (Allocation Date: <strong>' . $budgetItem['allocationDate'] . '</strong>)</li>';
}

// Generate the email subject
$subject = "Funding Verification Required $projectTitle Approved";

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
                                                    <strong>Funding Verification Required</strong>
                                                </h2>
                                                <p>Dear Finance Team,</p>
                                                <p>We are pleased to inform you that the concept note application for <strong>$projectTitle</strong> has been approved and is ready for funding. Please review and confirm the amount to be issued, per the approved funding breakdown.</p>
                                                <p><strong>Project Details:</strong></p>
                                                <ul>
                                                    <li>Applicant Name: <strong>$contactName</strong></li>
                                                    <li>Organization: <strong>$organization</strong></li>
                                                    <li>Contact Title: <strong>$contactTitle</strong></li>
                                                    <li>Contact Email: <a href="mailto:$contactEmail">$contactEmail</a></li>
                                                    <li>Contact Phone: <strong>$contactPhone</strong></li>
                                                    <li>Grant Application ID: <strong>$conceptNoteId</strong></li>
                                                    <li>Project Title: <strong>$projectTitle</strong></li>
                                                    <li>Project Location: <strong>$projectLocation, $countryProject</strong></li>
                                                    <li>Tax Status: <strong>$$tax</strong></li>
                                                    <li>Total Approved Amount: <strong>$$totalBudget</strong></li>
                                                </ul>
                                                <p style="color:#d8364d;"><strong>Budget Breakdown:</strong></p>
                                                <ul>
                                                    $budgetBreakdown
                                                </ul>
                                                <p>If you have any immediate questions or require further clarification, please feel free to reach out to us at <a href="mailto:programs@mercywithoutlimits.org">programs@mercywithoutlimits.org</a>.</p>
                                                <p>Thank you for your prompt attention to this request.</p>
                                                <p>Best regards,</p>
                                                <p>Programs Department <br> Mercy Without Limits</p>
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
                        <p style="font-size:10.0pt;font-family:'Roboto', sans-serif;">© 2025 Mercy Without Limits. All rights reserved.</p>
                    </td>
                </tr>
            </tbody>
        </table>
        <!-- Footer ends here -->
</body>
</html>
HTML;

// Construct the dynamic API URL
$apiUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] . "/forms-processing/api/grant-application-finance-attachment-api.php?grant_application_id={$conceptNoteId}";
sleep(5);

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

// Execute cURL request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Check for cURL errors
if (curl_errno($ch)) {
    echo json_encode(["status" => "error", "message" => "cURL Error: " . curl_error($ch)]);
    exit;
}

// Close cURL session
curl_close($ch);

// Validate API response
if ($httpCode !== 200 || empty($response)) {
    echo json_encode(["status" => "error", "message" => "Failed to fetch attachments from API. HTTP Code: $httpCode"]);
    exit;
}

// Decode JSON response
$fileData = json_decode($response, true);

if (!isset($fileData['files']) || !is_array($fileData['files'])) {
    echo json_encode(["status" => "error", "message" => "Invalid API response structure"]);
    exit;
}


$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = EMAIL_OUTGOING_SERVER; // SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_USERNAME; // SMTP username
    $mail->Password = EMAIL_PASSWORD; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Encryption
    $mail->Port = EMAIL_SMTP_PORT; // SMTP port
    $mail->setFrom(EMAIL_USERNAME, 'Mercy Without Limits');
    $mail->addReplyTo(EMAIL_USERNAME, 'Mercy Without Limits');
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $emailContent;

    // Attach files
    $fileBasePath = "/forms-processing/api";
    foreach ($fileData['files'] as $file) {
        $filePath = $_SERVER['DOCUMENT_ROOT'] . $fileBasePath . $file['file_path'];
        if (file_exists($filePath)) {
            $mail->addAttachment($filePath);
        }
    }

    // Send email to each recipient
    $allSent = true;
    foreach ($recipientEmails as $email) {
        $mail->clearAddresses(); // Clear previous recipient(s)
        $mail->addAddress($email);

        if (!$mail->send()) {
            $allSent = false;
            break; // Stop on first failure
        }
    }

    if (!$allSent) {
        echo json_encode(["status" => "error", "message" => "Failed to send email to all recipients."]);
    } else {
        echo json_encode(["status" => "success", "message" => "Email sent successfully to all recipients."]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => "Mailer Error: {$mail->ErrorInfo}"]);
}
?>

