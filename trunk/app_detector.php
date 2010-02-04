<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// this file is not overwritten in upgrade distributions.
// application detection goes here
// which means that the app folder can be set using
// any means you need - i.e. url inspection, etc.
// all functions in the url detection core lib are loaded at this point.

// if you need to use a database for application detection
// you'll need this:
// load_database_libraries();

// then you'll need to set up a connection
// dbm()->useSetup( new dbSetup( 'user', 'password', 'db_name' ) );

define( 'APP_FOLDER', 'application/' );
