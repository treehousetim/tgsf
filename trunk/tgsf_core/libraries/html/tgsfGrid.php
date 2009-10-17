<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// enum for sort type
// stSTR, strDATE, strMONEY
enum ( 'st', array( 'STR', 'DATE', 'MONEY' ) );

// ROW_HEADER, ROW_NORMAL, ROW_FOOTER
enum ( 'ROW_', array( 'ALL', 'HEADER', 'NORMAL', 'FOOTER' ) );

load_library( 'html/tgsfHtmlTag', IS_CORE_LIB );

class tgsfGridCol extends tgsfHtmlTag
{
	protected $_ro_sortable		= false;
	protected $_ro_sort_type	= stSTR;
	protected $_ro_caption		= '';
	protected $_ro_fieldName	= '';
	protected $_ro_headerCell	= null;
	protected $_ro_footerCell	= null;
	
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
	public function &renderFunc( $callBack, $cellType = ROW_ALL )
	{
		if ( !is_array($callBack) )
		{
			throw new tgsfGridException( 'Callback must be an array.' );
		}

		$this->_renderFunc[$cellType] = $callBack;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Assign a callback to a column
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
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderCell( &$row, $tr, $cellType = ROW_NORMAL )
	{
		if ( $cellType == ROW_HEADER )
		{
			$cell = $tr->_( 'th' );
			$this->_ro_headerCell =& $cell;
		}
		elseif ( $cellType == ROW_FOOTER )
		{
			$cell = $tr->_( 'td' );
			$this->_ro_footerCell =& $cell;
		}
		else
		{
			$cell = $tr->_( 'td' );
		}
		
		$cell->setAttributes( $this->attributes );
		
		if ( $cellType == ROW_HEADER )
		{
		    $cell->content( $this->_ro_caption );
		}
		else
		{
		    if ( $row === null )
		    {
		    	throw new tgsfGridException( 'Grid Row may not be null for non-header rows.' );
		    }
		    
		    $fields = (array)$row;
			
			if ( isset($fields[$this->_ro_fieldName]) )		    
			{
		    	$cell->content( $row->{$this->_ro_fieldName} );
		    }
		}

		if ( $this->_renderFunc !== null )
		{
			if ( isset($this->_renderFunc[ROW_ALL]) )
			{
				call_user_func( $this->_renderFunc[ROW_ALL], $this, $cell, $row, $cellType );
			}
		
			if ( isset($this->_renderFunc[$cellType]) )
			{
				call_user_func( $this->_renderFunc[$cellType], $this, $cell, $row, $cellType );
			}
		}
		
		if ( ! $cell->content && !empty($this->empty_message) && $cellType == ROW_NORMAL )
		{
			$cell->content( $this->empty_message, false );
		}
		
		return $cell;
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
	public function cellType( &$cell )
	{
		foreach( $this->_cols as $col )
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
		$this->_footer = (object)$data;
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
			$col->renderCell( $row, $tr, ROW_HEADER );
		}
		
		$this->_onRow( $tr, $row );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderFooter()
	{
		if ( !is_null($this->_footer) || !empty($this->_footer) )
		{	
			$tr = $this->_( 'tfoot' )->_( 'tr' );
			$row = (object)$this->_footer;
			
			foreach( $this->_cols as $col )
			{
				$col->renderCell( $row, $tr, ROW_FOOTER );
			}
			
			$this->_onRow( $tr, $row );
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
			$tr->_( 'td' )->content( $this->emptyMessage, false )->addAttribute( 'colspan', count( $this->_cols ) )->css_class( 'empty' );
		}
	}
	//------------------------------------------------------------------------
	public function renderRow( &$row )
	{
		$tr = $this->_( 'tr' );

		foreach( $this->_cols as $col )
		{
			$col->renderCell( $row, $tr, ROW_NORMAL );
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
		$this->renderRows();
		$this->renderFooter();
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
	/**
	* Formats a date
	*/
	public function date( $col, $cell, $row, $cellType )
	{
		if ( $cellType == ROW_NORMAL )
		{
			$cell->content( FORMAT()->date( $row->{$col->fieldName} ) );
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
	//------------------------------------------------------------------------
}
