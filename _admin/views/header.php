<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" /><?

html_title( $windowTitle );
if ( ! empty( $metaDescription ) )
{
	meta_description( $metaDescription );
}

css_import(
	array(
		css_path( '/', IS_CORE_PATH ) . 'yuiZero.css',
		css_path() . 'structure.css',
		css_path() . 'nav.css',
		css_path() . 'site.css',
		css_path() . 'grids.css',
		
		
	), 'core' );

css_import(
	array(
		css_path() . 'print.css'
		), 'print' );

if ( isset( $extraCss ) && is_array( $extraCss ) )
{
	css_import( $extraCss, $page );
}

css_import_ie( css_path( '' ) . 'ie.css' );

js(
	array(
		jquery_path()					. 'jquery.js',
		jquery_path()					. 'jquery-ui.js',
		js_path( 'tgsf', IS_CORE_PATH )	. 'tgsf.js',
		js_path( 'tgsf', IS_CORE_PATH )	. 'url.js',
		js_path()						. 'site.js',
		js_path( '', IS_CORE_PATH )		. 'utility.js'
	), 'core' );

if ( isset( $extraJs ) && is_array( $extraJs ) )
{
	js( $extraJs, $page );
}

favicon( image_url( 'favicon.ico' ), 'image/x-icon' );

if ( ! empty( $style ) )
{
	html_inline_style( $style );
}

js_output_url_func();
do_action( 'html_header' );
$nav_links = config( 'nav_links' );
do_filter( 'nav_links', $nav_links );
?>
</head>
<body>
	<div id="head-bar">
		<div id="dashboard-title">tgsf Admin Dashboard</div>
		<div id="user-info">Logout</div>
		<div id="version"><?=TGSF_VERSION; ?></div>
		<i class="clearboth"></i>
	</div>
	<div id="left-nav">
		<?= urlMenu( $nav_links ); ?>
	</div>
	<div id="page_body">