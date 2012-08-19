<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// the lifetime between requests, in seconds.
// this is handled on the server, not through cookies to prevent
// discrepencies between server and client from being a problem
$config['session/lifetime']				= 900;							// 900 = 15 minutes
$config['session/page-cache-expire']	= 0;
$config['session/httponly']				= true;							// helps prevent xss by preventing javascript access to the session cookie (on SOME browsers, not all)
$config['session/hash_function']		= 1;							// sha-1
$config['session/hash_bits']			= 6;							// full resolution of hash for better security

// host should be set to 'www.' if you are forcing www urls
$host = '';

$config['session/cookie_domain']		= $host . current_domain();			// the current domain and all sub domains
$config['session/cookie_path']			= '/' . current_base_url_path();	// automatically restricts cookies for sessions to your install of tgsf
$config['session/cache_limiter']		= 'nocache';						// logged in pages need to not be cached so that someone can't click the back button
$config['session/cookie_secure']		= false;
