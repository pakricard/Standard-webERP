CREATE TABLE `sessions` (
  `sessionid` char(32),
  `last_poll` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3