<?php
// Include the helper file where forms_fetcher function is defined
include(__DIR__ . '/../microservices/helper.php');

while (true) {
    // Call the forms_fetcher function to sync the forms
    forms_fetcher();
    
    // Sleep for 60 seconds (1 minute) before calling the function again
    sleep(60);
}
?>
