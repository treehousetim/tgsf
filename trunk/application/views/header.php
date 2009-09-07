<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
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
		css_path( '/', IS_CORE_PATH ) . 'yuiZero.css',
		css_path() . 'site.css'
	), 'core' );

css_import_ie( css_path( '' ) . 'ie.css' );

if ( isset( $extraCss ) && is_array( $extraCss ) )
{
	css_import( $importArray, $page );
}

js(
	array(
		jquery_path()				. 'jquery.js',
		jquery_path()				. 'jquery-ui.js',
		//js_path()					. 'site.js',
		jquery_path()				. 'newsticker.js',
		js_path( '', IS_CORE_PATH )	. 'utility.js'
	), 'core' );

favicon( image( 'favicon.png', 'image/png' ) );
html_inline_style( $style );
js_output_url_func();
do_action( 'html_header' );
echo '</head>';
