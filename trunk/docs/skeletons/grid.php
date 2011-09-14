<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class exampleGrid extends tgsfGrid
{
	// set this in the controller where you load a grid.
	// example: $myGrid->nameUrl = URL( 'user/view' );
	public $nameUrl = null;

	// set this in the controller - in this example
	// it is the result of a query
	public $account = null;
	
	//------------------------------------------------------------------------
	/**
	* The internal setup function - called when we render
	*/
	protected function _setup()
	{
		// a message that is returned in a <td> that has colspan of the number of defined grid columns.
		$this->emptyMessage = 'No records found';
		
		// the id value on the table tag - <table id="example_grid">
		$this->id( 'example_grid' );
		
		// a switch to turn on/off the header ROW's (don't confuse this with header COLUMNS)
		$this->renderHeaderRow( true );
		
		// an example of a column that combines 2 fields (a first and last name)
		// multiple fields can be specified here.  If a value isn't in the datasource, it is simply included as static text.
		$this->addCol( 'lastname', ', ', 'firstname' )
			->caption( 'Name' )
			
			// a url can be set on a col.  Pass it a tgsfUrl object and an array that maps row field names to url variable names
			->url( $this->$nameUrl, array( 'login_id' => 'i' ) );

		// this creates a mail to link using the content of the cell as the link and the link text
		$this->addCol( 'email' )->caption( 'Email Address' )
			->mailTo( 'email' );
			
		// a column with no datafield assigned to it
		$this->addCol( '' )
			->caption( 'Accounts' )
			
			// marks this column as a header COLUMN (not row)
			// this makes it so it that we never try to output any data fields from a datasource row
			// calling addHeaderRow at least once is required.
			->header()
			
			// This create a th cell object that is later inserted into the table at the correct location
			// The Text is the caption.  In this example, we are marking the header row to span 2 ROWs
			// this means that in the data that's used to populate the grid, we need to have 2 rows for each main record.
			->addHeaderRow( 'Savings' )->rowSpan( 2 )->col // put col on the end of it to switch our context back to the
                                                           // headerCol that we just created this headerRow in
			
			// header ROWs have a col property that allows us to get back to the col it belongs to
			->addHeaderRow( 'Checking' )->col
			// multiple header ROWs are simply output one after the other as we move through the dataset rows
			// keep in mind that we are in the context of a COLUMN, so we are defining a column
			// that has repeating row captions that are output in <th> cells.
			->addHeaderRow( 'IRA' )->col
			->addHeaderRow( 'Money Market' );
			// the above header rows are repeated over and over for the entire data set
		
		$this->addCol( 'balance' )->caption( 'Amount' );
		
		// this is odd and creates a very interesting and probably completely useless grid
		// but this is to show that you can have more than 1 column that has multiple header row definitions in it
		// refer to the comments above for explanations
		$this->addCol( '' )
			->caption( 'Test' )
			->header()
			->addHeaderRow( 'right 1' )->rowSpan( 4 )
			->col->addHeaderRow( 'right 2' )
			->col->addHeaderRow( 'right 3' )
			->col->addHeaderRow( 'right 4' )
			->col->addHeaderRow( 'right 5' );
			
		$this->addCol( 'balance' )->caption( 'Samples' );
	}
	//------------------------------------------------------------------------
	// this is an abstract function that is defined in the base grid class
	// it is not implemented yet.
	protected function _sort()
	{
		
	}
	//------------------------------------------------------------------------
	protected function _loadRows()
	{
		// typically you would load this from a model, but array based rows are allowed too.
		$model = load_model( 'example' );
		$rows = $model->fetchAll();
		return $rows;
	}
}

return new exampleGrid();
