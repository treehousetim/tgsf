<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class queryJoin extends tgsfBase
{
	public $type = '';
	public $foreignTable = '';
	public $clause = '';
	public function __construct( $type, $table, $clause )
	{
		$this->type = $type;
		$this->foreignTable = $table;
		$this->clause = $clause;
	}

	//------------------------------------------------------------------------
	/**
	* Generates the SQL for the join.
	*/
	function generate()
	{
		$out = '';
		$out .= ' ' . $this->type . ' ';
		$out .= $this->foreignTable;
		$out .= ' ON ( ';
		$out .= trim( $this->clause, ' )(' ) . ' ) ';

		return $out;
	}
}
