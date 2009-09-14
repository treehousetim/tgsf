<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

enum( 'dsType',
	array(
		'NONE',
		'DB',
		'POST',
		'GET'
		)
	);

class tgsfDatasource extends tgsfBase
{
	private $_data = array();
	private $_type = dsTypeNONE;
	
	//------------------------------------------------------------------------
	
	protected function __construct( $type = dsTypeNONE )
	{
		$this->_type = $type;
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
		$this->_data = (array) $source; // this should convert objects into an array.
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function remap( $map )
	{
		if ( ! is_array( $map ) )
		{
			throw new tgsfException( 'When remapping datasources, the map must be an array' );
		}
		
		$data = $this->_data;
		$this->_data = array();
		
		foreach ( $map as $old => $new )
		{
			$this->_data[$new] = $data[$old];
		}
	}
	//------------------------------------------------------------------------
	/**
	* checks to see if a particular data element is empty, using the php function empty.
	* @param String The name of the data element
	*/
	public function isEmpty( $name )
	{
		return empty( $this->_data[$name] );
	}

	/**
	* Returns true/false if the internal type is the same as the supplied argument
	* @param ENUM::dsType The type to compare to the internal type
	*/
	public function is( $type )
	{
		return (bool)$this->_type === $type;
	}
	
	/*
	//------------------------------------------------------------------------
	/ * *
	* The setter to store values in an array.
	* /
	function __set( $name, $value )
	{
		$this->_data[$name] = $value;
	}
	*/
	
	//------------------------------------------------------------------------
	/**
	* Manually set a member of the data source
	*/
	public function setVar( $varName, $varValue )
	{
		$this->_data[$varName] = $varValue;
	}

	//------------------------------------------------------------------------
	/**
	* Manually get a member of the data source
	*/
	public function getVar( $varName )
	{
		return $this->_data[$varName];
	}
	
	//------------------------------------------------------------------------
	/**
	* Returns the datasource data as an array
	*/
	function dataArray()
	{
		return (array)$this->_data;
	}

	//------------------------------------------------------------------------
	/**
	* Returns the datasource data as an object.
	*/
	function dataObject()
	{
		return (object) $this->_data;
	}

	/**
	* A shortcut function to get individual values
	*/
	public function _( $name )
	{
		if ( ! isset( $this->_data[$name] ) )
		{
			return '';

		}

		return $this->_data[$name];
	}
}
