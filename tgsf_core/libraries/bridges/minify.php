<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

tgsfEventFactory::handler()->event( 'pre_resolve_controller' )->func( 'tgsf_minify_bridge' )->attach();

function tgsf_minify_bridge( $event )
{
	$page = $event->ds->page;

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
		
		require path( '3rd_party/min', IS_CORE_PATH ) . 'index.php';
		exit();
	}

}