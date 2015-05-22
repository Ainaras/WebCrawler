CREATE TABLE `import_jobs` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`parent_job_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`md5url` VARCHAR(32) NOT NULL,
	`url` VARCHAR(1000) NOT NULL,
	`status` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY (`id`),
	UNIQUE INDEX `md5url` (`md5url`),
	INDEX `FK_imported_imported` (`parent_job_id`),
	CONSTRAINT `FK_imported_imported` FOREIGN KEY (`parent_job_id`) REFERENCES `import_jobs` (`id`) ON UPDATE NO ACTION ON DELETE NO ACTION
)
COLLATE='utf8_general_ci'
ENGINE=InnoDB;