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
    $budgetBreakdown .= '<li>Budget: <strong>' . $budgetItem['budget'] . '</strong> (Allocation Date: <strong>' . $budgetItem['allocationDate'] . '</strong>)</li>';
}

// Generate the email subject
$subject = "Funding Verification Required – $projectTitle – Approved";

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
                                                    <strong>Funding Verification Required</strong>
                                                </h2>
                                                <p>Dear Finance Team,</p>
                                                <p>We are pleased to inform you that the concept note application for <strong>$projectTitle</strong> has been approved and is ready for funding. Please review and confirm the amount to be issued, per the approved funding breakdown.</p>
                                                <p><strong>Project Details:</strong></p>
                                                <ul>
                                                    <li>Applicant’s Name: <strong>$contactName</strong></li>
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
                                                <p><strong>Budget Breakdown:</strong></p>
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

// Send the email to all recipients
$allSent = true;
foreach ($recipientEmails as $email) {
    if (!mail($email, $subject, $emailContent, $headers)) {
        $allSent = false;
        break;
    }
}

if (!$allSent) {
    echo json_encode(["status" => "error", "message" => "Failed to send email to all recipients. Please try again"]);
} else {
    echo json_encode(["status" => "success", "message" => "Email sent successfully to all recipients"]);
}
?>
