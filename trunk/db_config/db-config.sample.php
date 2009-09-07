<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// Using root database configurations is the preferred method of providing
// database support for your app.  Using root configs allows the database to connect
// super-early in the loading process and enhances the types of plugins you can use in the root
// context.  Only if you need per-application database configurations should you use
// the application/config/db.php file.

// rename this file to db-config.php and change it for your setup.

// standard and typical way of configuring a database.
// this is user,pass,database name.
// it is set up in the database manager using the 'default' logical connection name
// 99% of simple web apps will only need a single setup and this is the preferred form.
// this would be used like this...
// $q = new query();
dbm()->useSetup( new dbSetup( 'username', 'password', 'example-database' ) );

//------------------------------------------------------------------------
// user,pass,database name,server type,server host,server port
// notice it is added (above it is "useSetup")
// and is added using the logical name of data2
// which would be used like this...
// $q = new query( 'data2' );
$customPort = 42;
dbm()->addSetup(
	new dbSetup(
		'username',
		'password',
		'example-database',
		'mysql',
		'database2.example.com',
		$customPort ), 'data2' );
