<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class dsFactory
{
	public static function ds()
	{
		return tgsfDataSource::ds_factory();
	}
	public static function db()
	{
		return dbDataSource::db_factory();
	}
	//------------------------------------------------------------------------
	public static function get()
	{
		return clone GET();
	}
	//------------------------------------------------------------------------
	public static function post()
	{
		return clone POST();
	}
}
//------------------------------------------------------------------------
class tgsfDataSource extends tgsfBase
{
	private		$_data 				= array();
	protected	$_type				= dsTypeAPP;
	protected	$_ro_dataPresent	= false;
	protected	$_rows				= array();
	protected	$_ro_multiRow		= false;
	protected	$_ro_strict			= false;
	//------------------------------------------------------------------------
	protected function __construct( $type = dsTypeAPP )
	{
		$this->_type = $type;
	}
	//------------------------------------------------------------------------
	public static function &ds_factory( $type = dsTypeAPP )
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
			if ($this->_ro_strict )
			{
				throw new tgsfException( 'Undefined variable (' . $name . ') on a datasource marked as strict.' );
			}
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
	* Sets the datasource to enforce variable existance
	* @param Bool Enforce strict checking?
	*/
	public function &strict( $value = true )
	{
		$this->_ro_strict = (bool)$value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Typically you should pass arrays to this function.
	* However it is permissible to pass an object that is returned
	* as a query result.  If the type of the passed variable is neither
	* an array nor an object, a tgsfException exception is thrown.
	* @param Mixed (Array/Object) Do not pass a multi-dimensional array
	*/
	public function &set( $in )
	{
		if ( $in instanceof tgsfDataSource )
		{
			$source = $in->dataArray();
		}
		else
		{
			$source = $in;
		}

		$this->_set( $source );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Merges new fields into a datasource, overwriting existing ones with the supplied
	* Passed value can be another datasource, an array, or an object whose properties you want to use
	* @param Mixed The merge data
	*/
	public function &merge( $in )
	{
		if ( $in instanceof tgsfDataSource )
		{
			$source = $in->dataArray();
		}
		else
		{
			if ( is_array( $in ) || is_object( $in )  )
			{
				$source = (array)$in;
			}
			else
			{
				throw new tgsfException( 'When merging, value must be an array, an object or an instance of tgsfDataSource.' );
			}
		}

		$this->_set( array_merge( $this->_data, (array)$source ) );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Remaps a variable from an old name into a new name.  This is non-destructive.
	* Only variables named in the map are touched - existing variables in the datasource
	* are left intact.
	* format is $array['old_field'] = $newField or array( 'old'=>'new')
	*/
	public function &remap( $map )
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

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* This completely resets the data that is available in the datasource.
	* This only resets dsTypeAPP data sources - in other words, no database or GET/POST
	*/
	public function &reset()
	{
		$this->_ro_dataPresent = false;
		$this->_data = array();
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Removes members of the datasource
	* Does nothing if a member doesn't exist
	*/
	public function &remove()
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

		return $this;
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
	* Checks to see if a particular data element exists.  this is different than empty
	* because this will return true even if an element is empty as long as
	* it is present in the datasource
	* @return bool true if exists
	*/
	public function exists( $name )
	{
		return array_key_exists( $name, (array)$this->_data );
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
	* Sets the current date when a field is empty
	* @param String The field name
	* @param String The date format string - defaults to DT_FORMAT_SQL
	*/
	public function &setCurDateOnEmpty( $field, $dateFormat = DT_FORMAT_SQL )
	{
		if ( $this->isEmpty( $field ) )
		{
			$this->setVar( $field, date::UTCcurrentDatetime($dateFormat) );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Manually get a member of the data source
	*/
	public function getVar( $varName, $default = DS_IGNORE_DEFAULT )
	{
		if ( ! isset( $this->_data[$varName] ) )
		{
			return $default != DS_IGNORE_DEFAULT ? $default : '';

		}

		return $this->_data[$varName];
	}
	//------------------------------------------------------------------------
	/**
	* Manually unset a member of the data source
	*/
	public function &unsetVar( $varName )
	{
		if ( array_key_exists( $varName, $this->_data ) )
		{
			unset( $this->_data[$varName] );
		}
		return $this;
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
		return (object)$this->_data;
	}
	//------------------------------------------------------------------------
	/**
	* A shortcut function to get individual values
	*/
	public function _( $varName, $default = DS_IGNORE_DEFAULT )
	{
		return $this->getVar( $varName, $default );
	}
	//------------------------------------------------------------------------
	// multi-row datasource functions
	//------------------------------------------------------------------------
	/**
	* For multi-row datasources returns the number of rows.
	*/
	public function rowCount()
	{
		return count( $this->_rows );
	}
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

		return $this;
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
	/**
	*
	*/
	public function &ipField( $field )
	{
		if ( TGSF_CLI )
		{
			$this->setVar( $field, '127.0.0.1' );
		}
		elseif ( array_key_exists( 'REMOTE_ADDR', $_SERVER ) )
		{
			$this->setVar( $field, $_SERVER['REMOTE_ADDR'] );
		}
		else
		{
			$this->setVar( $field, 'N/A' );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the current Date on a field
	*/
	public function &dateField( $field, $format = DT_FORMAT_SQL )
	{
		$this->setVar( $field, date::UTCcurrentDate( $format ) );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &resetRows()
	{
		reset( $this->_rows );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Outputs debugging information for a datasource
	*/
	public function &debug()
	{
		if ( in_debug_mode() )
		{
			if ( $this->multiRow )
			{
				while ( $this->each() )
				{
					$this->debugDetail();
				}
			}
			else
			{
				$this->debugDetail();
			}
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function debugDetail()
	{
		if ( in_debug_mode() )
		{
			foreach ( $this->_data as $var => $value )
			{
				$det = trim( get_dump( $value ) );

				echo str_pad( $var, 30 ) . $det;
				echo PHP_EOL;
			}
			echo PHP_EOL;
		}
	}
}
