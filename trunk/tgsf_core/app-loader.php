<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

define( 'APP_PATH', BASEPATH . APP_FOLDER );

// the early first config file that is loaded.
// use this for setting up url determining config values used in the core config
load_config( 'first' );

// load order is important - config first, then get plugins as soon as possible
load_config( 'config' ); // the main config
load_config( 'db' );

if ( $config['host_www'] === false )
{
	force_no_www();
}
else
{
	force_www();
}

if ( $config['force_trailing_slash'] === true )
{
	force_trailing_slash();
}

load_config( 'plugins' ); // where to specify which plugin files to load
load_plugins();

do_action( 'pre_system' );
do_action( 'plugins_loaded' );

load_config( 'user_agent' ); // where to put user agent detection code

do_action( 'load_library_config' );
load_config( 'libraries' );

do_action( 'core_load_complete' );
do_action( 'app_loaded' );