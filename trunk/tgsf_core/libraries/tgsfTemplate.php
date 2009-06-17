<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


function js( $filename, $local = true, $group = null )
{
	$prefix = '';
	if ( $local )
	{
		$prefix = config( 'js_url' );
		$filePrefix = config( 'js_path' );
	}
	
	if ( ! is_array( $filename ) )
	{
		$files[] = $filename;
	}
	else
	{
		$files = $filename;
	}
	
	if ( is_null( $group ) )
	{
		foreach ( $files as $file )
		{
			echo "\t" . '<script type="text/javascript" src="' . $prefix . $file . '"></script>' . "\n";
		}
	}
	else
	{
		foreach ( $files as $file )
		{
			$arrayContents[] = "'" . $filePrefix . $file . "'";
		}
		$contents = implode( ",\n", $arrayContents );
		$content = <<<EOC
<?php
		return array( '$group' => array( $contents ) );
EOC;
		file_put_contents( path( 'assets', IS_CORE_PATH ) . 'minify_groups/' . $group . '.php', $content );
		echo "\t" . '<script type="text/javascript" src="' . current_base_url() . 'min/?g=' . $group . '"></script>' . "\n";
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

function css_import( $filename, $local = true, $group = null )
{
	$prefix = '';
	$suffix = '';
	if ( $local )
	{
		$prefix = config( 'css_url' );
		$filePrefix = config( 'css_path' );
		$suffix = '.css';
	}
	
	if ( ! is_array( $filename ) )
	{
		$files[] = $filename;
	}
	else
	{
		$files = $filename;
	}
	
	if ( is_null( $group ) )
	{
		foreach ( $files as $file )
		{
			echo "\t" . '<style type="text/css">@import url(' . $prefix . $file . $suffix . ');</style>' . "\n";
		}
	}
	else
	{
		foreach ( $files as $file )
		{
			$arrayContents[] = "'" . $filePrefix . $file . $suffix . "'";
		}
		$contents = implode( ",\n", $arrayContents );
		$content = <<<EOC
<?php
		return array( '$group' => array( $contents ) );
EOC;
		file_put_contents( path( 'assets', IS_CORE_PATH ) . 'minify_groups/' . $group . '.php', $content );
		echo "\t" . '<style type="text/css">@import url(' . config( 'base_url' ) . 'min/?g=' . $group . ');</style>' . "\n";
					
//		echo "\t" . '<script type="text/javascript" src="' . config( 'base_url' ) . 'min/?g=' . $group . '"></script>' . "\n";
	}
}

//------------------------------------------------------------------------

function css_import_ie( $file, $local = true )
{
	echo '<!--[if IE]>';
	css_import( $file, $local );
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