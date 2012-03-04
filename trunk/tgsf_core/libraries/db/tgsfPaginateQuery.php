<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//------------------------------------------------------------------------
class tgsfPaginateQuery extends query
{
	// due to the performance of mysql's limit clause we don't allow more than this total records ever
	// this is allowed to be overridden via the ->maxRecords method
	// the point of this is that limits with a high starting record are slow so we only ever allow a total number of *records*
	protected $_ro_maxRecords = 1500;
	protected $_ro_curPage; // defaults in constructor
	protected $_ro_resultsPer; // defaults in constructor
	protected $_ro_totalResults = null; // set in the exec function
	protected $_ro_enforceLimit = true;
	protected $_ro_totalPages = 0;
	protected $_ro_getName = '';
	public $countQuery = null;

	protected $_selectList	= array();

	public static function &factory( $which = 'default' )
	{
		$c = __CLASS__;
		$instance = new $c( $which );
		return $instance;
	}
	//------------------------------------------------------------------------
	public function __construct( $which = 'default' )
	{
		parent::__construct( $which );
		$this->_setFromGet();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	protected function _setFromGet()
	{
		
		$this->_ro_curPage = GET()->_( (string)$this->_ro_getName . 'page', 1 );
		$this->_ro_resultsPer = GET()->_( (string)$this->_ro_getName . 'per', 10 );
		$this->_ro_totalResults = GET()->_( (string)$this->_ro_getName . 'latot', NULL );
		$this->_ro_totalPages = (int)( $this->_ro_totalResults / $this->_ro_resultsPer );

		if ( $this->_ro_totalResults % $this->_ro_resultsPer != 0 )
		{
			$this->_ro_totalPages++;
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function setGetName( $name )
	{
		$this->_ro_getName = $name;
		$this->_setFromGet();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &enforceLimit( $value )
	{
		$this->_ro_enforceLimit = $value;

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the maximum records allowed to be returned
	*/
	public function &maxRecords( $max )
	{
		$this->_ro_maxRecords = $max;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &resultsPer( $value )
	{
		$this->_ro_resultsPer = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Enforces the max record limit for the page
	*/
	protected function _getCurPage( $increment = 0 )
	{
		if ( $increment > 0 )
		{
			return min( $this->_ro_maxRecords/$this->_ro_resultsPer, $this->_ro_curPage + $increment );
		}
		elseif ( $increment < 0 )
		{
			return max( 1, $this->_ro_curPage + $increment );
		}

		return $this->_ro_curPage;
	}
	//------------------------------------------------------------------------
	/**
	* Pass in a url string and get a URL object back with pagination variables set on it for the next page.
	* @param String The url fragment
	* @param An optional datasource to get variables from
	*/
	public function getNextLinkUrl( $_url, $ds = null )
	{
		$url = PaginateURL( $_url );

		if ( $ds != null )
		{
			$url->set( clone $ds );
		}

		if ( empty( $this->_ro_totalResults ) )
		{
			$this->setPageCount();
		}

		if ( $this->_getCurPage() >= $this->_ro_totalPages )
		{
			$url->anchorTextOnly();
		}

		$url->unSetVar( (string)$this->_ro_getName . 'page');
		$url->setVar( (string)$this->_ro_getName . 'page', $this->_getCurPage( 1 ) );
		$url->setVar( (string)$this->_ro_getName . 'per', $this->_ro_resultsPer );
		$url->setVar( (string)$this->_ro_getName . 'latot', $this->_ro_totalResults );

		return $url;
	}
	//------------------------------------------------------------------------
	/**
	* Pass in a url string and get a URL object back with pagination variables set on it for the next page.
	* @param String The url fragment
	*/
	public function getPrevLinkUrl( $_url, $ds = null )
	{
		$url = PaginateURL( $_url );

		if ( $ds != null )
		{
			$url->set( clone $ds );
		}

		if ( empty( $this->_ro_totalResults ) )
		{
			$this->setPageCount();
		}

		if ( $this->_getCurPage() == 1 )
		{
			$url->anchorTextOnly();
		}

		$url->unSetVar( (string)$this->_ro_getName . 'page' );
		$url->setVar( (string)$this->_ro_getName . 'page', $this->_getCurPage( -1 ) );
		$url->setVar( (string)$this->_ro_getName . 'per', $this->_ro_resultsPer );
		$url->setVar( (string)$this->_ro_getName . 'latot', $this->_ro_totalResults );

		return $url;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &setPageCount()
	{
		if ( $this->countQuery instanceOf query )
		{
			$this->_ro_totalResults = $this->countQuery->debug()->exec()->fetchColumn( 0 );
		}
		else
		{
			$_selectList = $this->_selectList;
			$this->_selectList = array();
			$this->count();
			parent::exec();
			$this->_ro_totalResults = $this->fetchColumn(0);

			$this->_stmHandle = null;
			$this->_ro_executed = false;
			$this->_selectList = $_selectList;
		}

		$this->_ro_totalPages = (int)( $this->_ro_totalResults/$this->_ro_resultsPer );

		if ( $this->_ro_totalResults % $this->_ro_resultsPer != 0 )
		{
			$this->_ro_totalPages++;
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &exec()
	{
		if ( empty( $this->_ro_totalResults ) )
		{
			$this->setPageCount();
		}

		if ( $this->enforceLimit )
		{
			$this->limit( ( $this->_getcurPage() * $this->_ro_resultsPer ) - $this->_ro_resultsPer . ", {$this->_ro_resultsPer}" );
		}

		return parent::exec();
	}
}

