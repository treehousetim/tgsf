CREATE TABLE `registry`
(
  `registry_key` char(32) NOT NULL,
  `registry_group` char(32) NOT NULL,
  `registry_value` char(64) DEFAULT NULL,
  `registry_label` char(32) DEFAULT NULL,
  `registry_desc` char(128) DEFAULT NULL,

  PRIMARY KEY (`registry_key`),

  KEY `registry_group` (`registry_group`)
)