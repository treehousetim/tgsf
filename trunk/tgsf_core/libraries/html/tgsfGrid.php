<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
// enum for sort type
enum ( 'st', array( 'STR', 'DATE', 'MONEY' ) );
define( 'HEADER_ROW', true );
define( 'NON_HEADER_ROW', false );

load_library( 'html/tgsfHtmlTag', IS_CORE_LIB );

class tgsfGridCol extends tgsfHtmlTag
{
	protected $_ro_sortable		= false;
	protected $_ro_sort_type	= stSTR;
	protected $_ro_caption		= '';
	protected $_ro_fieldName	= '';
	protected $_ro_headerCell	= null;
	
	protected $_renderFunc		= null;
	//------------------------------------------------------------------------
	public function __construct( $name )
	{
		$this->_ro_fieldName = $name;
	}
	//------------------------------------------------------------------------
	/**
	* Sets this column to be sortable
	*/
	public function sortable( $type = stSTR )
	{
		$this->ro_sort_type = $type;
		$this->_ro_sortable = true;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the caption on this column object.  If the ID is not set on the
	* column, this sets the ID to clean_text( $caption )
	* @param String The caption
	* @return Object $this
	*/
	public function &caption( $caption )
	{
		$this->_ro_caption = $caption;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &renderFunc( $callBack )
	{
		$this->_renderFunc = $callBack;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderCell( &$row, $tr, $header = false )
	{
		if ( $header === true )
		{
			$cell = $tr->_( 'th' );
			$this->_ro_headerCell =& $cell;
		}
		else
		{
			$cell = $tr->_( 'td' );
		}
		
		$cell->setAttributes( $this->attributes );

		if ( $this->_renderFunc !== null )
		{
			call_user_func( $this->_renderFunc, $this, $cell, $row );
		}
		else
		{
			if ( $header === true )
			{
				$cell->content( $this->_ro_caption );
			}
			else
			{
				if ( $row === null )
				{
					throw new tgsfGridException( 'Grid Row may not be null for non-header rows.' );
				}
				$cell->content( $row->{$this->_ro_fieldName} );
			}
		}
		
		if ( ! $cell->content && !empty($this->empty_message) )
		{
			$cell->content( $this->empty_message );
		}
	}
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
/**
* html table
*/
abstract class tgsfGrid extends tgsfHtmlTag
{
	abstract protected function _setup();
	abstract protected function _sort();
	abstract protected function _loadRows();
	/*abstract*/ protected function _onRow( &$tr, &$row ) {}
	
	protected	$_cols			= array();
	protected	$_rows			= array();
	protected	$_currentRow	= null;
	protected	$_footer		= null;
	public		$emptyMessage	= 'Nothing to show';
	public		$altRowClasses	= array( '', 'alt' );
	//------------------------------------------------------------------------
	final public function __construct()
	{
		parent::__construct( 'table' );
		$this->_setup();
	}
	//------------------------------------------------------------------------
	/**
	* Adds a column to the grid
	* @param String The name of the column - should be the name of the property in the row object
	* unless we're providing a render function for a column.
	*/
	public function &addCol( $name )
	{
		$col = new tgsfGridCol( $name );
		$this->_cols[] =& $col;
		return $col;
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
	/**
	*
	*/
	public function renderHeader()
	{
		$tr = $this->_( 'thead' )->_( 'tr' );
		$row = null;

		foreach( $this->_cols as $col )
		{
			$col->renderCell( $row, $tr, HEADER_ROW );
		}
		
		$this->_onRow( $tr, $row );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderFooter()
	{
		//$table->
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderRows()
	{
		if ( count( $this->_rows ) > 0 )
		{
			foreach( $this->_rows as $row )
			{
				$row = (object)$row;
				$this->_currentRow =& $row;
				$this->renderRow( $row );	
			}
		}
		else
		{
			$tr = $this->_( 'tr' );
			$tr->_( 'td' )->content( $this->emptyMessage )->addAttribute( 'colspan', count( $this->_cols ) )->css_class( 'empty' );
			
		}
	}
	//------------------------------------------------------------------------
	public function renderRow( &$row )
	{
		$tr = $this->_( 'tr' );

		foreach( $this->_cols as $col )
		{
			$col->renderCell( $row, $tr, NON_HEADER_ROW );
		}
		
		if ( count( $this->altRowClasses ) > 0 )
		{
			$tr->css_class( call_user_func_array( 'alternate', $this->altRowClasses ) );
		}
		
		$this->_onRow( $tr, $row );
	}
	//------------------------------------------------------------------------
	public function render()
	{
		if ( empty( $this->_rows ) )
		{
			$this->_rows = $this->_loadRows();
		}

		$this->renderHeader();
		$this->renderFooter();
		$this->renderRows();
		alternate(); // reset for next grid

		return parent::render();
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	// COMMON FORMATTERS
	//------------------------------------------------------------------------
	/**
	* Just a wrapper for ucwords()
	*/
	public function ucwords( $col, $cell, $row )
	{
		if ( $row == null )
		{
			$cell->content( $col->caption );
		}
		else
		{

			$cell->content( ucwords($row->{$col->fieldName}) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Mask data such as account numbers, etc
	*/
	public function usa_phone( $col, $cell, $row )
	{
		if ( $row === null )
		{
			$cell->content( $col->caption );
		}
		else
		{
			$cell->content( FORMAT()->usa_phone( $row->{$col->fieldName} ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Mask data such as account numbers, etc
	*/
	public function obfuscate( $col, $cell, $row )
	{
		if ( $row === null )
		{
			$cell->content( $col->caption );
		}
		else
		{
			$cell->content( FORMAT()->obfuscate( $row->{$col->fieldName} ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a date
	*/
	public function date( $col, $cell, $row )
	{
		if ( $row === null )
		{
			$cell->content( $col->caption );
		}
		else
		{
			$cell->content( FORMAT()->date( $row->{$col->fieldName} ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a currency amount
	*/
	public function currency( $col, $cell, $row )
	{
		if ( $row === null )
		{
			$cell->content( $col->caption );
		}
		else
		{
			$cell->content( FORMAT()->currency( $row->{$col->fieldName} ) );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Formats a float amount with 2 decimal points
	*/
	public function float( $col, $cell, $row )
	{
		if ( $row === null )
		{
			$cell->content( $col->caption );
		}
		else
		{
			$cell->content( number_format( (float)$row->{$col->fieldName}, 2 ) );
		}
	}
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
}
