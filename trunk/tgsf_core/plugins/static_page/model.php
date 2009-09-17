<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class staticPageModel extends model
{
	function __construct()
	{
		parent::__construct( 'static_page_pages' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function pageExists( $slug )
	{
		$q = new query();
		$q->count('page_slug' )->from( 'static_page_pages' )->where( 'page_slug=:page_slug' );
		$q->bindValue( 'page_slug', $slug, ptSTR );
		return $q->exec()->fetchColumn(0) > 0;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getPage( $slug )
	{
		if ( $this->pageExists( $slug ) === false )
		{
			return false;
		}
		else
		{
			$q = new query();
			$q->select()->from( 'static_page_pages' )->where( 'page_slug=:page_slug' );
			$q->bindValue( 'page_slug', $slug, ptSTR );
			try
			{
				$row = $q->exec()->fetch();
			}
			catch( Exception $e )
			{
				log_exception( $e );
				show_error( 'An error occurred while loading this page.  A site administrator has been notified with the details.  Please try again later.' );
			}
		}
		return $row;
	}
}
