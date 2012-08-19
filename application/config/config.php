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

if ( array_key_exists( 'SERVER_NAME', $_SERVER ) )
{
	$config['current_http_host'] = $_SERVER['SERVER_NAME'];
}
else
{
	$config['current_http_host'] = 'localhost';
}

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

load_config( 'server_id/local' );

load_config( 'datetime' );
load_config( 'constants' );