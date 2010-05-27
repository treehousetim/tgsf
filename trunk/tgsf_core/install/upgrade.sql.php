<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

switch ( TGSF_VERSION )
{
case '0.9.2':
	$tableName = coreTable( 'user_login' );
	$sql[] = "ALTER TABLE $tableName add user_login_bio text";
	
case '0.9.3':
	$tableName = coreTable( 'registry' );
	$sql[] = "ALTER TABLE $tableName add reg_long_desc text";
}