CREATE TABLE `concept_note_narrative_report_attachment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `file_path` text NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `concept_note_narrative_report_attachment_ibfk_1` FOREIGN KEY (`report_id`) 
  REFERENCES `concept_note_narrative_report` (`report_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=95 DEFAULT CHARSET=latin1;

CREATE TABLE `concept_note_narrative_report_metadata` (
  `metadata_id` int(11) NOT NULL AUTO_INCREMENT,
  `report_id` int(11) NOT NULL,
  `approve` int(11) DEFAULT NULL,
  `revise` int(11) DEFAULT NULL,
  `finance` int(11) DEFAULT NULL,
  `declined` int(11) DEFAULT NULL,
  PRIMARY KEY (`metadata_id`),
  KEY `report_id` (`report_id`),
  CONSTRAINT `concept_note_narrative_report_metadata_ibfk_1` 
    FOREIGN KEY (`report_id`) REFERENCES `concept_note_narrative_report` (`report_id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
