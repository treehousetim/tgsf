<?php
include( 'sync-core.php' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

define( 'BASEPATH',			dirname( __FILE__ ) . '/'		);
define( 'CORE_PATH',		BASEPATH . 'tgsf_core/'			);
define( 'CORE_ASSET_PATH',	BASEPATH . 'tgsf_core_assets/'	);

// the functions comprising the core of the framework
// no initializing code is found in this file
// no additional libraries are loaded in this file.
include CORE_PATH . 'tgSimpleFramework.php';

// contains some important functions that are used in core-loader.php
include CORE_PATH . 'tgUtilityFunctions.php';

// this variable controls whether or not we load a database config file in /db_config
// this is useful for getting the database connected as early as possible and
// is the recommended way of configuring a database.
// true = load /db_config/db-config.php
// false = do nothing
$useRootDbConfig = true;

// this variable controls whether or not we load the database config file
// that is located in the applications /config folder - typically just /application/config/db.php
// true = load application/config/db.php
// false = do nothing
$useAppDbConfig = false;

// if both root and app db config variables are set to false, database functionality is disabled entirely
// and will make it so that the database libraries aren't loaded at all (saving load time)

// useForms will load the form library if it's set to true
$useForms = true;
// you should copy the template libraries from
// tgsf_core/libraries/templates/form/ to your application/libraries/templates/form folder
// and then set this variable to true.  This way, future core updates won't change the design
// of your forms.
$useAppFormTemplates = false;

// load the core libraries and whatnot
include CORE_PATH . 'core-loader.php';

// application detection goes here
// which means that the app folder can be set using
// any means you need - i.e. url inspection, etc.
define( 'APP_FOLDER', 'application/' );

// this loads app configurations and
// loads app plugins and libraries
// and core libraries too
include( CORE_PATH . 'app-loader.php' );

try
{
	content_buffer();

	// vars is passed back through a callback from the tg_parse_url function
	// we capture this in order to make it available to the controller that is included below
	$vars = array();
	$page = tg_parse_url( $vars );
	
	require resolve_controller( $page );
}
/*

catch ( tgsfException $e )
{
	show_error( (string)$e );
}
catch( ErrorException $e )
{
	show_error( $e->getMessage() . "<br>\n" . nl2br( $e->getTraceAsString() ) );
}
*/
catch ( Exception $e )
{
	show_error( $e->getMessage() . "<br>\n" . nl2br( $e->getTraceAsString() ) );
}

end_buffer();
