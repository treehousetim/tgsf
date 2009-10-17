<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
$config['debug_mode']		= true;

$config['live_host'] = 'example.com';
$config['get_string']		= '/_/';
$config['get_separator']	= '&amp;';
$config['get_equals']		= '/';

// before proceeding, we make sure that we shouldn't do a perm redirect for adding a trailing slash
// or to force no www on the hostname - force_www(); is the other option
force_no_www();
force_trailing_slash();

//------------------------------------------------------------------------

$config['maintenanceMode'] = false;
$config['maintenanceModeVar'] = ''; // customize this
$config['maintenanceModeVarValue'] = ''; // customize this

$config['css_minify'] = true;
$config['js_minify'] = true;

// site
$config['siteName'] = 'TGSF';

//output
$config['doctype'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd"><html lang="en">';
//$config['doctype'] = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd"><html lang="en">';

$config['content-type'] = 'text/html; charset=iso-8859-1';
