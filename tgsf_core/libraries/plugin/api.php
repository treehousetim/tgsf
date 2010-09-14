<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
function &tPLUGIN()
{
	static $pluginApi = null;
	
	if ( $pluginApi === null )
	{
		$pluginApi = new tgsfPlugin();
	}

	return $pluginApi;
}
//------------------------------------------------------------------------
function register_plugin( $file, $name )
{
	if ( tPLUGIN()->pluginRegistered( $name ) )
	{
		return false;
	}
	
	return tPLUGIN()->registerPlugin( $file, $name );
}
//------------------------------------------------------------------------
/*
function add_action( $name, $handler, $level = 0 )
{
	tPLUGIN()->addHandler( eventACTION, $name, $handler, $level );
}*/

//------------------------------------------------------------------------
function add_filter( $name, $handler, $level = 0 )
{
	tPLUGIN()->addHandler( eventFILTER, $name, $handler, $level );
}
//------------------------------------------------------------------------
function load_plugins()
{
	
	$plugins = tPLUGIN()->getPlugins();
	
	foreach ( $plugins as $info )
	{
		extract( $info );
		require_once $file;
		tPLUGIN()->markPluginAsLoaded( $file, $name );
		tPLUGIN()->doAction( $name . '_init', $info );
	}
}