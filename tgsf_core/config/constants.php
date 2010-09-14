<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
// registry context defines
enum( 'context', 
	array(
		'CORE'	=> 'tgsf_core',
		'ADMIN'	=> 'tgsf_admin',
		'APP'	=> 'single_app'
		)
	);
//------------------------------------------------------------------------
// plugin event types
enum( 'event', 
	array(
		'ACTION',
		'FILTER',
		'HANDLER',
		'UNKNOWN'
		)
	);
	
// event collection type
enum( 'ect',
	array(
		'ARRAY',
		'STRING'
		)
	);
//------------------------------------------------------------------------
// datasource types
enum( 'dsType',
	array(
		// general datasources created by application developers
		'APP',
		
		// database
		'DB',
		
		// get & post for http handling
		'POST',
		'GET',
		
		// command line interface
		'CLI',
		
		// database registry
		'REG',
		
		// plugin events
		'ACTION',
		'FILTER'
		)
	);


define( 'DS_IGNORE_DEFAULT', 'tgsf-ignore-default' );

enum( 'FIELD_',
	array(
		'NO_SIZE' => NULL,
		'NOT_NULL',
		'AUTO_INC',
		'UNSIGNED',
		'PRIMARY_KEY',
		'UNIQUE'
		)
	);

// fpt = field / param type
$fpt = array(
	'BOOL'		=> PDO::PARAM_BOOL,
	'NULL'		=> PDO::PARAM_NULL,
	'INT'		=> PDO::PARAM_INT,
	'STR'		=> PDO::PARAM_STR,
	'LOB'		=> PDO::PARAM_LOB,

	// aliases for code readability
	'DECIMAL'	=> PDO::PARAM_STR,
	'FLOAT'		=> PDO::PARAM_STR,
	'DEC'		=> PDO::PARAM_STR,
	'FLT'		=> PDO::PARAM_STR,
	'DATE'		=> PDO::PARAM_STR,
	'DATETIME'	=> PDO::PARAM_STR
	);

// param type
enum( 'pt', $fpt );

// field type
enum( 'ft', $fpt );

// update on duplicate key enum
enum( 'UPDATE_',
	array(
		'ON_DUPLICATE'	=> true,
		'OFF'			=> false
		)
	);