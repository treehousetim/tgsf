<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
class userLoginModel extends tgsfBase implements tgsfUserAuthModel
{
	protected $_ro_tableName;
	protected $_ro_tzTableName;
	public $includeSuspended = false;
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( )
	{
		$this->_ro_tableName = coreTable( 'user_login' );
		$this->_ro_tzTableName = coreTable( 'tz' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function stdFetch()
	{
		return query::factory()
		->select( $this->tableName . '.*' )
		->select( $this->tzTableName . '.tz_zone' )
		->from( $this->_ro_tableName )
		->join( 'tz', $this->_ro_tableName . '.user_login_tz_id = ' . $this->_ro_tzTableName . '.tz_id' );
	}
	//------------------------------------------------------------------------
	/**
	* Fetches a single user_login record for the provided user_login_id
	*/
	public function fetchById( $id )
	{
		return $this->stdFetch()
		->where( 'user_login_id=:id' )
		->bindValue( 'id', $id, ptINT )
		->pluginAction( 'core:userLogin:fetchById', array( 'user_login_id' => $id ) )
		->fetch_ds();
	}
	//------------------------------------------------------------------------
	/**
	* Used to interface with tgsfUserAuth - returns the record ID for a given user_login datasource
	* @param The datasource object that we return from userLoginModel::getForAuth()
	*/
	public function getAuthRecordId( $ds )
	{
		if ( ! is_object( $ds ) || empty( $ds->user_login_id ) )
		{
			throw new tgsfException( 'No user_login_id available in getAuthRecordId' );
		}
		return $ds->user_login_id;
	}
	//------------------------------------------------------------------------
	/**
	* Used to interface with tgsfUserAuth - returns the role (integer value) for the given user_login datasource
	* @param The datasource object we return from userLoginModel::getForAuth()
	*/
	public function getAuthRole( $ds )
	{
		if ( ! is_object( $ds ) || empty( $ds->user_login_id ) || empty( $ds->user_login_role ) )
		{
			throw new tgsfException( 'No user_login_role in getAuthRole' );
		}
		return $ds->user_login_role;
	}
	//------------------------------------------------------------------------
	/**
	* Returns a user for the given login_id
	* @param Int/String The login_id to load.
	*/
	public function getForAuth( $id )
	{
		return $this->fetchById( $id );
	}
	
	//------------------------------------------------------------------------
	/*
	 * Used to interface with tgsfUserAuth - returns the timezone for the given user_login datasource
	* @param The datasource object we return from userLoginModel::getForAuth()
	 */
	public function getLoginTimeZone( $ds )
	{
		return $ds->tz_zone;
	}
	//------------------------------------------------------------------------
	/**
	* Loads a user by username
	* @param String The username
	* @param Bool Do we include public roles
	*/
	public function getByUsername( $username )
	{
		return $this->stdFetch()
			->where( 'user_login_username = :user_login_username' )
			->bindValue( 'user_login_username', $username,	ptSTR )
			->pluginAction( 'core:userLogin:getByUsername', array( 'username' => $username ) )
			->exec()
			->fetch();
	}
	//------------------------------------------------------------------------
	/**
	* returns true/false if the username exists
	* @param String The username
	*/
	public function usernameExists( $username )
	{
		$q = query::factory()
			->count( 'user_login_id' )
			->from( $this->tableName )
			->where( 'user_login_username = :user_login_username' )
			->bindValue( 'user_login_username', $username,	ptSTR )
			->pluginAction( 'core:userLogin:usernameExists', array( 'username' => $username ) );
		
		return ( $q->exec()->fetchColumn( 0 ) ) > 0;
	}
	//------------------------------------------------------------------------
	/**
	* returns true/false if the email exists (username or email)
	*/
	public function emailExists( $email )
	{
		// force suspended records to be included when checking for email
		$tmpSuspended = $this->includeSuspended;
		$this->includeSuspended = true;

		$q = new query();
		$q->count( 'login_id' )->from( 'login' )->where( 'login_username=:login_username' )->or_where( 'login_email=:login_email' );

		$q->bindValue( 'login_username', $email,	ptSTR );
		$q->bindValue( 'login_email', $email,		ptSTR );

		$this->filterQuery( $q );

		$this->includeSuspended = $tmpSuspended;

		return $q->exec()->fetchColumn( 0 ) > 0;
	}
	//------------------------------------------------------------------------
	public function login( $ds )
	{
		// for login, we FORCE suspended users to not be included
		$tmpSuspended = $this->includeSuspended;
		$this->includeSuspended = false;
		if ( $this->usernameExists( $ds->login_username, false ) )
		{
			$row = $this->getByUsername( $ds->_( 'login_username', false ) );
			$pw = $ds->_( 'login_password' );
			if ( hash_password( $pw, $row->login_password ) == $row->login_password )
			{
				// if the password reset code isn't empty then we clear it after a successful login
				if ( $row->login_password_reset != '' )
				{
					$this->clearPasswordReset( $row->login_id );
				}
				$this->includeSuspended = $tmpSuspended;
				return $row;
			}
		}

		$this->includeSuspended = $tmpSuspended;

		return false;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function adminInstallUser( $ds, &$version )
	{
		$version->addItem(
			versionItemFactory::query(
				query::factory()
					->insert_into( $this->_ro_tableName )
					->insert_fields(
							'user_login_username',
							'user_login_password',
							'user_login_role',
							'user_login_signup_date',
							'user_login_activated'
							)
					->bindValue( 'user_login_username', $ds->user_login_username, ptSTR )
					->bindValue( 'user_login_password', hash_password( $ds->user_login_password ), ptSTR )
					->bindValue( 'user_login_role', tgsfRoleAdmin, ptINT )
					->bindValue( 'user_login_signup_date', gmdate( DT_FORMAT_SQL ), ptDATETIME )
					->bindValue( 'user_login_activated', true, ptBOOL )
				)
			->description( 'Creating Admin User' ) );
	}
}

return new userLoginModel();