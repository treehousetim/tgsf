<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
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
		$this->localfield		= $localField;
		$this->foreignTable		= $foreignTable;
		$this->foreignField		= $foreignField;
		$this->relName			= $relName;
	}
}
