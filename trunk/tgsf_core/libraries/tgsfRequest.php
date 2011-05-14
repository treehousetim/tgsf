<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
// unlike most singletons in tgsf, tgsfGet, tgsfRequest and tgsfPost allow cloning.
// this is so that they can be cloned and manipulated
// prior to use in a query.  
//------------------------------------------------------------------------
function &REQUEST()
{
	return tgsfRequest::get_instance();
}
//------------------------------------------------------------------------
class tgsfRequest extends tgsfDataSource
{
	private static	$_instance			= null;

	//------------------------------------------------------------------------
	/**
	* The constructor sets the type and detects if there is any REQUEST data
	* if there is, it will add all the REQUEST variables into this datasource (itself).
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	public function __construct()
	{
		parent::__construct( dsTypeREQUEST );
		
		if ( isset( $_REQUEST ) && count( $_REQUEST ) > 0 )
		{
			$this->_ro_dataPresent = true;
			$this->_set( $_REQUEST );
		}
		
		if ( isset( $_POST ) && count( $_POST ) > 0 )
		{
			$this->_ro_dataPresent = true;
			$this->merge( POST() );
		}
		
		if ( isset( $_GET ) && count( $_GET ) > 0 )
		{
			$this->_ro_dataPresent = true;
			$this->merge( GET() );
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
	* disallow resetting this if we're still a REQUEST type
	*/
	public function &reset()
	{
		if ( $this->_type == dsTypeREQUEST )
		{
			throw new tgsfException( 'Resetting a REQUEST datasource is disallowed.' );
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
		if ( $this->_type == dsTypeREQUEST )
		{
			throw new tgsfException( 'You may not use setVar on REQUEST datasources - Maybe you could use the remap function instead.' );
		}
		parent::setVar( $varName, $varValue );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &set( $source, $merge = false )
	{
		if ( $this->_type == dsTypeREQUEST )
		{
			throw new tgsfException( 'You may not use set on REQUEST datasources.' );
		}
		parent::set( $source, $merge );
		return $this;
	}
}
