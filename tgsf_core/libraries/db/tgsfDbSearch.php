<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
abstract class tgsfDbSearch extends tgsfGrid
{
	protected $_query = null;
	protected $_ro_url = null;
	protected $queryDs = null;
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct()
	{
		$this->emptyMessage = '<em>No Records Found.</em>';
		$this->css_class( 'search' );
		parent::__construct();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function setUrl( &$url )
	{
		$this->_ro_url = clone $url;
		$this->_ro_url->set( GET()->dataArray() );
	}
	//------------------------------------------------------------------------
	public function &nextAnchorTag( $linkText = '&raquo; Next', $linkTitle = 'Next Page' )
	{
		$url = clone $this->_ro_url;
		$this->_query->nextLinkUrlVars( $url );
					
		return $url->anchorTag()->addAttribute( 'title', 'Next Page' )->content( $linkTitle );
	}
	//------------------------------------------------------------------------
	/**
	* Returns a tgsfHtmlTag object that is an anchor, all set up and ready to be echoed
	* in a view for the previous page link
	*/
	public function &prevAnchorTag( $linkText = 'Prev &laquo;', $linkTitle = 'Previous Page' )
	{
		$url = clone $this->_ro_url;
		
		if ( $this->_query->prevLinkUrlVars( $url ) === false )
		{
			
		}
					
		return $url->anchorTag()->addAttribute( 'title', $linkTitle )->content( $linkTitle );
	}
	//------------------------------------------------------------------------
	/**
	* Sets the query object and executes it and sets $this->_rows
	* @param Object::paginateQuery The pagination query
	*/
	public function &query( &$pq )
	{
		if ( $pq instanceof tgsfPaginateQuery === false )
		{
			throw new tgsfException( 'Search queries must be an instance of tgsfPaginateQuery' );
		}
		
		$this->_query = $pq;
		$this->_query->exec();
		$this->_rows = $this->_query->fetchAll();
		return $this;
	}
	//------------------------------------------------------------------------
	protected function _loadRows()
	{
		// empty since we load the rows in the query method above
	}
	//------------------------------------------------------------------------
	/**
	* do now allow the render function to be used - we want better code readability
	*/
	public function render()
	{
		throw new tgsfException( 'Do not use render on search - use the renderGrid method.' );
	}
	//------------------------------------------------------------------------
	/**
	* Renders the grid
	*/
	public function renderGrid()
	{
		return parent::render();
	}

}