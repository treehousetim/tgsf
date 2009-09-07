<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
// instantiate the global plugin handling object.
//------------------------------------------------------------------------
global $pluginSystem;
$pluginSystem = new tgPlugin();
//------------------------------------------------------------------------
// This is the procedural api to integrate the tgPlugin class with tgsf
//------------------------------------------------------------------------
function do_action( $name )
{
	$h =& tgPlugin();
	$args = func_get_args();

	if ( is_array( $args ) )
	{
		array_shift( $args );
	}
	else
	{
		$args = array();
	}

	return $h->doAction( $name, $args );
}

//------------------------------------------------------------------------

function do_filter( $name, $value )
{
	$h =& tgPlugin();
	$args = func_get_args();
	
	if ( is_array( $args ) )
	{
		array_shift( $args );
		array_shift( $args );
	}
	else
	{
		$args = array();
	}

	return $h->doFilter( $name, $value, $args );
}
//------------------------------------------------------------------------
function register_plugin( $file, $name )
{
	$h =& tgPlugin();
	if ( $h->pluginRegistered( $name ) )
	{
		return false;
	}
	
	return $h->registerPlugin( $file, $name );
}
//------------------------------------------------------------------------
function add_action( $name, $handler, $level = 0 )
{
	$h =& tgPlugin();
	$h->addAction( $name, $handler, $level );
}
//------------------------------------------------------------------------
function add_filter( $name, $handler, $level = 0 )
{
	$h =& tgPlugin();
	$h->addFilter( $name, $handler, $level );
}
//------------------------------------------------------------------------
// TODO: verify that this is needed. or that this is the appropriate place for it.
function tg_head()
{
	do_action( 'head' );
}
//------------------------------------------------------------------------
function load_plugins()
{
	$h =& tgPlugin();
	$plugins = $h->getPlugins();
	
	foreach ( $plugins as $info )
	{
		extract( $info );
		require_once( $file );
		$h->markPluginAsLoaded( $file, $name );
		$h->doAction( $name . '_init', $info );
	}
}


