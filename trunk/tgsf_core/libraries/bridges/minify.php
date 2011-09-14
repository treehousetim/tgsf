<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

tgsfEventFactory::actionHandler()->event( 'pre_resolve_controller' )->func( 'tgsf_minify_bridge' )->attach();

function tgsf_minify_bridge( $event )
{
	$page = $event->page;

	if ( $page == '_minify' )
	{
		chdir( path( '3rd_party/min', IS_CORE_PATH ) );
		/*
		// this is a super simple minify controller
		define('MINIFY_MIN_DIR', path( '3rd_party/min', IS_CORE_PATH ) );
		require MINIFY_MIN_DIR . '/config.php';
		set_include_path( $min_libPath . PATH_SEPARATOR . get_include_path() );
		require 'Minify.php';

		$min_serveOptions['minApp']['groups'] = (require MINIFY_MIN_DIR . '/groupsConfig.php');

		if (  isset($_GET['g']))
		{
	    	// serve!
	    	Minify::serve('MinApp', $min_serveOptions);
		}
		*/
		if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) == false )
		{
			$_SERVER['HTTP_USER_AGENT'] = '';
		}
		//$min_serveOptions['debug'] = starts_with( GET()->g, 'js' ) && config( 'debug_mode' );
		require path( '3rd_party/min', IS_CORE_PATH ) . 'index.php';
		exit();
	}
}
