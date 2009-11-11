<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//load_library( 'example' );
//load_library( 'example', IS_CORE_LIB )

// what follows is simply an example.
load_library( 'tgsfUserAuth', IS_CORE_LIB );

$authModel = load_model( 'login' );
$auth = AUTH( $authModel );
$auth->loginUrl = URL( 'login' );

load_library( 'tgsfFormat', IS_CORE_LIB );

add_action( 'AUTH_login_check', 'mmEnforceRequiredProfileInfo' );
add_action( 'post_resolve_controller', 'mmAutoBreadcrumbHome' );

//------------------------------------------------------------------------
load_library( 'tgsfCrypt', IS_CORE_LIB );
ENCRYPT( 'passphrase' );

//------------------------------------------------------------------------
// requires a table to be set up
load_library( 'db/tgsfLog/tgsf_log', IS_CORE_LIB );

// initialize the logger with a custom table name
// load this after auth so logger can log user_id's if available
LOGGER( 'my_log' );

//------------------------------------------------------------------------
load_form_libraries();
load_library( 'html/tgsfBreadcrumb', IS_CORE_LIB );
