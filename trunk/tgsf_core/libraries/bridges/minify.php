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

		if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) == false )
		{
			$_SERVER['HTTP_USER_AGENT'] = '';
		}

		require path( '3rd_party/min', IS_CORE_PATH ) . 'index.php';
		exit();
	}
}
