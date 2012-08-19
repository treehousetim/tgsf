<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

define( 'SINGLE_ATTR_ONLY', true );
define( 'MULTI_ATTR', false );
define( 'NON_TAG_NODE', '' );
define( 'RENDER_OPEN_TAG_ONLY', true );
define( 'APPEND_CONTENT', true );
//------------------------------------------------------------------------
/**
* An html tag container
*/
class tgsfHtmlTag extends tgsfBase
{
	public $parent;
	protected $_children				= array();
	protected $_ro_tag;
	protected $_ro_attributes			= array();
	protected $_ro_content				= '';
	protected $_ro_unfilteredContent	= '';
	protected $_ro_contentOnly			= false;
	protected $empty_message    		= null;
	protected $_ro_contentFilter		= null;
	protected $_beforeTags				= array();
	protected $_afterTags				= array();
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( $tag )
	{
		$this->_ro_tag = $tag;
		$this->_ro_contentOnly = $tag == NON_TAG_NODE;
	}

	public function deep_free()
	{
//		echo 'deep_free' . PHP_EOL;

		foreach( $this->_children as $child )
		{
			$child->deep_free();
		}
		$this->_children = array();
	}

	//------------------------------------------------------------------------
	public static function factory( $tag )
	{
		return new tgsfHtmlTag( $tag );
	}
	//------------------------------------------------------------------------
	/**
	* Gets an attribute
	*/
	public function __get( $name )
	{
		try
		{
			return parent::__get( $name );
		}
		catch( Exception $e )
		{
			if ( array_key_exists( $name, $this->_ro_attributes ) )
			{
				return $this->_ro_attributes[$name];
			}
		}

		throw new tgsfException( 'No attribute by that name has been set.' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function child( $ix = -1 )
	{
		if ( $ix < 0 )
		{
			return $this->_children;
		}

		return $this->_children[$ix];
	}
	//------------------------------------------------------------------------
	/**
	* Sets an attribute
	*/
	public function __set( $name, $value )
	{
		$this->setAttribute( $name, $value );
	}
	//------------------------------------------------------------------------
	public function __call( $name, $value )
	{
		if ( $value == null )
		{
			throw new tgsfException( 'A value is required when calling ' . $name . ' on URL()' );
		}

		$this->setAttribute( $name, $value );

		return $this;
	}
	//------------------------------------------------------------------------
	public function __toString()
	{
		return $this->render();
	}
	//------------------------------------------------------------------------
	/**
	* Allows a tag to be changed.
	*/
	public function &changeTag( $tag )
	{
		$this->_ro_tag = $tag;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* An alias of addTag
	*/
	public function &_( $tag )
	{
		return $this->addTag( $tag );
	}
	//------------------------------------------------------------------------
	/**
	* If supplied tag is an existing tgsfHtmlTag object, it is cloned and added
	* to this tag as a child element.
	* If it is a string, a new tag is created and added to this tag as a child
	* Either way, the child tag is returned (this might be a clone of a supplied tag object)
	* @param Mixed tgsfHtmlTag/String Either the type of tag or an existing tag object
	*/
	public function &addTag( $tag )
	{
		if ( $tag instanceof tgsfHtmlTag )
		{
			$item = clone $tag;
		}
		else
		{
			$item = new tgsfHtmlTag( (string)$tag );
		}
		$item->parent = $this;

		$this->_children[] = $item;
		return $item;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function addFirstChild( $tag )
	{
		if ( $tag instanceof tgsfHtmlTag )
		{
			$item = clone $tag;
		}
		else
		{
			$item = new tgsfHtmlTag( (string)$tag );
		}

		$item->parent = $this;
		array_unshift( $this->_children, $item );

		return $item;
	}
	//------------------------------------------------------------------------
	/**
	* If supplied tag is an existing tgsfHtmlTag object, it is cloned and added
	* to this tag as a before tag sibling element.
	* If it is a string, a new tag is created
	* Either way, the sibling tag is returned (this might be a clone of a supplied tag object)
	* @param Mixed tgsfHtmlTag/String Either the type of tag or an existing tag object
	*/
	public function &beforeTag( $tag )
	{
		if ( $tag instanceof tgsfHtmlTag )
		{
			$item = clone $tag;
		}
		else
		{
			$item = new tgsfHtmlTag( (string)$tag );
		}
		$item->parent = $this->parent;

		$this->_beforeTags[] = $item;
		return $item;
	}
	//------------------------------------------------------------------------
	/**
	* If supplied tag is an existing tgsfHtmlTag object, it is cloned and added
	* to this tag as an after tag sibling element.
	* If it is a string, a new tag is created
	* Either way, the sibling tag is returned (this might be a clone of a supplied tag object)
	* @param Mixed tgsfHtmlTag/String Either the type of tag or an existing tag object
	*/
	public function &afterTag( $tag )
	{
		if ( $tag instanceof tgsfHtmlTag )
		{
			$item = clone $tag;
		}
		else
		{
			$item = new tgsfHtmlTag( (string)$tag );
		}
		$item->parent = $this->parent;

		$this->_afterTags[] = $item;
		return $item;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if element has items in the _children array
	*/
	public function hasChildren()
	{
		return ! empty( $this->_children );
	}
	//------------------------------------------------------------------------
	/**
	* adds a value attribute
	*/
	public function &value( $value )
	{
		return $this->addAttribute( 'value', $value, SINGLE_ATTR_ONLY );
	}
	//------------------------------------------------------------------------
	/**
	* Sets the CSS Class for the tag
	* @param String The name of a css class to apply to this tag
	*/
	public function &css_class( $class  )
	{
		if ( $class != '' )
		{
			return $this->addAttribute( 'class', $class, MULTI_ATTR );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function cssClass( $class )
	{
		return $this->css_class( $class );
	}
	//------------------------------------------------------------------------
	/**
	* sets the ID attribute for the tag
	* @param String The value of the ID to set on this tag
	*/
	public function &id( $id )
	{
		return $this->setAttribute( 'id', $id );
	}
	//------------------------------------------------------------------------
	/**
	* Overwrites and sets all attributes using an associative array
	* @param Array The array of attributes to use
	*/
	public function &setAttributes( $atr )
	{
		$this->_ro_attributes = (array)$atr;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Adds an attribute to the HTML tag
	* @param String The name of the attribute
	* @param String The value of the attribute
	* @param Bool (Use defined MULTI_ATTR/SINGLE_ATTR_ONLY) Allow multiple additions of this attribute - multiple additions will be separated with spaces when rendered.
	*/
	public function &addAttribute( $name, $value, $single = false )
	{
		// if single is false we don't store in an array
		if ( $single )
		{
			$this->_ro_attributes[$name] = $value;
		}
		else if
		// we don't add duplicate values
		// but we keep attribute values in an array
		// so that, for instance, a css class can be added multiple times.
		// it's up to somewhere else to implode or otherwise handle these arrays
		( ! empty( $this->_ro_attributes[$name] ) )
		{
			if ( ! is_array($this->_ro_attributes[$name]) )
			{
				$this->_ro_attributes[$name] =(array)$this->_ro_attributes[$name];
			}

			if ( !in_array( $value, $this->_ro_attributes[$name] ) )
			{
				$this->_ro_attributes[$name][] = $value;
			}
		}
		else
		{
			$this->_ro_attributes[$name][] = $value;
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* returns true/false if an attribute has been set on this tag
	*/
	public function hasAttribute( $name )
	{
		return array_key_exists( $name, $this->_ro_attributes );
	}
	//------------------------------------------------------------------------
	/**
	* Sets an attribute ($name) to a $value
	* @param String The name of the attribute
	* @param String The value of the attribute
	*/
	public function &setAttribute( $name, $value )
	{
		return $this->addAttribute( $name, $value, SINGLE_ATTR_ONLY );
	}
	/**
	* Sets the content displayed when the content is empty
	* @param String The message to display when the content is empty
	*/
	public function &emptyMessage( $text  )
	{
		$this->empty_message = $text;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &removeAttribute( $name )
	{
		if ( ! empty( $this->_ro_attributes[$name] ) )
		{
			unset( $this->_ro_attributes[$name] );
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &contentFilter( $callBack, $object = null )
	{
		$cb = array( $object, $callBack );

		if ( is_callable( $cb ) )
		{
			$this->_ro_contentFilter =& $cb;
		}
		else
		{
			$this->_ro_contentFilter = $callBack;
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Adds to the content of the tag.  Tag content always comes above child items that are added to the tag
	* @param String The content to add to the tag.
	* @param Bool Append new content to existing content.  Use define APPEND_CONTENT
	*/
	public function &content( $content, $append = false )
	{
		if ( $append === true )
		{
			$this->_ro_content .= $content;
		}
		else
		{
			$this->_ro_content = $content;
		}

		$this->_ro_unfilteredContent = $this->_ro_content;

		if ( is_callable( $this->_ro_contentFilter ) )
		{
			$this->_ro_content = call_user_func( $this->_ro_contentFilter, $this->_ro_content );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Renders the tag
	* @param Object The tag to render
	*/
	protected function _renderItem( &$htmlTag, $openTagOnly = false )
	{
		$content = $htmlTag->content;

		if ( empty( $content ) )
		{
			$content = $this->empty_message;
		}

		// if an item is only content then we simply return the content.
		if ( $htmlTag->contentOnly === true )
		{
			return $content;
		}

		$atrString = '';
		foreach ( $htmlTag->attributes as $key => $val )
		{
			if ( is_array( $val ) )
			{
				$val = implode( ' ', $val );
			}

			$val = htmlspecialchars( $val );

			$atrString .= " $key=\"$val\"";
		}

		$out = '';

		foreach( $htmlTag->_beforeTags as $tag )
		{
			$out .= $tag->render();
		}

		$out .= "<{$htmlTag->tag}{$atrString}";
		if ( in_array( $htmlTag->tag, array( 'input', 'br', 'hr', 'embed' ) ) === false )
		{
			if ( $openTagOnly == false )
			{
				$out .= ">{$content}</{$htmlTag->tag}>";
			}
			else
			{
				$out .= '>';
			}
/*
			if ( $content != '' )
			{
				$out .= ">{$content}</{$htmlTag->tag}>";
			}
			else
			{
				$out .= '>';
			}*/

		}
		else
		{
			$out .= '>';
		}

		foreach( $htmlTag->_afterTags as $tag )
		{
			$out .= $tag->render();
		}

		return $out;
	}

	//--------------------------------------------------------------------------
	/**
	* Generates and returns the HTML for the tags
	*/
	function render()
	{
		foreach ( $this->_children as &$item )
		{
			if ( $item->hasChildren() )
			{
				$item->render();
			}

			$this->_ro_content .= $this->_renderItem( $item );
		}
		$result = $this->_renderItem( $this );

		$this->deep_free();

		return $result;
	}
	//------------------------------------------------------------------------
	public function renderTagOnly()
	{
		return $this->_renderItem( $this, RENDER_OPEN_TAG_ONLY );
	}
}
