<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class dbSetup
{
	private $_user;
	private $_password;
	private $_host;
	private $_port;
	private $_database;
	private $_type;
	private $_handle = false;
	
	public $connected = false;
	
	//------------------------------------------------------------------------
	
	public function __construct( $user, $password, $database, $type = 'mysql', $host = 'localhost', $port = null )
	{
		$this->_user = $user;
		$this->_password = $password;
		$this->_database = $database;
		$this->_type = $type;
		$this->_host = $host;
		$this->_port = $port;
	}
	
	//------------------------------------------------------------------------
	/**
	* Makes the DSN string
	*/
	private function _makeDSN()
	{
		$dsn[] = "host={$this->_host}";
		$dsn[] = "dbname={$this->_database}";

		if ( ! is_null( $this->_port ) )
		{
			$dsn[] = "port={$this->_port}";
		}
		return $this->_type . ':' . implode( ';', $dsn );
	}
	
	//------------------------------------------------------------------------
	public function connect()
	{
		if ( $this->connected === true )
		{
			return $this->_handle;
		}
		
		$dsn = $this->_makeDSN();
		
		try
		{
			$this->_handle = new PDO( $dsn, $this->_user, $this->_password );
			$this->connected = true;
		}
		catch (PDOException $e )
		{
			$this->connected = false;
			$this->handle = false;
			$this->errors[] = $e->getMessage();
		}
	}
	
	public function disconnect()
	{
		$this->_handle = null;
	}
}

class dbManager
{
	private $_setup;
	public $dbh = null;
	
	//------------------------------------------------------------------------
	/**
	* The constructor.
	* @param Mixed You can either pass a single setup object or an array of setup objects
	* @see useSetup
	*/
	public function __construct( $setupObject = null )
	{
		if ( ! is_null( $setupObject ) )
		{
			$this->useSetup( $setupObject );
		}
	}
	
	//------------------------------------------------------------------------
	/**
	* Use a setup object (or array of setup objects) for connecting with
	* Arrays should be associative - ['name'] = object
	* @param Mixed Setup Object or array of setup objects.  These are used to connect to the database(s)
	*/
	public function useSetup( $setupObject )
	{
		if ( ! is_array( $setupObject ) )
		{
			$this->_setup['default'] =& $setupObject;
		}
		else
		{
			$this->_setup = $setupObject;
		}
	}

	//------------------------------------------------------------------------
	/**
	* Used to add an additional database setup later on
	* @see useSetup()
	*/
	public function addSetup( $name, $setupObject )
	{
		$this->_setup[$name] =& $setupObject;
	}
	
	//------------------------------------------------------------------------
	/**
	* Connects to the database using the settings for the supplied connection name
	* @param String The logical name of the database server to connect to
	* This is 'default' by ... um... default.
	*/
	public function connect( $which = 'default' )
	{
		return $this->_setup[$which]->connect();
	}
	
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
//------------------------------------------------------------------------
class query
{
	protected $_table = array();
	protected $_dbManager;
	protected $_conn;
	protected $_where = array( 1 => 1 );
	
	public function __construct( $which = 'default' )
	{
		$this->_dbManager = config( 'dbm' );
	}
	
	protected function _table()
	{
		return implode( ',', $this->_table );
	}
	
	protected function _where()
	{
		return implode( ',', $this->_where );
	}
	
	/**
	* Adds an AND section to the where clause
	*/
	public function w_and()
	{
		
	}
}
/*
UPDATE table set field=value WHERE
SELECT FIELD,FIELD FROM TABLE,TABLE LEFT OUTER JOIN table on ()  WHERE Clause GROUP BY ORDER BY LIMIT
DELETE FROM TABLE WHERE
*/
//------------------------------------------------------------------------
class update_query extends query
{
	private $_fields = array();
	protected $_dbManager;
	
	public function __construct( $dbm, $table = '' )
	{
		//$this->
		$this->_table = $table;
	}
	
	
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function generate()
	{
		$out = 'UPDATE ';
		$out .= $this->_table[0];
	}
}
//------------------------------------------------------------------------
class select extends query
{
	private $_selects = array();
	private $_joins = array();
	private $_wheres = array();
	private $_orders = array();
	private $_limit = array();
	
	
}

/*
function updateConfig( $subFolder, $host, $port, $prefix )
{
	if ( $port == '80' )
	{
		$port = '';
	}
	
	if ( $port != '' )
	{
		$port = ':' . $port;
	}
	
	$table = $prefix . 'options';
	$root = trim( 'http://' . $host . $port . '/' . $subFolder, '/' );

	try
	{
		$dbh = new PDO( "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD );
	}
	catch (PDOException $e )
	{
		print ("Could not connect to server.\n");
		print ("getMessage(): " . $e->getMessage () . "\n");
		exit();
	}

	$sth = $dbh->prepare( 'UPDATE ' . $table . " SET option_value = ? WHERE blog_id=0 and ( option_name=? or option_name=? )" );
		$sth->bindValue( 1, $root );
		$sth->bindValue( 2, 'siteurl' );
		$sth->bindValue( 3, 'home' );
	$sth->execute();
	$dbh = null;
}
*/