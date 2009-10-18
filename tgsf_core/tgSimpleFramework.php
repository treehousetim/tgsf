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
define( 'IS_APP', false );
define( 'IS_CORE_TEMPLATE', true );
define( 'IS_APP_TEMPLATE', false );
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
function load_database_libraries()
{
	// enums for the database libraries
	load_library( 'db/enum',			IS_CORE_LIB );
	load_library( 'db/dbManager',		IS_CORE_LIB );
	load_library( 'db/dbSetup',			IS_CORE_LIB );
	load_library( 'db/queryJoin',		IS_CORE_LIB );
	load_library( 'db/query',			IS_CORE_LIB );
	load_library( 'db/foreignKey',		IS_CORE_LIB );
	load_library( 'db/field',			IS_CORE_LIB );
	load_library( 'db/dbIndex',			IS_CORE_LIB );
	load_library( 'db/table',			IS_CORE_LIB );
	load_library( 'db/model',			IS_CORE_LIB );
	load_library( 'db/dbDataSource',	IS_CORE_LIB );
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
	$config['host_www'] = false;
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
	global $config;
	$config['host_www'] = true;
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
		$page = tgsf_parse_url( $vars );

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
function url_vars( $varArray )
{
	$prefix		= config( 'get_string' );
	$separator	= config( 'get_separator');
	$equals		= config( 'get_equals' );

	foreach ( $varArray as $name => $value )
	{
		$vars[] = $name . $equals . $value;
	}
	return $prefix . implode( $separator, $vars );
}
//------------------------------------------------------------------------
// parse_url is a built in function, that's why this is named tgsf_parse_url
function tgsf_parse_url()
{
	$baseUrlPart = current_base_url_path();

	$page = empty( $_SERVER['REDIRECT_URL'] )?'':trim( $_SERVER['REDIRECT_URL'], '/' );
	$page = substr( $page, strlen( $baseUrlPart ) );

	$pieces = explode( '/_/', $page );
	$varPieces = '';
	if ( count( $pieces ) > 1 )
	{
		$page = $pieces[0];
		$varPieces = $pieces[1];
		tgsf_parse_url_vars( $varPieces );
	}
	$page = trim( $page, ' /' );
	
	if ( $page == '' )
	{
	    $page = 'home';
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

		$_GET['__tgsf_vars'] =& $vars;
		
		if ( $pieceCnt % 2 != 0  )
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
function resolve_controller( $page )
{
	do_action( 'pre_resolve_controller', $page );
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
			do_action( 'pre_404', $page );
			// we don't output 404 headers here so that the 404 controller can make choices of its own
			// it should output the 404 header.
			$out = controller( '404' );
			$out = do_filter( 'controller_404', $out, $page );
		}
	}

	$out = do_filter( 'post_resolve_controller', $out, $page );
	do_action( 'post_resolve_controller', $page, $out );
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
	if ( can_plugin() )
	{
		$url = do_filter( 'perm_redirect_url', $url );
	}
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
	if ( can_plugin() )
	{
		$url = do_filter( 'temp_redirect_url', $url );
	}
	
	if ( $local )
	{
		header( 'Location: ' . url( $url ) );
	}
	else
	{
		header( 'Location: ' . $url );
	}
	exit();
}
//------------------------------------------------------------------------
/**
* Generates an extremely secure password hash (one way) using a salt
* to prevent dictionary attacks against a comprimised database
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
	do_action( 'hash_password', $hashed, $salt, $clearText );

	// we should never be calling this before the plugin library has loaded
	$hashed = do_filter( 'hash_password', $hashed, $clearText, $salt );
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
	$file = clean_text( $file, '_', "." );
	$file = path( 'logs', IS_CORE_PATH ) . $file;
	
	$out = PHP_EOL . '------------------------------------------------------------------------' . PHP_EOL;
	$out .= date( 'Y/m/d H:i:s' ) . PHP_EOL;
	$out .= '----------------------' . PHP_EOL;
	$out .= $message . PHP_EOL;
	
	if ( can_plugin() )
	{
		$out = do_filter( 'general_log', $out, $message );
	}
	
	file_put_contents( $file, $out, FILE_APPEND );
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
// utility functions
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
* @param bool Should enum use the value for the defined value or use the given array key 
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
* if the passed argument is already an array then nothing is done.
* if the passed argument is not an array then an a
* @param Mixed The variable to test for arrayness
* @param Array The return variable 
*/
function arrayify( &$in, &$out )
{
	if ( ! is_array( $in ) )
	{
		$out = array();
		$out[] = $in;
	}
	else
	{
		$out = array();
		$out = $in;
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
function memory_stats()
{
	//echo number_format( memory_get_usage() ) . '<br>';
	//echo number_format( memory_get_peak_usage() ) . '<br><br>';

	fb( number_format(memory_get_usage()), 'Mem Usage', FirePHP::INFO );
	fb( number_format(memory_get_peak_usage()), 'Mem Usage (Peak)', FirePHP::INFO );
}