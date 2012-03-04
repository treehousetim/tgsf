<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
-----------------------------------------------------------------------------
| This file is copyright 2012 by TMLA INC ALL RIGHTS RESERVED.
|----------------------------------------------------------------------------
| 
|----------------------------------------------------------------------------
| Date			| Person		| Change Description
|----------------------------------------------------------------------------
| 2012-02-06	| 	| Created
-----------------------------------------------------------------------------
*/
abstract class tgsfReportBase extends tgsfBase
{
	public $title = '';

	public $form = null;
	public $paramDs = null;
	public $query = null;
	protected $_outputs = array();
	protected $_ro_columns = array();
	protected $rows;
	protected $_ro_settings;
	protected $_ro_htmlSettings;
	abstract protected function reportSetup( $ds );
	abstract protected function setupQuery( $ds );

	//------------------------------------------------------------------------
	public function __construct()
	{
		$this->_ro_settings = dsFactory::ds();
		$this->_ro_htmlSettings = dsFactory::ds();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function id( $id )
	{
		$this->_ro_htmlSettings->setVar( 'id', $id );
	}
	//------------------------------------------------------------------------
	/**
	* Sets or adds to the css class assigned to this report - assigned to both the inner and outer report grid
	*/
	public function cssClass( $class )
	{
		if ( ! $this->_ro_htmlSettings->isEmpty( 'class' ) )
		{
			$class = $this->_ro_htmlSettings->class . ' ' . $class;
		}

		$this->_ro_htmlSettings->setVar( 'class', $class );
	}
	//------------------------------------------------------------------------
	public function addCol( tgsfReportColBase $object, $type = ctALL )
	{
		$this->_ro_columns[$type][] = $object;
	}
	//------------------------------------------------------------------------
	public function addOutput( tgsfReportOutput $output )
	{
		$output->report = $this;
		$this->_outputs[] = $output;
	}
	//------------------------------------------------------------------------
	public function render()
	{
		if ( $this->form instanceOf tgsfForm )
		{
			$ds = $this->form->ds;
		}
		elseif ( $this->paramDs )
		{
			$ds = $this->paramDs;
		}
		else
		{
			$ds = dsFactory::ds();
		}

		$this->reportSetup( $ds );
		$this->query = $this->setupQuery( $ds );
		$this->rows = $this->query->dbDataSource();

		if ( $this->rows == false )
		{
			throw new tgsfException( 'Unable to load data source after execQuery in report.' );
		}
		$rows = $this->rows;

		//------------------------------------------------------------------------
		// begin output
		//------------------------------------------------------------------------
		foreach( $this->_outputs as $handler )
		{
			$handler->header( $ds );
		}


		while ( $rows->fetch() )
		{
			foreach( $this->_outputs as $handler )
			{
				$handler->line( $rows, false ); // false = not header row
			}
		}

		foreach( $this->_outputs as $handler )
		{
			$handler->footer( $ds );
		}
	}
}
//------------------------------------------------------------------------
abstract class tgsfReportOutput
{
	public $type;
	public $stream;
	public $report;
	abstract public function header( $formDs );
	abstract public function footer( $formDs );
	abstract public function line( tgsfDataSource $row, $header );
}
//------------------------------------------------------------------------
