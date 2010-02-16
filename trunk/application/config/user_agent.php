<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// User Agent configuration goes here.

$config['itouch'] = false;
$config['iphone'] = false;

if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) )
{
	$config['itouch'] = preg_match('/ipod|iphone/sim', $_SERVER['HTTP_USER_AGENT'] );
	$config['iphone'] = preg_match('/iphone/sim', $_SERVER['HTTP_USER_AGENT'] );

}

