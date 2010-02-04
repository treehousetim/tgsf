<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
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
	protected $_ro_tableName	= '';
	protected $_logicalDb	 	= '';
	protected $_engine			= '';

	/**
	* The datasource
	*/
	protected $_ds;

	private $_fields		= array();
	protected $_primaryKey	= array();
	private $_foreignKey	= array();
	private $_fieldsByName	= array();
	private $_idxDefs		= array();
	private $_fieldValues	= array();

	//------------------------------------------------------------------------
	/**
	* A constructor - Takes the table name and logical connection name (defaults to default)
	* @param String The name of the table
	* @param String The logical database connection
	*/
	public function __construct( $name, $which = 'default' )
	{
		$this->_ro_tableName = $name;
		$this->_logicalDb = $which;
	}

	/**
	* Used for getting field values
	*/
	public function __get( $name )
	{
		$retVal = null;

		if ( isset( $this->_fieldValues[$name] ) )
		{
			$retVal = $this->_fieldValues[$name];
		}
		else
		{
			$retVal = parent::__get( $name );
		}

		return $retVal;
	}

	//------------------------------------------------------------------------
	/**
	* Used for setting field values
	*/
	public function __set( $name, $val )
	{
		$this->_fieldValues[$name] = $val;
	}

	//------------------------------------------------------------------------
	/**
	* Adds a field object to the list of primary key fields
	* @param Object The field that is part of the primary key
	*/
	private function _primaryKey( &$field )
	{
		if ( $field->primaryKey == false )
		{
			$this->_primaryKey[] =& $field;
			$field->primaryKey = true;
		}
	}

	//------------------------------------------------------------------------
	/**
	* Adds a simple index to this table
	* @param String The field name
	* @param Int The width to index
	*/
	function &idx( $field, $width = null )
	{
		$idx = new dbIndex( $this->_ro_tableName, $field );
		$this->_idxDefs[] =& $idx;

		return $idx;
	}

	//------------------------------------------------------------------------
	/**
	* Adds an index object to the table
	* @param Object The index object to add
	*/
	function addIdx( &$idx )
	{
		$this->_idxDefs[] = &$idx;
	}

	//------------------------------------------------------------------------
	/**
	* Adds a foreign key object to the list of foreign key fields
	* @param String The field on the local table that will be joined with a foreign key
	* @param String The foreign table name
	* @param String The field name on the foreign table
	* @param String Optional - the relationship name.  One is created automatically if not supplied.
	*/
	public function fk( $localField , $foreignTable , $foreignField, $relName = null )
	{
		if ( is_null( $relName ) )
		{
			$relName = $this->_ro_tableName . '_' . $localField . '_fk';
		}

		$this->_foreignKey[] = new foreignKey( $this->_ro_tableName, $localField, $foreignTable, $foreignField, $relName );
	}

	//------------------------------------------------------------------------
	/**
	* Adds a new field to this table
	* @param String The name of the field
	* @param String The type of the field
	* @param Mixed(String or Int) The size of the field (can be x,y for decimal fields) - Omit the enclosing parenthesis.  If the field has no size, use FIELD_NO_SIZE
	*/
	public function &field( $name, $type, $size = FIELD_NO_SIZE )
	{
		$args = func_get_args();
		$args = array_slice( $args, 3 );

		$field = new field( $name, $type, $size );
		$this->_fields[] =& $field;
		$this->_fieldsByName[$name] =& $field;

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
				$this->_primaryKey( $field );
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
	* Creates an auto inc field using the table name_id as the field name.
	*/
	function autoInc()
	{
		$this->field( $this->_ro_tableName . '_id', 'BIGINT', FIELD_NO_SIZE, FIELD_NOT_NULL, FIELD_AUTO_INC, FIELD_UNSIGNED );
	}

	//------------------------------------------------------------------------
	/**
	* Generates the DDL for the entire table - create table, alter table add foreign key, and create index
	*/
	function generateDDL()
	{
		$out[] = 'CREATE TABLE ' . $this->_ro_tableName;
		$out[] = '(';
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

		if ( $this->_engine != '' )
		{
			$out[] = ") ENGINE={$this->_engine};\n";
		}
		else
		{
			$out[] = ");\n";
		}

		$tableDDL = implode( "\n", $out );

		$out = array(); // recycle this variable now that we're done with it

		foreach ( $this->_foreignKey as &$fk )
		{
			$out[] = $fk->generateDDL();
		}
		$tableDDL .= implode( "\n", $out ) . "\n";

		$out = array();
		foreach ( $this->_idxDefs as &$idx )
		{
			$out[] = $idx->generateDDL();
		}

		$tableDDL .= implode( "\n", $out ) . "\n";

		return $tableDDL;
	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	function insert()
	{
		throw new tgsfDbException( 'Not Implemented' );
	}
}
