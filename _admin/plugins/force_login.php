<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2010-2011 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class plugin_forceLogin extends tgsfBase
{
	static public function init()
	{
		AUTH()->startSession();
		$instance = new plugin_forceLogin();
		tgsfEventFactory::actionHandler()
			->event( 'post_resolve_controller' )
			->func( 'exec' )
			->object( $instance )
			->attach();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function exec( $event )
	{
		$page = URL( $event->page );

		if (
			$page != (string)AUTH()->loginUrl &&
			$page != (string)URL( 'install' ) &&
			$page != (string)URL( '_minify' )
		)
		{
			AUTH()->requireLogin();
		}
	}
}