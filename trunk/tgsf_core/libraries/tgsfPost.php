<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
function &POST()
{
	return tgsfPost::get_instance();
}
//------------------------------------------------------------------------
class tgsfPost extends tgsfDataSource
{
	private static	$_instance			= null;
	
	//------------------------------------------------------------------------
	/**
	* The constructor sets the type and detects if a POST has occurred
	* if it has, it add all the POST variables into this datasource (itself).
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	public function __construct()
	{
		parent::__construct( dsTypePOST );
		
		if ( isset( $_POST ) && count( $_POST ) > 0 )
		{
			$this->_set( $_POST );
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
	* disallow resetting this if we're still a post type
	*/
	public function &reset()
	{
		if ( $this->_type == dsTypePOST )
		{
			throw new tgsfException( 'Resetting a POST datasource is disallowed.' );
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
		if ( $this->_type == dsTypePOST )
		{
			throw new tgsfException( 'You may not use setVar on POST datasources - Maybe you could use the remap function instead.' );
		}
		parent::setVar( $varName, $varValue );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &set( $source, $merge = false )
	{
		if ( $this->_type == dsTypePOST )
		{
			throw new tgsfException( 'You may not use set on POST datasources.' );
		}
		parent::set( $source, $merge );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function cancelRedirect( $var, $url, $value = 'cancel' )
	{
		if ( $this->dataPresent && strtolower( $this->_( $var ) ) == strtolower( $value ) )
		{
			if ( $url instanceof tgsfUrl )
			{
				$url->redirect();
			}

			URL( $url )->redirect();
		}
	}
}
