<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license
for complete licensing information.
*/

define( 'tgsfRoleGuest', 0 );
define( 'tgsfRoleMember', 20 );
define( 'tgsfRoleContributor', 40 );
define( 'tgsfRoleEditor', 60 );
define( 'tgsfRoleAdmin', 80 );
define( 'tgsfRoleSuperAdmin', 100 );

function &AUTH( $model = null )
{
	if ( TGSF_CLI === true )
	{
		return tgsfUserAuthCLI::get_instance( $model );
	}
	else
	{
		return tgsfUserAuth::get_instance( $model );
	}
}
//------------------------------------------------------------------------
function AUTH_is_configured()
{
	if ( TGSF_CLI === true )
	{
		return tgsfUserAuthCLI::$configured;
	}
	else
	{
		return tgsfUserAuth::$configured;
	}
}
//------------------------------------------------------------------------
class tgsfUserAuth extends tgsfBase
{
	private static	$_instance			= null;
	public static $configured			= false;

	protected	$_ro_loggedIn			= false;
	protected	$_ro_user				= null;
	protected	$model					= null;
	public		$loginUrl				= null;

	//------------------------------------------------------------------------
	/**
	* The constructor detects if a user is already logged in, and loads the
	* user's login record if so.
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	protected function __construct( $model )
	{
		$this->model = $model;
		SESSION()->start();
		$this->_ro_loggedIn = ! empty( $_SESSION['loggedin'] ) && $_SESSION['loggedin'] === true;

		if ( $this->_ro_loggedIn === true && $this->model !== null )
		{
			if ( array_key_exists( 'record_id', $_SESSION ) === false )
			{
				$this->logout();
			}
			else
			{
				$this->_ro_user	= $this->model->getForAuth( $_SESSION['record_id'] );
			}
		}
		else
		{
			$this->logout();
		}

		self::$configured = true;
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
			$_SESSION['record_id'] = $this->model->getAuthRecordId( $row );
			$this->_ro_user	= $this->model->getForAuth( $_SESSION['record_id'] );
			return true;
		}

		$this->logout();
		return false;
	}
	
	//------------------------------------------------------------------------
	/*
	 * Return the logged in users time zone
	 */
	public function getLoginTimeZone()
	{
		if ( ! $this->loggedIn ) return TZ_DEFAULT;
		return $this->model->getLoginTimeZone( $this->_ro_user );
	}
	
	//------------------------------------------------------------------------
	/**
	* Returns the logged in user's id
	*/
	public function getLoginId()
	{
		if ( ! is_null( $this->_ro_user ) && $this->_ro_user !== false )
		{
			return $this->model->getAuthRecordId( $this->_ro_user );
		}

		$this->logout();

		return null;
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
	public function &requireLogin()
	{
		tgsfEventFactory::action()
			->event( 'AUTH_login_check' )
			->ds
				->setVal( 'scope', $this )
				->event
			->exec();

		if ( ! $this->loggedIn )
		{
			$this->loginUrl->redirect();
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* If a user does not have the minimum role specified (integer) or higher,
	* a 404 is displayed.
	*/
	public function &minRole( $minRole )
	{
		tgsfEventFactory::action()
			->event( 'AUTH_min_role' )
			->ds
				->setVal( 'scope', $this )
				->event
			->exec();

		if ( $this->loggedIn === true )
		{
			if ( $this->model->getAuthRole( $this->_ro_user ) >= $minRole )
			{
				return $this;
			}
		}

		// we display a 404 because we don't want to even acknowledge
		// that a page exists to a user unless they have the clearance to
		// view it.

		display_404();
	}
	//------------------------------------------------------------------------
	/**
	* Returns true if the current user has at least the specified role, else false
	*/
	public function hasRole( $role )
	{
		if ( $this->loggedIn === true )
		{
			if ( $this->model->getAuthRole( $this->_ro_user ) >= $role )
			{
				return true;
			}
		}

		return false;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true if the current user IS the specified role, else false
	*/
	public function isRole( $role )
	{
		if ( $this->loggedIn === true )
		{
			if ( $this->model->getAuthRole( $this->_ro_user ) == $role )
			{
				return true;
			}
		}

		return false;
	}
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance( $model )
	{
		if ( self::$_instance === null )
		{
			if ( is_null( $model ) )
			{
				throw new tgsfException( 'A model is required when calling AUTH for the first time.' );
			}

			$c = __CLASS__;
			self::$_instance = new $c( $model );
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
//------------------------------------------------------------------------
class tgsfUserAuthCLI extends tgsfUserAuth
{
	private static	$_instance			= null;
	public static $configured			= false;
	//------------------------------------------------------------------------
	protected function __construct( $model )
	{
		$this->model = $model;
		self::$configured = true;
	}
	//------------------------------------------------------------------------
	public function login( $ds )
	{
		return false;
	}
	//------------------------------------------------------------------------
	public function getLoginTimeZone()
	{
		return TZ_DEFAULT;
	}
	
	//------------------------------------------------------------------------
	/**
	* Returns the logged in user's id
	*/
	public function getLoginId()
	{
		return null;
	}
	//------------------------------------------------------------------------
	public function logout()
	{
	}
	//------------------------------------------------------------------------
	public function &requireLogin()
	{
		return $this;
	}
	//------------------------------------------------------------------------
	public function &minRole( $minRole )
	{
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true if the current user has at least the specified role, else false
	*/
	public function hasRole( $role )
	{
		return false;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true if the current user IS the specified role, else false
	*/
	public function isRole( $role )
	{
		return false;
	}
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance( $model )
	{
		if ( self::$_instance === null )
		{
			if ( is_null( $model ) )
			{
				throw new tgsfException( 'A model is required when calling AUTH for the first time.' );
			}

			$c = __CLASS__;
			self::$_instance = new $c( $model );
		}

		return self::$_instance;
	}
	//------------------------------------------------------------------------
	public function __clone()
	{
		throw new tgsfException( 'Cloning a singleton (userAuthCLI) is not allowed. Use the AUTH() function to get its instance.' );
	}
}
