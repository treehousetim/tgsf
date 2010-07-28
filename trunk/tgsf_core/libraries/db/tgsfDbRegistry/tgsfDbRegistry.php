<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license
for complete licensing information.
*/

/*
This library needs a table in your database.  You can find the DDL for it in
/tgsf_core/libraries/db/tgsfDbRegistry/table.sql
*/


//------------------------------------------------------------------------
function &REG( $table = null )
{
	return tgsfDbRegistry::get_instance( $table );
}
//------------------------------------------------------------------------
function reg_get( $key, $group )
{
	return REG()->fetchValueForKey( $key, $group );
}
//------------------------------------------------------------------------
class tgsfDbRegistry extends tgsfBase
{
	private static	$_instance			= null;
	protected $_ro_tableName			= '';

	//------------------------------------------------------------------------
	/**
	* The constructor detects if a user is already logged in, and loads the
	* user's login record if so.
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	protected function __construct( $table )
	{
		$this->setApp( 'app' );
		$this->_ro_tableName = $table;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the application to work with reg values for
	*/
	public function &setApp( $app )
	{
		$this->_ro_app = $app;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance( $table )
	{
		if ( self::$_instance === null )
		{
			if ( $table === null )
			{
				throw new tgsfException( 'A table name is required when calling REG() for the first time.' );
			}

			$c = __CLASS__;
			self::$_instance = new $c( $table );
		}

		return self::$_instance;
	}
	//------------------------------------------------------------------------
	public function insert( $ds )
	{
		if ( $ds->isEmpty( 'registry_app' ) )
		{
			$ds->setVar( 'registry_app', 'app' )
		}
		try
		{
			$q = new query();

			$q->insert_into( $this->tableName )
			  ->pt( ptSTR )
			  ->insert_fields( array(
					'registry_key',
					'registry_app',
					'registry_value',
					'registry_group'
				))
			  ->autoBind( $ds )
			  ->exec();
		}
		catch ( Exception $e )
		{
			LOGGER()->exception( $e );
			return false;
		}

		return dbm()->lastInsertId();
	}
	//------------------------------------------------------------------------
	public function update( $ds )
	{
		$q = new query();

		return $q->update( $this->tableName )
		         ->pt( ptSTR )
		         ->set( array(
					'registry_value'
		           ))
		         ->autoBind( $ds )
		         ->where( 'registry_key=:registry_key' )
		         ->bindValue( 'registry_key', $ds->registry_key, ptSTR )
		         ->and_where( 'registry_group=:registry_group' )
		         ->bindValue( 'registry_group', $ds->registry_group, ptSTR )
		         ->exec();
	}
	//------------------------------------------------------------------------
	public function updateValueForKey( $key, $group, $value )
	{
		$ds = new dbDataSource();
		$ds->set( array(
		    'registry_key'   => $key,
		    'registry_group' => $group,
		    'registry_value' => $value
		) );

		return $this->update( $ds );
	}
	//------------------------------------------------------------------------
	function fetchValueForKey( $key, $group )
	{
		$q = new query();

		$result = $q->select( 'registry_value' )
		            ->from( $this->tableName )
		            ->where( 'registry_key=:registry_key' )
		            ->bindValue( 'registry_key', $key, ptSTR )
		            ->and_where( 'registry_group=:registry_group' )
		            ->bindValue( 'registry_group', $group, ptSTR )
		            ->exec()
		            ->fetchColumn();


		return trim($result);
	}
	//------------------------------------------------------------------------
	/**
	* Alias to fetchValueForKey
	*/
	public function fetch( $key, $group )
	{
		return $this->fetchValueForKey( $key, $group );
	}
	//------------------------------------------------------------------------
	function fetchByKey( $key, $group )
	{
		$q = new query();

		return $q->select()
		         ->from( $this->tableName )
		         ->where( 'registry_key=:registry_key' )
		         ->bindValue( 'registry_key', $key, ptSTR )
		         ->and_where( 'registry_group=:registry_group' )
		         ->bindValue( 'registry_group', $group, ptSTR )
		         ->exec()
		         ->fetch_ds();
	}
	//------------------------------------------------------------------------
	function fetchAllByGroup( $group )
	{
		$results = query::factory()
		 	->select()
			->from( $this->tableName )
			->where( 'registry_group=:registry_group' )
			->order_by( 'registry_group,registry_key' )
			->bindValue( 'registry_group', $group, ptSTR )
			->exec()
			->fetchAll();

		$data = array();
		foreach ( $results as $result )
		{
			$_data = array(
				'key'   => $result->registry_key,
				'group' => $result->registry_group,
				'value' => trim( $result->registry_value ),
				'label' => $result->registry_label,
				'desc'  => $result->registry_desc,
				'input_size' => $result->registry_input_size
			);

			$data[$result->registry_key] = (object)$_data;
		}

		return (object)$data;
	}
	//------------------------------------------------------------------------
	function fetchAll()
	{
		$results = query::factory()
			->select()
			->from( $this->tableName )
			->order_by( 'registry_group,registry_key' )
			->exec()
			->fetchAll();

		$data = array();
		foreach ( $results as $result )
		{
			if ( !isset($data[$result->registry_group]) ) $data[$result->registry_group] = array();

			$_data = array(
				'key'   => $result->registry_key,
				'group' => $result->registry_group,
				'value' => trim( $result->registry_value ),
				'label' => $result->registry_label,
				'desc'  => $result->registry_desc,
				'input_size' => $result->registry_input_size
			);

			$data[$result->registry_group][$result->registry_key] = (object)$_data;
		}

		return (object)$data;
	}
}
