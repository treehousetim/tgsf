<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// load that db early, LOAD IT! ANTICI
load_library( 'tgDB', IS_CORE_LIB );

// might want these for detecting the application.
load_library( 'tgsfUrlDetection', IS_CORE_LIB );