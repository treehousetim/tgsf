<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// see table.sql for a table create ddl statement
function &LOGGER( $tableName = null )
{
	return tgsfLog::get_instance( $tableName );
}
//------------------------------------------------------------------------
class tgsfLog extends tgsfBase
{
	private static	$_instance		= null;
	protected		$_ro_user_id	= null;
	public 			$tableName		= 'tgsf_log';
	
	protected function __construct( $tableName = null )
	{
		if ( ! is_null( $tableName ) )
		{
			$this->tableName = $tableName;
		}
	}
/* use logic in $this->log function instead
	//------------------------------------------------------------------------
	/**
	* detects a login id from the provided auth object which should be
	* @param Object::tgsfUserAuth - Use AUTH() to get the instance to pass to this function
	* /
	public function detectLogin( $auth )
	{
		$this->_ro_user_id = $auth->getLoginId();
	}*/

	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance( $tableName )
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c( $tableName );
		}
		
		return self::$_instance;
	}

	//------------------------------------------------------------------------
	/**
	* Logs an exception
	* @param Object An exception object
	*/
	public function exception( $e, $message = '' )
	{
		if ( $message != '' )
		{
			$message .= PHP_EOL;
		}

		try
		{
			$message .= get_class( $e ) . ' :: ' . $e->getMessage() . PHP_EOL;
			$message .= 'File: ' . $e->getFile() . PHP_EOL;
			$message .= 'Line: ' . $e->getLine() . PHP_EOL;
			$message .= $e->getTraceAsString();
		}
		catch ( Exception $ee )
		{
			$message = get_dump( $e );
		}

		if ( can_plugin() )
		{
			$message = do_filter( 'log_exception', $message, $e );
		}
		$this->log( $message, 'exception' );
	}
	//------------------------------------------------------------------------
	/**
	* Logs a query that generated a database error
	* @param String The query the caused the error
	* @param 
	*/
	public function queryError( $query, $error, $ds = null )
	{
		$message = $query . PHP_EOL;
		$message .= $error;
		if ( is_null( $ds ) === false )
		{
			$message .= get_dump( $ds->getDataObject() );
		}
		
		$this->log( $message, 'query' );
	}
	//------------------------------------------------------------------------
	/**
	* Logs an application error
	* @param String The error message
	*/
	public function app( $message )
	{
		$this->log( $message, 'app' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function ownershipError( $message, $table, $record_key )
	{
		if ( $table instanceof table )
		{
			$table = $table->tableName;
		}
		$this->log( $message, 'ownership_error', $table, $record_key );
	}
	//------------------------------------------------------------------------
	/**
	* performs a log operation to the database.
	*/
	public function log( $message, $type = 'generic', $table = '', $record_key = '' )
	{
		$userRec = new stdClass();
		$userId = '';
		if ( function_exists( 'AUTH_is_configured' ) && AUTH_is_configured() && AUTH()->loggedIn )
		{
			$userRec = AUTH()->user;
			$userId = AUTH()->getLoginId();
		}
		
		$ds = new tgsfDataSource();
		$ds->setVar( 'log_type',				$type );
		$ds->setVar( 'log_datetime',			date( 'Y-m-d H:i:s' ) );
		$ds->setVar( 'log_remote_addr',			$_SERVER['REMOTE_ADDR'] );
		$ds->setVar( 'log_message',				$message );
		$ds->setVar( 'log_table',				$table );
		$ds->setVar( 'log_table_record_key',	$record_key );
		$ds->setVar( 'log_user_id',				$userId );
		$ds->setVar( 'log_url',					$_SERVER['REQUEST_URI'] );

		$ds->setVar( 'log_get',					get_dump( $_GET ) );
		$ds->setVar( 'log_post',				get_dump( $_POST ) );
		$ds->setVar( 'log_cookie',				get_dump( $_COOKIE ) );
		$ds->setVar( 'log_session',				get_dump( $_SESSION ) );
		$ds->setVar( 'log_server',				get_dump( $_SERVER ) );
		$ds->setVar( 'log_env',					get_dump( $_ENV ) );
		$ds->setVar( 'log_files',				get_dump( $_FILES ) );

		$q = new query();
		$q->insert_into( $this->tableName );
		$q->pt( ptSTR )->insert_fields( array(
				'log_type','log_remote_addr','log_message',
				'log_table','log_table_record_key','log_url',
				'log_get','log_post','log_cookie','log_session',
				'log_server','log_env','log_files','log_user_id' ) );

		$q->pt( ptDATETIME )->insert_fields( 'log_datetime' );
		$q->autoBind( $ds );

		try
		{
			$q->exec();
		}
		catch ( Exception $e )
		{
			log_exception( $e, true );
			log_error( $message );
		}
	}
}
