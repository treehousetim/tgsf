<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/* Time shorthands for time calcs */
enum( 'DT_TIME_', array(
		'MINUTE' => 60,
		'HOUR'   => 3600,
		'DAY'    => 86400,
		'WEEK'   => 604800
	));

// DT_FORMAT_SQL, DT_FORMAT_SQL_DATE, DT_FORMAT_SQL_TIME, DT_FORMAT_SQL_START, DT_FORMAT_SQL_END, DT_FORMAT_UI_SHORT, DT_FORMAT_UI_MED, DT_FORMAT_UI_LONG
// DT_FORMAT_UI_DATE, DT_FORMAT_UI_DATE_DAY, DT_FORMAT_UI_TIME, DT_FORMAT_ACH_DATE, DT_FORMAT_ACH_DATE_SHORT, DT_FORMAT_ACH_TIME
/* Date formats */
enum( 'DT_FORMAT_', array(
		'SQL'       => 'Y-m-d H:i:s',
		'SQL_DATE'  => 'Y-m-d',
		'SQL_TIME'  => 'H:i:s',
		'SQL_START' => 'Y-m-d 00:00:00',
		'SQL_END'   => 'Y-m-d 23:59:59',
		'UI_SHORT'  => 'm/d/Y g:i A',
		'UI_MED'	=> 'm/d/Y g:i:s A T',
		'UI_LONG'   => 'D, m/d/Y g:i:s A T (P)',
		'UI_DATE'   => 'm/d/Y',
		'UI_DATE_DAY'=> 'D, m/d/Y',
		'UI_TIME'   => 'g:i A',
		'ACH_DATE'  => 'Ymd',
		'ACH_DATE_SHORT'  => 'ymd',
		'ACH_TIME'  => 'Hi',
		'TEXT_TIMESTAMP'	=> 'YmdHis'
	));
