<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$tableName = coreTable( 'user_login' );
$sql['user_login'] = <<< end_of_login
CREATE TABLE $tableName
(
	user_login_id					bigint(20)		unsigned NOT NULL AUTO_INCREMENT,
	user_login_first_name			varchar(60)		DEFAULT NULL,
	user_login_last_name			varchar(60)		DEFAULT NULL,
	user_login_address1				varchar(150)	DEFAULT NULL,
	user_login_address2				varchar(150)	DEFAULT NULL,
	user_login_state				varchar(60)		DEFAULT NULL,
	user_login_province				varchar(60)		DEFAULT NULL,
	user_login_postal_code			varchar(20)		DEFAULT NULL,
	user_login_country				varchar(60)		DEFAULT NULL,
	user_login_phone1				varchar(20)		DEFAULT NULL,
	user_login_phone2				varchar(20)		DEFAULT NULL,
	user_login_dob					datetime		DEFAULT NULL,
	user_login_anniversary			datetime		DEFAULT NULL,
	
	user_login_email				varchar(384)	NOT NULL,
	user_login_username				varchar(255)	DEFAULT NULL,
	user_login_role					tinyint(4)		DEFAULT NULL,
	user_login_password				char(80)		DEFAULT NULL
		COMMENT 'This field is the sha1 hash of the password with a salt attached - 40 for each.  salt is first 40 chars, password hash is second 40 chars',

	user_login_tz_id				bigint(20)		unsigned DEFAULT NULL,
	user_login_suspend				bool			NOT NULL DEFAULT false,
	user_login_suspend_notes		text,
	user_login_signup_date			datetime		DEFAULT NULL,

	user_login_sec_question1		varchar(120)	DEFAULT NULL,
	user_login_sec_question2		varchar(120)	DEFAULT NULL,
	user_login_sec_question3		varchar(120)	DEFAULT NULL,
	user_login_sec_q1_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',

	user_login_sec_q2_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',

	user_login_sec_q3_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',

	user_login_password_reset		varchar(12)		DEFAULT NULL,
	user_login_activated			bool			NOT NULL DEFAULT false,
	user_login_activation_code		varchar(12)		DEFAULT NULL,


	PRIMARY KEY	(user_login_id),
	UNIQUE	KEY	user_login_username		(user_login_username),
			KEY	user_login_suspend			(user_login_suspend),
) ENGINE=InnoDB;
end_of_login;

// user meta table
$tableName = coreTable( 'um' );
$sql['user_login'] = <<< end_of_login_meta
CREATE TABLE $tableName
(
	um_							bigint(20)		unsigned NOT NULL AUTO_INCREMENT,
	um_user_login_id				bigint(20)		unsigned,
	user_login_first_name			varchar(60)		DEFAULT NULL,
	user_login_last_name			varchar(60)		DEFAULT NULL,
	user_login_address1				varchar(150)	DEFAULT NULL,
	user_login_address2				varchar(150)	DEFAULT NULL,
	user_login_state				varchar(60)		DEFAULT NULL,
	user_login_province				varchar(60)		DEFAULT NULL,
	user_login_postal_code			varchar(20)		DEFAULT NULL,
	user_login_country				varchar(60)		DEFAULT NULL,
	user_login_phone1				varchar(20)		DEFAULT NULL,
	user_login_phone2				varchar(20)		DEFAULT NULL,
	user_login_dob					datetime		DEFAULT NULL,
	user_login_anniversary			datetime		DEFAULT NULL,
	
	user_login_email				varchar(384)	NOT NULL,
	user_login_username				varchar(255)	DEFAULT NULL,
	user_login_role					tinyint(4)		DEFAULT NULL,
	user_login_password				char(80)		DEFAULT NULL
		COMMENT 'This field is the sha1 hash of the password with a salt attached - 40 for each.  salt is first 40 chars, password hash is second 40 chars',

	user_login_tz_id				bigint(20)		unsigned DEFAULT NULL,
	user_login_suspend				bool			NOT NULL DEFAULT false,
	user_login_suspend_notes		text,
	user_login_signup_date			datetime		DEFAULT NULL,

	user_login_sec_question1		varchar(120)	DEFAULT NULL,
	user_login_sec_question2		varchar(120)	DEFAULT NULL,
	user_login_sec_question3		varchar(120)	DEFAULT NULL,
	user_login_sec_q1_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',

	user_login_sec_q2_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',

	user_login_sec_q3_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',

	user_login_password_reset		varchar(12)		DEFAULT NULL,
	user_login_activated			bool			NOT NULL DEFAULT false,
	user_login_activation_code		varchar(12)		DEFAULT NULL,


	PRIMARY KEY	(user_login_id),
	UNIQUE	KEY	user_login_username		(user_login_username),
			KEY	user_login_suspend			(user_login_suspend),
) ENGINE=InnoDB;
end_of_login;

$tableName = coreTable( 'tz' );
$sql['tz'] = <<< end_of_tz
CREATE TABLE $tableName
(
	tz_id							bigint(20)		unsigned NOT NULL AUTO_INCREMENT,
	tz_area							varchar(32)		NOT NULL,
	tz_offset						tinyint(4)		NOT NULL,
	tz_abbr							varchar(8)		NOT NULL,
	tz_zone							varchar(32)		NOT NULL,
	tz_dst							bool			unsigned NOT NULL,
	tz_dst_offset					tinyint(4)		DEFAULT NULL,
	tz_dst_abbr						varchar(8)		DEFAULT NULL,
	
	
	PRIMARY	KEY (tz_id)
) ENGINE=MyISAM COMMENT='http://www.statoids.com/tus.html';
end_of_tz;

$sql['tz_insert'] = <<< end_of_tz_insert
	INSERT INTO $tableName (tz_id, tz_area, tz_offset, tz_abbr, tz_zone, tz_dst, tz_dst_offset, tz_dst_abbr)
	VALUES
	(1, 'Eastern Time', -5, 'EST', 'America/New_York', 1, -4, 'EDT'),
	(2, 'Central Time', -6, 'CST', 'America/Chicago', 1, -5, 'CDT'),
	(3, 'Mountain Time', -7, 'MST', 'America/Denver', 1, -6, 'MDT'),
	(4, 'Mountain Time (no DST)', -7, 'MDT', 'America/Phoenix', 0, NULL, NULL),
	(5, 'Pacific Time', -8, 'PST', 'America/Los_Angeles', 1, -7, 'PDT'),
	(6, 'Alaska Time', -9, 'AST', 'America/Anchorage', 1, -8, 'ADT'),
	(7, 'Hawaii-Aleutian Time', -10, 'HST', 'America/Adak', 1, -9, 'HDT'),
	(8, 'Hawaii-Aleutian Time (no DST)', -10, 'HST', 'Pacific/Honolulu', 0, NULL, NULL);
end_of_tz_insert;

$tableName = coreTable( 'registry' );
$sql['registry'] = <<< end_of_registry
CREATE TABLE $tableName
(
	registry_key				char(32)	NOT NULL,
	registry_group				char(32)	NOT NULL,
	registry_value				char(64)	DEFAULT NULL,
	registry_label				char(32)	DEFAULT NULL,
	registry_desc				char(128)	DEFAULT NULL,
	registry_input_size			int(11)		NOT NULL DEFAULT '4',


	PRIMARY KEY (registry_key,registry_group),
	KEY registry_group (registry_group)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
end_of_registry;