<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class query extends tgsfBase
{
	protected $_conn;
	
	protected $_selectList		= array();
	protected $_fromList		= array();
	protected $_joinList		= array();
	protected $_updateTable		= '';
	protected $_whereList		= array( "1=1" );
	protected $_type			= qtNone;
	protected $_fieldValues 	= array();
	protected $_expectedParams	= array();
	
	public function __construct( $which = 'default' )
	{
		//$this->_conn = dbm()->connect( $which );
	}
	
	//------------------------------------------------------------------------
	/**
	* Resets the query - calling this is just like destroying a query object
	* and creating a new one
	*/
	public function reset()
	{
		$this->_selectList		= array();
		$this->_fromList		= array();
		$this->_joinList		= array();
		$this->_updateTable		= '';
		$this->_whereList		= array( "1=1" );
		$this->_type			= qtNone;
		$this->_fieldValues		= array();
		$this->_expectedParams	= array();
	}
	
	protected function _table()
	{
		return implode( ',', $this->_table );
	}
	
	/**
	* Used in rendering the query
	* this is used to render select queries - the from table list.
	*/
	protected function _from()
	{
		return ' FROM ' . implode( ',', $this->_fromList ) . ' ';
	}
	
	//------------------------------------------------------------------------
	/**
	* Returns a string containing the entire where clause
	*/
	protected function _where()
	{
		return ' WHERE ' . implode( ' ', $this->_whereList ) . ' ';
	}
	
	//------------------------------------------------------------------------
	/**
	* generates the sql for the joins on a select query
	*/
	protected function _join()
	{
		$out = array();
		
		foreach ( $this->_joinList as &$join )
		{
			$out[] = $join->generate();
		}
		return ' ' . implode( ',', $out ) . ' ';
	}
	
	//------------------------------------------------------------------------
	/**
	* generates the select list (fields) when we render the query
	*/
	protected function _select()
	{
		return 'SELECT ' . implode( ',', $this->_selectList ) . ' ';
	}
	
	
	/**
	* generates the update table for rendering the query
	*/
	protected function _update()
	{
		return "UPDATE {$this->_updateTable} ";
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function _set( )
	{
		$tmp = array();
		foreach ( $this->_fieldValues as $item )
		{
			$this->_expectedParams[] = $item;
			$tmp[] = $item . " = :{$item}";
		}
		return "SET " . implode( ',', $tmp );
	}
	
	//------------------------------------------------------------------------
	/**
	* The function used to generate the sql for a select query.
	* Don't call this directly - instead use the generate function.
	* (nobody will yell at you if you do use this though)
	*/
	function generateSelect()
	{	
		return $this->_select() . $this->_from() . $this->_join() . $this->_where();
	}
	
	//------------------------------------------------------------------------
	/**
	* The function used to generate the sql for an update query.
	* Don't call this directly - instead use the generate function
	* (nobody will yell at you if you do use this though.)
	*/
	public function generateUpdate()
	{
		if ( count( $this->_fieldValues ) == 0 )
		{
			throw new tgsfDbException( "Can't Update when no field values have been set." );
		}
		
		return $this->_update() . $this->_set() . $this->_where();
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds an AND section to the where clause
	* @param String The AND section to add.
	*/
	public function &where( $where )
	{
		return $this->and_where( $where );
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds an AND section to the where clause
	* @param String The AND section to add.
	*/
	public function &and_where( $where )
	{
		$this->_whereList[] = 'AND ' . $where;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds an OR section to the WHERE clause
	* @param String The OR section to add.
	*/
	public function &where_or( $where )
	{
		$this->_whereList[] = 'OR ' . $where;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds a table name to the from list for a select query
	*/
	public function &from( $table )
	{
		$this->_fromList[] = $table;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Add fields to the field list for a select query
	* Also marks this query as a select query;
	* @param Mixed Array or String of the fields to include in the query
	*/
	public function &select( $fieldList )
	{
		$this->_type = qtSelect;
		if ( is_array( $fieldList ) )
		{
			$this->_selectList = array_merge( $this->_selectList, $fieldList );
		}
		else
		{
			$this->_selectList[] = $fieldList;
		}

		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds 
	*/
	public function set( $fields )
	{
		//$fields = array();
		//arrayify( $flds, $fields );
		
		$this->_fieldValues = array_merge( $fields, $this->_fieldValues );

		return $this;
	}
	
	
	//------------------------------------------------------------------------
	/**
	* Sets query type to update.  Sets the update query table
	* This is required for update queries.
	*/
	public function &update( $tableName )
	{
		$this->_updateTable = $tableName;
		$this->_type = qtUpdate;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Set a join for a select query
	* @param String The table to join
	* @param String The clause for the join - used in the "ON"
	* @param String The type of join.  Defaults to LEFT OUTER JOIN, but can be any type.
	* No error checking is done to ensure correctness
	*/
	public function &join( $table, $clause, $type = 'LEFT OUTER JOIN' )
	{
		$this->_joinList[] = new selectQueryJoin( $type, $table, $clause );
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Generates the SQL for a query
	*/
	public function generate()
	{
		switch ( $this->_type )
		{
		case qtSelect:
			return $this->generateSelect();
			break;

		case qtUpdate:
			return $this->generateUpdate();
			break;
		}
	}
	
}
