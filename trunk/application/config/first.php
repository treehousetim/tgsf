<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


// this file is used for config vars that modify the way that urls are determined.
// It is loaded early.
// Immediately after this file is loaded, the tgsf_core/config/core.php config file is loaded.

$config['host_www'] = false; // true/false to force www or no www on urls - causes a re-direct if opposite condition is discovered.
$config['force_trailing_slash'] = true;

$config['live_host'] = 'example.com';