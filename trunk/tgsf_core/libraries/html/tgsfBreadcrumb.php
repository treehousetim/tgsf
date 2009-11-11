<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
define( 'BC_REVERSE', true );
define( 'BC_NO_REVERSE', false );
define( 'BC_RESET_ALL', true );
define( 'BC_RESET_SEGMENTS', false );
//------------------------------------------------------------------------
function &BREADCRUMB()
{
	$args = func_get_args();
	$instance =& tgsfBreadcrumb::get_instance();

	if ( count( $args ) > 0 )
	{
		$instance->segments( $args );
	}
	return $instance;
}
//------------------------------------------------------------------------
class tgsfBreadcrumbItem extends tgsfBase
{
	protected	$_ro_caption	= '';
	protected	$_ro_url		= '';
	protected	$_display		= true;
	//------------------------------------------------------------------------
	/**
	* a data container
	*/
	public function __construct( $caption, $url = '' )
	{
		$this->_ro_caption	= $caption;
		$this->_ro_url		= $url;
	}
	//------------------------------------------------------------------------
	/**
	* Renders this item
	*/
	public function render( $position )
	{
		if ( $this->_ro_url == '' )
		{
			return $this->_ro_caption;
		}
		
		if ( $this->_ro_url instanceof tgsfUrl )
		{
			return $this->_ro_url->anchorTag()->content( $this->_ro_caption );
		}

		return html_tag( 'a', array( 'href' => url( $this->_ro_url ) ), $this->_ro_caption );
	}
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
class tgsfBreadcrumb extends tgsfBase
{
	private static	$_instance				= null;
	protected		$_items					= array();
	protected		$_reverse				= false;
	protected		$_home					= null;

	//------------------------------------------------------------------------
	/**
	* protected to prevent instantiation
	*/
	protected function __construct()
	{
	}
	//------------------------------------------------------------------------
	/**
	* Prevent users from cloning the instance
	*/
	public function __clone()
	{
		throw new tgsfException( 'Cloning a singleton (tgsfBreadcrumb) is not allowed. Use the BREADCRUMB() function to get its instance.' );
	}
	//------------------------------------------------------------------------
	/**
	* Resets the Breadcrumb nav for reuse
	* @param Bool Use the following defines: BC_RESET_ALL, BC_RESET_SEGMENTS
	*/
	public function reset( $resetAll = false )
	{
		$this->_items = array();
		if ( $resetAll )
		{
			$this->_home = null;
		}
	}
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance()
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		
		return self::$_instance;
	}
	//------------------------------------------------------------------------
	/**
	* Adds a home link at the beginning of the breadcrumb nav
	*/
	public function &addHome( $caption = 'Home', $url = '' )
	{
		$this->_home = new tgsfBreadcrumbItem( $caption, $url );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets the segments of the BC nav.
	*/
	public function &segments()
	{
		$args = func_get_args();
		$argCount = func_num_args();
		
		if ( $argCount == 1 && is_array( $args[0] ) )
		{
			$args = $args[0];
			$argCount = count( (array)$args );
		}

		for ( $ix = 0; $ix < $argCount ; $ix++ )
		{ 
			if ( ! empty( $args[$ix+1] ) )
			{
				$item = new tgsfBreadcrumbItem( $args[$ix], $args[$ix+1] );
				$ix++;
			}
			else
			{
				$item = new tgsfBreadcrumbItem( $args[$ix] );
			}
			$this->_items[] = $item;
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the breadcrumb to reverse order.  Call using BC_NO_REVERSE as a parameter to
	* switch from reverse back to normal
	*/
	public function &reverse( $value = true )
	{
		$this->_reverse = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Renders the breadcrumb segments
	*/
	function render()
	{
		$out = '';

		if ( $this->_home !== null )
		{
			$items = array_merge( array( $this->_home ), $this->_items );
		}
		else
		{
			$items = $this->_items;
		}

		if ( is_array( $items ) && count( $items ) > 0 )
		{
			if ( $this->_reverse )
			{
				$items = array_reverse( $items );
			}

			$liTags = '';
			$itemCount = count( $items );
			$ix = 0;
		
			foreach ( $items as $item )
			{
				$atr = array();
				$class = getArrayFirstLastCssClass( $ix, $itemCount );
				if ( $class != '' )
				{
					$atr['class'] = $class;
				}
				$liTags .= html_tag( 'li', $atr, $item->render( $ix ) );
				$ix++;
			}

			$liTags .= html_tag( 'li', array( 'class' => 'horz-ul-clear' ), ' ' );

			$atr = array();
			$atr['class'] = 'breadcrumb';
			$out = html_tag( 'ul', $atr, $liTags );
		}
		return $out;
	}
}
