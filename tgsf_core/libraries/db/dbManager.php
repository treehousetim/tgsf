<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
/**
* API function call to return the singelton instance of the dbManager class
*/
function &dbm()
{
	return dbManager::get_instance();
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
class dbManager extends tgsfBase
{
	private $_setup = array();
	private static $instance = null; // the singelton instance.
	
	//------------------------------------------------------------------------
	/**
	* The constructor - private to prevent direct instantiation.
	*/
	private function __construct()
	{
	}
	// Prevent users to clone the instance
	public function __clone()
	{
	trigger_error('Clone is not allowed.', E_USER_ERROR);
	}
	//------------------------------------------------------------------------
	/**
	* This function makes sure that if there is an existing setup object with the given name
	* that we disconnect the database before overwriting the object with the new one.
	* The existing setup object is unset too.
	* @param Object The dbSetup object to set in the _setup[] array
	* @param String The logical database connection name
	*/
	private function _setSetupObject( &$object, $which )
	{
		if ( $this->setupExists( $which ) )
		{
			$this->_setup[$which]->disconnect();
			unset( $this->_setup[$which] );
		}
		
		$this->_setup[$which] =& $object;
	}
	
	//------------------------------------------------------------------------
	/**
	* Unsets the setup array, explicitly disconnecting each setup item from its database
	* Then it re-initializes it to an empty array.
	*/
	private function _unsetSetup()
	{
		foreach ( $this->_setup as &$setup )
		{
			$setup->disconnect();
		}
		
		unset( $this->_setup );
		$this->_setup = array();
	}
	
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singelton instance of this class.
	*/
	public static function &get_instance()
	{
		if ( self::$instance === null )
		{
			$c = __CLASS__;
			self::$instance = new $c;
		}
		
		return self::$instance;
	}
	
	//------------------------------------------------------------------------
	/**
	* Use a setup object (or array of setup objects) for connecting with
	* Arrays should be associative - ['name'] = object
	* @param Mixed Setup Object or array of setup objects.  These are used to connect to the database(s)
	* if this is not an array, it is considered to be the default connection.
	* @see addSetup()
	*/
	public function useSetup( $setupObject )
	{
		if ( ! is_array( $setupObject ) )
		{
			$this->_setSetupObject( $setupObject, 'default' );
		}
		else
		{
			$this->_unsetSetup();
			unset( $this->_setup ); // this should 
			$this->_setup = $setupObject;
		}
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds an additional database setup object
	* @param Object The setup object to add
	* @param String The name of the connection - defaults to 'default'
	* be careful to not unintentionally overwrite an existing default connection
	* @see useSetup()
	*/
	public function addSetup( $setupObject, $which = 'default' )
	{
		$this->_setSetupObject( $setupObject, $which );
	}
	
	//------------------------------------------------------------------------
	/**
	* Returns true/false if the logical connection exists.  Makes sure that
	* the variable is an object of the correct type.
	* @param String The name of the connection - defaults to 'default'
	*/
	public function setupExists( $which = 'default' )
	{
		$result = false;
		
		if ( isset( $this->_setup[$which] ) )
		{
			$ob =& $this->_setup[$which];
			$result = is_object( $ob ) && get_class( $ob ) == 'dbSetup';
		}
		return $result;
	}
	
	//------------------------------------------------------------------------
	/**
	* Connects to the database using the settings for the supplied connection name
	* Returns false if the connection is unavailable
	* @param String The logical name of the database server to connect to
	* This is 'default' by ... um... default.
	*/
	public function connect( $which = 'default' )
	{
		if ( $this->setupExists( $which ) === false )
		{
			show_error( 'The Logical database Connection named "' . $which . '" has not been defined.' );
		}
		
		return $this->_setup[$which]->connect();
	}
	//------------------------------------------------------------------------
	/**
	* Alias of connect
	* @see connect()
	* @param String The logical name of the database server to connect to
	* This is 'default' by ... um... default.
	*/
	public function getHandle( $which = 'default' )
	{
		return $this->connect( $which );
	}
}
