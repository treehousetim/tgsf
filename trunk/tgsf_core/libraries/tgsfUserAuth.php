<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license
for complete licensing information.
*/


function &AUTH()
{
	return tgsfUserAuth::get_instance();
}

class tgsfUserAuth extends tgsfBase
{
	private static	$_instance			= null;
	
	protected	$_ro_loggedIn			= false;
	protected	$_ro_user				= null;
	public		$model					= null;
	public		$loginUrl				= '';

	//------------------------------------------------------------------------
	/**
	* The constructor detects if a user is already logged in, and loads the
	* user's login record if so.
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	protected function __construct()
	{
		session_start();
		$this->_ro_loggedIn = ! empty( $_SESSION['loggedin'] ) && $_SESSION['loggedin'] === true;
		$this->model = load_model( 'login' );
		
		if ( $this->_ro_loggedIn === true )
		{
			$this->_ro_user	= $this->model->getForAuth( $_SESSION['record_id'] );
		}
	}
	//------------------------------------------------------------------------
	/**
	* performs a login using the given data source.
	* @param datasource object The data source from a form submission.
	* @returns the database row for the logged in user if successful
	*/
	public function login( $ds )
	{
		$row = $this->model->login( $ds );
		if ( $row !== false )
		{
			$this->_ro_user = $row;
			$_SESSION['loggedin'] = true;
			$_SESSION['record_id'] = $this->model->getRecordId( $row );
			return true;
		}

		$this->logout();
		return false;
	}
	//------------------------------------------------------------------------
	/**
	* performs a logout
	*/
	public function logout()
	{
		$_SESSION['loggedin'] = false;
		if ( ! empty( $_SESSION['record_id'] ) )
		{
			unset( $_SESSION['record_id'] );
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function requireLogin()
	{
		if ( ! $this->loggedIn )
		{
			redirect( $this->loginUrl );
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
		throw new tgsfException( 'Cloning a singleton (userAuth) is not allowed. Use the AUTH() function to get its instance.' );
	}
}