CREATE TABLE `timesheets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `wo` int NOT NULL COMMENT 'loose FK with workorders',
  `employeeid` int NOT NULL,
  `weekending` date NOT NULL DEFAULT '1900-01-01',
  `workcentre` varchar(5) NOT NULL COMMENT 'loose FK with workcentres',
  `day1` double NOT NULL DEFAULT '0',
  `day2` double NOT NULL DEFAULT '0',
  `day3` double NOT NULL DEFAULT '0',
  `day4` double NOT NULL DEFAULT '0',
  `day5` double NOT NULL DEFAULT '0',
  `day6` double NOT NULL DEFAULT '0',
  `day7` double NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `workcentre` (`workcentre`),
  KEY `employees` (`employeeid`),
  KEY `wo` (`wo`),
  KEY `weekending` (`weekending`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`employeeid`) REFERENCES `employees` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3;
