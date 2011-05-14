<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license
for complete licensing information.
*/

//------------------------------------------------------------------------
class foreignKey extends tgsfBase
{
	public $localTable;
	public $relName;
	public $localField;
	public $foreignTable;
	public $foreignField;

	public function __construct( $localTable, $localField, $foreignTable, $foreignField, $relName )
	{
		$this->localTable		= $localTable;
		$this->localField		= $localField;
		$this->foreignTable		= $foreignTable;
		$this->foreignField		= $foreignField;
		$this->relName			= $relName;
	}

	//------------------------------------------------------------------------
	/**
	* Create the ALTER TABLE DDL statement to add this foreign key to the database
	*/
	function generateDDL()
	{
		return "ALTER TABLE {$this->localTable} ADD FOREIGN KEY {$this->relName}({$this->localField}) REFERENCES {$this->foreignTable}({$this->foreignField});";
	}
}
