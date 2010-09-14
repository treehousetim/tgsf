<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// syntax is (file is the .php file without the .php extension )
// register_plugin( plugin( 'folder/file' ), 'logical_plugin_name' );

// this registers a plugin in the application's plugins folder.

// registers the static page plugin
//register_plugin( plugin( 'static_page/static_page', IS_CORE ), 'static_page' );
add_action( 'static_page_init', 'config_static_page' );
function config_static_page( $name )
{
	global $config;
	$config['static_page_minRole'] = roleADMIN;
	$config['static_page_view'] = '_admin/page_editor';
}
//------------------------------------------------------------------------
/*
add_action( 'core_load_complete', 'appOnLoadComplete' );
function appOnLoadComplete()
{

}*/
