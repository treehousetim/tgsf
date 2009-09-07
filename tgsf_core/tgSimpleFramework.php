<?php defined( 'BASEPATH' ) or die( 'Restricted' );
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
define( 'HTM', '.htm' );

define( 'PNG', '.png' );
define( 'JPG', '.jpg' );
define( 'JPEG', '.jpeg' );
define( 'GIF', '.gif' );
//------------------------------------------------------------------------
define( 'IS_CORE_PATH', true );
define( 'IS_CORE_LIB', true );
define( 'IS_CORE_CONFIG', true );
define( 'IS_CORE', true );
define( 'IS_CORE_TEMPLATE', true );
//------------------------------------------------------------------------
define( 'ENUM_USE_VALUE', true );
//------------------------------------------------------------------------
define( 'SALT_LENGTH', 40 );
//------------------------------------------------------------------------
// 
/**
* @param String The folder
* @param Is the folder located in the core (assets is parsed correctly)
*/
function relative_path( $folder, $core = false )
{
	$folder = trim( $folder, ' /' );

	if ( $core === IS_CORE_PATH )
	{
		$root = 'tgsf_core/';
		if( starts_with( $folder, 'assets' ) )
		{
			$root = 'tgsf_core_assets/';
			$folder = trim( substr( $folder, 6 ), ' /' );
		}
	}
	else
	{
		$root = APP_FOLDER;
	}
	
	if ( $folder != '' )
	{
		$folder .= '/';
	}
	
	return $root . $folder;
}
//------------------------------------------------------------------------
function path( $folder, $core = false )
{
	return BASEPATH . relative_path( $folder, $core );
}
//------------------------------------------------------------------------
function asset_path( $folder, $core = false )
{
	$folder = trim( 'assets/' . $folder, ' /' );
	return path( $folder, $core );
}
//------------------------------------------------------------------------
function css_path( $folder = '', $core = false )
{
	$folder = trim( 'css/' . $folder, ' /' );
	return asset_path( $folder, $core );
}
//------------------------------------------------------------------------
function js_path( $folder = '', $core = false )
{
	$folder = trim( 'js/' . $folder, ' /' );
	return asset_path( $folder, $core );
}
//------------------------------------------------------------------------
function jquery_path( $folder = '' )
{
	$folder = trim( 'jquery/' . $folder, ' /' );
	return js_path( $folder, IS_CORE_PATH );
}
//------------------------------------------------------------------------
function url_path( $folder, $core = false )
{
	return current_base_url() . relative_path( $folder, $core );
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
		
	return require_once  $path . $name . PHP;
}
//------------------------------------------------------------------------
/**
* Loads an instantiated template library.  Just like models, a template library
* needs to return a new instance of the class just defined.
* This uses path: libraries/templates and is primarily intended for php class based templates
* like those used in the form library.
* Unlike most other load functions, this one is controlled by a global variable
* that is located 
* @param String The path and name (minus the extension) of the template library 
*/
function &load_template_library( $name )
{
	global $useAppFormTemplates;
	$core = $useAppFormTemplates === false;
	$path = path( 'libraries/templates', $core );
	$ret = require_once( $path . $name . PHP );
	return $ret;
}
//------------------------------------------------------------------------
/**
* Loads a form - similar to load_model in that it returns an instance of an object
* Form files are required to return a new instance of the form
* @param String The path and name (minus the extension) of the form to load)
* This is prefixed by the current application's forms path
* @param Bool Is the form located in the core?  This would only be for built in forms
* like might be used in a core library (like a user lib).
*/
function &load_form( $name, $core = false )
{
	$path = path( 'forms', $core );
	$ret = require( $path . $name . PHP );
	return $ret;
}
//------------------------------------------------------------------------
/**
* Loads a model.  Returns an instance of a model.  Models are only allowed a single
* global existence.  Model instances are stored in a static variable in this function
* so that if a model is already loaded its instance is returned and no further filesystem
* performance is incurred.
* Model files are required to return a new instance of the model
* @param String The path and name of the model.  This is prefixed by the application's
* models path
* @param Bool Is the model located in the core?  This would only be used for built in models
* like might be used in a core library (like a user lib).
*/
function &load_model( $name, $core = false )
{
	$path = path( 'models', $core );
	static $loadedModels = array();
	
	if ( ! in_array( $name, array_keys( $loadedModels ) ) )
	{
		$loadedModels[$name] = require_once $path . $name . PHP;
	}

	return $loadedModels[$name];
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
	if ( empty( $_SERVER['REDIRECT_QUERY_STRING'] ) && ! empty( $_SERVER['REDIRECT_URL'] ) && strlen( $_SERVER['REDIRECT_URL'] ) && substr( $_SERVER['REDIRECT_URL'], -1 ) != '/' )
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
		
		if ( can_plugin() )
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
function image( $file, $core = false )
{
	$root = path( 'assets', $core );
	$root .= path( 'images' );
	
	return $root . $file;
}
//------------------------------------------------------------------------
function image_url( $file, $absolute = false )
{
	$loc = url_path( 'assets/images' );
	
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

	$page = empty( $_SERVER['REDIRECT_URL'] )?'':trim( $_SERVER['REDIRECT_URL'], '/' );
	$page = substr( $page, strlen( $baseUrlPart ) );

	$pieces = explode( '/_/', $page );
	$varPieces = '';
	if ( count( $pieces ) > 1 )
	{
		$page = $pieces[0];
		$varPieces = $pieces[1];
		$vars =& parse_url_vars( $varPieces );
	}
	$page = trim( $page, ' /' );
	
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
	$page = tg_parse_url( $vars );
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
/**
* A permanent redirect
*/
function location_301( $url, $local = true )
{
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
/**
* Generates an extremely secure password hash (one way) using a salt
* to prevent dictionary attacks against a comprimised database
* @param String The clear text password
* @param String The salt (actually a password string produced by this function)
*/
function hashPassword( $clearText, $salt = null )
{
    if ( is_null( $salt ) )
    {
        $salt = substr( sha1( uniqid( rand(), true ) ), 0, SALT_LENGTH) ;
    }
    else
    {
        $salt = substr( $salt, 0, SALT_LENGTH );
    }
    $hashed = $salt . sha1($salt . $clearText);

    return $hashed;
}