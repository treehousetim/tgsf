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
		
css_import( array(
	'system/yuiZero',
	'site/site',
	'site/topnav',
	'site/forms'
	), true, 'core-css' );

css_import( url( 'css_grid' ), false );
css_import_ie( 'site/ie' );

if ( isset( $extraCss ) && is_array( $extraCss ) )
{
	foreach ( $extraCss as $cssFile )
	{
		$importArray[] = 'site/' . $cssFile;
	}
	css_import( $importArray, true, 'css_' . $page );
}

js( array(
	'jquery.js',
	'jquery-ui.js',
	'site.js',
	'newsticker.js',
	'tppajax.js',
	'utility.js'
	), true, 'core' );

favicon( image( 'favicon.png', 'image/png' ) );
html_inline_style( $style );
js_output_url_func();
do_action( 'html_header' );
echo '</head>';
