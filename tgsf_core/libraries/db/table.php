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
define( 'DB_NO_SIZE', '' );
enum( array( 'FIELD_NOT_NULL', 'FIELD_AUTO_INC', 'FIELD_UNSIGNED', 'FIELD_PRIMARY_KEY', 'FIELD_UNIQUE' ) );
echo FIELD_AUTO_INC;
//------------------------------------------------------------------------

class table
{
	private $_name;
	private $_fields = array();
	private $_primaryKey = array();
	
	//------------------------------------------------------------------------
	/**
	* A constructor - might someday be used.
	*/
	public function __construct( $name )
	{
		$this->_name = $name;
	}
	
	
	//------------------------------------------------------------------------
	/**
	* Adds a field object to the list of primary key fields
	* @param Object The field that is part of the primary key
	*/
	public function primaryKey( &$field )
	{
		if ( $field->primaryKey = false )
		{
			$this->_primaryKey[] =& $field;
			$field->primaryKey = true;
		}
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

		foreach ( $args as $value )
		{
			switch( $value )
			{
			case FIELD_NOT_NULL:
				$options[] = 'NOT NULL';
				break;
				
			case FIELD_AUTO_INC:
				$options[] = 'AUTO_INCREMENT';
				$this->primaryKey( $field );
				break;
			
			case FIELD_PRIMARY_KEY:
				$this->primaryKey( $field );
				break;
			
			case FIELD_UNSIGNED:
				$options[] = 'UNSIGNED';
				break;
			}
		}

		$field->setOptions( $options );

		return $field;
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function autoInc()
	{
		$this->field( $this->_name . '_id', 'BIGINT', FIELD_NOT_NULL, FIELD_AUTO_INC, FIELD_UNSIGNED );
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
}