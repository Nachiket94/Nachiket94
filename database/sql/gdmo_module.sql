CREATE TABLE IF NOT EXISTS `gdmo_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `effective_date` date NOT NULL,
  `shift_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `emergency_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gdmo_rates_effective_date` (`effective_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gdmo_masters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `rate_id` int(11) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gdmo_masters_rate_id` (`rate_id`),
  KEY `idx_gdmo_masters_active` (`is_active`),
  CONSTRAINT `fk_gdmo_masters_rate_id` FOREIGN KEY (`rate_id`) REFERENCES `gdmo_rates` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gdmo_tds_rates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gdmo_id` int(11) DEFAULT NULL,
  `effective_date` date NOT NULL,
  `tds_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_gdmo_tds_rates_gdmo_id` (`gdmo_id`),
  KEY `idx_gdmo_tds_rates_effective_date` (`effective_date`),
  CONSTRAINT `fk_gdmo_tds_rates_gdmo_id` FOREIGN KEY (`gdmo_id`) REFERENCES `gdmo_masters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gdmo_attendance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `attendance_month` char(7) NOT NULL,
  `gdmo_id` int(11) NOT NULL,
  `shift_a_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shift_b_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shift_c_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `emergency_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `extra_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_gdmo_attendance_month_gdmo` (`attendance_month`,`gdmo_id`),
  KEY `idx_gdmo_attendance_gdmo_id` (`gdmo_id`),
  CONSTRAINT `fk_gdmo_attendance_gdmo_id` FOREIGN KEY (`gdmo_id`) REFERENCES `gdmo_masters` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gdmo_salaries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salary_month` char(7) NOT NULL,
  `gdmo_id` int(11) NOT NULL,
  `gdmo_name` varchar(150) NOT NULL,
  `rate_id` int(11) NOT NULL,
  `shift_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `emergency_rate` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tds_percent` decimal(5,2) NOT NULL DEFAULT '0.00',
  `shift_a_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shift_b_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shift_c_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `emergency_days` decimal(6,2) NOT NULL DEFAULT '0.00',
  `shift_a_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `shift_b_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `shift_c_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `shift_total_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `shift_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `emergency_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `emergency_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `extra_hours` decimal(8,2) NOT NULL DEFAULT '0.00',
  `extra_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gross_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tds_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `net_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_gdmo_salaries_month_gdmo` (`salary_month`,`gdmo_id`),
  KEY `idx_gdmo_salaries_gdmo_id` (`gdmo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `gdmo_salary_locks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `salary_month` char(7) NOT NULL,
  `locked_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_gdmo_salary_locks_month` (`salary_month`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
