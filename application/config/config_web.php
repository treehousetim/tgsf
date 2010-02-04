<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// this file only loads for web based requests 
// this is not loaded for CLI

// before proceeding, we make sure that we shouldn't do a perm redirect for adding a trailing slash
// or to force no www on the hostname - force_www(); is the other option
force_no_www();
force_trailing_slash();