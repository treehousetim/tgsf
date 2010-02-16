<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class tgsfGridRowHeader extends tgsfHtmlTag
{
	protected $_ro_caption		= '';
	protected $_ro_rowSpan		= 1;
	public $subRow		= 1;
	protected $_ro_col			= null;
	//------------------------------------------------------------------------
	public function __construct( $caption, &$colObj )
	{
		parent::__construct( 'th' );
		$this->content( $caption );
		$this->_ro_col =& $colObj;
	}
	//------------------------------------------------------------------------
	public function &rowSpan( $value )
	{
		$this->rowspan = $value;
		$this->_ro_rowSpan = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function incrementSubRow( &$cnt )
	{
		if ( $this->_ro_rowSpan == 1 )
		{
			$cnt++;
			return ;
		}
		
		if ( $this->subRow < $this->_ro_rowSpan  )
		{
			$this->subRow++;
		}
		else
		{
			$cnt++;
			$this->subRow = 1;
		}
	}
}
