<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
// unlike most singletons in tgsf, tgsfGet and tgsfPost both allow cloning.
// this is so that a get datasource can be cloned and manipulated
// prior to use in a query.  
//------------------------------------------------------------------------
function &GET()
{
	return tgsfGet::get_instance();
}
//------------------------------------------------------------------------
class tgsfGet extends tgsfDataSource
{
	private static	$_instance			= null;

	//------------------------------------------------------------------------
	/**
	* The constructor sets the type and detects if there is any GET data
	* if there is, it add all the GET variables into this datasource (itself).
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	public function __construct()
	{
		parent::__construct( dsTypeGET );
		
		if ( isset( $_GET ) && count( $_GET ) > 0 )
		{
			$this->_ro_dataPresent = true;
			$this->_set( $_GET );
		}
	}
	
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance()
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		
		return self::$_instance;
	}
	//------------------------------------------------------------------------
	/**
	* disallow resetting this if we're still a get type
	*/
	public function &reset()
	{
		if ( $this->_type == dsTypeGET )
		{
			throw new tgsfException( 'Resetting a GET datasource is disallowed.' );
		}
		parent::reset();
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Manually set a member of the data source
	*/
	public function &setVar( $varName, $varValue )
	{
		if ( $this->_type == dsTypeGET )
		{
			throw new tgsfException( 'You may not use setVar on GET datasources - Maybe you could use the remap function instead.' );
		}
		parent::setVar( $varName, $varValue );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &set( $source, $merge = false )
	{
		if ( $this->_type == dsTypeGET )
		{
			throw new tgsfException( 'You may not use set on GET datasources.' );
		}
		parent::set( $source, $merge );
		return $this;
	}
}
