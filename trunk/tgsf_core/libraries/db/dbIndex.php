<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license
for complete licensing information.
*/
//------------------------------------------------------------------------
// An index column class
//------------------------------------------------------------------------
class dbIndexCol extends tgsfBase
{
	public $colName		= '';
	public $width 		= null;
	public $direction	= '';

	//------------------------------------------------------------------------
	/**
	* The constructor
	* @param String The name of the column
	* @param Int The width to use for the index
	* @param String The direction of the index (asc/desc) (optional)
	*/
	public function __construct( $name, $width, $direction = '' )
	{
		$this->colName = $name;
		$this->width = $width;
	}

	//------------------------------------------------------------------------
	/**
	* Generates the inner DDL for this one column
	*/
	function generateDDL()
	{
		$out = $this->colName;

		if ( $this->width != '' && ! is_null( $this->width ) )
		{
			$out .= '(' . $this->width . ') ';
		}

		$out .= $this->direction . ' ';

		return $out;
	}
}



//------------------------------------------------------------------------
// Main Index Class
//------------------------------------------------------------------------
class dbIndex extends tgsfBase
{
	protected $_exactDDL	= '';
	public $relName			= '';
	public $tableName		= '';
	public $direction		= '';
	protected $_cols		= array();
	public $type			= '';


	/**
	* The constructor - table name is required - all others can be left out.
	* supply all for a simple index, or call $this->simple()
	* @param String The table name.
	* @param String The field name.
	* @param String The direction
	* @param String The relationship name - leave empty to create a relationship name by default (field name is required for this)
	* @see simple()
	*/
	public function __construct( $tableName, $fieldName = null, $direction = null, $relName = null )
	{
		$this->tableName = $tableName;
		if ( ! is_null( $fieldName ) )
		{
			$this->simple( $tableName, $fieldName, $direction, $relName );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Adds a column to the index
	* @param String The field name of the column to add
	* @param Int The width to index in this column
	* @param String The direction of the index - ascending or descending (asc,desc) - leave null for database default (mysql default is asc)
	*/
	public function addCol( $name, $width = null, $direction = null )
	{
		$this->_cols[] = new dbIndexCol( $name, $width, $direction );
	}
	//------------------------------------------------------------------------
	/**
	* Creates a simple index
	*/
	function simple( $tableName, $fieldName, $direction, $relName = null )
	{
		if ( is_null( $relName ) )
		{
			if ( starts_with( $fieldName, $tableName ) )
			{
				$relName = $fieldName . '_idx';
			}
			else
			{
				$relName = $tableName . '_' . $fieldName . '_idx';
			}
		}

		$this->tableName	= $tableName;
		$this->relName		= $relName;
		$this->direction	= $direction;
		$this->addCol( $fieldName );
	}

	//------------------------------------------------------------------------
	/**
	* Gives the ability to store the exact DDL statement used to create the index.
	* @param String The exact DDL string
	*/
	function exact( $exactDDL )
	{
		$this->_exactDDL = $exactDDL;
	}

	//------------------------------------------------------------------------
	/**
	* Create the ALTER TABLE DDL statement to add this index to the database
	*/
	function generateDDL()
	{
		if ( $this->_exactDDL != '' )
		{
			return $this->_exactDDL;
		}

		$out = "CREATE INDEX ";
		$out .= "{$this->relName} ON {$this->tableName}";

		$lines = array();
		foreach ( $this->_cols as & $col )
		{
			$lines[] = $col->generateDDL();
		}

		$out .= '(' . implode( ',', $lines ) . ');';

		return  $out;
	}

}
