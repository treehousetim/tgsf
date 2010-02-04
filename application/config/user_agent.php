<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// User Agent configuration goes here.

$itouch = preg_match('/ipod|iphone/sim', $_SERVER['HTTP_USER_AGENT'] );
$iphone = preg_match('/iphone/sim', $_SERVER['HTTP_USER_AGENT'] );

$config['itouch'] = false;//$itouch;
$config['iphone'] = false;//$iphone;