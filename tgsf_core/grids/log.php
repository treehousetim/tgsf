<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/* This code is Copyright (C) by TMLA INC ALL RIGHTS RESERVED. */

class logListGrid extends tgsfGrid
{
	public $startDate = null;
	public $query;
	public $viewUrl;

	//------------------------------------------------------------------------
	/**
	*
	*/
	protected function _setup()
	{
		$this->emptyMessage = 'No entries found';
		$this->id( 'log_list' );

		$this->addCol( 'log_id' )
			->caption( 'ID' )
			->url( $this->viewUrl, array( 'log_id' => 'i' ) );

		$this->addCol( 'log_type' )
			->caption( 'Type' )
			->url( URL('admin/error_view'), array( 'log_id' => 'i' ) );

		$this->addCol( 'log_datetime'  )
			->caption( 'Date/Time' )
			->onRender( 'datetime', $this );
		
		$this->addCol( 'log_severity' )
			->caption( 'Severity' )
			->onRender( 'renderSeverity', $this );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderSeverity( $col, $cell, $row, $cellType )
	{
		$cell->content( ucwords( $cell->content ) );
	}
	//------------------------------------------------------------------------

	protected function _loadRows()
	{
		return $this->query->exec()->fetchAll();
	}
	//------------------------------------------------------------------------
}

return new logListGrid();