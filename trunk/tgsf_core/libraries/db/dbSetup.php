<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class dbSetup extends tgsfBase
{
	private		$_user;
	private		$_password;
	private		$_host;
	private		$_port;
	private		$_database;
	private		$_type;
	private		$_ro_handle = false;
	protected	$_transactionLevel = 0;
	//------------------------------------------------------------------------
	public		$connected = false;
	public		$allowNestedTransactions = false;
	//------------------------------------------------------------------------
	/**
	* Creates a new dbSetup object
	* @param String (Mysql only) The username for connecting
	* @param String (Mysql only) The password for connecting
	* @param String The database (name for mysql, file for sqlite)
	* @param String The type of the database - special handing for sqlite/sqlite2 and for mssql
	* @param
	*/
	public function __construct( $user, $password, $database, $type = 'mysql', $host = 'localhost', $port = null )
	{
		// if type is sqlite or sqlite2 user becomes the database file
		$this->_user = $user;
		$this->_password = $password;
		$this->_database = $database;
		$this->_type = $type;
		$this->_host = $host;
		$this->_port = $port;
	}
	//------------------------------------------------------------------------
	/**
	* disconnects from the database
	*/
	public function __destruct()
	{
		$this->_ro_handle = null;
	}

	//------------------------------------------------------------------------
	/**
	* UNTESTED Creates a Sqlite dsn string
	*/
	private function _sqliteDSN()
	{
		return $this->_type . ':' . $this->_database;
	}

	//------------------------------------------------------------------------
	/**
	* UNTESTED Creates a PostgreSQL dsn string
	*/
	private function _pgsqlDSN()
	{
		$out = 'pgsql:';

		if ( ! $this->_host == '' )
		{
			$out .= "host={$this->_host}";

			if ( ! is_null( $this->_port ) || $this->_port != '' )
			{
				$out .= " port={$this->_port}";
			}
		}
		$out .= " dbname={$this->_database}";

		return $out;
	}

	//------------------------------------------------------------------------
	/**
	* Creates a MySQL dsn string
	*/
	private function _mysqlDSN()
	{
		$out = 'mysql:';

		$out .= "host={$this->_host}";
		if ( ! is_null( $this->_port ) && $this->_port != '' )
		{
			$out .= ";port={$this->_port}";
		}

		$out .= ";dbname={$this->_database}";
		return $out;
	}

	//------------------------------------------------------------------------
	/**
	* UNTESTED Creates an MSSQL dsn string
	*/
	private function _mssqlDSN()
	{
		$out = 'mssql:';
		$out .= "host={$this->_host}";

		if ( ! is_null( $this->_port ) && $this->_port != '' )
		{
			$out .= ":{$this->_port}";
		}
		$out .= ";dbname={$this->_database}";
		return $out;
	}

	//------------------------------------------------------------------------
	/**
	* Makes the DSN string - based on the server type.
	*/
	private function _makeDSN()
	{
		switch ( $this->_type )
		{
		case 'sqlite':
		case 'sqlite2':
			return $this->_sqliteDSN();
			break;

		case 'pgsql':
			return $this->_pgsqlDSN();
			break;

		case 'mysql':
			return $this->_mysqlDSN();
			break;

		case 'mssql':
			return $this->_mssqlDSN();
			break;

		default:
			throw new tgsfDbException( 'Unsupported database type: ' . $this->_type . ' - visit http://code.google.com/p/tgsf/ to submit a feature request.' );
			break;
		}
	}
	//------------------------------------------------------------------------
	/**
	* Connects to the database server
	*/
	public function &connect()
	{
		if ( $this->connected === true )
		{
			return $this->_ro_handle;
		}

		$dsn = $this->_makeDSN();

		if ( $dsn == '' )
		{
			throw new tgsfDbException( 'Empty DSN' );
		}

		try
		{
			$this->_ro_handle = new PDO( $dsn, $this->_user, $this->_password );
			$this->connected = true;
			return $this->_ro_handle;
		}
		catch (PDOException $e )
		{
			$this->connected = false;
			$this->_ro_handle = false;
			throw new tgsfDbException( $dsn . PHP_EOL . $e->getMessage() );
		}
	}
	//------------------------------------------------------------------------
	/**
	* returns the string with the savepoint
	*/
	protected function _savepoint()
	{
		return 'SAVEPOINT tgsfTL' . $this->_transactionLevel;
	}
	//------------------------------------------------------------------------
	/**
	* Starts a transaction
	*/
	public function beginTransaction()
	{
		if ( $this->inTransaction() )
		{
			throw new tgsfDbException( 'Nested Transactions Are Not Allowed (begin trans)' );
		}
		
		$this->_transactionLevel = $this->_transactionLevel +1;
		$this->handle()->beginTransaction();
	}
	//------------------------------------------------------------------------
	/**
	* Commits a transaction
	*/
	public function commit()
	{
		if ( ! $this->inTransaction() )
		{
			$this->_transactionLevel = $this->_transactionLevel -1;
			throw new tgsfDbException( 'Unable to commit - no active transaction: ' .$this->_transactionLevel );
		}

		$this->_transactionLevel = $this->_transactionLevel -1;
		$this->handle()->commit();
	}
	//------------------------------------------------------------------------
	/**
	* checks if there is an active transaction
	* @param Object::exception The exception that caused the rollback.
	*/
	public function inTransaction( )
	{
		return $this->_transactionLevel == 1;
	}
	//------------------------------------------------------------------------
	/**
	* rolls back a transaction
	* @param Object::exception The exception that caused the rollback.
	*/
	public function rollBack( $exception = null )
	{
		if ( ! $this->inTransaction() )
		{
			$this->_transactionLevel = $this->_transactionLevel -1;
			throw new tgsfDbException( 'Unable to rollback - no active transaction: ' . ($this->_transactionLevel+1) );
		}

		$this->_transactionLevel = $this->_transactionLevel -1;
		$this->handle()->rollBack();
	}
	//------------------------------------------------------------------------
	public function lastInsertId()
	{
		return $this->handle()->lastInsertId();
	}
	//------------------------------------------------------------------------
	/**
	* An alias for connect.  This improves code readability
	* example: $conn = dbm()->connect( 'default' );
	* $conn->handle;
	*/
	function &handle()
	{
		return $this->connect();
	}

	//------------------------------------------------------------------------
	public function disconnect()
	{
		$this->connected = false;
		$this->_ro_handle = null;
	}
}
