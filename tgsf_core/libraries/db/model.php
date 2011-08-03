<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class model extends table
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( $name, $which = 'default' )
	{
		parent::__construct( $name, $which );
	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	function pkGet()
	{
		$q = new query();
		$q->select( '*' )->from( $this->_ro_name );
		$this->wherePK( $q );
		echo $q->generate();
	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	function wherePK( &$query )
	{
		if ( ! is_object( $query ) || ! $query instanceof query )
		{
			throw new tgsfDbException( 'wherePK expects to receive an object that is an instance of the query class' );
		}

		foreach( $this->_primaryKey as $field )
		{
			$query->and_where( $field->getWhereParamString() );
		}
	}

	//------------------------------------------------------------------------
	/**
	* returns a datasource containing the record fetched by the given id, using {table}_id as the field
	* @param Int The ID of the record
	*/
	public function fetchById( $id, $selectList = '*' )
	{
		$_field = $this->_ro_tableName . '_id';

		$q = new query();

		return $q->select( $selectList )
		         ->from( $this->_ro_tableName )
		         ->where( $_field . '=:' . $_field )
		         ->bindValue( $_field, $id, ptINT )
		         ->exec()
		         ->fetch_ds();
	}

	//------------------------------------------------------------------------

	public function deleteById( $id, $selectList = '*' )
	{
		$_field = $this->_ro_tableName . '_id';

		$q = new query();

		return $q->delete_from( $this->_ro_tableName )
		         ->where( $_field . '=:' . $_field )
		         ->bindValue( $_field, $id, ptINT )
		         ->exec();
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	/**
	* Returns the date for the supplied timestamp for use with queries
	*/
	public function datetime( $ts )
	{
		return gmdate( DT_FORMAT_SQL, $ts );
	}
}
