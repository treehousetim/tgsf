<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

tgsfEventFactory::actionHandler()
	->event( 'check_install' )
	->func( 'forceInstall' ) // at the bottom of this file
	->attach();
//------------------------------------------------------------------------
// registers the static page plugin
tgsfPlugin::loaderFactory()
	->name( 'static_page' )
	->file( plugin( 'static_page/static_page', IS_CORE ) )
	->register();

tgsfPlugin::loaderFactory()
	->file( plugin( 'force_login' ) )
	->name( 'force_login' )
	->register();
//------------------------------------------------------------------------
tgsfEventFactory::actionHandler()->event( 'static_page_init' )->func( 'config_static_page' )->attach();
tgsfEventFactory::actionHandler()->event( 'force_login_init' )->func( 'config_force_login' )->attach();
//------------------------------------------------------------------------
function config_static_page( $event )
{
	global $config;
	$config['static_page_minRole'] = tgsfRoleAdmin;
	$config['static_page_view'] = '_admin/page_editor';
}
//------------------------------------------------------------------------
function config_force_login( $event )
{
	plugin_forceLogin::init();
}
//------------------------------------------------------------------------
function forceInstall( $event )
{
	global $page;
	if ( $page == 'install' || starts_with( $page, '_minify' ) )
	{
		return;
	}

	$table = coreTable( 'registry' );
	if ( dbm()->tableExists( $table ) == false )
	{
		URL( 'install' )->setVar( 'new', 1 )->redirect();
	}
}