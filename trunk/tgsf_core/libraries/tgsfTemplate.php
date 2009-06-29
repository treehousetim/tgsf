<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


function js( $jsFiles, $group = null )
{
	$loopFiles = array();
	$groupFiles = array();
	$files = array();
	
	arrayify( $jsFiles, $loopFiles );
	
	foreach ( $loopFiles as $jsFile )
	{
		if ( ! is_local( $jsFile ) )
		{
			echo "\t" . '<script type="text/javascript" src="' . $jsfile . '"></script>' . "\n";
		}
		else
		{
			if ( ! file_exists( $jsFile )  )
			{
				throw new tgsfException( 'File Does Not Exist when trying to create a script tag: ' . $jsFile );
			}
			$groupFiles[] = "'{$jsFile}'";
		}
	}

	if ( count( $groupFiles ) > 0 )
	{
		if ( is_null( $group ) )
		{
			$group = md5( implode( '', $groupFiles ) );
		}

		$contents = implode( ",\n", $groupFiles );
		$content = "<?php return array( 'js_$group' => array( $contents ) );";

		file_put_contents( path( 'assets/minify_groups', IS_CORE_PATH ) . 'js_' . $group . PHP, $content );
		echo "\t" . '<script type="text/javascript" src="' .url_path( '3rd_party/min', IS_CORE_PATH ) . '?g=js_' . $group . '"></script>' . "\n";

	}
}

//------------------------------------------------------------------------

function css( $file, $local = true )
{
	$prefix = '';
	$suffix = '';
	if ( $local )
	{
		$prefix = config( 'css_url' );
		$suffix = '.css';
	}
	echo "\t" . '<link type="text/css" href="' . $prefix . $file . $suffix . '" rel="Stylesheet" />	' . "\n";
}

//------------------------------------------------------------------------
/**
* Outputs one or more style tags with an @import rule
* This function also integrates with the bundled minify to create groups of minified CSS
* in which case it outputs a single style tag with an @import rule pointing to /tgsf_core/3rd_party/min/?g=example
* @param Mixed Either a string or an array of css files to include.
* If the name does not start with http:// or https:// then it is considered local and will be put through minify
* @param The name of the minify group - files will not be minified unless the group name is provided.
* and is 
*/
function css_import( $cssFiles, $group = null )
{
	$loopFiles = array();
	$groupFiles = array();
	$files = array();
	
	arrayify( $cssFiles, $loopFiles );
	
	foreach ( $loopFiles as $cssFile )
	{
		if ( ! is_local( $cssFile ) )
		{
			echo "\t" . '<style type="text/css">@import url(' . $cssFile . ');</style>' . "\n";
		}
		else
		{	
			if ( ! file_exists( $cssFile )  )
			{
				throw new tgsfException( 'File Does Not Exist when trying to create an imported CSS tag: ' . $cssFile );
			}
			$groupFiles[] = "'{$cssFile}'";
		}
	}

	if ( count( $groupFiles ) > 0 )
	{
		if ( is_null( $group ) )
		{
			$group = md5( implode( '', $groupFiles ) );
		}

		$contents = implode( ",\n", $groupFiles );
		$content = "<?php return array( '$group' => array( $contents ) );";
		//echo path( 'assets/minify_groups', IS_CORE_PATH ) . $group . PHP;
		
		file_put_contents( path( 'assets/minify_groups', IS_CORE_PATH ) . $group . PHP, $content );
		echo "\t" . '<style type="text/css">@import url(' . url_path( '3rd_party/min', IS_CORE_PATH ) . '?g=' . $group . ');</style>' . "\n";
	}
}
//------------------------------------------------------------------------
function css_import_ie( $file, $if = 'if IE' )
{
	echo '<!--[' . $if . ']>';
	css_import( $file );
	echo '<![endif]-->';
}
//------------------------------------------------------------------------
function output_css_properties( $array )
{
	foreach ( $array as $prop => $value )
	{
		echo $prop . ': ' . $value . '; ';
	}
}
//------------------------------------------------------------------------
function js_output_url_func()
{
	?><script type="text/javascript">function url( url ) { url=url.trim();<?php if ( defined( 'tgTrailingSlash' ) && tgTrailingSlash === true ) { echo "url=url+'/';"; }; ?> if(url=='/'){url=''};return '<?php echo current_base_url(); ?>' + url; };</script><?php 
}
//------------------------------------------------------------------------
/**
* This loads the error controller and then exits script execution.
* @var String The error message to display.
*/
function show_error( $message )
{
	global $page;
	require_once controller( 'error' );
	exit();
}
//------------------------------------------------------------------------
// HTML Output functions
//------------------------------------------------------------------------
function favicon( $url, $type = 'image/jpeg' )
{
	?><link rel="icon" type="<?php echo $type; ?>" href="<?php echo $url; ?>"><?php
}
//------------------------------------------------------------------------
function html_inline_style( $content )
{
	?><style type="text/css"><?php echo $content; ?></style><?php
}
//------------------------------------------------------------------------
function html_title( $title )
{
	?><title><?php echo $title; ?></title><?php
}
//------------------------------------------------------------------------
function content_type( $type )
{
	?><meta http-equiv="Content-Type" content="<?php echo $type; ?>"><?php
}

