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
	$ret = new tgsfDbRegistryValue();
	return $ret;
}
//------------------------------------------------------------------------
class tgsfDbRegistryValue extends dbDataSource
{
	protected $_ro_context = contextCORE;
	/**
	* creates a new registry value object
	*/
	public function __construct()
	{
		parent::__construct( dsTypeREG );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &key( $key )
	{
		$this->setVar( 'registry_key', $key );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &group( $group )
	{
		$this->setVar( 'registry_group', $group );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the value of a registry item in this object (not persistent storage - use ->store() to store it.)
	*/
	public function &value( $value )
	{
		$this->setVar( 'registry_value', $value );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Use this to set the type of registry field.
	* Used only when inserting a brand new registry value for the first time.
	*/
	public function &type( $type )
	{
		$this->setVar( 'registry_type', $type );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &store()
	{
		if ( $this->isEmpty( 'registry_key' ) )
		{
			throw new tgsfException( 'registry_key must not be empty when storing.' );
		}

		REG()->setContext( $this->_ro_context );
		if ( REG()->exists( $this->registry_key, $this->registry_group ) )
		{
			REG()->updateValue( $this->registry_key, $this->registry_group, $this->registry_value );
		}
		else
		{
			// require needed values for inserting
			if ( (
				$this->exists( 'registry_key' ) &&
				$this->exists( 'registry_value' ) &&
				$this->exists( 'registry_group' ) &&
				$this->exists( 'registry_type' ) 
			) == false || 
			(( $this->registry_type == rtHIDDEN || $this->registry_type == rtSERIALIZED ) &&
			! $this->exists( 'registry_label' ))
			)
			{
				throw new tgsfException( 'Unable to store registry value - incomplete data.' . PHP_EOL . nl2br( get_dump( $this ) ) );
			}

			REG()->insert( $this );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetch()
	{
		REG()->setContext( $this->_ro_context );
		if ( REG()->exists( $this->registry_key, $this->registry_group ) )
		{
			return REG()->fetchValue( $this->registry_key, $this->registry_group );
		}
		return regNOT_EXISTS;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the context to work with reg values for
	*/
	public function &context( $context )
	{
		$this->_ro_context = $context;
		return $this;
	}
}
//------------------------------------------------------------------------
class tgsfDbRegistry extends tgsfBase
{
	private static	$_instance		= null;
	protected		$_ro_tableName	= '';
	protected		$_ro_context	= contextAPP;
	private			$_cache			= array();
	protected		$_ro_whichDb	= 'default';

	//------------------------------------------------------------------------
	/**
	* The constructor defaults the context to app
	* and sets the table name
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
	*
	*/
	public function setDb( $which = 'default' )
	{
		$this->_ro_whichDb = $which;
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
				$table = coreTable( 'reg' );
			}

			$c = __CLASS__;
			self::$_instance = new $c( $table );
		}

		return self::$_instance;
		REG_VALUE()->name( 'name' )->value( 'test' )->store();
	}
	//------------------------------------------------------------------------
	/**
	* checks to see if a value is in the memory cache
	* @param tgsfDataSource :: The data source containing the location of the value
	*/
	public function cacheHit( $ds )
	{
		$this->setContextDs( $ds );
		return empty( $this->_cache[$ds->registry_context][$ds->registry_group][$ds->registry_key] ) == false;
	}
	//------------------------------------------------------------------------
	/**
	* Returns a value from the cache
	* @param tgsfDataSource :: The data source containing the location of the value
	*/
	public function cacheFetch( $ds )
	{
		$this->setContextDs( $ds );
		return $this->_cache[$ds->registry_context][$ds->registry_group][$ds->registry_key];
	}
	//------------------------------------------------------------------------
	/**
	* Sets a value in the memory cache
	* @param tgsfDataSource :: The data source containing the location and value
	*/
	public function cacheStore( $ds )
	{
		$this->unserializeDs( $ds );
		$this->_cache[$ds->registry_context][$ds->registry_group][$ds->registry_key] = $ds->registry_value;
	}
	//------------------------------------------------------------------------
	/**
	* Checks to see if a value exists in the database or in the local cache.
	* @param String The key
	* @param String The group
	*/
	public function exists( $registry_key, $registry_group )
	{
		if ( dbm()->tableExists( $this->tableName, $this->_ro_whichDb ) == false )
		{
			return false;
		}

		$ds = tgsfDataSource::factory();
		$ds->setVar( 'registry_group', $registry_group );
		$ds->setVar( 'registry_key', $registry_key );
		
		if ( $this->cacheHit( $ds ) )
		{
			return true;
		}

		$this->setContextDs( $ds );

		return query::factory( $this->_ro_whichDb )
			->count()
			->from( $this->tableName )
			->where( 'registry_key=:registry_key' )
			->and_where( 'registry_group=:registry_group' )
			->and_where( 'registry_context=:registry_context' )

			->bindValue( 'registry_key', $registry_key, ptSTR )
			->bindValue( 'registry_group', $registry_group, ptSTR )
			->bindValue( 'registry_context', $ds->registry_context, ptSTR )
			->exec()
			->fetchColumn( 0 ) > 0;
	}
	//------------------------------------------------------------------------
	public function insert( $ds )
	{
		$this->setContextDs( $ds );
		$this->serializeDs( $ds );

		return query::factory( $this->_ro_whichDb )
			->insert_into( $this->tableName )
			->pt( ptSTR )
			->insert_fields(
				'registry_key',
				'registry_context',
				'registry_value',
				'registry_group',
				'registry_type',
				'registry_list_values',
				'registry_label',
				'registry_desc',
				'registry_help'
			)
			->autoBind( $ds )
			->exec();
			
		$this->cacheStore( $ds );
	}
	//------------------------------------------------------------------------
	public function &update( $ds )
	{
		$this->setContextForDs( $ds );
		$this->serializeDs( $ds );

		query::factory( $this->_ro_whichDb )
			->update( $this->tableName )
			->pt( ptSTR )
			->set( 'registry_value', 'registry_type', 'registry_list_values', 'registry_label', 'registry_desc', 'registry_help' )
			->autoBind( $ds )

			->where( 'registry_key=:registry_key' )
			->and_where( 'registry_group=:registry_group' )
			->and_where( 'registry_context=:registry_context' )

			->bindValue( 'registry_key', $ds->registry_key, ptSTR )
			->bindValue( 'registry_group', $ds->registry_group, ptSTR )
			->bindValue( 'registry_context', $ds->registry_context, ptSTR )
			->exec();
	
		$this->cacheStore( $ds );

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Updates a value for the given key and group
	* @param String The key
	* @param String The group
	* @param String The value to store - if not a string, it will be serialized and stored
	*/
	public function &updateValue( $key, $group, $value )
	{
		$ds = new dbDataSource();
		$ds->set( array(
		    'registry_key'   => $key,
		    'registry_group' => $group,
		    'registry_value' => $value
		) );
		
		$this->setContextDs( $ds );
		$this->serializeDs( $ds );

		query::factory( $this->_ro_whichDb )
			->update( $this->tableName )
			->pt( ptSTR )
			->set( 'registry_value' )
			->autoBind( $ds )

			->where( 'registry_key=:registry_key' )	
			->and_where( 'registry_group=:registry_group' )
			->and_where( 'registry_context=:registry_context' )

			->bindValue( 'registry_key', $ds->registry_key, ptSTR )
			->bindValue( 'registry_group', $ds->registry_group, ptSTR )
			->bindValue( 'registry_context', $ds->registry_context, ptSTR )

			->exec();

		$this->cacheStore( $ds );
		return $this;
	}
	//------------------------------------------------------------------------
	function fetchValue( $key, $group )
	{
		$ds = dbDataSource::factory();
		$this->setContextDs( $ds );
		$ds->setVar( 'registry_group', $group );
		$ds->setVar( 'registry_key', $key );
		
		if( $this->cacheHit( $ds ) )
		{
			return $this->cacheFetch( $ds );
		}

		$val = query::factory( $this->_ro_whichDb )
			->select( 'registry_value' )
            ->from( $this->tableName )
            ->where( 'registry_key=:registry_key' )
            	->bindValue( 'registry_key', $ds->registry_key, ptSTR )

			->where( 'registry_group=:registry_group' )
            	->bindValue( 'registry_group', $ds->registry_group, ptSTR )

			->where( 'registry_context=:registry_context' )
				->bindValue( 'registry_context', $ds->registry_context, ptSTR )

            ->exec()
			->fetchColumn( 0 );

		return $this->unserialize( $val );
	}
	//------------------------------------------------------------------------
	/**
	* Alias to fetchValue
	*/
	public function fetch( $key, $group )
	{
		return $this->fetchValue( $key, $group );
	}
	//------------------------------------------------------------------------
	function fetchCompleteForKey( $key, $group )
	{
		return query::factory( $this->_ro_whichDb )
			->select()
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
		$results = query::factory( $this->_ro_whichDb )
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
		$results = query::factory( $this->_ro_whichDb )
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
		if( $this->willSerialize( $ds->registry_value ) )
		{
			$ds->setVar( 'registry_type', rtSERIALIZED );
			$ds->setVar( 'registry_value', $this->serialize( $ds->registry_value ) );
		}
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
	* Returns true/false if the value needs to be serialized to be stored in a db
	* @param Mixed - The value to test
	*/
	public function willSerialize( $value )
	{
		// if a string we don't need to serialize
		return is_string( $value ) === false;
	}
	//------------------------------------------------------------------------
	/**
	* Serializes everything but strings.
	*/
	public function serialize( $value )
	{
		if ( $this->willSerialize( $value ) )
		{
			return serialize( $value );
		}
		return $value;
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
			$retVal = $value;
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
