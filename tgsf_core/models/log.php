<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/* This code is Copyright (C) by TMLA INC ALL RIGHTS RESERVED. */

class logModel extends tgsfBase
{
	public $tableName;

	public function __construct()
	{
		$this->tableName = config( 'log_tablename' );
	}

	//------------------------------------------------------------------------
	/**
	* fetch the record using the given id
	* @param Int The record id
	*/
	public function fetchById( $id, $query = null )
	{
		if ( is_null( $query ) || $query instanceOf query == false )
		{
			$query = query::factory();
		}

		return $query
			->select()
			->from( $this->tableName )
			->where( 'log_id = :id' )
			->bindValue( 'id', $id, ptINT )
			->exec()
			->fetch_ds();
	}
	//------------------------------------------------------------------------
	/**
	* Returns a new tgsfPaginateQuery object that can be used to set url values
	*/
	public function getFetchAllQuery()
	{
		$q = new tgsfPaginateQuery();
		$q	->select()
			->from( $this->tableName )
			->order_by( 'log_datetime desc' );
		return $q;

	}
	//------------------------------------------------------------------------
	/**
	* Update the severity of a log record
	* @param tgsfDataSource The datasource with the log_id and log_severity values
	*/
	public function updateSeverity( $ds )
	{
		query::factory()
			->update( $this->tableName )
			->set( 'log_severity' )
			->bindValue( 'log_severity', $ds->log_severity, ptSTR )
			->where( 'log_id = :log_id' )
			->bindValue( 'log_id', $ds->log_id, ptINT )
			->exec();
	}
}

return new logModel();
