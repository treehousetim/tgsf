<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


// User Agent configuration goes here.

$config['itouch'] = false;
$config['iphone'] = false;
$config['ipod'] = false;
$config['ipad'] = false;

if ( array_key_exists( 'HTTP_USER_AGENT', $_SERVER ) )
{
	$iPod = stripos($_SERVER['HTTP_USER_AGENT'],"iPod");
	$iPhone = stripos($_SERVER['HTTP_USER_AGENT'],"iPhone");
	$iPad = stripos($_SERVER['HTTP_USER_AGENT'],"iPad");

	$iTouch = $iPod || $iPhone || $iPad;

	$config['itouch'] = $iTouch;
	$config['iphone'] = $iPhone;
	$config['ipod'] = $iPod;
	$config['ipad'] = $iPad;
}

