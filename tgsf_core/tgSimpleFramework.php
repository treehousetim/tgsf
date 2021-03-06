<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

define( 'REPORT_EOL', "\n" );
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
define( 'IS_APP', false );
define( 'IS_CORE_TEMPLATE', true );
define( 'IS_APP_TEMPLATE', false );
//------------------------------------------------------------------------
define( 'ENUM_USE_VALUE', true );
//------------------------------------------------------------------------
define( 'SALT_LENGTH', 40 );
//------------------------------------------------------------------------
// used when redirecting - used in tgsfUrl.php
define( 'DO_NOT_EXIT', false );
//------------------------------------------------------------------------
define( 'GET_DUMP_HTML', true );
//------------------------------------------------------------------------
define( 'IMAGE_URL_ABSOLUTE', true );
define( 'IMAGE_URL_RELATIVE', false );
//------------------------------------------------------------------------
function current_server_id()
{
	if ( TGSF_CLI )
	{
		return strtoupper( md5 ( __FILE__ . PHP_OS ) );
	}

	/*
	this is case sensitive.  On Windows the CSS will be retrieved based on the actual
	  directory name case-sensitive.  if the url is a different case than
	  the file system then the CSS request will use a different server_id

	*/

	return strtoupper( md5( current_host() . current_base_url_path() ) );
}
//------------------------------------------------------------------------
function show_current_server_id_parts()
{
	 echo __FILE__ . PHP_OS;
}
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
		if ( starts_with( $folder, 'assets' ) )
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
function removeBasePath( $path )
{
	if ( starts_with( $path, BASEPATH ) )
	{
		return substr( $path, strlen( BASEPATH ) );
	}

	return $path;
}
//------------------------------------------------------------------------
function basepathToUrl( $path )
{
	$path = removeBasePath( $path );
	return current_base_url() . $path;
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
function load_search( $name, $core = false )
{
	return load_cloned_object( path( 'searches', $core ), $name );
}
//------------------------------------------------------------------------
function load_report( $name, $core = false )
{
	load_library( 'report/tgsfReport', IS_CORE_LIB );
	return load_cloned_object( path( 'reports', $core ), $name );
}
//------------------------------------------------------------------------
function load_new_report( $name, $core = false )
{
	load_library( 'report/report', IS_CORE_LIB );
	load_library( 'report/reportCol', IS_CORE_LIB );
	load_library( 'report/reportFactory', IS_CORE_LIB );
	load_library( 'report/reportOutputHtml', IS_CORE_LIB );
	load_library( 'report/reportOutputCsv', IS_CORE_LIB );

	return load_cloned_object( path( 'reports', $core ), $name );
}
//------------------------------------------------------------------------
/**
* Loads an instantiated template library.  Works just like models.
* Unlike most other load functions, this one is controlled by a global variable
* that is located in the front controller index.php
* @param String The path and name (minus the extension) of the template library
* @see load_cloned_object
*/
function &load_template_library( $name, $core = true )
{
	return load_cloned_object( path( 'libraries/templates', $core ), $name );
}
//------------------------------------------------------------------------
/**
* Loads a form.  Works just like models.
* @param String The path and name (minus the extension) of the form to load)
* This is prefixed by the current application's forms path
* @param Bool Is the form located in the core?  This would only be for built in forms
* like might be used in a core library (like a user lib).
* @see load_cloned_object
*/
function &load_form( $name, $core = false )
{
	return load_cloned_object( path( 'forms', $core ), $name );
}
//------------------------------------------------------------------------
/**
* The first time a model is loaded, this function will require_once on the model file.
* It will take the return value from that required file and use it as an internal instance
* of that model.
* All return values from this function will be performed using object cloning.
* if a model has already loaded, a new instance is returned and no further filesystem
* access occurs.
* Model files are required to return a new instance of the model
* Models should not require constructor parameters as this loader will know nothing about that.
* @param String The path and name of the model.  This is prefixed by the application's
* model's path
* @param Bool Is the model located in the core?  This would only be used for built in models
* like might be used in a core library (like a user lib).
* @see load_cloned_object
*/
function &load_model( $name, $core = false )
{
	return load_cloned_object( path( 'models', $core ), $name );
}
//------------------------------------------------------------------------
function &load_view_model( $name, $core = false )
{
	return load_cloned_object( path( 'view_models', $core ), $name );
}
//------------------------------------------------------------------------
function &load_controller_model( $name, $core = false )
{
	load_library( 'tgsfCm', true );
	return load_cloned_object( path( 'controller_models', $core ), $name );
}
//------------------------------------------------------------------------
/**
* Works like models and forms.  Used for grids (html tables)
* @param String The path and name of the model.  This is prefixed by the application's
* grid's path
* @param Bool Is the grid located in the core?  This would only be used for built in grids
* like might be used in a core library (like a user lib).
* @see load_cloned_object
*/
function &load_grid( $name, $core = false )
{
	return load_cloned_object( path( 'grids', $core ), $name );
}
//------------------------------------------------------------------------
/**
* Requires a file based on path and name.  Stores the return value from that included file
* in a static array.  Assumes return values are object instances.
* Returns a clone of that return value.  If a file has already been included, we skip the require_once
* and simply return a clone of the original that has been stored in the static array.
* @param String The path to the file.
* @param String The name of the file - without the php extension
*/
function &load_cloned_object( $path, $name )
{
	static $masterObjects = array();

	$file = $path . $name . PHP;

	if ( ! in_array( $file, array_keys( $masterObjects ) ) )
	{
		$obj = require_once( $file );

		if ( ! is_object( $obj ) )
		{
			throw new tgsfException( "You must return an object instance when loading {$file}" );
		}

		$masterObjects[$file] =& $obj;
	}

	$returnObj = clone $masterObjects[$file];
	return $returnObj;
}
//------------------------------------------------------------------------
function config_file_exists( $name, $core )
{
	return file_exists( path( 'config', $core ) . $name . PHP );
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

		tgsfEventFactory::action()
		->event( ($core?'core-':'') . 'config' . $name )
		->exec();
	}

	if ( ! config_file_exists( $name, $core ) )
	{
		throw new tgsfException( 'Unable to load config file: ' . $name );
	}

	require_once path( 'config', $core ) . $name . PHP;
}
//------------------------------------------------------------------------
function load_database_libraries()
{
	// db search extends grid
	load_library( 'html/tgsfGrid', 			IS_CORE_LIB );

	// enums for the database libraries
	load_library( 'db/enum',				IS_CORE_LIB );
	load_library( 'db/dbManager',			IS_CORE_LIB );
	load_library( 'db/dbSetup',				IS_CORE_LIB );
	load_library( 'db/queryJoin',			IS_CORE_LIB );
	load_library( 'db/query',				IS_CORE_LIB );
	load_library( 'db/tgsfPaginateQuery',	IS_CORE_LIB );
	load_library( 'db/tgsfDbSearch',		IS_CORE_LIB );
	load_library( 'db/foreignKey',			IS_CORE_LIB );
	load_library( 'db/field',				IS_CORE_LIB );
	load_library( 'db/dbIndex',				IS_CORE_LIB );
	load_library( 'db/table',				IS_CORE_LIB );
	load_library( 'db/model',				IS_CORE_LIB );
	load_library( 'db/dbDataSource',		IS_CORE_LIB );
}
//------------------------------------------------------------------------
function load_form_libraries()
{
	load_library( 'html/tgForm',			IS_CORE_LIB );
	load_library( 'html/tgFormField',		IS_CORE_LIB );
}
//------------------------------------------------------------------------
function maintenance_mode_check()
{
	tgsfEventFactory::action()->event( 'maintenance_mode_check' )->exec();

	if ( config( 'maintenanceMode' ) )
	{
		if ( ! isset( $_GET[config( 'maintenanceModeVar' )] ) || $_GET[config( 'maintenanceModeVar' )] != config('maintenanceModeVarValue' )  )
		{
			tgsfEventFactory::action()->event( 'maintenance_mode_message' )->exec();
			echo 'Our website is currently down for maintenance. Please check back a little later.';
			exit();
		}
	}
}
//------------------------------------------------------------------------
function force_no_www( $checkFor = true )
{
	global $config;
	if ( starts_with( current_http_host(), 'www.' ) )
	{
		$config['host_www'] = false;
		URL( $_SERVER['REQUEST_URI'] )->permRedirect();
	}
}
//------------------------------------------------------------------------
/**
* Forces a reload (permanent redirect) to the base url as defined in your config file.
*/
function force_www()
{
	global $config;
	$config['host_www'] = true;
	if ( ! starts_with( current_host(), 'www.' ) )
	{
		URL( $_SERVER['REQUEST_URI'] )->permRedirect();
	}
}
//------------------------------------------------------------------------
function force_trailing_slash()
{
	define( 'tgTrailingSlash', true );

	if ( empty( $_SERVER['REDIRECT_QUERY_STRING'] ) && ! empty( $_SERVER['REDIRECT_URL'] ) && strlen( $_SERVER['REDIRECT_URL'] ) && substr( $_SERVER['REDIRECT_URL'], -1 ) != '/' )
	{
		$url = URL( tgsf_parse_url() );

		$vars = empty( $_GET['__tgsf_vars'] )?array():$_GET['__tgsf_vars'];

		foreach ( $vars as $name => $val )
		{
			$url->setVar( $name, $val );
		}

		if ( can_plugin() )
		{
			tgsfEventFactory::filter()
				->event( 'force_trailing_slash_redirect_url' )
				->content( $url )
				->exec();

			tgsfEventFactory::action()
				->event( 'force_trailing_slash_redirect' )
				->setVar( 'url', $url )
				->exec();
		}

		$url->permRedirect();
	}
}
//------------------------------------------------------------------------
function force_https()
{
	if ( TGSF_CLI === true )
	{
		return;
	}

	if ( current_has_ssl() == false )
	{
		header( "HTTP/1.1 301 Moved Permanently" );
	    header( 'Location: ' . current_https_url() );
		exit();
	}
}
//------------------------------------------------------------------------
function config( $item, $defaultValue = false )
{
	global $config;

	$retVal = $defaultValue;
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
function cli_controller_exists( $name, $core = false )
{
	return file_exists( cli_controller( $name, $core ) );
}
//------------------------------------------------------------------------
function controller_exists( $name, $core = false )
{
	return file_exists( controller( $name, $core ) );
}
//------------------------------------------------------------------------
function cli_controller( $name, $core = false )
{
	return path( 'cli', $core ) . $name . PHP;
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
function get_view_content( $name, $vars = array(), $core = false )
{
	extract( $vars );
	ob_start();
	include view( $name, $core );
	return ob_get_clean();
}
//------------------------------------------------------------------------
function image( $file, $core = false )
{
	$root = path( 'assets', $core );
	$root .= path( 'images' );

	return $root . $file;
}
//------------------------------------------------------------------------
function imageUrl( $file, $absolute = false, $core = false )
{
	return image_url( $file, $absolute, $core );
}
//------------------------------------------------------------------------
function image_url( $file, $absolute = false, $core = false )
{
	$loc = url_path( 'assets/images', $core );

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
// parse_url is a PHP function, that's why this is named tgsf_parse_url
function tgsf_parse_url()
{
	static $page = null;

	if ( ! $page === null )
	{
		return $page;
	}

	if ( TGSF_CLI )
	{
		return CLI();
	}

	$baseUrlPart = rtrim( current_base_url_path(), '/' );

	$page = empty( $_SERVER['REDIRECT_URL'] ) ? '' : $_SERVER['REDIRECT_URL'];

	if ( $baseUrlPart != '' )
	{
		$page = ltrim( $page, '/ ' );
		$page = substr( $page, strlen( $baseUrlPart ) );
	}

	$pieces = explode( config( 'get_string' ), $page );
	$varPieces = '';
	if ( count( $pieces ) > 1 )
	{
		$page = trim( $pieces[0], '/' );
		$varPieces = $pieces[1];
		tgsf_parse_url_vars( $varPieces );
	}

	if ( APP_URL_FOLDER != '' )
	{
		$pieces = explode( APP_URL_FOLDER, $page );
		$page = $pieces[1];
	}

	$page = trim( $page, ' /' );

    if ( $page == '' )
	{
	    $page = 'index';
	}

	return $page;
}
//------------------------------------------------------------------------
function tgsf_parse_url_vars( $varPieces )
{
	static $vars = null;

	if ( ! $vars === null )
	{
		return $vars;
	}

	// get our pieces by exploding on the slash
	$pieces = array();
	if ( ! is_null( $varPieces ) )
	{
		if ( ends_with( $varPieces, '//' ) )
		{
			$varPieces = trim( $varPieces,'/' ) . '/';
		}
		else
		{
			$varPieces = trim( $varPieces,' /' );
		}

		$pieces = explode( '/', $varPieces );
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
			else
			{
				$val = true;
			}
			// attempt to set $_GET
			// we don't overwrite existing get vars though
			if ( ! isset( $_GET[$name] ) )
			{
				$_GET[$name] = $val;
			}

			$vars[$name] = $val;
			$ix++;
			unset( $val );
		}

		$_GET['__tgsf_vars'] = $vars;

		// throw error only while in debug mode - i.e. only for dev setups
		// the idea being that once in production, we don't want to be bothered by lots of random potential error logs
		if ( config( 'debug_mode' ) && $pieceCnt % 2 != 0 )
		{
			throw new tgsfException( 'Count of variables is not even - pass variables using name/value pairs' );
		}
	}
	return $vars;
}
//------------------------------------------------------------------------
function url_array()
{
	return explode( '/', tg_parse_url() );
}
//------------------------------------------------------------------------
function display_404( $page = null )
{
	if ( $page === null )
	{
		$page = $GLOBALS['page'];
	}

	require get_404( $page );
	exit();
}
//------------------------------------------------------------------------
function get_404( $page )
{
	tgsfEventFactory::action()->event( 'pre_404' )->setVar( 'page', $page )->exec();

	// we don't output 404 headers here so that the 404 controller can make choices of its own
	// it should output the 404 header.
	$out = controller( '404' );

	$out = tgsfEventFactory::filter()
		->event( 'controller_404' )
		->content( $out )
		->setVar( 'page', $page )
		->exec();

	return $out;
}
//------------------------------------------------------------------------
function resolve_controller( $page )
{
	tgsfEventFactory::action()
		->event( 'pre_resolve_controller' )
		->setVar( 'page', $page )
		->exec();

	$page = tgsfEventFactory::filter()
			->event( 'pre_resolve_controller' )
			->content( $page )
			->exec();
	if ( config_file_exists( 'routes', false ) )
	{
		load_config( 'routes' );
		$routes = config( 'routes' );

		if ( array_key_exists( $page, (array)$routes ) )
		$page = $routes[$page];
	}

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
			$out = get_404( $page );
		}
	}

	$out = tgsfEventFactory::filter()
		->event( 'post_resolve_controller' )
		->content( $out )
		->setVar( 'page', $page )
		->exec();

	tgsfEventFactory::action()
		->event( 'post_resolve_controller' )
		->setVar( 'page', $page )
		->setVar( 'controller', $out )
		->exec();

	return $out;
}
//------------------------------------------------------------------------
function resolve_cli_controller( $name )
{
	tgsfEventFactory::action()
		->event( 'pre_resolve_cli_controller' )
		->setVar( 'controller', $name )
		->exec();

	tgsfEventFactory::filter()
		->event( 'pre_resolve_cli_controller' )
		->content( $name )
		->exec();

	// check to see if controllers exist in:
	// app/cli/$name.php
	// app/cli/$name/index.php
	// app/controllers/$name.php
	// app/controllers/$name/index.php

	if ( cli_controller_exists( $name ) )
	{
		$out = cli_controller( $name );
	}
	else
	{
		if ( cli_controller_exists( $name . '/index' ) )
		{
			$out = cli_controller( $name . '/index' );
		}
		else
		{
			if ( controller_exists( $name ) )
			{
				$out = controller( $name );
			}
			else
			{
				if ( controller_exists( $name . '/index' ) )
				{
					$out = controller( $name . '/index' );
				}
				else
				{
					echo 'CLI Controller not found.' . PHP_EOL;
					$out = false;
				}
			}
		}
	}

	$out = tgsfEventFactory::filter()
		->event( 'post_resolve_cli_controller' )
		->content(  $out )
		->setVar( 'name', $name )
		->exec();

	tgsfEventFactory::action()
		->event( 'post_resolve_cli_controller' )
		->setVar( 'name', $name )
		->setVar( 'controller', $out )
		->exec();

	return $out;
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
* Returns true/false if debug_mode has been set in config files
* returns false if the config function hasn't been defined yet since we can't check.
*/
function in_debug_mode()
{
	if ( ! function_exists( 'config' ) || config( 'debug_mode' ) === false )
	{
		return false;
	}

	return true;
}
//------------------------------------------------------------------------
/**
* Selectively buffers content.  This function is plugin enabled so you can hook the filter: 'cancel_content_buffer' to turn off buffering entirely.
* If the user agent is capable of accepting gz compressed output, then we turn on gz compression.
*/
function content_buffer()
{
	global $no_content_buffer;

	$cancel = tgsfEventFactory::filter()
		->event( 'cancel_content_buffer' )
		->content( false )
		->exec();

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
* Generates an extremely secure password hash (one way) using a salt
* to prevent dictionary attacks against a compromised database
* @param String The clear text password
* @param String The salt (actually a password string produced by this function)
*/
function hash_password( $clearText, $salt = null )
{
    if ( is_null( $salt ) )
    {
        $salt = randomHash( SALT_LENGTH );
    }
    else
    {
        $salt = substr( $salt, 0, SALT_LENGTH );
    }
    $hashed = $salt . sha1($salt . $clearText);

    return $hashed;
}
//------------------------------------------------------------------------
/**
* Random hash - defaults to being 12 characters long
*/
function randomHash( $length = 12 )
{
	return strtoupper( substr( sha1( uniqid( rand(), true ) ), 0, $length ) );
}
//------------------------------------------------------------------------
function randomCode ( $length = 4, $useNumbers = true, $useLower = false, $useSpecial = false )
{
	$set = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';

	if ( $useNumbers )
	{
		$set .= "0123456789";
	}

	if ( $useLower )
	{
		$set .= "abcdefghijklmnopqrstuvwxyz";;
	}

	if ( $useSpecial )
	{
		$set .= "~@#$%^*()_+-={}|][";
	}
	$code = '';
	for ( $ix=0; $ix < $length; $ix++ )
	{
		$code .= $set[(mt_rand(0, (strlen( $set ) - 1 ) ) )];
	}
	return $code;
}
//------------------------------------------------------------------------
/**
* Sends the headers to force a browser to start a download.
* @param String the filename the browser should save the file as
* @param String the file data
* @param String The content-type to send to the browser - defaults to text/plain
*/
function sendDownload( $filename, $data, $type = 'text/plain' )
{
	header( 'Cache-Control: must-revalidate' );
	header( 'Pragma: must-revalidate' );

	header( 'Content-Description: File Transfer' );
 	header( 'Content-Disposition: attachment; filename=' . $filename );
	header( 'Content-Type: ' . $type );
	echo $data;
	exit();
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
// log functions
//------------------------------------------------------------------------
function log_exception( $e, $tgsfLogError = false )
{
	$out  = $e->getMessage() . PHP_EOL;
	$out .= 'File: ' . $e->getFile() . PHP_EOL;
	$out .= 'Line: ' . $e->getLine() . PHP_EOL;
	$out .= $e->getTraceAsString();

	if ( can_plugin() )
	{
		$out = do_filter( 'log_exception', $out, $e );
	}
	general_log( $out, 'exception_log.txt' );
}
//------------------------------------------------------------------------
function log_query_error( $query )
{
	if ( can_plugin() )
	{
		$query = do_filter( 'log_query_error', $query );
	}
	general_log( $query, 'query_error_log.txt' );
}
//------------------------------------------------------------------------
function log_error( $message )
{
	if ( can_plugin() )
	{
		$message = do_filter( 'log_error', $message );
	}
	general_log( $message, 'error_log.txt' );
}
//------------------------------------------------------------------------
function general_log( $message, $file = 'general_log.txt' )
{
	$formattedDate = date( 'Y-m-d H:i:s T' );
	try
	{
		$date = new DateTime();
		$cst = new DateTimeZone( 'America/Chicago' );
		$date->setTimezone( $cst );
		$formattedDate = $date->format( 'Y-m-d H:i:s T' );
	}
	catch( Exception $e )
	{

	}

	$file = clean_text( $file, '_', "." );
	$file = path( 'logs', IS_CORE_PATH ) . $file;
	$out = PHP_EOL . '------------------------------------------------------------------------' . PHP_EOL;
	$out .= $formattedDate . PHP_EOL;
	$out .= '----------------------' . PHP_EOL;
	$out .= $message . PHP_EOL;

	if ( can_plugin() )
	{
		$out = do_filter( 'general_log', $out, $message );
	}

	try
	{
		file_put_contents( $file, $out, FILE_APPEND );
	}
	catch( Exception $e )
	{
		echo 'Unable to log errors - you should use database logging.';
		echo PHP_EOL . '<pre>' . PHP_EOL . $e->getMessage() . PHP_EOL . '</pre>' . PHP_EOL;
		exit();
	}
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
// utility functions
//------------------------------------------------------------------------
function remote_addr_clean()
{
	if ( TGSF_CLI )
	{
		return '127.0.0.1';
	}
	elseif ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) )
	{
		return $_SERVER['REMOTE_ADDR'];
	}
	else
	{
		return 'N/A';
	}
}
//------------------------------------------------------------------------
/**
*
*/
function wrap_not_empty( $before, $subject, $after = '' )
{
	if ( ! empty( $subject ) )
	{
		$subject = $before . $subject . $after;
	}

	return $subject;
}
//------------------------------------------------------------------------
/**
* Cleans out possible email header injection attacks.  Spammers try to inject new line characters as delimiters
* so they can put in their own headers in an email message.  We're simply removing them.  these characters
* should not exist in email addresses or in email subjects.  don't use this on message bodies.
* @param String The text to clean (something like an email address that was typed in)
*/
function clean_for_email( $inbound )
{
    return str_replace( array( "\n", "\r" ), "", $inbound );
}
//------------------------------------------------------------------------
/**
* Trims an array - removes empty and null elements
* @param String The entire string to compare against
* @param String The snippet to test for at the beginning of $compare
*/
function trimArray( $inArray )
{
    foreach ( $inArray as $key => $value )
    {
        if ( trim( $value ) !="" )
        {
            if ( is_int( $key ) )
            {
                $outArray[] = trim( $value );
            }
            elseif ( is_string( $key ) )
            {
                $outArray[$key] = trim( $value );
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
*/
function starts_with( $subject, $snippet )
{
	if ( is_array( $snippet ) )
	{
		$out = false;
		foreach ( $snippet as $value )
		{
			if ( $value == substr( $subject, 0, strlen( $value ) ) )
			{
				$out = true;
				break;
			}
		}
	}
	else
	{
		$out = $snippet === substr( $subject, 0, strlen( $snippet ) );
	}

	return $out;
}
//------------------------------------------------------------------------
/**
* Returns true if the string to compare ends with the snippet
* @param String The entire string to compare against
* @param String The snippet to test for at the end of $compare
*/
function ends_with( $subject, $snippet )
{
	if ( is_array( $snippet ) )
	{
		$out = false;
		foreach ( $snippet as $value )
		{
			if ( $value == substr( $subject, -1 * strlen( $value ) ) )
			{
				$out = true;
				break;
			}
		}
	}
	else
	{
		$out = $snippet == substr( $subject, -1 * strlen( $snippet ) );
	}

	return $out;
}
//------------------------------------------------------------------------
/**
* Returns the specified number of tab characters - a silly function
* that only serves to create pretty looking code.
* @param Int The number of tab characters to return.
*/
function tab( $repeat )
{
	return str_repeat( "\t", $repeat );
}
//------------------------------------------------------------------------
/**
* Attempts to simulate the C language enum construct by creating defines
* for the array items passed in.
* @param String The name of the group/prefix for the enum'd values. example: qt or QUERY_TYPE_
* @param Array The array of items to define values for.  If an array key is non-numeric  then that becomes the define name.
* @param bool Should enum use the value for the defined value or use the given array key.  Use define: ENUM_USE_VALUE
* example: $arrayExample['DEF'] = 'value'; enum( 'example', $arrayExample ); creates this define:
* define( 'exampleDEF', 'value' );
*/
function enum( $prefix, $items, $useValueForDefine = false )
{
	if ( $useValueForDefine )
	{
		foreach ( $items as $key => $value )
		{
			define( $prefix . $value, $value );
		}
	}
	else
	{
		foreach ( $items as $key => $value )
		{
			if ( is_numeric( $key ) )
			{
				define( $prefix . $value, $key );
			}
			else
			{
				define( $prefix . $key, $value );
			}
		}
	}
}
//------------------------------------------------------------------------
/**
* Determines if a string is a local file based on whether or not it
* begins with http:// or https://
* @param String The file or path to check
*/
function is_local( $file )
{
	// return if the file does not start with
	return ! starts_with( $file, array( 'http://', 'https://' ) );
}
//------------------------------------------------------------------------
function must_end_with( &$subject, $ending )
{
	if ( ! ends_with( $subject, $ending ) )
	{
		$subject .= $ending;
	}
}
//------------------------------------------------------------------------
function clean_text( $subject, $replace = '_', $extraAllowedChars = '' )
{
	return preg_replace( '/[^a-z0-9' . $extraAllowedChars . ']+/sim', $replace, $subject );
}
//------------------------------------------------------------------------
function zeroPad( $value, $places )
{
	$value = rtrim( substr( $value, 0, $places ), " \n\r0" );
	if ( empty( $value ) )
	{
		return str_repeat( '0', $places );
	}

	if ( strlen( $value ) < $places )
	{
		$value = $value . str_repeat( '0', $places - strlen( $value ) );
	}

	return $value;
}
//------------------------------------------------------------------------
function truncateNumberFormat( $value, $places )
{
	$neg = '';
	if ( $value < 0 && $value > -1 )
	{
		$neg = '-';
	}

	$vals = preg_split( '/\\./', (string)$value );

	if ( $places == 0 )
	{
		return $neg . number_format( $vals[0] );
	}

	if ( ! empty( $vals[1] ) )
	{
		return $neg . number_format( $vals[0] ) . '.' . zeroPad( $vals[1], $places );
	}
	else
	{
		// rex-php-5.3 - use intval
		return $neg . number_format( intval($vals[0]) ) . '.' . zeroPad( 0, $places );
	}
}
//------------------------------------------------------------------------
function get_dump( &$var, $formatHTML = false )
{
	$prefix = '';
	$postfix = '';

	if ( $formatHTML === true )
	{
		$prefix = '<pre>';
		$postfix = '</pre>';
	}

	ob_start();
	var_dump( $var );
	return $prefix . ob_get_clean() . $postfix;
}
//------------------------------------------------------------------------
function pre_dump( &$var )
{
	echo get_dump( $var, true );
}
//------------------------------------------------------------------------
function rtrace($test)
{
	$btrace = array();

	$t = debug_backtrace();

	array_shift( $t );
	array_shift( $t );

	foreach ( $t as $lineInfo )
	{
		$btrace[] = '<b>' . $lineInfo['function'] . '</b>   in ' . $lineInfo['file'] . '  ('. $lineInfo['line'] . ')';

		if ( isset( $lineInfo['args'] ) && count($lineInfo['args']) )
		{
			foreach ( $lineInfo['args'] as $argName => $argValue )
			{
				$btrace[] = "        arg[" . $argName . '] = <blockquote><pre><code>' . get_dump( $argValue ) . '</code></pre></blockquote>';
			}
		}

		$btrace[] = '';
	}

	$btrace = implode( "<br>", $btrace );

	return $btrace;
}
//------------------------------------------------------------------------
function rdebug( $var, $stackTrace = false )
{
	ob_start();

	var_dump( $var );

	$var_value = ob_get_clean();

	if ( $stackTrace == false )
	{
		$place = '(set stackTrace to true to see trace)';
	}
	else
	{
		$place = rtrace('rex');
	}

	?>
	<div style="align: top; background-color: orange; border-style: solid; border-color: black; border-width: 2px;">
		<div style="align: left; margin-left: 40px; border-width: 2px; border-style: solid; border-color: black;">
			<pre><code><?= $var_value ?></code></pre>
		</div>
		<div style="align: left; margin-left: 40px; border-width: 2px; border-style: solid; border-color: black;">
			<?= $place ?>
		</div>
	</div>
	<div style="align: top; background-color: green; ">
		&nbsp
	</div>
<?php
}
//------------------------------------------------------------------------
function memory_stats()
{
	if ( ! headers_sent() )
	{
		fb( number_format(memory_get_usage()), 'Mem Usage', FirePHP::INFO );
		fb( number_format(memory_get_peak_usage()), 'Mem Usage (Peak)', FirePHP::INFO );
	}
}
//------------------------------------------------------------------------
function requestIsAjax()
{
	if ( array_key_exists( 'HTTP_X_REQUESTED_WITH', $_SERVER ) )
	{
		return strtolower( $_SERVER['HTTP_X_REQUESTED_WITH'] ) == 'xmlhttprequest';
	}
	return false;
	
}
//------------------------------------------------------------------------
function console_log( $label, $var )
{
	echo 'console.log( "' . $label . ' = ' . addslashes( $var ) . '" );' . PHP_EOL;
}
