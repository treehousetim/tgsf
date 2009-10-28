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
	
	protected $_ro_url			= null;
	protected $_ro_urlvars		= array();
	protected $_ro_fields		= array();
	protected $_ro_mailToField		= '';

	//------------------------------------------------------------------------
	public function __construct( $name )
	{
		$this->_ro_fields = (array)$name;
		$this->_ro_fieldName = $this->_ro_fields[0];
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
	* Sets a url object on the column - this is set as the content of cells
	* with the rendered content as the link text
	* @param Object::tgsfUrl A url object used to create a link with the cell contents
	*/
	public function &url( $url, $urlVars )
	{
		if ( $url instanceof tgsfUrl )
		{
			$this->_ro_url = clone $url;
		}
		else
		{
			$this->_ro_url = URL( (string)$url );
		}

		$this->_ro_urlVars = (array)$urlVars;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a column up to generate a mailto link
	*/
	public function mailTo( $fieldName )
	{
		$this->_ro_mailToField = $fieldName;
	}
	//------------------------------------------------------------------------
	/**
	* Internal function to get the content of a cell
	*/
	protected function _getCellContent( $cell, &$row )
	{
		$fields = (array)$row;
		
		foreach( $this->_ro_fields as $fieldPart )
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
		
		if ( $this->_ro_url instanceof tgsfUrl )
		{
			foreach( $this->_ro_urlVars as $fieldName => $urlVar )
			{
				$this->_ro_url->setVar( $urlVar, $fields[$fieldName] );
			}
			$a = $this->_ro_url->anchorTag()->content( $cell->content );
			$cell->content( $a );
		}
		elseif( ! empty( $this->_ro_mailToField ) )
		{
			$email = $fields[$this->_ro_mailToField];
			$cell->content( '<a class="mailto" href="mailto:' . $email . '">' . $email . '</a>' );
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	protected function &_getCellObject( &$tr, $type )
	{
		if ( $type == ROW_HEADER )
		{
			$cell = $tr->_( 'th' );
			$this->_ro_headerCell =& $cell;
			$cell->content( $this->_ro_caption );
		}
		elseif ( $type == ROW_FOOTER )
		{
			$cell = $tr->_( 'td' );
			$this->_ro_footerCell =& $cell;
		}
		else
		{
			$cell = $tr->_( 'td' );
		}
		
		$cell->setAttributes( $this->attributes );

		return $cell;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderCell( &$row, $tr, $cellType = ROW_NORMAL )
	{
		$cell = $this->_getCellObject( $tr, $cellType );
		
		if ( $cellType != ROW_HEADER )
		{
			if ( $row === null )
			{
				throw new tgsfGridException( 'Grid Row may not be null for non-header rows.' );
			}

			$this->_getCellContent( $cell, $row );
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
	public function __construct()
	{
		$this->css_class( 'grid' );
		parent::__construct( 'table' );
		$this->_setup();
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
