CREATE TABLE IF NOT EXISTS civicrm_pswincom_inbound (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `from` varchar(128) NOT NULL,
  `to` varchar(128) NOT NULL,
  `body` TEXT NULL,
  `date` DATETIME NULL,
  `provider_id` INT NOT NULL,
  PRIMARY KEY (id)
) ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;