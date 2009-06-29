<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class dbSetup extends tgsfBase
{
	private $_user;
	private $_password;
	private $_host;
	private $_port;
	private $_database;
	private $_type;
	private $_handle = false;
	//------------------------------------------------------------------------
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
	public function __destruct()
	{
		$this->_handle = null;
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
	//------------------------------------------------------------------------	
	public function disconnect()
	{
		$this->_handle = null;
	}
}