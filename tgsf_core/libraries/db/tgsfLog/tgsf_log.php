<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// see table.sql for a table create ddl statement
function &LOGGER( $dbSetupName = null, $tableName = null )
{
	return tgsfLog::get_instance( $dbSetupName, $tableName );
}
//------------------------------------------------------------------------
class tgsfLog extends tgsfBase
{
	private         $_inLog         = false;
	private static	$_instance		= null;
	protected		$_ro_user_id	= null;
	public 			$tableName		= 'tgsf_log';
	public          $dbSetupName    = '';

	protected function __construct( $dbSetupName = null, $tableName = null )
// public
	{
//		parent::__construct();

		if ( ! is_null( $dbSetupName ) )
		{
			$this->dbSetupName = $dbSetupName;
		}

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
	public static function &get_instance( $dbSetupName, $tableName )
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c( $dbSetupName, $tableName );
		}

		return self::$_instance;
	}

	//------------------------------------------------------------------------
	/**
	* Logs an exception
	* @param Object An exception object
	*/
	public function exception( $e, $message = '', $type = 'exception' )
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
			$message = tgsfEventFactory::filter()->event( 'log_exception' )->content( $message )
			->setVar( 'exception', $e )
			->exec();
		}
		$this->log( $message, $type );
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
			$dd = $ds->dataObject();

			$message .= get_dump( $dd );
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
		if ( $this->_inLog == true )
		{
			echo "Error Logging an Error: \n\n" . $message ;
			return;
		}

		global $argv;

		$this->_inLog = true;

		try
		{
			$userId = '';

			if ( function_exists( 'AUTH_is_configured' ) && AUTH_is_configured() && AUTH()->loggedIn )
			{
				$userId = AUTH()->getLoginId();
			}

			$ds = dsFactory::ds();
			$ds->setVar( 'log_type',				$type );
			$ds->setVar( 'log_datetime',			date( DT_FORMAT_SQL ) );
			$ds->setVar( 'log_remote_addr',			TGSF_CLI ? '127.0.0.1' : $_SERVER['REMOTE_ADDR'] );
			$ds->setVar( 'log_message',				$message );
			$ds->setVar( 'log_table',				$table );
			$ds->setVar( 'log_table_record_key',	$record_key );
			$ds->setVar( 'log_user_id',				$userId );
			$ds->setVar( 'log_url',					TGSF_CLI ? CLI() : $_SERVER['REQUEST_URI'] );

			$ds->setVar( 'log_get',					get_dump( $_GET ) );
			$ds->setVar( 'log_post',				get_dump( $_POST ) );
			$ds->setVar( 'log_cookie',				get_dump( $_COOKIE ) );
			$ds->setVar( 'log_session',				get_dump( $_SESSION ) );
			$ds->setVar( 'log_server',				get_dump( $_SERVER ) );
			$ds->setVar( 'log_env',					get_dump( $_ENV ) );
			$ds->setVar( 'log_files',				get_dump( $_FILES ) );
			$ds->setVar( 'log_argv',				get_dump( $argv ) );

			try
			{
				$q = new query($this->dbSetupName);

				$q->insert_into( $this->tableName );
				$q->pt( ptSTR )->insert_fields( array(
					'log_type','log_remote_addr','log_message',
					'log_table','log_table_record_key','log_url',
					'log_get','log_post','log_cookie','log_session',
					'log_server','log_env','log_files','log_argv','log_user_id' ) );

				$q->pt( ptDATETIME )->insert_fields( 'log_datetime' );

				$q->autoBind( $ds );

				$id = $q->exec()->lastInsertId;

				$ds->setVar( 'log_id', $id );

				tgsfEventFactory::action()
					->event( 'tgsf_log_insert' )
					->merge( $ds )
					->exec();

			}
			catch ( Exception $e )
			{
				if ( TGSF_CLI )
				{
					echo $e->get_message();
				}
				log_exception( $e, true );
				log_error( $message );
			}
		}
		catch( Exception $e )
		{
		}

		$this->_inLog = false;
	}
	//------------------------------------------------------------------------
	/**
	* updates a log record's severity from a POST from the core log_severity form
	*/
	public function updateSeverityFromPOST()
	{
		$model= load_model( 'log', IS_CORE );
		$model->updateSeverity( clone POST() );
	}
	//------------------------------------------------------------------------
	/**
	* Saves a log note from a POST from the core log_note form
	*/
	public function saveNoteFromPost()
	{
		$model= load_model( 'log_note', IS_CORE );
		$model->insert( clone POST() );
	}
}
