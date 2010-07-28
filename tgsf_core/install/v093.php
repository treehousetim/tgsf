<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
load_library( 'db/tgsfDbRegistry/tgsfDbRegistry' );
$version = tgsfVersion::factory( '0.9.3' );
$table = coreTable( 'registry' );
$version->startVer( '093' );
$version->addItem()
	->description( 'Adding registry table' )
	->table( $table )
	->ddl( <<< end_of_registry
	CREATE TABLE $table
	(
		registry_key				char(32)		NOT NULL,
		registry_app				char(32)		NOT NULL,
		registry_group				char(32)		NOT NULL,
		registry_value				text			DEFAULT NULL,
		registry_type				enum( 'text','checkbox','textarea','dropdown','date'),
		registry_list_values		text			DEFAULT NULL,
		registry_label				char(32)		DEFAULT NULL,
		registry_desc				varchar(255)	DEFAULT NULL,
		registry_help				text			DEFAULT NULL,

		PRIMARY KEY (registry_key,registry_app,registry_group),
		KEY registry_group (registry_group),
		KEY registry_app(registry_app)
	) ENGINE=MyISAM;
end_of_registry
);
//------------------------------------------------------------------------
$table = coreTable( 'user_login' );	
$version->addItem()
	->description( 'Adding user_login table' )
	->table( $table )
	->ddl( <<< end_of_login
	CREATE TABLE $table
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
				KEY	user_login_suspend			(user_login_suspend)
	) ENGINE=InnoDB;
end_of_login
);
//------------------------------------------------------------------------
// super meta table
// holds meta values for any table
$table = coreTable( 'supermeta' );
$version->addItem()
	->description( 'Adding supermeta table' )
	->table( $table )
	->ddl( <<< end_of_supermeta
	CREATE TABLE $table
	(
		supermeta_record_id			varchar(20)		NOT NULL,
		supermeta_table				varchar(40)		NOT NULL,
		supermeta_name				varchar(60)		NOT NULL,
		supermeta_display_group		varchar(30)		DEFAULT NULL,
		supermeta_display_type		enum( 'input','textarea','checkbox' ),
		supermeta_value				varchar(255)	NOT NULL,
		supermeta_extended			text			DEFAULT NULL,
		supermeta_struct			text			DEFAULT NULL,

		PRIMARY KEY ( supermeta_record_id,supermeta_table,supermeta_name )
	) ENGINE=InnoDB;
end_of_supermeta
);
//------------------------------------------------------------------------
$table = coreTable( 'tz' );
$version->addItem()
	->description( 'Adding tz table' )
	->table( $table )
	->ddl( <<< end_of_tz
	CREATE TABLE $table
	(
		tz_id							smallint		unsigned NOT NULL AUTO_INCREMENT,
		tz_area							varchar(32)		NOT NULL,
		tz_offset						tinyint(4)		NOT NULL,
		tz_abbr							varchar(8)		NOT NULL,
		tz_zone							varchar(32)		NOT NULL,
		tz_dst							bool			NOT NULL,
		tz_dst_offset					tinyint(4)		DEFAULT NULL,
		tz_dst_abbr						varchar(8)		DEFAULT NULL,
	
	
		PRIMARY	KEY (tz_id),
		UNIQUE KEY `tz_area` (`tz_area`,`tz_abbr`)
	) ENGINE=MyISAM COMMENT='http://www.statoids.com/tus.html';
end_of_tz
);
//------------------------------------------------------------------------
// if you need more timezones, please submit a patch to the project.
// :)
$version->addItem()
	->description( 'Inserting Time Zone Records' )
	->query( <<< end_of_tz_insert
	INSERT INTO $table (tz_area, tz_offset, tz_abbr, tz_zone, tz_dst, tz_dst_offset, tz_dst_abbr)
	VALUES
	('Eastern Time', -5, 'EST', 'America/New_York', 1, -4, 'EDT'),
	('Central Time', -6, 'CST', 'America/Chicago', 1, -5, 'CDT'),
	('Mountain Time', -7, 'MST', 'America/Denver', 1, -6, 'MDT'),
	('Mountain Time (no DST)', -7, 'MDT', 'America/Phoenix', 0, NULL, NULL),
	('Pacific Time', -8, 'PST', 'America/Los_Angeles', 1, -7, 'PDT'),
	('Alaska Time', -9, 'AST', 'America/Anchorage', 1, -8, 'ADT'),
	('Hawaii-Aleutian Time', -10, 'HST', 'America/Adak', 1, -9, 'HDT'),
	('Hawaii-Aleutian Time (no DST)', -10, 'HST', 'Pacific/Honolulu', 0, NULL, NULL);
end_of_tz_insert
);

return $version;