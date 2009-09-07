<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

enum( 'qt',
	array(
		'NONE',
		'SELECT',
		'INSERT',
		'UPDATE',
		'DELETE'
		)
	);

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
	'FLT'		=> PDO::PARAM_STR
	);
	
// param type
enum( 'pt', $fpt );
// field type
enum( 'ft', $fpt );
