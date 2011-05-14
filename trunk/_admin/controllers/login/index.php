<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2010-2011 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// this happens automaticaly on all other pages due to the force_login plugin.
AUTH()->startSession();

$form =& load_form( 'login' );
$form->processor( URL( 'login' ) );
$formValid = true;

if ( POST()->dataPresent )
{
	$ds = clone POST();
	$form->ds( $ds );
	$form->validate();
	$formValid = $form->valid;
	
	if ( $formValid )
	{
		if ( AUTH()->login( $ds ) )
		{
			if ( $ds->isEmpty( 'redir' ) == false )
			{
				URL( urldecode( urldecode( $ds->redir ) ) )->notLocal()->redirect();
			}

			switch( AUTH()->user->login_role )
			{
			case roleCSR:
				URL( 'admin/csr' )->redirect();
				break;

			case roleADMIN:
			case roleSUPER:
				URL( 'admin' )->redirect();
				break;

			case roleMEMBER:
			default:
				URL( 'user/home' )->redirect();
				break;
			}
		}
		else
		{
			$formValid = false;
		}
	}
}

if ( REQUEST()->dataPresent && REQUEST()->exists( 'redir' ) )
{
	$form->ds->setVar( 'redir', REQUEST()->redir );
}
$formHtml = $form->render();

$windowTitle = 'Login';

$extraJs[] = js_path() . 'login-form.js';

include view( 'header' );
include view( 'login/index' );
include view( 'footer' );
