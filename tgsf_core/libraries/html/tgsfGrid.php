<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// enum for sort type
// stSTR, strDATE, strMONEY
enum ( 'st', array( 'STR', 'DATE', 'MONEY' ) );

// ROW_HEADER, ROW_NORMAL, ROW_FOOTER
enum ( 'ROW_', array( 'ALL', 'HEADER', 'NORMAL', 'FOOTER' ) );

enum( 'grt', array(
	'HTML_TABLE',
	'CSV'
	));

define( 'CSV_INCLUDE_HEADER', true );

load_library( 'html/tgsfHtmlTag', IS_CORE_LIB );
load_library( 'html/tgsfGridCol', IS_CORE_LIB );
load_library( 'html/tgsfGridRow', IS_CORE_LIB );
load_library( 'html/tgsfGridGroup', IS_CORE_LIB );
load_library( 'html/tgsfGridGroupFooterCell', IS_CORE_LIB );
//------------------------------------------------------------------------
//------------------------------------------------------------------------
/**
* html table
*/
abstract class tgsfGrid extends tgsfHtmlTag
{
	abstract protected function _setup();
	/*abstract*/ protected function _sort(){}
	abstract protected function _loadRows();
	/*abstract*/ protected function _onRow( &$tr, &$row ) {}
	/*abstract*/ protected function _onGroup( &$tr, &$row ) {}

	protected	$_colDefs				= array();
	protected	$_rows					= array();
	protected	$_groups				= array();
	protected	$_currentRow			= null;
	protected	$_footer				= null;
	protected	$_ro_renderHeaderRow	= true;
	protected	$_ro_headerRow			= null;
	public		$emptyMessage			= 'Nothing to show';
	public		$altRowClasses			= array( '', 'alt' );
	private		$_renderSetup			= false;
	public		$timezone				= 'UTC';
	protected	$_ro_renderFormat		= grtHTML_TABLE;
	//------------------------------------------------------------------------
	public function __construct()
	{
		if ( function_exists( 'AUTH' ) ) $this->timezone = AUTH()->getLoginTimeZone();
		$this->css_class( 'grid' );
		parent::__construct( 'table' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderHeaderRow( $render = true )
	{
		$this->_ro_renderHeaderRow = $render;
	}
	//------------------------------------------------------------------------
	/**
	* Adds a column to the grid
	* Pass any number of parameters.  Any literal text should be prefixed by two underscores - otherwise it will be assumed to be a field name.
	* example: addCol( 'last_name', ', ', 'first_name' );
	* @param String The name of the column - should be the name of the property in the row object
	* unless we're providing a render function for a column.
	*/
	public function &addCol()
	{
		$parts = func_get_args();
		$col = new tgsfGridCol( $parts );
		$this->_colDefs[] =& $col;
		return $col;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function colCount()
	{
		return count( $this->_colDefs );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function rowCount()
	{
		return count( $this->_rows );
	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getRow($ix)
	{
		return $this->_rows[$ix];
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &addGroup()
	{
		$item = new tgsfGridGroup();
		$this->_groups[] =& $item;
		return $item;
	}
	//------------------------------------------------------------------------
	/**
	* sets the rows to use when rendering.
	*/
	public function &rows( $rows )
	{
		$this->_rows = $rows;
		return $this;
	}
	//------------------------------------------------------------------------
	public function cellType( &$cell )
	{
		foreach( $this->_colDefs as &$col )
		{
			if ( $cell == $col->headerCell ) return ROW_HEADER;
			if ( $cell == $col->footerCell ) return ROW_FOOTER;
		}

		return ROW_NORMAL;
	}
	//------------------------------------------------------------------------
	/**
	* sets the rows to use when rendering.
	*/
	public function &setFooter( $data )
	{
		if ( $data instanceof tgsfHtmlTag )
		{
			$this->_footer = $data;
		}
		else
		{
			$this->_footer = (object)$data;
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	protected function _renderPossibleGroup( $ix )
	{
		if ( $ix >= count( $this->_rows ) )
		{
			foreach( $this->_groups as &$group )
			{
				$group->renderFooter( $this, $this->_rows[$ix-1], $ix-1 );
			}
			return;
		}

		foreach( $this->_groups as &$group )
		{
			if ( $ix == 0 )
			{
				$group->renderHeader( $this, $this->_rows[$ix], $ix );
			}

			if ( $group->groupChanged( $this->_rows[$ix], $ix ) )
			{
				if ( $ix > 0 )
				{
					$group->renderFooter( $this, $this->_rows[$ix-1], $ix-1 );
				}
				$group->renderHeader( $this, $this->_rows[$ix], $ix );
			}
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderHeader()
	{
		if ( $this->renderHeaderRow === false )
		{
			return;
		}

		$tr = $this->_( 'thead' )->_( 'tr' )->css_class( 'header' );
		$row = null;

		foreach( $this->_colDefs as &$col )
		{
			$col->renderCell( $row, $tr, ROW_HEADER );
		}

		$this->_onRow( $tr, $row );
		$this->_ro_headerRow = clone $tr;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderFooter()
	{
		if ( !is_null($this->_footer) || !empty($this->_footer) )
		{
			if ( $this->_footer instanceof tgsfHtmlTag )
			{
				$this->addTag( $this->_footer );
			}
			else
			{
				$tr = $this->addTag( 'tfoot' )->_( 'tr' );
				$row = (object)$this->_footer;

				foreach( $this->_colDefs as &$col )
				{
					$col->renderCell( $row, $tr, ROW_FOOTER );
				}

				$this->_onRow( $tr, $row );
			}
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getNextRow()
	{
		if ( is_array( $this->_rows ) )
		{
			return next( $this->_rows );
		}

		if ( $this->_rows instanceOf dbDataSource  )
		{

		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderRows()
	{
		if ( count( $this->_rows ) > 0 )
		{
			$rowCnt = count( $this->_rows );

			for ( $ix = 0; $ix < $rowCnt; $ix++ )
			{
				$row = (object)$this->_rows[$ix];
				$this->_currentRow =& $row;
				$this->_renderPossibleGroup( $ix );
				$this->renderRow( $row );
			}
			$this->_renderPossibleGroup( $ix );
		}
		else
		{
			$tr = $this->_( 'tr' );
			$tr->_( 'td' )->content( $this->emptyMessage, false )->addAttribute( 'colspan', count( $this->_colDefs ) )->css_class( 'empty' );
		}
	}
	//------------------------------------------------------------------------
	public function renderRow( &$row )
	{
		$tr = $this->_( 'tr' )->css_class( 'grouplevel' . count( $this->_groups ) );

		foreach( $this->_colDefs as &$col )
		{
			$col->renderCell( $row, $tr, ROW_NORMAL );
		}

		if ( count( $this->altRowClasses ) > 0 )
		{
			$tr->css_class( call_user_func_array( 'alternate', $this->altRowClasses ) );
		}

		$this->_onRow( $tr, $row );
	}
	public function preLoad()
	{
		if ( empty( $this->_rows ) )
		{
			$this->_rows = $this->_loadRows();
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderCSV()
	{
		if ( empty( $this->_rows ) )
		{
			//$this->_rows =
		}
	}
	//------------------------------------------------------------------------
	/**
	* Renders a grid and returns the html as a string.
	* @param ENUM::grt - The grid render type.  Supported types are: grtHTML_TABLE, grtCSV
	*/
	public function render( $format = grtHTML_TABLE, $csvIncludeHeader = false )
	{
		$this->_ro_renderFormat = $format;

		if ( empty( $this->_colDefs ) )
		{
			$this->_setup();
		}

/*
		if ( $this->_ro_renderFormat == grtCSV )
		{
			return $this->renderCSV();
		}*/


		if ( empty( $this->_rows ) )
		{
			$this->_rows = $this->_loadRows();
		}

		if ( $this->_renderSetup === false )
		{
			$this->renderHeader();
			$this->renderRows();
			$this->renderFooter();
			alternate(); // reset for next grid
			$this->_renderSetup = true;
		}

		switch( $format )
		{
		case grtHTML_TABLE:
			return parent::render();
			break;

		case grtCSV:
			$lines = array();

			$childCount = count( $this->_children );

			for ( $ix = 0; $ix < $childCount; $ix++ )
			{
				if ( $csvIncludeHeader === true )
				{
					$child =& $this->_ro_headerRow;
					$ix--;
					$csvIncludeHeader = false;
				}
				else
				{
					$child =& $this->_children[$ix];
				}

				if ( $child->tag == 'tr' )
				{
					$tagChildren = $child->child();
					$fields = array();
					foreach( $tagChildren as $subChild )
					{
						$fields[] = '"' . $subChild->unfilteredContent . '"';
					}
					if ( count( $fields ) )
					{
						$lines[] = implode( ',', $fields );
					}
				}
			}
			return implode( "\n", $lines );
		}
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	// COMMON FORMATTERS
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function abs( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( abs( $cell->content ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Just a wrapper for ucwords()
	*/
	public function ucwords( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{

			$cell->content( ucwords($cell->content) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Mask data such as account numbers, etc
	*/
	public function usa_phone( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( FORMAT()->usa_phone( $cell->content ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Mask data such as account numbers, etc
	*/
	public function obfuscate( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( FORMAT()->obfuscate( $cell->content ) );
		}
	}
	//------------------------------------------------------------------------
	public function raw_date( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			if ( empty( $row->{$col->fieldName} ) || $row->{$col->fieldName} == '0000-00-00 00:00:00' )
			{
				$cell->content( '' );
				return;
			}
			$cell->content( FORMAT()->raw_date( $row->{$col->fieldName}, DT_FORMAT_UI_DATE ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a date
	*/
	public function date( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			if ( empty( $row->{$col->fieldName} ) || $row->{$col->fieldName} == '0000-00-00 00:00:00' )
			{
				$cell->content( '' );
				return;
			}
			$cell->content( FORMAT()->date( $row->{$col->fieldName}, DT_FORMAT_UI_DATE, $this->timezone ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a date time
	*/
	public function datetime( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( FORMAT()->datetime( $row->{$col->fieldName}, $this->timezone ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a currency amount
	*/
	public function currency( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( FORMAT()->currency( $cell->content ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a float amount with 2 decimal points
	*/
	public function float( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( number_format( (float)$cell->content, 2 ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function boolYN( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( FORMAT()->boolToYN( $row->{$col->fieldName} ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function whoisIp( $col, $cell, $row, $cellType )
	{
		if ( trim( $row->{$col->fieldName} ) != '' )
		{
			$cell->content( URL('http://tools.whois.net/whoisbyip/' )->notLocal()->setVar( 'host', $row->{$col->fieldName} )->anchorTag( $row->{$col->fieldName} )->addAttribute( 'target','_blank' )->css_class( 'orange button' ) );
		}
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
}
