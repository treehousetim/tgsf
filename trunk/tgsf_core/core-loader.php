<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
/*
this file loads everything that the front-controller doesn't load.

If you modify this file, it will only get overwritten the next time you upgrade.

*/
//------------------------------------------------------------------------
define( 'APP_PATH', BASEPATH . APP_FOLDER );
//------------------------------------------------------------------------
// datasources
//------------------------------------------------------------------------
load_library( 'tgsfUrl',			IS_CORE_LIB ); // the url function/Library
load_library( 'tgsfPost',			IS_CORE_LIB );
load_library( 'tgsfGet',			IS_CORE_LIB );

//------------------------------------------------------------------------
// Misc Libraries
//------------------------------------------------------------------------

load_library( 'tgsfTemplate',		IS_CORE_LIB );
load_library( 'tgsfSession',		IS_CORE_LIB );

//------------------------------------------------------------------------
// Plugin Library
//------------------------------------------------------------------------
load_library( 'tgsfPlugin',			IS_CORE_LIB ); // the plugin api and base class

//------------------------------------------------------------------------
// The minify bridge - adds hooks to handle minify requests
//------------------------------------------------------------------------
load_library( 'bridges/minify',		IS_CORE_LIB );

// load order is important - config first, then get plugins as soon as possible

// main config
load_config( 'version', IS_CORE );
load_config( 'config' );

if ( config( 'debug_mode' ) === true )
{
	require_once 'FirePHPCore/fb.php';
}

load_config( 'plugins' ); // where to specify which plugin files to load
load_plugins();

do_action( 'pre_system' );
do_action( 'plugins_loaded' );

do_action( 'load_library_config' );
load_config( 'libraries' );

if ( TGSF_CLI === false )
{
	load_config( 'config_web' );
	load_config( 'user_agent' );
}

do_action( 'core_load_complete' );
do_action( 'app_loaded' );
