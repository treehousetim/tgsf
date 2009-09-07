<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
/*
this file loads core libraries.  It is loaded before the application
is determined. which means that the application does not have any say in
the decision of what libraries to load.

You do have some control over the loading of database libraries using the
two variables $useRootDbConfig and $useAppDbConfig in the index.php
front controller.  Set both to false to prevent any database libraries
from loading.

Furthermore, if you modify this file, it will only get overwritten
the next time you upgrade. If you need or want the ability to have
your application decide what libraries to load please submit or comment
on a previous feature request ticket here:
http://code.google.com/p/tgsf/issues/
*/
//------------------------------------------------------------------------

// a base class that is used in all core classes
load_library( 'tgsfBase',			IS_CORE_LIB );
// needed to determine if we're loading a live site.
load_library( 'tgsfUrlDetection',	IS_CORE_LIB );

//------------------------------------------------------------------------
// datasources
//------------------------------------------------------------------------
load_library( 'tgsfDataSource',		IS_CORE_LIB );
load_library( 'tgsfPost',			IS_CORE_LIB );

//------------------------------------------------------------------------
// only load the database libraries and config if we're actually using it
//------------------------------------------------------------------------

if ( $useRootDbConfig === true || $useAppDbConfig === true )
{
	//------------------------------------------------------------------------
	// load that db early, LOAD IT! THAT'S IT - WORK IT, YOU'VE GOT IT!
	//------------------------------------------------------------------------
	// enums for the database libraries
	load_library( 'db/enum',			IS_CORE_LIB );
	load_library( 'db/dbManager',		IS_CORE_LIB );
	load_library( 'db/dbSetup',			IS_CORE_LIB );
	load_library( 'db/queryJoin',		IS_CORE_LIB );
	load_library( 'db/query',			IS_CORE_LIB );
	load_library( 'db/foreignKey',		IS_CORE_LIB );
	load_library( 'db/field',			IS_CORE_LIB );
	load_library( 'db/dbIndex',			IS_CORE_LIB );
	load_library( 'db/table',			IS_CORE_LIB );
	load_library( 'db/model',			IS_CORE_LIB );
	load_library( 'db/dbDataSource',	IS_CORE_LIB );
}

if ( $useRootDbConfig === true )
{
	include BASEPATH . 'db_config/db-config.php';
}

//------------------------------------------------------------------------
// form library
//------------------------------------------------------------------------
if ( $useForms === true )
{
	load_library( 'tgForm',				IS_CORE_LIB );
	load_library( 'tgFormField',		IS_CORE_LIB );
}

//------------------------------------------------------------------------
// Misc Libraries
//------------------------------------------------------------------------

load_library( 'tgsfTemplate',		IS_CORE_LIB );

//------------------------------------------------------------------------
// Plugin Library
//------------------------------------------------------------------------
load_library( 'tgPlugin',			IS_CORE_LIB ); // the core class the plugin api functions use
load_library( 'tgsfPlugin',			IS_CORE_LIB ); // the plugin api functions
// 
//------------------------------------------------------------------------
// load the core plugin config
//------------------------------------------------------------------------
load_config( 'plugins',				IS_CORE_CONFIG );

// this will only load the plugins configured in the core plugins config file.
// even though we load plugins again in the app-loader, we're using require_once to do so
// which means that there will be no duplicate loading going on.
load_plugins();

//------------------------------------------------------------------------
// The core config
//------------------------------------------------------------------------
//load_config( 'core',				IS_CORE_CONFIG );
