<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license/
for complete licensing information.
*/
//------------------------------------------------------------------------
/**
* API function call to return the singleton instance of the dbManager class
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
	private static $instance = null; // the singleton instance.
	//------------------------------------------------------------------------
	/**
	* The constructor - private to prevent direct instantiation.
	*/
	private function __construct()
	{
	}

	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
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

	/**
	* Prevent cloning the instance
	*/
	public function __clone()
	{
		throw new tgsfDbException( 'Cloning a singleton (datamanager) is not allowed.  Use the dbm() function to get its instance.' );
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
		if ( ! $object instanceof dbSetup )
		{
			throw new tgsfDbException( 'Wrong type when calling _setSetupObject in the Database manager.' );
		}

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
	* Use a setup object (or array of setup objects) for connecting with
	* Arrays should be associative - ['name'] = object
	* @param Mixed Setup Object or array of setup objects.  These are used to connect to the database(s)
	* if this is not an array, it is considered to be the default connection.
	* @see addSetup()
	*/
	public function useSetup( $setupObject )
	{
		if ( is_object( $setupObject ) )
		{
			$this->_setSetupObject( $setupObject, 'default' );
		}
		else
		{
			// disconnects any connections to the database that might exist.
			$this->_unsetSetup();
			foreach ( $setupObject as $which => &$obj )
			{
				$this->addSetup( $obj, $which );
			}
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
			$result = is_object( $ob ) && $ob instanceof dbSetup;
		}
		return $result;
	}

	//------------------------------------------------------------------------
	/**
	* Connects to the database using the settings for the supplied connection name
	* Returns false if the connection is unavailable
	* @param String The logical name of the database server to connect to
	* This is 'default' by ... um... default.
	* @return dbSetup Object
	*/
	public function &connect( $which = 'default' )
	{
		if ( $this->setupExists( $which ) === false )
		{
			throw new tgsfDbException( 'The Logical database Connection named "' . $which . '" has not been defined.' );
		}

		$this->_setup[$which]->connect();

		return $this->_setup[$which];
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &reconnect( $which = 'default' )
	{
		if ( $this->setupExists( $which ) === false )
		{
			throw new tgsfDbException( 'The Logical database Connection named "' . $which . '" has not been defined.' );
		}

		$this->_setup[$which]->disconnect();
		$this->_setup[$which]->connect();

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the logical database connection object
	* @param String The logical name of the database server connection object
	*/
	public function getLogicalDb( $which = 'default' )
	{
		if ( $this->setupExists( $which ) === false )
		{
			throw new tgsfDbException( 'The logical database Connection named "' . $which . '" has not been defined.' );
		}

		return $this->_setup[$which];
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
	//------------------------------------------------------------------------
	/**
	* Turns nested transactions on
	* @param String The logical name of the database server connection object
	*/
	public function nestedTransactionsOn( $which = 'default' )
	{
		dbm()->getLogicalDb( $which )->allowNestedTransactions = true;
	}
	//------------------------------------------------------------------------
	/**
	* Turns nested transactions off
	* @param String The logical name of the database server connection object
	*/
	public function nestedTransactionsOff( $which = 'default' )
	{
		dbm()->getLogicalDb( $which )->allowNestedTransactions = false;
	}
	//------------------------------------------------------------------------
	/**
	* Begins a transaction on the given named logical database connection
	* @param String The logical name of the database server to connect to
	*/
	public function beginTransaction( $which = 'default' )
	{
		$this->getHandle( $which )->beginTransaction();
	}
	//------------------------------------------------------------------------
	/**
	* checks if there is an active transaction
	* @param String The logical name of the database server to connect to
	*/
	public function inTransaction( $which = 'default' )
	{
		return $this->getHandle( $which )->inTransaction();
	}
	//------------------------------------------------------------------------
	/**
	* Commits a transaction on the given named logical database connection
	* @param String The logical name of the database server to connect to
	*/
	public function commit( $which = 'default' )
	{
		$this->getHandle( $which )->commit();
	}
	//------------------------------------------------------------------------
	/**
	* Rolls back a transaction on the given named logical database connection
	* @param Exception - The exception that triggered a rollback - may be null
	* @param String The logical name of the database server to connect to
	*/
	public function rollBack( $exception = null, $which = 'default' )
	{
		$this->getHandle( $which )->rollBack( $exception );
	}
	//------------------------------------------------------------------------
	public function lastInsertId( $name = '', $which = 'default' )
	{
		if ( empty( $name ) )
		{
			return $this->getHandle( $which )->lastInsertId();
		}
		else
		{
			return $this->getHandle( $which )->lastInsertId( $name );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if a table exists
	* @param String The name of the table
	* @param String The logical name of the database server to connect to - defaults to 'default'
	*/
	public function tableExists( $tableName, $which = 'default' )
	{
		$stm = $this->getHandle( $which )->handle()->prepare( 'SHOW TABLES LIKE :tablename' );
		$stm->bindValue( ":tablename", $tableName, PDO::PARAM_STR );
		$stm->execute();
		while ( $name = $stm->fetchColumn( 0 ) )
		{
			if ( $name == $tableName )
			{
				return true;
			}
		}

		return false;
	}
}
