<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// footer cell functions
enum( 'fcf', array (
	'NUL',		// no function is defined - a pseudo type - this is just the state when there is no function
	'SUM',		// fields are added together in a group
	'AVG',		// fields are added then divided by the total number of rows in a group
	'MUL'		// fields are multiplied together in a group
	));

// footer cells are either a function or static text
enum ( 'fct', array (
	'FUNC',
	'TEXT'
	));
	
class tgsfGridGroupFooterCell extends tgsfHtmlTag
{
	protected $_ro_type			= fctTEXT;
	protected $_ro_func			= fcfNUL;
	protected $_ro_funcField	= '';
	protected $_ro_text			= '';
	protected $_ro_funcValues	= array();
	
	protected $_onRender		= null;

	//------------------------------------------------------------------------
	public function __construct()
	{
		parent::__construct( 'th' );
	}
	//------------------------------------------------------------------------
	/**
	* the function you use for the callback must accept
	* $footerCell, $funcValues
	*/
	public function onRender( $callBack, $object = null )
	{
		$cb = array( $object, $callBack );

		if ( is_callable( $cb ) )
		{
			$this->_onRender =& $cb;
		}
		else
		{
			$this->_onRender = $callBack;
		}
	}
	/**
	* Sets up this footer cell as a function
	* @param String The name of the function
	* @param String The name of the field in the dataset
	*/
	public function func( $func, $field )
	{
		$this->_ro_type = fctFUNC;
		$this->_ro_funcField = $field;
		
		switch ( strtolower( $func ) )
		{
		case 'sum':
		case fcfSUM:
			$this->_ro_func = fcfSUM;
			break;

		case 'avg':
		case fcfAVG:
			$this->_ro_func = fcfAVG;
			break;

		case 'mul':
		case fcfMUL:
			$this->_ro_func = fcfMUL;
			break;

		default:
			throw new tgsfGridException( 'Unknown Function: ' . $func . ' when adding a group footer.' );
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function text( $text )
	{
		$this->_ro_type = fctTEXT;
		$this->_ro_text = $text;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function trackGroupValues( $row )
	{
		if ( $this->_ro_type != fctFUNC )
		{
			return;
		}
		
		$dataRow = (array)$row;
		if ( array_key_exists( $this->_ro_funcField, $dataRow ) === false )
		{
			throw new tgsfGridException( 'Unable to calculate grid group footer cell function. Missing field in grid data: ' . $this->_ro_funcField );
		}

		$this->_ro_funcValues[] = $dataRow[$this->_ro_funcField];
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function setContent()
	{
		if ( $this->_ro_type == fctTEXT )
		{
			$this->content( $this->_ro_text );
		}

		if ( $this->_ro_type == fctFUNC )
		{
			switch( $this->_ro_func )
			{
			case fcfSUM:
				$this->content( array_sum( $this->_ro_funcValues ) );
				break;

			case fcfAVG:
				$this->content( array_sum( $this->_ro_funcValues )/count( $this->_ro_funcValues ) );
				break;

			case fcfMUL:
				$this->content( array_product( $this->_ro_funcValues ) );
				break;
			}

			if ( $this->_onRender != null )
			{
				call_user_func( $this->_onRender, $this, $this->_ro_funcValues );
			}
			
			$this->_ro_funcValues = array();
		}
		else
		{
			if ( $this->_onRender != null )
			{
				call_user_func( $this->_onRender, $this, $this->_ro_funcValues );
			}
		}
	}
}
