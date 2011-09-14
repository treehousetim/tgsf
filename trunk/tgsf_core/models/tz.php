<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class tzModel extends tgsfBase
{
	protected $_ro_tableName = '';
	//------------------------------------------------------------------------
	public function __construct()
	{
		$this->_ro_tableName = coreTable( 'tz' );
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
		return query::factory()
			->select()
			->from( $this->_ro_tableName )
			->order_by( $order_by )
			->pluginAction( 'core:tz:fetchAll' )
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
		return query::factory()
			->select()
			->from( $this->_ro_tableName )
			->where( 'tz_id = :tz_id' )
			->bindValue( 'tz_id', $tz_id, ptINT )
			->pluginAction( 'core:tz:fetchById', array( 'tz_id' => $tz_id ) )
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