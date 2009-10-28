<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

enum( 'dsType',
	array(
		'DB',
		'POST',
		'GET',
		'APP',
		'CLI'
		)
	);

class tgsfDataSource extends tgsfBase
{
	private		$_data 				= array();
	protected	$_type				= dsTypeAPP;
	protected	$_ro_dataPresent	= false;
	protected	$_rows				= array();
	protected	$_ro_multiRow		= false;
	//------------------------------------------------------------------------
	protected function __construct( $type = dsTypeAPP )
	{
		$this->_type = $type;
	}
	//------------------------------------------------------------------------
	public static function &factory( $type = dsTypeAPP )
	{
		$c = __CLASS__;
		$instance = new $c( $type );
		return $instance;
	}
	//------------------------------------------------------------------------
	public function __get( $name )
	{
		if ( array_key_exists( $name, $this->_data ) )
		{
			return $this->_data[$name];
		}
		try
		{
			return parent::__get( $name );
		}
		catch( Exception $e )
		{

		}

		return '';
	}
	//------------------------------------------------------------------------
	protected function _set( $source )
	{
		// all internal storage will be arrays since they are self contained, and have all their information (key,value)
		// when making the data available to views, call ->dataObject() and we'll do a typecast to object on an array
		// which, according to the php manual, will return a stdObject() with keys as properties with the corresponding values

		$sourceIsObject	= is_object( $source );
		$sourceIsArray	= is_array( $source );

		if ( $sourceIsObject === false && $sourceIsArray === false )
		{
			throw new tgsfException( 'Datasource must be either an array or an object.' );
		}

		// cast all objects to array here - as long as we don't have funky property names
		// this should work fine.  that's why there is the warning in the doc block
		$this->_ro_dataPresent	= count( (array) $source ) > 0;
		$this->_data = (array) $source; // this should convert objects into an array.
	}
	//------------------------------------------------------------------------
	/**
	* If we clone a datasource, we convert its type into an application datasource.
	*/
	public function __clone()
	{
		$this->_type = dsTypeAPP;
	}
	//------------------------------------------------------------------------
	/**
	* Typically you should pass arrays to this function.
	* However it is permissible to pass an object that is returned
	* as a query result.  If the type of the passed variable is neither
	* an array nor an object, a tgsfException exception is thrown.
	* @param Mixed (Array/Object) Do not pass a multi-dimensional array
	*/
	public function set( $source )
	{
		$this->_set( $source );
	}
	//------------------------------------------------------------------------
	/**
	* Remaps a variable from an old name into a new name.  This is non-destructive.
	* Only variables named in the map are touched - existing variables in the datasource
	* are left intact.
	*/
	public function remap( $map )
	{
		if ( ! is_array( $map ) )
		{
			throw new tgsfException( 'When remapping datasources, the map must be an array' );
		}

		$data = $this->_data;

		foreach ( $map as $old => $new )
		{
			$this->_data[$new] = $data[$old];
			unset( $this->_data[$old] );
		}
	}
	//------------------------------------------------------------------------
	/**
	* This completely resets the data that is available in the datasource.
	* This only resets dsTypeAPP data sources - in other words, no database or GET/POST
	*/
	public function reset()
	{
		$this->_ro_dataPresent = false;
		$this->_data = array();
	}
	//------------------------------------------------------------------------
	/**
	* Removes members of the datasource
	* Does nothing if a member doesn't exist
	*/
	public function remove()
	{
		if ( $this->_type == dsTypeAPP || $this->_type == dsTypeDB )
		{
			if ( func_num_args() > 0 )
			{
				$args = func_get_args();

				foreach ( $args as $name )
				{
					if ( array_key_exists( $name, $this->_data ) )
					{
						unset( $this->_data[$name] );
					}
				}
			}
		}
		else
		{
			throw new tgsfException( 'Only Application and Database datasources allow members to be removed.' );
		}
	}
	//------------------------------------------------------------------------
	/**
	* checks to see if a particular data element is empty, using the php construct empty.
	* @param String The name of the data element
	*/
	public function isEmpty( $name )
	{
		return empty( $this->_data[$name] );
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if the internal type is the same as the supplied argument
	* @param ENUM::dsType The type to compare to the internal type
	*/
	public function is( $type )
	{
		return (bool)$this->_type === $type;
	}
	//------------------------------------------------------------------------
	/**
	* Manually set a member of the data source
	*/
	public function &setVar( $varName, $varValue )
	{
		$this->_data[$varName] = $varValue;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Manually get a member of the data source
	*/
	public function getVar( $varName, $default = 'tgsfignoredefault' )
	{
		if ( ! isset( $this->_data[$varName] ) )
		{
			return $default!='tgsfignoredefault'?$default:'';

		}

		return $this->_data[$varName];
	}
	//------------------------------------------------------------------------
	/**
	* Returns the datasource data as an array
	*/
	public function dataArray()
	{
		return (array)$this->_data;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the datasource data as an object.
	*/
	public function dataObject()
	{
		return (object) $this->_data;
	}
	//------------------------------------------------------------------------
	/**
	* A shortcut function to get individual values
	*/
	public function _( $varName, $default = 'tgsfignoredefault' )
	{
		return $this->getVar( $varName, $default );
	}
	//------------------------------------------------------------------------
	// multi-row datasource functions
	//------------------------------------------------------------------------
	/**
	* Sets the rows for a multi-row datasource.  Also turns the multiRow read only parameter to true
	* @param Array
	*/
	public function setRows( $rows )
	{
		if ( $this->_type != dsTypeAPP && $this->_type != dsTypeDB )
		{
			throw new tgsfException( 'Only Application and Database DataSources may be multi-row' );
		}

		$this->_ro_dataPresent	= true;
		$this->_ro_multiRow		= true;
		$this->_rows			= $rows;
	}

	//------------------------------------------------------------------------
	public function each()
	{
		if ( $this->_ro_multiRow )
		{
			$er = each( $this->_rows );
		}

		if ( $er === false )
		{
			return false;
		}

		list( $key, $item ) = $er;

		$this->set( $item );

		return true;
	}
	//------------------------------------------------------------------------
	public function resetRows()
	{
		reset( $this->_rows );
	}

}
