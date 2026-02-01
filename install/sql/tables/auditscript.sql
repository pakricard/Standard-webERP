CREATE TABLE `auditscripts` (
  `executiondate` datetime NOT NULL DEFAULT current_timestamp(),
  `secondsrunning` decimal(10,5) NOT NULL DEFAULT 0.00000,
  `userid` varchar(20) NOT NULL DEFAULT '',
  `scripttitle` varchar(200) NOT NULL DEFAULT '',
  KEY `idx_auditscripts_userid` (`userid`),
  KEY `idx_auditscripts_executiondate` (`executiondate`),
  KEY `idx_auditscripts_scripttitle` (`scripttitle`),
  CONSTRAINT `auditscript_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `www_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
