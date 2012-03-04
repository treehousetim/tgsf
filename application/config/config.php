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

switch ( $config['server_id'] )
{
case '2FE2E706A46369037D7F84A40E30A7AC':
	load_config( 'server_id/example' );
	break;

default:
	echo $config['server_id'] . ' is not registered.';
	exit();
}

load_config( 'datetime' );
load_config( 'constants' );