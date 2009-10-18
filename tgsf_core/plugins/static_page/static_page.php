<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
add_action( 'static_page_init', 'static_page_setup' );
function static_page_setup( $file )
{
	$class = new staticPage();
	add_action( 'pre_404', array( &$class, 'pre404' ) );
}


class staticPage
{
	function __construct()
	{
		$this->model = load_cloned_object( path( 'plugins/static_page', IS_CORE ), 'model' );
	}
	function pre404( $page )
	{
		$row = $this->model->getPage( $page );
		
		if ( $row === false )
		{
			// we have no page for this url, return and let the core handle the 404
			return;
		}
		
		if ( $row->page_template == '' || file_exists( view( $row->page_template ) ) === false )
		{
			include view( 'static_page' );
			exit();
		}
		else
		{
			include view( $row->page_template );
			exit();
		}
	}
}

