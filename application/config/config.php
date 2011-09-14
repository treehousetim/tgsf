<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$config['debug_mode']		= true;

$config['live_host'] = 'example.com';
$config['get_string']		= '/_/';
$config['get_separator']	= '/';
$config['get_equals']		= '/';

//------------------------------------------------------------------------

$config['maintenanceMode'] = false;
$config['maintenanceModeVar'] = ''; // customize this
$config['maintenanceModeVarValue'] = ''; // customize this

$config['css_minify'] = true;
$config['js_minify'] = true;

// site
$config['siteName'] = 'TGSF';

//output
$config['doctype'] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml">';

$config['content-type'] = 'text/html; charset=utf-8';

$config['server_id'] = current_server_id();

//------------------------------------------------------------------------
// This code is not necessary, but works great for development teams
// where each team member can have their own setup
// the hash values are created using the current_server_id() function
// from the file located in tgsf_core/tgSimpleFramework.php
//------------------------------------------------------------------------
switch ( $config['server_id'] )
{
// example server id - http://localhost/tgsf
case '2FE2E706A46369037D7F84A40E30A7AC':

// some configs work for more than one server id - this happens to be for CLI
// on Tim's development machine
case '72A14737F8C84D562C5BB0BBBF117D76':
	load_config( 'server_id/example' ); // not distributed
	break;

case 'EB8AE43870617E9449060FB0F4A5AF93':
	load_config( 'server_id/server1' );
	break;

//another server
case 'AB93040B5D3662A9AB70222DB35C2982':
	load_config( 'server_id/server2' );
	break;

default:
	echo PHP_EOL . 'Update your config with your server ID: ' . $config['server_id'];
	if ( TGSF_CLI )
	{
		echo PHP_EOL;
		show_current_server_id_parts();
		echo PHP_EOL . PHP_EOL;
	}
	exit();
}


load_config( 'datetime' );
load_config( 'constants' );