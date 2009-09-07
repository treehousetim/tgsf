<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class dbDataSource extends tgsfDataSource
{
	protected $_rows	= false;
	//------------------------------------------------------------------------
	public $isMulti		= false;
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct()
	{
		parent::__construct( dsTypeDB );
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function setRows( $rows )
	{
		$this->isMulti	= true;
		$this->_rows	= $rows;
	}
	
	//------------------------------------------------------------------------
	public function each()
	{
		list( $key, $item ) = each( $this->_rows );
		
		if ( $item === false )
		{
			return false;
		}
		
		$this->set( $item );
		return true;
	}
	//------------------------------------------------------------------------
	public function reset()
	{
		reset( $this->_rows );
	}
}