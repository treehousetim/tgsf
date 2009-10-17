/* remote addr has enough room for ipv6 */
CREATE TABLE `tgsf_log` (
  `log_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `log_type` varchar(100) NOT NULL,
  `log_datetime` datetime NOT NULL,
  `log_remote_addr` varchar(40), /* enough room for ipv6 */
  `log_message` text NOT NULL,
  `log_table` varchar(120),
  `log_table_record_key` varchar(120),
  `log_user_id` varchar(120),
  `log_url` varchar(255) NOT NULL,
  `log_get` text NOT NULL,
  `log_post` text NOT NULL,
  `log_cookie` text NOT NULL,
  `log_session` text NOT NULL,
  `log_server` text NOT NULL,
  `log_env` text NOT NULL,
  `log_files` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8
