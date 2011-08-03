<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2011 by TMLA INC.  ALL RIGHTS RESERVED.
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

enum( 'qt',
	array(
		'NONE'   => NULL,
		'STATIC' => 'STATIC',
		'SELECT' => 'SELECT',
		'INSERT' => 'INSERT',
		'UPDATE' => 'UPDATE',
		'DELETE' => 'DELETE'
		)
	);

define( 'qiDUP_CHECK', true );
//------------------------------------------------------------------------
define( 'tgsfRoleGuest', 0 );
define( 'tgsfRoleMember', 20 );
define( 'tgsfRoleContributor', 40 );
define( 'tgsfRoleEditor', 60 );
define( 'tgsfRoleAdmin', 80 );
define( 'tgsfRoleSuperAdmin', 100 );

//------------------------------------------------------------------------

enum( 'cbt', 
	array(
		'CLASS',
		'FUNCTION',
		'OBJECT'
		)
	);
	
//------------------------------------------------------------------------
// form enums

// form field type
enum( 'fft',
	array(
		'Hidden'	=> 'hidden',
		'Text'		=> 'text',
		'TextArea'	=> 'textarea',
		'File'		=> 'file',
		'DropDown'	=> 'dropdown',
		'List'		=> 'list',
		'Radio'		=> 'radio',
		'Check'		=> 'checkbox',
		'Image'		=> 'image',
		'Button'	=> 'button',
		'Submit'	=> 'submit',
		'Reset'		=> 'reset',
		'Password'	=> 'password',
		'OtherTag'	=> 'other',
		'Span'		=> 'span',
		'Static'	=> 'statictext'
		)
	);
define( 'FORM_AUTOCOMPLETE_ON', true );
define( 'FORM_AUTOCOMPLETE_OFF', false );
//------------------------------------------------------------------------
// log severity values
enum( 'ls',
	array(
		'UNREVIEWED',
		'IGNORE',
		'MINOR',
		'BIG',
		'CRITICAL',
		'IMMEDIATE ACTION'
		)
	);
//------------------------------------------------------------------------
