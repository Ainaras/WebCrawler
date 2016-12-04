CREATE TABLE IF NOT EXISTS `import_jobs` (
	`id`	INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	`parent_job_id`	INTEGER,
	`init_md5url`	TEXT NOT NULL,
	`md5url`	TEXT NOT NULL UNIQUE,
	`url`	TEXT NOT NULL,
	`status`	INTEGER NOT NULL DEFAULT 0,
	`created`	DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	FOREIGN KEY(`parent_job_id`) REFERENCES `import_jobs`(`id`)
);

CREATE INDEX IF NOT EXISTS FK_imported_imported on import_jobs (parent_job_id);