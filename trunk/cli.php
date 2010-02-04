<?php
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
define( 'TGSF_CLI', php_sapi_name() == 'cli' );

TGSF_CLI or die ( 'Restricted' );

define( 'BASEPATH', dirname( __FILE__ ) . '/' );

// attempt to change the directory to our currently executing front controller.
// this seems important for CLI scripts but it might not be.
chdir( BASEPATH );

define( 'CORE_PATH',		BASEPATH . 'tgsf_core/'			);
define( 'CORE_ASSET_PATH',	BASEPATH . 'tgsf_core_assets/'	);

// zend framework support
ini_set('include_path', ini_get( 'include_path' ) . PATH_SEPARATOR . CORE_PATH . '3rd_party' );

//------------------------------------------------------------------------
// a base class that is used in all core classes
require_once  CORE_PATH . 'libraries/tgsfBase.php';

try
{
	// the functions comprising the core of the framework
	// no initializing code is found in this file
	// no additional libraries are loaded in this file.
	require_once CORE_PATH . 'tgSimpleFramework.php';

	// tgsfCli is a datasource object descendant so we need to load this first
	load_library( 'tgsfDataSource', IS_CORE_LIB );

	// Needed for the app_detector_cli.php code
	load_library( 'tgsfCli', IS_CORE_LIB );

	// the file that determines which application we're loading - not modified for updates.
	require_once BASEPATH . 'app_detector_cli.php';

	// load the core libraries and whatnot
	require_once CORE_PATH . 'core-loader.php';

	$page = CLI()->controller;

	require resolve_cli_controller( $page );
}
catch ( Exception $e )
{
	
	echo $e->getMessage();
	show_error( 'An error occurred and was logged.', $e );
}