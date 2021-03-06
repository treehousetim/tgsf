<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//load_library( 'example' );
//load_library( 'example', IS_CORE_LIB )

// what follows is simply an example.
load_library( 'tgsfUserAuth', IS_CORE_LIB );
load_library( 'tgsfTime', IS_CORE_LIB );
load_library( 'tgsfDate', IS_CORE_LIB );

/*
$authModel = load_model( 'user' );
$auth = AUTH( $authModel );
$auth->loginUrl = URL( 'login' );*/


load_library( 'tgsfFormat', IS_CORE_LIB );

/*
tgsfEventFactory::actionHandler()
	->event( 'AUTH_login_check' )
	->func( 'yourFunction' )
	->attach();
*/


//------------------------------------------------------------------------
load_library( 'tgsfCrypt', IS_CORE_LIB );
ENCRYPT( 'passphrase' );

//------------------------------------------------------------------------
// requires a table to be set up
//load_library( 'db/tgsfLog/tgsf_log', IS_CORE_LIB );

// initialize the logger with a custom table name
// load this after auth so logger can log user_id's if available
//LOGGER( 'my_log' );

//------------------------------------------------------------------------
load_form_libraries();
load_library( 'html/tgsfBreadcrumb', IS_CORE_LIB );
