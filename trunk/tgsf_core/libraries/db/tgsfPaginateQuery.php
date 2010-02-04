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
	protected $_ro_maxRecords = 1500; // due to the performance of mysql's limit clause we don't allow more than this total records ever
	protected $_ro_curPage; // defaults in constructor
	protected $_ro_resultsPer; // defaults in constructor
	protected $_ro_totalResults; // set in the exec function
	protected $_ro_enforceLimit = true;
	
	protected $_selectList	= array();
	
	public function __construct( $which = 'default' )
	{
		parent::__construct( $which );
		if ( GET()->dataPresent )
		{
			$this->_ro_curPage = GET()->_( 'page', 1 );
			$this->_ro_resultsPer = GET()->_( 'per', 10 );
			$this->_ro_totalResults = GET()->_( 'latot', 0 );
		}
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
	* Pass in your url object to have pagination variables set on it for the next page.
	* @param Object::tgsfUrl The url object
	*/
	public function &nextLinkUrlVars( &$url )
	{
		if ( empty( $this->_ro_totalResults ) )
		{
			$this->getPagCount();
		}
		
		$url->setVar( 'page', min( $this->_ro_maxRecords, $this->_ro_curPage + 1 ) );
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
			$this->getPagCount();
		}
		
		$url->setVar( 'page', max( 1, $this->_ro_curPage -1 ) );
		$url->setVar( 'per', $this->_ro_resultsPer );
		$url->setVar( 'latot', $this->_ro_totalResults );

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &getPagCount()
	{
		if ( ! empty( $this->_ro_totalResults ) )
		{
			return $this->_ro_totalResults;
		}
		
		$_selectList = $this->_selectList;
		$this->count();
		parent::exec();
		
		$this->_ro_totalResults = $this->fetchColumn(0);

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
			$this->getPagCount();
		}

		if ( $this->enforceLimit )
		{
			$this->limit( ( $this->_ro_curPage * $this->_ro_resultsPer ) - $this->_ro_resultsPer . ", {$this->_ro_resultsPer}" );
		}

		return parent::exec();
	}
}

