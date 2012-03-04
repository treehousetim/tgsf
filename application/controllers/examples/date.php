<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

header( 'content-type: text/plain' );

echo date::currentDatetime();
echo "\n";
//echo tz_date( DT_FORMAT_SQL, time::currentTs() );
echo "\n";
echo date::tzDate( time::currentTs(), DT_FORMAT_SQL );
echo "\n";
