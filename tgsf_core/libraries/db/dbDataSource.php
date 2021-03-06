<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class dbDataSource extends tgsfDataSource
{
	protected $_tableList = array();
	protected $_ro_defaultFieldName = null;
	private $_query;

	//------------------------------------------------------------------------
	/**
	* Sets the type to dsTypeDB
	*/
	public function __construct()
	{
		parent::__construct( dsTypeDB );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __destruct()
	{
		unset( $this->_query );
		unset( $this->_tableList );
	}
	//------------------------------------------------------------------------
	/**
	* Allows a datasource to provide a default value when used as a string.
	* @param String The member field name
	*/
	public function &defaultField( $fieldName )
	{
		$this->_ro_defaultFieldName = $fieldName;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* If we have a default field, we return its value from this
	*/
	public function __toString()
	{
		if ( $this->_ro_defaultFieldName !== null )
		{
			return $this->{$this->_ro_defaultFieldName};
		}
	}
	//------------------------------------------------------------------------
	public static function &db_factory()
	{
		$c = __CLASS__;
		$instance = new $c();
		return $instance;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the table list for this datasource - used later by $this->hasTable
	* @param Array/String The table(s) that are the source of the data in this ds
	*/
	public function &setTables( $list )
	{
		$this->_tableList = (array)$list;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if this datasource has fields in it from the specified table
	* @param String The table name
	*/
	public function hasTable( $table )
	{
		return in_array( $table, $this->_tableList );
	}
	//------------------------------------------------------------------------
	/**
	* Typically you should pass arrays to this function.
	* However it is permissible to pass an object that is returned
	* as a query result.  If the type of the passed variable is neither
	* an array nor an object, a tgsfException exception is thrown.
	* @param Mixed (Array/Object) Do not pass a multi-dimensional array
	*/
	public function &set( $source )
	{
		// We reset the table list if set is called
		$this->_tableList = array();
		return parent::set( $source );
	}
	//------------------------------------------------------------------------
	/**
	* Merge data
	*/
	public function &merge( $in )
	{
		if ( $in instanceof tgsfDbDataSource )
		{
			$this->_tableList = array_merge( $this->_tableList, $in->getTableList() );
		}
		return parent::merge( $in );
	}
	//------------------------------------------------------------------------
	/**
	* Returns the array of table names associated with this datasource
	*/
	public function getTableList()
	{
		return $this->_tableList;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a query object on this data source.  This is typically called from within the query object
	* @param object::query An executed select query
	*/
	public function &setQuery( &$query )
	{
		$this->_query =& $query;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetch()
	{
		if ( $this->_query )
		{
			$row = $this->_query->fetch();
			if ( $row != false )
			{
				$this->set( $row );
				return true;
			}
		}

		return false;
	}
}
