<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// registers the static page plugin
tgsfPlugin::loaderFactory()
	->file( plugin( 'static_page/static_page', IS_CORE ) )
	->name( 'static_page' )
	->register();
	
tgsfEventFactory::actionHandler()
	->event( 'static_page_init' )
	->func( 'config_static_page' )
	->attach();

function config_static_page( $name )
{
	global $config;
	$config['static_page_minRole'] = tgsfRoleAdmin;
	$config['static_page_view'] = '_admin/page_editor';
}
//------------------------------------------------------------------------
// sample plugin that loads after core loader is done loading core and app
/*
tgsfEventFactory::handler()
	->event( 'app_loaded' )
	->func( 'appOnLoadComplete' )
	->attach();

function appOnLoadComplete()
{

}
*/
