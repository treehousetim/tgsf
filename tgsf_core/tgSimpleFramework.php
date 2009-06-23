<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

define( 'EXT', '.php' );
define( 'PHP', '.php' );

define( 'JS', '.js' );
define( 'CSS', '.css' );
define( 'HTML', '.html' );
define( 'htm', '.htm' );

define( 'PNG', '.png' );
define( 'JPG', '.jpg' );
define( 'JPEG', '.jpeg' );
define( 'GIF', '.gif' );
//------------------------------------------------------------------------
define( 'IS_CORE_PATH', true );
define( 'IS_CORE_LIB', true );
define( 'IS_CORE_CONFIG', true );
//------------------------------------------------------------------------


//------------------------------------------------------------------------
function path( $folder, $core = false )
{
	$folder = trim( $folder, ' /' );

	$root = APP_PATH;

	if ( $core === IS_CORE_PATH )
	{
		$root = CORE_PATH;
		if ( $folder === 'assets' )
		{
			$root = BASEPATH;
			$folder = 'tgsf_core_assets';
		}
	}
	


	return $root . $folder . '/';
}
//------------------------------------------------------------------------
function can_plugin()
{
	return function_exists( 'do_filter' );
}
//------------------------------------------------------------------------
function load_library( $name, $core = false )
{
	$path = path( 'libraries', $core );
		
	require_once  $path . $name . PHP;
}
//------------------------------------------------------------------------
function load_model( $name, $core = false )
{
	$path = path( 'models', $core );
	
	require_once $path . $name . PHP;
}
//------------------------------------------------------------------------
function load_config( $name, $core=false )
{
	global $config;
	global $plugins;

	// while bootstrapping we don't have the plugin functions, but later on we will
	if ( can_plugin() )
	{
		$name = do_filter( 'load_config', $name );
		
		$action = ($core?'core-':'') . 'config' . $name;
		do_action( $action );
	}

	require_once path( 'config', $core ) . $name . PHP;
}
//------------------------------------------------------------------------
function maintenance_mode_check()
{
	do_action( 'maintenance_mode_check' );
	
	if ( config( 'maintenanceMode' ) )
	{
		if ( ! isset( $_GET[config( 'maintenanceModeVar' )] ) || $_GET[config( 'maintenanceModeVar' )] != config('maintenanceModeVarValue' )  )
		{
			do_action( 'maintenance_mode_message' );
			echo 'Our website is currently down for maintenance. Please check back a little later.';
			exit();
		}
	}
}
//------------------------------------------------------------------------
function force_no_www( $checkFor = true )
{
	if ( starts_with( $_SERVER['HTTP_HOST'], 'www.' ) )
	{
		location_301( $_SERVER['REQUEST_URI'] );
	}
}
//------------------------------------------------------------------------
/**
* Forces a reload (permanent redirect) to the base url as defined in your config file.
*/
function force_www()
{
	if ( ! starts_with( current_host(), 'www.' ) )
	{
		location_301( $_SERVER['REQUEST_URI'] );
	}
}
//------------------------------------------------------------------------
function force_trailing_slash()
{
	define( 'tgTrailingSlash', true );
	if ( strlen( $_SERVER['REDIRECT_QUERY_STRING'] ) == 0 && strlen( $_SERVER['REDIRECT_URL'] ) && substr( $_SERVER['REDIRECT_URL'], -1 ) != '/' )
	{
		$vars = array();
		$page = tg_parse_url( $vars );

		$extra = '';
		if ( count( $vars ) )
		{
			$extra = '_/';

			foreach ( $vars as $name => $val )
			{
				$extra .= $name . '/' . $val . '/';
			}
			$extra = trim( $extra, ' /' ) . '/';
		}
		
		$url = current_base_url() . $page . '/' . $extra;
		
		if ( plugin_system_ready() )
		{
			$url = do_filter( 'force_trailing_slash_redirect_url', $url );
			do_action( 'force_trailing_slash_redirect', $url );
		}
		
	 	header( "HTTP/1.1 301 Moved Permanently" );
	    header( 'Location: ' . $url );
	    exit();
	}
}
//------------------------------------------------------------------------
function config( $item )
{
	global $config;
	
	$retVal = false;
	if ( isset( $config[$item] ) )
	{
		$retVal = $config[$item];
	}
	
	if ( can_plugin() )
	{
		$retVal = do_filter( 'config_item', $retVal );
	}

	return $retVal;
}
//------------------------------------------------------------------------
function controller_exists( $name, $core = false )
{
	return file_exists( controller( $name, $core ) );
}
//------------------------------------------------------------------------
function controller( $name, $core = false )
{
	return path( 'controllers', $core ) . $name . PHP;
}
//------------------------------------------------------------------------
function view( $name, $core = false )
{
	return path( 'views', $core ) . $name . PHP;
}
//------------------------------------------------------------------------
function image( $file, $core = false, $path = false )
{
	$loc = config( 'image_url' );
	if ( $path )
	{
		$loc = config( 'image_path' );
	}
	
	return $loc . $file;
}
//------------------------------------------------------------------------
function image_url( $file, $absolute = false )
{
	$loc = config( 'image_url' );
	
	if ( $absolute )
	{
		$loc = '';
	}
	
	return $loc . $file;
}

//------------------------------------------------------------------------

function plugin( $file, $core = false )
{
	return path( 'plugins', $core ) . $file . PHP;
}

//------------------------------------------------------------------------

function font( $file, $core = false )
{
	return path( 'assets', $core ) . 'fonts/' . $file;
}

//------------------------------------------------------------------------

function url( $url, $core = false )
{
	$url = trim( $url, "\t\n\r /\\" ); // remove leading/trailing whitespace and slashes( back and forward)
	
	if ( defined( 'tgTrailingSlash' ) && tgTrailingSlash === true )
	{
		$url .= '/';
	}
	
	if ( $url == '/' )
	{
		$url = '';
	}
	
	$url = do_filter( 'generate_url', $url );

	return current_base_url() . $url;
}

//------------------------------------------------------------------------

function tg_parse_url( &$vars )
{
	global $page, $pageVars;

	$baseUrlPart = current_base_url_path();
	
	$page = trim( $_SERVER['REDIRECT_URL'], '/' );
	$page = substr( $page, strlen( $baseUrlPart ) );

	list( $page, $varPieces ) = explode( '/_/', $page );
	$page = trim( $page, ' /' );
	
	$vars =& parse_url_vars( $varPieces );
	$pageVars =& $vars;
	
	if ( $page == '' )
	{
	    $page = 'home';
	}
	
	return $page;
}

//------------------------------------------------------------------------

function parse_url_vars( $varPieces )
{
	// get our pieces by exploding on the slash
	$pieces = array();
	if ( ! is_null( $varPieces ) )
	{
		$pieces = explode( '/', trim( $varPieces, '/' ) );
	}
	
	// if we have pieces, then loop through them assigning the array [piece1] => piece2
	// throw an error if we have an odd number of pieces
	$pieceCnt = count( $pieces );
	if ( $pieceCnt > 0 )
	{
		for ( $ix = 0; $ix < $pieceCnt; $ix++ )
		{
			$name = $pieces[$ix];
			$val = null;
			if ( isset( $pieces[$ix+1] ) )
			{
				$val = $pieces[$ix+1];
			}
			// attempt to set $_GET
			// we don't overwrite existing get vars though
			if ( ! isset( $_GET[$name] ) )
			{
				$_GET[$name] =& $val;
			}
			
			// also set $_GET using an underscore prefix
			// this provides an additional attempt in case the first one fails
			// but also provides a way for someone to use _vars for their
			// application in case they decide to use that as a naming convention
			if ( ! isset( $_GET['_' . $name] ) )
			{
				$_GET['_' . $name] =& $val;
			}
			
			$vars[$name] =& $val;
			$ix++;
			unset( $val );
		}
		
		if ( $pieceCnt % 2 != 0  )
		{
			show_error( 'Count of variables is not even - pass variables using name/value pairs' );
		}
	}
	return $vars;
}

//------------------------------------------------------------------------

function url_array( &$vars )
{
	$page = tg_parse_url( &$vars );
	return explode( '/', $page );
}

//------------------------------------------------------------------------
function resolve_controller( $page )
{
	$page = do_filter( 'pre_resolve_controller', $page );
	if ( controller_exists( $page ) )
	{
		$out = controller( $page );
	}
	else
	{
		if ( controller_exists( $page . '/index' ) )
		{
			$out = controller( $page . '/index' );
		}
		else
		{
			// we don't output 404 headers here so that the 404 controller can make choices of its own
			// it should output the 404 header.
			$out = controller( '404' );
			$out = do_filter( 'controller_404', $out, $page );
		}
	}

	$out = do_filter( 'post_resolve_controller', $out, $page );
	return $out;
}

//------------------------------------------------------------------------

function dispatch( $page )
{
	do_action( 'dispatch' );
	
	$page = do_filter( 'dispatch', $page );

	content_buffer();
	
	include resolve_controller( $page );
	
	end_buffer();
}

//------------------------------------------------------------------------

function is_gz_capable()
{
	return false;
	$cnt1 = substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip');
	$cnt2 = substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], '*');
	
	return $cnt1 > 0 || $cnt2 > 0;
	/*
		
    $ua = $_SERVER['HTTP_USER_AGENT'];
    // quick escape for non-IEs
    if (0 !== strpos($ua, 'MSIE')
        || false !== strpos($ua, 'Opera')) {
        return false;
    }

    // no regex = faaast
    $version = (float)substr($ua, 30);

    return (
        $version < 6
        || ($version == 6  && false === strpos($ua, 'SV1'))
    );
*/
}

//------------------------------------------------------------------------
/**
* Selectively buffers content.  This function is plugin enabled so you can hook the filter: 'cancel_content_buffer' to turn off buffering entirely.
* If the user agent is capable of accepting gz compressed output, then we turn on gz compression.
*/
function content_buffer()
{
	global $no_content_buffer;
	$cancel = do_filter( 'cancel_content_buffer', false );
	
	if ( $cancel )
	{
		$no_content_buffer = true;
		return;
	}

	$no_content_buffer = false;
	
	if (  is_gz_capable() )
	{
	    //ob_start("ob_gzhandler");
		ob_start();
	}
	else
	{
		//ob_start("ob_gzhandler");
	    ob_start();
	}
}

//------------------------------------------------------------------------

function end_buffer()
{
	global $no_content_buffer;
	if ( $no_content_buffer )
	{
		return;
	}
	

	ob_end_flush();
}

//------------------------------------------------------------------------
/**
* Trims an array - removes empty and null elements
* @param String The entire string to compare against
* @param String The snippet to test for at the beginning of $compare
* @author Tim Gallagher<treehousetim@gmail.com>
* @Package PHPocketKnife
* @version 2/28/2006 8:30 PM
*/
function trimArray($inArray)
{
    foreach ($inArray as $key => $value)
    {
        if ( trim($value) !="" )
        {
            if ( is_int( $key ) )
            {
                $outArray[] = trim( $value );
            }
            elseif ( is_string( $key ) )
            {
                $outArray[$key] = trim($value);
            }
        }
    }
    
    return $outArray;
}

//------------------------------------------------------------------------
/**
* Returns true if the string to compare starts with the snippet
* @param String The entire string to compare against
* @param String The snippet to test for at the beginning of $compare
* @author Tim Gallagher<treehousetim@gmail.com>
* @Package PHPocketKnife
* @version 2/28/2006 8:30 PM
*/
function starts_with( $compare, $snippet )
{
	if ( is_array( $snippet ) )
	{
		$out = false;
		foreach ( $snippet as $value )
		{
			if ( $value == substr( $compare, 0, strlen( $value ) ) )
			{
				$out = true;
				break;
			}
		}
	}
	else
	{
		$out = $snippet === substr( $compare, 0, strlen( $snippet ) );
	}
	
	return $out;
}

//------------------------------------------------------------------------
/**
* Returns true if the string to compare ends with the snippet
* @param String The entire string to compare against
* @param String The snippet to test for at the end of $compare
* @author Tim Gallagher<treehousetim@gmail.com>
* @Package PHPocketKnife
* @version 2/28/2006 8:30 PM
*/
function ends_with( $compare, $snippet )
{
	if ( is_array( $snippet ) )
	{
		$out = false;
		foreach ( $snippet as $value )
		{
			if ( $value == substr( $compare, -1 * strlen( $value ) ) )
			{
				$out = true;
				break;
			}
		}
	}
	else
	{
		$out = $snippet == substr( $compare, -1 * strlen( $snippet ) );
	}
	
	return $out;
}
//------------------------------------------------------------------------
function tab( $repeat )
{
	return str_repeat( "\t", $repeat );
}
//------------------------------------------------------------------------
function enum( $items )
{
	for ( $ix = 0; $ix < count( $items ); $ix++ )
	{
		define( $items[$ix], $ix^2 );
	}
}
//------------------------------------------------------------------------

function enable_browser_cache( $file )
{
	$last_modified_time = filemtime( $file );
	$etag = md5_file( $file );
	
	$lastModified = 'Last-Modified: ' . gmdate( 'D, d M Y H:i:s', $last_modified_time ) . ' GMT';
	
	$lastModified = do_filter( 'cache_last_modified', $lastModified );
	$etag = do_filter( 'cache_etag', $etag );

	header( $lastModified );
	header( "Etag: $etag" );

	if ( @strtotime( $_SERVER['HTTP_IF_MODIFIED_SINCE'] ) == $last_modified_time || trim( $_SERVER['HTTP_IF_NONE_MATCH'] ) == $etag )
	{
	    header( 'HTTP/1.1 304 Not Modified' );
	    exit();
	}
}

//------------------------------------------------------------------------

function location_301( $url, $local = true )
{
	//header()
	header( "HTTP/1.1 301 Moved Permanently" );
	if ( $local )
	{
		header( 'Location: ' . url( $url ) );
	}
	else
	{
		header( 'Location: ' . $url );
	}
}

//------------------------------------------------------------------------

// 303 is a standard redirect - i.e. not permanent
function redirect( $url, $local = true ) { location_303( $url, $local ); } // redirect is an alias of location_302
function location( $url, $local = true ) { location_303( $url, $local ); } // location is an alias of location_302
function location_303( $url, $local = true )
{
	header( "HTTP/1.1 303 See Other" );
	if ( $local )
	{
		header( 'Location: ' . url( $url ) );
	}
	else
	{
		header( 'Location: ' . $url );
	}
}

//------------------------------------------------------------------------