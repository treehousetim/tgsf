<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
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

		$this->_ro_curPage = GET()->_( 'page', 1 );
		$this->_ro_resultsPer = GET()->_( 'per', 10 );
		$this->_ro_totalResults = GET()->_( 'latot', NULL );
		$this->_ro_totalPages = (int)( $this->_ro_totalResults/$this->_ro_resultsPer ) + 1;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function enforceLimit( $value )
	{
		$this->_ro_enforceLimit = $value;
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
		return min( max( 1,$this->curPage + $increment ), $this->_ro_maxRecords/$this->_ro_resultsPer );
	}
	//------------------------------------------------------------------------
	/**
	* Pass in your url object to have pagination variables set on it for the next page.
	* @param Object::tgsfUrl The url object
	*/
	public function &nextLinkUrlVars( &$url )
	{
		if ( empty( $this->_ro_totalResults ) )
		{
			$this->setPageCount();
		}
		
		$url->setVar( 'page', $this->_getCurPage( 1 ) );
		$url->setVar( 'per', $this->_ro_resultsPer );
		$url->setVar( 'latot', $this->_ro_totalResults );

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Pass in your url object to have pagination variables set on it for the next page.
	* @param Object::tgsfUrl The url object
	*/
	public function &prevLinkUrlVars( &$url )
	{
		if ( empty( $this->_ro_totalResults ) )
		{
			$this->setPageCount();
		}
		
		$url->setVar( 'page', $this->_getCurPage( -1 ) );
		$url->setVar( 'per', $this->_ro_resultsPer );
		$url->setVar( 'latot', $this->_ro_totalResults );

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &setPageCount()
	{
		if ( $this->_ro_totalResults !== null )
		{
			return $this;
		}
		
		$_selectList = $this->_selectList;
		$this->_selectList = array();
		$this->count();
		parent::exec();

		$this->_ro_totalResults = $this->fetchColumn(0);
		$this->_ro_totalPages = (int)( $this->_ro_totalResults/$this->_ro_resultsPer ) + 1;

		$this->_stmHandle = null;
		$this->_executed = false;
		$this->_selectList = $_selectList;

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

