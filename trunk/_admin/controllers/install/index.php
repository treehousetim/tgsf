<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2011 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

load_library( 'version/tgsfVersion', IS_CORE );
$formHtml = '';
$blocks = array();

$form = load_form( 'new_install' );
$form->processor( URL( 'install' ) );

if ( GET()->exists( 'new' ) && GET()->new == 1 )
{
	$form->ds( clone GET() );
	$formHtml = $form->render();
}

if ( POST()->dataPresent )
{
	$form->ds( clone POST() );
	$form->validate();
	if ( $form->valid )
	{
		$user_loginModel = load_model( 'user_login', IS_CORE );
		$baseInstall = load_install_file( 'v093', IS_CORE );
		$blocks[] =& $baseInstall;
		$user_loginModel->adminInstallUser( $form->ds, $baseInstall );
	}
	else
	{
		$formHtml = $form->render();
	}
}

$installMessage = '';

foreach ( $blocks as $block )
{
	//echo $block->getBlockDescriptions();
	
	$installMessage .= $block->exec()->getMessages();
}

$windowTitle = 'tgsf Installer';
include view( 'header' );
include view( 'install/index' );
include view( 'footer' );