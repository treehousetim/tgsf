<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/* This code is copyright 2009 by Mass Metal LLC.  ALL RIGHTS RESERVED. */

class tzModel extends tgsfBase
{
	//------------------------------------------------------------------------
	public function __construct()
	{
		$this->tableName = coreTable( 'tz' );
	}
	
	//------------------------------------------------------------------------
	public function insert( $ds )
	{
		throw new tgsfException( 'Not implemented.' );
	}
	
	//------------------------------------------------------------------------
	public function update( $ds )
	{
		throw new tgsfException( 'Not implemented.' );
	}
	
	//------------------------------------------------------------------------
	/**
	 * Fetch a all timezone records
	 * @param Str The order by string (Default: 'tz_id ASC')
	 */
	function fetchAll( $order_by = 'tz_id ASC' )
	{
		$q = new query();

		return $q	->select()
					->from( $this->tableName )
					->order_by( $order_by )
					->exec()
					->fetchAll();
	}
	
	//------------------------------------------------------------------------
	/**
	 * Fetch a single timezone record
	 * @param Int The record id
	 */
	public function fetchById( $tz_id )
	{
		return $q	->select()
					->from( $this->tableName )
					->where( 'tz_id = :tz_id' )
					->bindValue( 'tz_id', $tz_id, ptINT )
					->pluginAction( 'core:tz:fetchById', $tz_id )
					->exec()
					->fetch();
	}

	//------------------------------------------------------------------------	
	function fetchAllForDropDown()
	{
		$tz_all = $this->fetchAll();
		
		$list = array();
		
		foreach( $tz_all as $_tz )
		{
			$list[$_tz->tz_id] = $_tz->tz_area;
		}
		
		unset( $tz_all );
		
		return $list;
	}
}

return new tzModel();