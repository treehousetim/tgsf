<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/*
TODO: Add support for versioned db schemas.
idea is to do the following in extending classes:

$this->version( '0.0.1' );
$this->field( 'example', 'varchar', 255 );

$this->version( '0.0.2' );
$this->field( 'example', 'varchar', 512 ); // increased size in version 0.0.2
$this->field( 'wowzers', 'varchar', 128 );

then we could auto-generate DDL ALTER statements
*/
//------------------------------------------------------------------------
class table extends tgsfBase
{
	private $_name;
	private $_fields = array();
	private $_primaryKey = array();
	private $_foreignKey = array();
	
	//------------------------------------------------------------------------
	/**
	* A constructor - might someday be used.
	*/
	public function __construct( $name, $which = 'default' )
	{
		$this->_name = $name;
		/*
		
		$this->s = new selectQuery( $which );
		$this->i = new insertQuery( $which );
		$this->u = new updateQuery( $which );
		$this->d = new deleteQuery( $which );
		
		$this->s->table =& $this;
		$this->i->table =& $this;
		$this->u->table =& $this;
		$this->d->table =& $this;
		*/
	}
	
	
	//------------------------------------------------------------------------
	/**
	* Adds a field object to the list of primary key fields
	* @param Object The field that is part of the primary key
	*/
	public function primaryKey( &$field )
	{
		if ( $field->primaryKey == false )
		{
			$this->_primaryKey[] =& $field;
			$field->primaryKey = true;
		}
	}

	//------------------------------------------------------------------------


	//------------------------------------------------------------------------
	/**
	* finds and returns a field
	* @param inFieldName is the name of the field being searched for
	*/
	private function _fieldsByName( $inFieldName )
	{
		foreach( $this->_fields as $field )
		{
			if ( $field->name == $inFieldName )
			{
				//php manual says not to return by ref. to increase performance.  correct in this case?
				return $field;

				//or should I
				//return &$field;
			}
		}
	}

	//------------------------------------------------------------------------

	/**
	* Adds a foreign key object to the list of foreign key fields
	* @param String  he field on the local table that will be joined with a foreign key
	* @param String The foreign table name
	* @param String The field name on the foreign table
	* @param String Optional - the relationship name.  One is created automatically if not supplied.
	*/
	public function fk( $localField , $foreignTable , $foreignField, $relName = null )
	{

		if( is_null( $relName ) )
		{
			$relName = $this->_name . '_' . $localField . '_fk';
		}	

		$this->_foreignKey[] = new foreignKey( $this->_name, $localField, $foreignTable, $foreignField, $relName );
	}

	//------------------------------------------------------------------------
	/**
	* Adds a new field to this table
	* @param String The name of the field
	* @param String The type of the field
	* @param String The size of the field (can be x,y for decimal fields) - Omit the enclosing parenthesis
	*/
	public function &field( $name, $type, $size = null )
	{
		$args = func_get_args();
		if ( is_null( $size ) )
		{
			$args = array_slice( $args, 2 );
		}
		else
		{
			$args = array_slice( $args, 3 );
		}
		
		$field =& new field( $name, $type, $size );
		$this->_fields[] =& $field;
		
		$options = array();

		foreach ( $args as $value )
		{
			switch( $value )
			{
			case FIELD_NOT_NULL:
				$options['NOT NULL'] = '';
				break;
				
			case FIELD_AUTO_INC:
				$options['AUTO_INCREMENT'] = '';;
				$this->primaryKey( $field );
				break;
			
			case FIELD_PRIMARY_KEY:
				$this->primaryKey( $field );
				break;
			
			case FIELD_UNSIGNED:
				$options['UNSIGNED'] = '';
				break;
			}
		}

		$field->setOptions( array_keys( $options ) );

		return $field;
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function autoInc()
	{
		$this->field( $this->_name . '_id', 'BIGINT', FIELD_NO_SIZE, FIELD_NOT_NULL, FIELD_AUTO_INC, FIELD_UNSIGNED );
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function generateDDL()
	{
		$out[] = 'CREATE TABLE ' . $this->_name;
		$out[] = '{';
		foreach ( $this->_fields as &$field )
		{
			$int[] = tab(1) . $field->generateDDL();
		}
		if ( count( $this->_primaryKey ) > 0 )
		{
			foreach ( $this->_primaryKey as &$field )
			{
				$pk[] = $field->name;
			}
			
			$int[] = tab(1) . 'PRIMARY KEY(' . implode( ',', $pk ) . ')';
		}
		
		$out[] = implode( ",\n", $int );
		$out[] = '}';
		
		return implode( "\n", $out );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	function insert()
	{
		$query = new insertQuery();
	}
}
