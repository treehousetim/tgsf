<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// parse all other args into the datasource object
// so we can use this as a standard datasource object

// we'll use this like this:
// CLI()->controller;
// CLI()->argName
// CLI()->flag - true/false

function &CLI()
{
	return tgsfCli::get_instance();
}
//------------------------------------------------------------------------
class tgsfCli extends tgsfDataSource
{
	private static	$_instance			= null;

	protected $_ro_argv					= array();
	//------------------------------------------------------------------------
	/**
	* The constructor sets the type and detects if a POST has occurred
	* if it has, it add all the POST variables into this datasource (itself).
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	public function __construct()
	{
		parent::__construct( dsTypeCLI );
		global $argv;
        $this->_ro_argv = $argv;
		$this->parseArgv();
	}
	//------------------------------------------------------------------------
	/**
	* Parses the argv array member variable set in the constructor.
	*/
	private function parseArgv()
	{
		$tmpArgv = $this->_ro_argv;

		// remove script name
		array_shift( $tmpArgv );

		$unnamed = array();

		foreach( $tmpArgv as $arg )
		{
			if ( substr( $arg, 0, 2 ) == '--' )
			{
				$eqPos = strpos( $arg, '=' );

				if ( $eqPos === false )
				{
					$key   = substr( $arg, 2 );
					$value = parent::getVar($key, 1);

					parent::setVar( $key, $value );
				}
				else
				{
					$key = substr( $arg, 2, $eqPos-2 );

					if ( strtoupper($key) == 'CONTROLLER' && parent::getVar($key) != '' )
					{
						throw new appException( 'Controller specified more than once.' );
					}

					parent::setVar( $key, substr( $arg, $eqPos + 1 ) );
				}
			}
			else if ( substr( $arg, 0, 1 ) == '-' )
			{
				if (substr( $arg, 2, 1 ) == '=' )
				{
					$key = substr( $arg, 1, 1 );
					parent::setVar( $key, substr( $arg, 3 ) );
				}
				else
				{
					$chars = str_split( substr( $arg, 1 ) );
					foreach( $chars as $char )
					{
						$key = $char;
						$value = parent::getVar($key, 1);

						parent::setVar( $key, $value );
					}
				}
			}
			else
			{
				$unnamed[] = $arg;
			}
		}
		if ( ! empty( $unnamed ) )
		{
			parent::setVar( 'unnamed', $unnamed );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Converts a CLI parsed object into a string - formatted like a url
	*/
	public function __toString()
	{
		$items = parent::dataArray();

		$out = array();

		foreach( $items as $key => $value )
		{
			if ( strtolower( $key ) != 'controller' )
			{
				$out[] = $key . '/' . $value;
			}
		}

		$itemString = '';

		if ( count( $out ) )
		{
			$itemString = '/_/' . implode( '/', $out );
		}

		return $this->controller . $itemString;
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
	* disallow resetting this if we're still a CLI type
	*/
	public function reset()
	{
		if ( $this->_type == dsTypeCLI )
		{
			throw new tgsfException( 'Resetting a CLI datasource is disallowed.' );
		}
		parent::reset();
	}
	//------------------------------------------------------------------------
	/**
	* Manually set a member of the data source
	*/
	public function setVar( $varName, $varValue )
	{
		if ( $this->_type == dsTypeCLI )
		{
			throw new tgsfException( 'You may not use setVar on CLI datasources - Maybe you could use the remap function instead.' );
		}
		parent::setVar( $varName, $varValue );
	}
	//------------------------------------------------------------------------
	public function set( $source )
	{
		if ( $this->_type == dsTypeCLI )
		{
			throw new tgsfException( 'You may not use set on CLI datasources.' );
		}
		parent::set( $source );
	}
}