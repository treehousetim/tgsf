<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

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
	protected $_ro_urlVars		= array();
	protected $_ro_fields		= array();
	protected $_ro_mailToField	= '';
	
	protected $_ro_headerCol	= false;
	protected $_ro_headerRows	= array();
	protected $_ro_currentSubRowIx = 0;
	protected $_ro_groupField	= '';
	protected $_ro_calcSum		= false;
	//------------------------------------------------------------------------
	public function __construct( $name )
	{
		parent::__construct( 'td' );
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
	* Sets a column to be a header (or pass false to turn that off)
	* this will generate <th> cells instead of <td> cells
	* header cells also support rowspans
	* @param Bool Header on/off - true/false
	*/
	public function &header( $header = true )
	{
		$this->_ro_headerCol = $header;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Creates, adds to the internal array and returns the tgsfGridRowHeader object
	* that holds the per-row setup for 
	*/
	public function &addHeaderRow( $caption )
	{
		$item = new tgsfGridRowHeader( $caption, $this );
		$this->_ro_headerRows[] =& $item;
		return $item;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &repeat( $repeat = true )
	{
		$this->_ro_repeatHeaders = $repeat;
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
	* @param Array associative array of variables to add to a url - array( 'query_field_name' => 'url_var_name' )
	* Example: ->url( 'admin/view', array( 'row_id' => 'i' ) );
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
			$a = clone $this->_ro_url->anchorTag()->content( $cell->content );
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
		$cnt =& $this->_ro_currentSubRowIx;
		$cell = null;
		if ( $type != ROW_HEADER && $this->_ro_headerCol )
		{
			$curRow = $this->_ro_headerRows[$cnt%count($this->_ro_headerRows)];

			if ( $curRow->subRow == 1 )
			{
				$tr->addTag( $curRow );
			}
			$curRow->incrementSubRow( $cnt );
		}
		else
		{
			$cell = $tr->addTag( $this );
			
			if ( $type == ROW_HEADER )
			{	
				$cell->changeTag( 'th' );
				$cell->contentFilter( null );
				
				$this->_ro_headerCell =& $cell;
				$cell->content( $this->_ro_caption );
			}
			elseif ( $type == ROW_FOOTER )
			{
				$cell->contentFilter( null );
				$this->_ro_footerCell =& $cell;
			}
		}
		return $cell;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderCell( &$row, $tr, $cellType = ROW_NORMAL )
	{
		$cell = $this->_getCellObject( $tr, $cellType );
		if ( empty( $cell ) )
		{
			return;
		}
		
		if ( $cellType != ROW_HEADER && $this->_ro_headerCol == false )
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
