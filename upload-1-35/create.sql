CREATE TABLE short_concept_note_finance_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    breakdown LONGTEXT NOT NULL
);


CREATE TABLE concept_note_finance_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    breakdown LONGTEXT NOT NULL
);



CREATE TABLE grant_application_finance_details (
    id INT AUTO_INCREMENT PRIMARY KEY,
    form_id INT NOT NULL,
    breakdown LONGTEXT NOT NULL
);