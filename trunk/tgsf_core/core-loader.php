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
// load the core constant definitions
load_config( 'constants',			IS_CORE );

//------------------------------------------------------------------------
define( 'APP_PATH', BASEPATH . APP_FOLDER );
//------------------------------------------------------------------------
// datasources
//------------------------------------------------------------------------
load_library( 'tgsfUrl',			IS_CORE_LIB ); // the url function/Library
load_library( 'tgsfRequest',		IS_CORE_LIB );
load_library( 'tgsfPost',			IS_CORE_LIB );
load_library( 'tgsfGet',			IS_CORE_LIB );

//------------------------------------------------------------------------
// Misc Libraries
//------------------------------------------------------------------------
load_library( 'html/tgsfHtmlTag',	IS_CORE_LIB );	// used in the template api
load_library( 'tgsfTemplate',		IS_CORE_LIB );	// misc template/view related api funcs
load_library( 'tgsfSession',		IS_CORE_LIB );

//------------------------------------------------------------------------
// Plugin Library
//------------------------------------------------------------------------
load_library( 'plugin/tgsfPlugin',			IS_CORE_LIB ); // the plugin api and base class
load_library( 'plugin/tgsfEvent',			IS_CORE_LIB ); // events
load_library( 'plugin/tgsfPluginLoader',	IS_CORE_LIB ); // loading plugins

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
tgsfPlugin::getInstance()->loadPlugins();

tgsfEventFactory::action()->event( 'pre_system' )->exec();
tgsfEventFactory::action()->event( 'plugins_loaded' )->exec();
tgsfEventFactory::action()->event( 'load_library_config' )->exec();

load_config( 'libraries' );

if ( TGSF_CLI === false )
{
	load_config( 'config_web' );
	load_config( 'user_agent' );
}

tgsfEventFactory::action()->event( 'core_load_complete' )->exec();
tgsfEventFactory::action()->event( 'app_loaded' )->exec();

