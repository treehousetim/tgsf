<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


echo config( 'doctype' );
echo '<head>';
content_type( config( 'content-type' ) );
html_title( $windowTitle . ' - ' . config( 'siteName' ) );
css_import(
	array(
		css_path() . 'print.css'
		), 'print' );

css_import(
	array(
		css_path( '/', IS_CORE_PATH ) . 'yuiZero.css',
		css_path() . 'site.css',
		css_path() . 'grids.css'
	), 'core' );

if ( isset( $extraCss ) && is_array( $extraCss ) )
{
	css_import( $extraCss, $page );
}

css_import_ie( css_path( '' ) . 'ie.css' );

js(
	array(
		jquery_path()				. 'jquery.js',
		jquery_path()				. 'jquery-ui.js',
		js_path( 'tgsf', IS_CORE_PATH )	. 'tgsf.js',
		js_path( 'tgsf', IS_CORE_PATH )	. 'url.js',
		//js_path()					. 'site.js',
		//jquery_path()				. 'newsticker.js',
		js_path( '', IS_CORE_PATH )	. 'utility.js'
	), 'core' );

if ( isset( $extraJs ) && is_array( $extraJs ) )
{
	js( $extraJs, $page );
}

favicon( image( 'favicon.png', 'image/png' ) );
if ( ! empty( $style ) )
{
	html_inline_style( $style );
}

js_output_url_func();
do_action( 'html_header' );
echo '</head>';
