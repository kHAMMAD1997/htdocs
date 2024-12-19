<?php
// Email system credentials file

// Define email system credentials as constants
define("EMAIL_USERNAME", "noreply@hammad-khan.org");
define("EMAIL_PASSWORD", "mwlimits.org");
define("EMAIL_INCOMING_SERVER", "mail.hammad-khan.org");
define("EMAIL_IMAP_PORT", 993);
define("EMAIL_POP3_PORT", 995);
define("EMAIL_OUTGOING_SERVER", "mail.hammad-khan.org");
define("EMAIL_SMTP_PORT", 465);

// Function to get email configuration (Optional, for structured access)
function getEmailConfig() {
    return [
        "username" => EMAIL_USERNAME,
        "password" => EMAIL_PASSWORD,
        "incoming_server" => EMAIL_INCOMING_SERVER,
        "imap_port" => EMAIL_IMAP_PORT,
        "pop3_port" => EMAIL_POP3_PORT,
        "outgoing_server" => EMAIL_OUTGOING_SERVER,
        "smtp_port" => EMAIL_SMTP_PORT,
    ];
}
?>
