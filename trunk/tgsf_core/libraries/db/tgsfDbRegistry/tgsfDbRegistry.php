<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license
for complete licensing information.
*/

//------------------------------------------------------------------------
function &REG( $table = null )
{
	return tgsfDbRegistry::get_instance( $table );
}
function &REG_VALUE()
{
	return new tgsfDbRegistryValue();
}
//------------------------------------------------------------------------
class tgsfDbRegistryValue extends dbDataSource
{
	/**
	* creates a new registry value object
	*/
	public function __construct()
	{
		parent::__construct( dsTypeREG );
		$this->setContext( REG()->context );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &name( $name )
	{
		$this->setVar( 'registry_name', $name );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &value( $value )
	{
		$this->setVar( 'registry_value', $value );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &store()
	{
		if ( REG()->exists( $this->registry_name ) )
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the context to work with reg values for
	*/
	public function &setContext( $context )
	{
		$this->_ro_context = $context;
		return $this;
	}
}
//------------------------------------------------------------------------
class tgsfDbRegistry extends tgsfBase
{
	private static	$_instance			= null;
	protected $_ro_tableName			= '';
	protected $_ro_context				= contextAPP;

	//------------------------------------------------------------------------
	/**
	* The constructor detects if a user is already logged in, and loads the
	* user's login record if so.
	* it is also protected as we will be using the get_instance method to instantiate
	*/
	protected function __construct( $table )
	{
		$this->setContext( contextAPP );
		$this->_ro_tableName = $table;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the context to work with reg values for
	*/
	public function &setContext( $context )
	{
		$this->_ro_context = $context;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the context in a datasource based on the class value.
	* does NOT override the context if it is already present in the ds
	*/
	protected function &setContextDs( $ds )
	{
		if ( $ds->isEmpty( 'registry_context' ) )
		{
			$ds->setVar( 'registry_context', $this->_ro_context );
		}

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
		REG_VALUE()->name( 'name' )->value( 'test' )->store();
	}

	//------------------------------------------------------------------------
	public function insert( $ds )
	{
		$this->setContextDs( $ds );

		return query::factory()
			->insert_into( $this->tableName )
			->pt( ptSTR )
			->insert_fields(
				'registry_key',
				'registry_context',
				'registry_value',
				'registry_group'
			)
			->autoBind( $ds )
			->exec()
			->lastInsertId;
	}
	//------------------------------------------------------------------------
	public function &update( $ds )
	{
		$this->setContextForDs( $ds );
		$this->serializeDs( $ds );

		query::factory()
			->update( $this->tableName )
			->pt( ptSTR )
			->set( 'registry_value' )
			->autoBind( $ds )
			->where( 'registry_key=:registry_key' )
			->bindValue( 'registry_key', $ds->registry_key, ptSTR )
			->and_where( 'registry_group=:registry_group' )
			->bindValue( 'registry_group', $ds->registry_group, ptSTR )
			->exec();
			
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Updates a value for the given key and group
	* @param String The key
	* @param String The group
	* @param String The value to store - if not a string, it will be serialized and stored
	*/
	public function updateValue( $key, $group, $value )
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
	function fetchValue( $key, $group )
	{
		$ds = dbDataSource::factory();
		$this->setContextForDs( $ds );

		$q = new query();

		return query::factory()
			->select( 'registry_value' )
            ->from( $this->tableName )
            ->where( 'registry_key=:registry_key' )
            	->bindValue( 'registry_key', $key, ptSTR )

			->where( 'registry_group=:registry_group' )
            	->bindValue( 'registry_group', $group, ptSTR )

			->where( 'registry_context=:context' )
				->bindValue( 'registry_context', $ds->registry_context, ptSTR )

            ->exec()
            ->fetchColumn();
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
	function fetchCompleteForKey( $key, $group )
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
	function fetchGroup( $group )
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
			->order_by( 'registry_context, registry_group,registry_key' )
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
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &serializeDs( &$ds )
	{
		$ds->setVar( 'registry_value', $this->serialize( $ds->registry_value ) );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &unserializeDs( &$ds )
	{
		$ds->setVar( 'registry_value', $this->unserialize( $ds->registry_value ) );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Serializes everything but strings.
	*/
	public function serialize( $value )
	{
		// if a string we don't need to serialize
		if ( is_string( $value ) === true )
		{
			return $value;
		}

		return serialize( $value );
	}
	//------------------------------------------------------------------------
	/**
	* Checks a value to detect a serialized string
	* @param String Value to check to see if was serialized.
	*/
	function unserialize( $value )
	{
		// if it isn't a string, it isn't serialized
		if ( is_string( $value ) === false )
		{
			return $value;
		}
		
		// handle nulls
	    if ( trim( $value ) == 'N;' )
		{
			return null;
		}
		
        if ( preg_match( '/^([adObis]):/', trim( $value ), $matches ) == false )
		{
			$retVal = value;
		}
		else
		{
			$m = '';

			if ( ! empty( $matches[1] ) )
			{
				list(, $m ) = $matches;
			}

	        switch ( $m )
			{
			case 'a':
			case 'O':
			case 's':
				if ( preg_match( "/^$m:[0-9]+:.*[;}]\$/s", trim( $value ) ) )
				{
					$retVal = unserialize( trim( $value ) );
				}
				break;

			case 'b':
			case 'i':
			case 'd':
				if ( preg_match( "/^$m:[0-9.E-]+;\$/", trim( $value ) ) )
				{
					$retVal = unserialize( trim( $value ) );
				}
				break;
				
			default:
				$retVal = $value;
				break;
	        }
		}
		
		return $retVal;
	}
}
