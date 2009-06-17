<?php
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
include( CORE_PATH . 'tgSimpleFramework.php' );

// application detection goes here
define( 'APP_FOLDER', 'application/' );

// this loads app configurations and
// loads app plugins and libraries
// and core libraries too
include( CORE_PATH . 'app_loader.php' );

content_buffer();

// vars is passed back through a callback from the tg_parse_url function
// we capture this in order to make it available to the controller that is included below
$vars = array();
$page = tg_parse_url( $vars );

require resolve_controller( $page );

end_buffer();

