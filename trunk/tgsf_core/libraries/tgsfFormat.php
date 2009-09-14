<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
function &FORMAT()
{
	return tgsfFormat::get_instance();
}
//------------------------------------------------------------------------
class tgsfFormat extends tgsfBase
{
	private static	$_instance			= null;

	//------------------------------------------------------------------------
	/**
	* protected to make a singleton instance
	*/
	protected function __construct()
	{
		// do nothing
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
		throw new tgsfException( 'Cloning a singleton (tgsfGet) is not allowed. Use the FORMAT() function to get its instance.' );
	}

	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	
	public function usa_phone( $text, $formatWithParens = false )
	{
		$pattern = '\\1-\\2-\\3';
		if ( $formatWithParens )
		{
			$pattern = '(\\1) \\2-\\3';
		}
		return trim( preg_replace('/\\(?([0-9]{3})\\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', $pattern, $text ) );
	}
	
}