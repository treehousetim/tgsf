<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
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
	private static	$_instance	= null;
	protected		$_ro_posted	= false;
	
	//------------------------------------------------------------------------
	/**
	* The constructor sets the type and detects if a POST has occurred
	* if it has, it add all the POST variables into this datasource (itself).
	* it is also private as we will be using the get_instance method to instantiate
	*/
	protected function __construct()
	{
		parent::__construct( dsTypePOST );
		
		if ( isset( $_POST ) && count( $_POST ) > 0 )
		{
			$this->_ro_posted = true;
			$this->set( $_POST );
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
	* Prevent users from cloning the instance
	*/
	public function __clone()
	{
		throw new tgsfException( 'Cloning a singleton (tgsfPost) is not allowed. Use the post() function to get its instance.' );
	}
}