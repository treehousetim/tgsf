<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
class superMetaModel extends tgsfBase
{
	protected $_ro_tableName;
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( )
	{
		$this->tableName = coreTable( 'supermeta' );
	}
	//------------------------------------------------------------------------
	/**
	* Joins the super meta table to a query
	* Aliases the table and all the table
	*/
	public function joinToQuery( $q, $table, $idField )
	{
		$alias = $table . '_meta';
		
		$q->select( $alias . '.supermeta_name as ' . $alias . '_name' );
		$q->select( $alias . '.supermeta_value as ' . $alias . '_value' );
		$q->join( $this->_ro_tableName . ' ' $alias, $alias . '.supermeta_record_id = ' $table . '.' . $idField );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	protected function _getQuery( $table )
	{
		return query::factory()
		->select( $this->tableName . '.*' )
		->from( $this->tableName )
		->where( 'supermeta_table=:supermeta_table' )
		->bindValue( 'supermeta_table', $table, ptSTR );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetchAllForTableAndId( $table, $id )
	{
		return $this->_getQuery( $table )
		->where( 'supermeta_record_id=:id' )
		->bindValue( 'id', $id, ptSTR )
		->exec();
		->fetchAll();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetch( $table, $id, $name )
	{
		return $this->_getQuery( $table )
		->where( 'supermeta_record_id=:id' )
		->bindValue( 'id', $id, ptSTR )
		->where( 'supermeta_name=:name' )
		->bindValue( 'name', $name, ptSTR )
		->exec()
		->fetch();
	}
	//------------------------------------------------------------------------
	/**
	* Stores (inserts or updates) the meta values for the record
	*@param String The table name
	*@param String/Int The record ID to retrieve records for
	*@param Array Associative array $ar['name'] = 'value' to store
	*/
	public function store( $table, $id, $values )
	{
		$iq = query::factory()
		->insert_into( $this->tableName )
		->pt( ptSTR )
		->insert_fields( 'supermeta_table', 'supermeta_record_id', 'supermeta_name' )
		->prepare();
		
		$uq = query::factory()
		->update( $this->tableName )
		->set( '' )
	}
}

return new superMetaModel();