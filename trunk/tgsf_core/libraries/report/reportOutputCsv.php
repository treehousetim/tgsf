<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
-----------------------------------------------------------------------------
| This file is copyright 2012 by TMLA INC ALL RIGHTS RESERVED.
|----------------------------------------------------------------------------
| A report output handler for csv data
|----------------------------------------------------------------------------
| Date			| Person		| Change Description
|----------------------------------------------------------------------------
| 2012-02-27	| TGallagher	| Created
-----------------------------------------------------------------------------
*/

class reportOutputCSV extends tgsfReportOutput
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( $filename = null )
	{
		$this->type = rotCSV;

		if ( $filename == null )
		{
			$filename = 'php://output';
		}

		$this->stream = new SplFileObject( $filename, 'w' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function line( tgsfDataSource $row, $header )
	{
		$report_columns			= $this->report->columns;
		$fields = array();

		foreach( $report_columns as $colType => $columns )
		{
			if ( $colType == ctALL || $colType == ctCSV )
			{
				foreach ( $columns as $column )
				{
					$fields[] = '"' . $column->render( $row, $header, $this->type ) . '"';
				}
			}
		}
		$this->stream->fwrite( implode( ',', $fields ) . "\n" );
	}
	//------------------------------------------------------------------------
	public function header( $formDs )
	{
		$reportSettings		= $this->report->settings;
		$htmlSettings		= $this->report->htmlSettings;
		$columns			= $this->report->columns;
		$this->line( dsFactory::ds(), true ); // output header
				
	}
	//------------------------------------------------------------------------
	public function footer( $formDs )
	{
		// no footers for csv
	}
}