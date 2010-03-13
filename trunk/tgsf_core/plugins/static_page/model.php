<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class staticPageModel extends model
{
	function __construct()
	{
		parent::__construct( 'page' );
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function exists( $slug )
	{
		$q = new query();
		$q->count('page_slug' )->from( 'page' )->where( 'page_slug=:page_slug' );
		$q->bindValue( 'page_slug', $slug, ptSTR );
		return $q->exec()->fetchColumn(0) > 0;
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetch( $slug )
	{
		if ( $this->exists( $slug ) === false )
		{
			return false;
		}
		else
		{
			$q = new query();
			$q->select()->from( 'page' )->where( 'page_slug=:page_slug' );
			$q->bindValue( 'page_slug', $slug, ptSTR );
			try
			{
				$row = $q->exec()->fetch();
			}
			catch( Exception $e )
			{
				show_error( 'An error occurred while loading this page.  A site administrator has been notified with the details.  Please try again later.', $e );
			}
		}
		return $row;
	}
	
	//------------------------------------------------------------------------
	
	public function fetchForDropdown()
	{
		$q = new query();
		
		$items = $q->select( 'page_slug, page_title' )
		           ->from( 'page' )
		           ->order_by( 'page_slug DESC' )
		           ->exec()
		           ->fetchAll();

		$list = array();
		foreach ( $items as $item )
		{
			$list[$item->page_slug] = $item->page_slug . ' - ' . $item->page_title;
		}
		
		return $list;
	}
	
	//------------------------------------------------------------------------
	
	public function update( $ds )
	{
		$q = new query();

		return $q->update( 'page' )
				->pt( ptSTR )
				->set( 'page_template', 'page_title', 'page_content', 'page_window_title', 'page_meta_description' )
				->pt( ptBOOL )
				->set( 'page_published' )
				->autoBind( $ds )
				->where( 'page_slug=:page_slug' )
				->bindValue( 'page_slug', $ds->page_slug, ptSTR )
				->exec();
	}
	
	//------------------------------------------------------------------------
	
	public function insert( $ds )
	{
		$q = new query();
		
		$q->insert_into( 'page' )
			->pt( ptSTR )
			->insert_fields( 'page_slug', 'page_template', 'page_title', 'page_content', 'page_window_title', 'page_meta_description' )
			->pt( ptBOOL )
			->insert_fields( 'page_published' )
			->autoBind( $ds )
			->exec();
	}
}
return new staticPageModel();
