<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


add_filter( 'controller_404', 'static_page_filter' );
add_action( 'plugin_config', static_page_config )

function static_page_filter( $controller, $params )
{
	list( $page ) = $params;
	$pageFile = config( 'page_path' ) . $page . EXT;
	
	if ( file_exists( $pageFile ) )
	{
		$controller = controller( 'static_page' );
	}
	
	return $controller;
}

//------------------------------------------------------------------------

function static_page_config()
{
	global $config;
	$config['page_path'] = $config['view_path'] . 'pages/';
}