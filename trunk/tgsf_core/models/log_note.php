<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2011 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class logNoteModel extends tgsfBase
{
	protected $_tableName;

	public function __construct()
	{
		$this->_tableName = config( 'log_note_tablename' );

		if ( $this->_tableName == '' )
		{
			$this->_tableName = 'log_note';
		}
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
			->from( $this->_tableName )
			->where( 'log_note_id = :id' )
			->bindValue( 'id', $id, ptINT )
			->exec()
			->fetch_ds();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetchForLog( $log_id, $query = null )
	{
		if ( is_null( $query ) || $query instanceOf query == false )
		{
			$query = query::factory();
		}

		return $query
			->select()
			->from( $this->_tableName )
			->where( 'log_note_log_id = :id' )
			->order_by( 'log_note_datetime asc' )
			->bindValue( 'id', $log_id, ptINT )
			->exec()
			->fetchAll();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getFetchAllQuery()
	{
		$q = new tgsfPaginateQuery();
		$q	->select()
			->from( $this->_tableName )
			->order_by( 'log_datetime desc' );
		return $q;

	}
	//------------------------------------------------------------------------
	/**
	* inserts a new entity record for the given data source
	* @param Object::DataSource The datasource containing the values
	*/
	public function insert( $ds )
	{
		$ds->setVar( 'log_note_datetime', date::UTCcurrentDatetime() );
		$ds->setVar( 'log_note_user_id', 0 );

		if ( function_exists( 'AUTH_is_configured' ) && AUTH_is_configured() && AUTH()->loggedIn )
		{
			$ds->setVar( 'log_note_user_id', AUTH()->getLoginId() );
		}

		return query::factory()
			->insert_into( $this->_tableName )
			->pt( ptSTR )
				->insert_fields( 'log_note_content'	)

			->pt( ptINT )
				->insert_fields( 'log_note_log_id', 'log_note_user_id' )

			->pt( ptDATETIME )
				->insert_fields( 'log_note_datetime' )

			->autoBind( $ds )
			->exec()
			->lastInsertId;
	}
}

return new logNoteModel();
