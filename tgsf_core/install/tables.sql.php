<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$tableName = coreTable( 'user_login' );
$sql[] = <<< end_of_login
CREATE TABLE $tableName
(
	user_login_id					bigint(20)		unsigned NOT NULL AUTO_INCREMENT,
	user_login_tz_id				bigint(20)		unsigned DEFAULT NULL,
	user_login_suspend				bool			NOT NULL DEFAULT false,
	user_login_suspend_notes		text,
	user_login_signup_date			datetime		DEFAULT NULL,

	user_login_sec_question1		varchar(120)	DEFAULT NULL,
	user_login_sec_question2		varchar(120)	DEFAULT NULL,
	user_login_sec_question3		varchar(120)	DEFAULT NULL,
	user_login_sec_q1_answer		char(80)		NOT NULL,
	user_login_sec_q2_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',
		
	user_login_sec_q3_answer		char(80)		NOT NULL
		COMMENT 'This field is the sha1 hash of the answer with a salt attached.',
		
	user_login_email				varchar(384)	NOT NULL,
	user_login_username				varchar(255)	DEFAULT NULL,
	user_login_role					tinyint(4)		DEFAULT NULL,
	user_login_password				char(80)		DEFAULT NULL
		COMMENT 'This field is the sha1 hash of the password with a salt attached - 40 for each.  salt is first 40 chars, password hash is second 40 chars',

	user_login_password_reset		varchar(12)		DEFAULT NULL,
	user_login_activated			bool			NOT NULL DEFAULT false,
	user_login_activation_code		varchar(12)		DEFAULT NULL,


	PRIMARY KEY	(user_login_id),
	UNIQUE	KEY	user_login_username		(user_login_username),
			KEY	login_suspend			(user_login_suspend),
) ENGINE=InnoDB;
end_of_login;

