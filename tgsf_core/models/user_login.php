<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
class userLoginModel extends tgsfBase
{
	protected $_ro_tableName;
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( )
	{
		$this->tableName = coreTable( 'user_login' );
		$this->tzTableName = coreTable( 'tz' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function stdFetch()
	{
		return query::factory()
		->select( $this->tableName . '.*' )
		->select( $this->tzTableName . '.tz_zone' );
		->from( $this->tableName )
		->join( 'tz', $this->tableName . '.user_login_tz_id = ' . $this->tzTableName . '.tz_id' );
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
		->pluginAction( 'core:userLogin:fetchById', $id )
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
			->pluginAction( 'core:userLogin:getByUsername', $username )
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
			->bindValue( 'login_username', $username,	ptSTR )
			->pluginAction( 'core:userLogin:usernameExists', $username )
		
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
}

return new userLoginModel();