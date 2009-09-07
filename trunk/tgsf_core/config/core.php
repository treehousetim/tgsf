<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// TODO: Make this file obsolete - it may already be and if it is, remove it.

//$config['base_url'] = current_base_url();
//$config['base_url']	= 'http://' . $host . '/';

//------------------------------------------------------------------------
// paths
//------------------------------------------------------------------------
$config['base_path']		= BASEPATH;
$config['app_path']			= BASEPATH . APP_FOLDER;

//$config['plugin_path']		= $config['app_path'] . 'plugins/';
//$config['controller_path']	= $config['app_path'] . 'controllers/';
//$config['view_path']		= $config['app_path'] . 'views/';

$config['asset_path']		= $config['app_path'] . 'assets/';
$config['image_path']		= $config['asset_path'] . 'images/';
$config['font_path']		= $config['asset_path'] . 'fonts/';
$config['css_path']			= $config['asset_path'] . 'css/';

$config['js_path']			= $config['asset_path'] . 'js/debug/';
$config['css_path']			= $config['asset_path'] . 'css/';


$config['core_path'] = CORE_PATH;
$config['system_path'] = $config['core_path'] . 'system/';
$config['core_plugin_path'] = $config['core_path'] . 'plugins/';
$config['class_path'] = $config['system_path'] . 'classes/';
//------------------------------------------------------------------------
// URL's
//------------------------------------------------------------------------
//define( 'BASE_URL', $config['base_url'] );

// default install locations for application files.  This should not be changed here, but it can be overridden in
// an application's config file.
$config['app_url'] = BASE_URL . APP_FOLDER;
$config['asset_url'] = $config['base_url'] . 'assets/';
$config['js_url'] = $config['asset_url'] . 'js/debug/';
$config['css_url'] = $config['asset_url'] . 'css/';
$config['image_url'] = $config['asset_url'] . 'images/';
