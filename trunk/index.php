<?php
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// this can be manually removed for production systems that are set up correctly.
if ( get_magic_quotes_gpc() == 1 )
{
	die( 'You must turn magic quotes off.<br><a href="http://us3.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc">Read More at http://us3.php.net/manual/en/info.configuration.php#ini.magic-quotes-gpc</a>' );
}

define( 'TGSF_CLI',			false							);
define( 'BASEPATH',			dirname(  __FILE__ ) . '/'		);
define( 'CORE_PATH',		BASEPATH . 'tgsf_core/'			);
define( 'CORE_ASSET_PATH',	BASEPATH . 'tgsf_core_assets/'	);

ini_set('include_path', ini_get( 'include_path' )

// zend framework support
. PATH_SEPARATOR . CORE_PATH . '3rd_party'

// PHP Secure Communications Library support
. PATH_SEPARATOR . CORE_PATH . '3rd_party/phpseclib0.2.1a' );

//------------------------------------------------------------------------
// a base class that is used in all core classes
include  CORE_PATH . 'libraries/tgsfBase.php';

try
{
	// the functions comprising the core of the framework
	// no initializing code is found in this file
	// no additional libraries are loaded in this file.
	include CORE_PATH . 'tgSimpleFramework.php';

	// load the url detection library so we can detect app before loading core.
	load_library( 'tgsfUrlDetection',	IS_CORE_LIB );

	// the file that determines which application we're loading - not modified for updates.
	require BASEPATH . 'app_detector.php';

	// not loaded in core-loader.php because of how CLI works
	load_library( 'tgsfDataSource', IS_CORE_LIB );

	// load the core libraries and whatnot
	include CORE_PATH . 'core-loader.php';

//	content_buffer();

	// page is a global variable that is used in other places.
	$page = tgsf_parse_url();

	require resolve_controller( $page );

	if ( config( 'debug_mode' ) )
	{
		memory_stats();
	}

//	end_buffer();
}
catch ( Exception $e )
{
	show_error( 'An error occurred when trying to view the page.  A site administrator has been notified of the problem.', $e );
}
