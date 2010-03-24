<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// example server 2
load_database_libraries();

dbm()->useSetup( new dbSetup( 'username', 'password', 'example-database' ) );
dbm()->addSetup( new dbSetup( 'username', 'password', 'database-2' ), 'database2' );