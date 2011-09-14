<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//------------------------------------------------------------------------
// our recommendation is to store UTC dates in the database
// and transform those when you display them on pages
date_default_timezone_set( 'UTC' );

// this is for mysql
//query::factory()->static_query( 'SET time_zone = "+0:00";' )->exec();

//------------------------------------------------------------------------

// if you load the ZendFramework in tgsf_core/3rd_party/Zend use this
/*
require_once( 'Zend/Date.php' );
Zend_Date::setOptions( array( 'format_type' => 'php' ) );
Zend_Locale::disableCache( true );
*/

//------------------------------------------------------------------------

//you should set this to be the default for the timezone you want your web site to operate in

define( 'TZ_DEFAULT', 'America/Chicago' ); // CST
load_config( 'datetime', IS_CORE );
