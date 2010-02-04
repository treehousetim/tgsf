<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
/*

/**
* Sample usage in a grid setup
* addGroup()->watchField( 'account_login_id' )->header( 'login_last_name', ', ', 'login_first_name' )->footer( 'Total:', '{sum:field', '' );
*/


// grid group type
enum( 'ggt', array (
	'XROWS',	// groups are defined by x number of rows
	'FIELD',	// groups are defined by 1 or more fields
	'CUSTOM'	// a rendering func is defined
	));

// grid group row type
enum( 'ggrt', array(
	'HEADER',
	'FOOTER'
	));

class tgsfGridGroup extends tgsfHtmlTag
{
	protected $_ro_type				= ggtXROWS;
	protected $_ro_breakRows		= 20;
	protected $_ro_breakField		= null;
	protected $_ro_breakFieldValue	= '';

	protected $_ro_headerFields		= array();
	protected $_ro_footerFields		= array();
	protected $_renderFunc;

	protected $_ro_rowTagHeader		= null;
	protected $_ro_rowTagFooter		= null;
	protected $_ro_cellTagHeader	= null;
	
	protected $_headerRenderFunc		= null; 

	//------------------------------------------------------------------------
	public function __construct()
	{
		$this->_ro_cellTagHeader = new tgsfHtmlTag( 'th' );
		$this->_ro_rowTagHeader = new tgsfHtmlTag( 'tr' );
		$this->_ro_rowTagFooter = clone $this->_ro_rowTagHeader;

		$this->_ro_rowTagHeader->css_class( 'group-header' );
		$this->_ro_rowTagFooter->css_class( 'group-footer' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function groupChanged( &$row, $ix )
	{
		$fields = (array)$row;

		switch ( $this->_ro_type )
		{
		case ggtFIELD:
		case ggtCUSTOM:
			if ( array_key_exists( $this->_ro_breakField, $fields ) === false )
			{
				throw new tgsfGridException( 'Group break field does not exist in the data row: ' . $this->_ro_breakField );
			}

			foreach ( $this->_ro_footerFields as &$footerCell )
			{
				$footerCell->trackGroupValues( $row );
			}

			$retVal=  $ix > 0 && $this->_ro_breakFieldValue != $fields[$this->_ro_breakField];
			$this->_ro_breakFieldValue = $fields[$this->_ro_breakField];
			break;

		case ggtXROWS:
			$retVal = $ix > 0 && $ix % $this->_ro_breakRows == 0;
			break;
		}




		return $retVal;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &breakField( $field )
	{
		$this->_ro_type = ggtFIELD;
		$this->_ro_breakField = $field;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &breakRows( $rowCount )
	{
		$this->_ro_type = ggtXROWS;
		$this->_ro_breakRows = $rowCount;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &renderFuncHeader()
	{
		$vars = func_get_args();
		$callback = null;
		
		if ( count( $vars == 1 ) )
		{
			$callback = $vars[0];
		}
		elseif ( count( $vars == 2 ) )
		{
			$callback = array( $vars[1], $vars[0] );
		}
		
		if ( is_callable( $callback ) == false )
		{
			throw new tgsfException( 'When calling renderFuncHeader on a grid group, you must pass a valid callback.' );
		}
		$this->_ro_type = ggtCUSTOM;
		$this->headerRenderFunc = $callback;

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* This sets the content on the header cell of a group.
	* Group headers are made up of a single cell.
	* pass multiple parameters to this method to set up the content.
	* each parameter can be a field name or static text
	* example: header( 'last_name', ', ', 'first_name' )
	* might output Smith, Joe
	*/
	public function &header()
	{
		$this->_ro_headerFields = func_get_args();
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &footer()
	{
		$flds = func_get_args();

		foreach( $flds as $fld )
		{
			$cell = new tgsfGridGroupFooterCell();
			$fld = trim( $fld );

			if ( starts_with( $fld, '{' ) )
			{
				if ( strpos( $fld, ':' ) === false )
				{
					throw new tgsfGridException( 'When adding a grid group with a footer, footer functions must specify their field using this format. "{function:field"' );
				}

				list( $func, $field ) = explode( ':', trim( $fld, '{}' ) );
				$cell->func( $func, $field );
			}
			else
			{
				$cell->text( $fld );
			}
			$this->_ro_footerFields[] = $cell;
		}
		return $this;
	}
	//------------------------------------------------------------------------
	public function renderHeader( &$table, &$row, $ix )
	{
		switch ( $this->_ro_type )
		{
		case ggtFIELD:
			$this->ggtFieldHeader( $table, $row );
			break;

		case ggtXROWS:
			$this->ggtXRowsHeader( $table, $row );
			break;

		case ggtCUSTOM:
			call_user_func( $this->_headerRenderFunc, $table, $row, $ix );
			break;
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderFooter( &$table, $row, $ix )
	{
		$tr =& $table->addTag( $this->_ro_rowTagFooter );

		$ix = 0;
		foreach ( $this->_ro_footerFields as &$cell )
		{
			$ix++;
			$cell->setContent();
			$cell->css_class( 'col-' . $ix );
			$tr->addTag( $cell );
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function ggtXRowsHeader( &$table, &$row )
	{
		$table->addTag( clone $table->headerRow );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function ggtFieldHeader( &$table, &$row )
	{
		if ( empty( $this->_ro_headerFields ) )
		{
			return;
		}

		$cell = $table->addTag( $this->_ro_rowTagHeader )->addTag( $this->_ro_cellTagHeader );
		$cell->colspan = $table->colCount();

		$fields = (array)$row;

		foreach( $this->_ro_headerFields as $fieldPart )
		{
			if ( array_key_exists( $fieldPart, $fields ) )
			{
				$cell->content( $fields[$fieldPart], APPEND_CONTENT );
			}
			else
			{
				$cell->content( $fieldPart, APPEND_CONTENT );
			}
		}
	}
	//------------------------------------------------------------------------
	/**
	* Assign a callback to a group row
	* @param String The name of the callback method
	* @param Object The object to call the method on
	* @param String [ROW_NORMAL] The cell type to execute the callback on (null, ROW_HEADER, ROW_NORMAL, ROW_FOOTER)
	* @return Object $this
	*/
	public function &onRender( $callBack, &$obj, $cellType = ROW_NORMAL )
	{
		$this->_renderFunc[$cellType] = array( $obj, $callBack );
		return $this;
	}
}