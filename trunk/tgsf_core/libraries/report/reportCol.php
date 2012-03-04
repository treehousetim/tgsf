<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class tgsfReportColBase extends tgsfBase
{
	protected $_ro_caption			= '';
	protected $_decorators			= array();
	protected $_validContexts		= array( rotBROWSER, rotCSV );

	protected $_ro_fields			= array();
	protected $_ro_fieldname;

	protected $_ro_headerRows		= array();
	protected $_ro_currentSubRowIx	= 0;
	protected $_ro_htmlAttributes	= array();

	//------------------------------------------------------------------------
	public function __construct( $name )
	{
		$this->_ro_fields = (array)$name;

		$this->_ro_fieldname = $this->_ro_fields[0];
	}
	//------------------------------------------------------------------------
	/**
	* Sets the CSS Class for the tag
	* @param String The name of a css class to apply to this tag
	*/
	public function &cssClass( $class )
	{
		if ( $class != '' )
		{
			// false for not a single attribute
			return $this->addAttribute( 'class', $class, false );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets the ID attribute for the tag
	* @param String The value of the ID to set on this tag
	*/
	public function &id( $id )
	{
		return $this->setAttribute( 'id', $id, true );
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
			$this->_ro_htmlAttributes[$name] = $value;
		}
		else if
		// we don't add duplicate values
		// but we keep attribute values in an array
		// so that, for instance, a css class can be added multiple times.
		// it's up to somewhere else to implode or otherwise handle these arrays
		( ! empty( $this->_ro_htmlAttributes[$name] ) )
		{
			if ( ! is_array($this->_ro_htmlAttributes[$name]) )
			{
				$this->_ro_htmlAttributes[$name] =(array)$this->_ro_htmlAttributes[$name];
			}

			if ( !in_array( $value, $this->_ro_htmlAttributes[$name] ) )
			{
				$this->_ro_htmlAttributes[$name][] = $value;
			}
		}
		else
		{
			$this->_ro_htmlAttributes[$name][] = $value;
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* returns true/false if an attribute has been set on this tag
	*/
	public function hasAttribute( $name )
	{
		return array_key_exists( $name, $this->_ro_htmlAttributes );
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
	//------------------------------------------------------------------------
	/**
	* Overwrites and sets all attributes using an associative array
	* @param Array The array of attributes to use
	*/
	public function &setAttributes( $atr )
	{
		$this->_ro_htmlAttributes = (array)$atr;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &removeAttribute( $name )
	{
		if ( ! empty( $this->_ro_htmlAttributes[$name] ) )
		{
			unset( $this->_ro_htmlAttributes[$name] );
		}

		return $this;
	}
	//------------------------------------------------------------------------
	public function getHtmlTagExtras()
	{
		foreach ( $this->_ro_htmlAttributes as $key => $val )
		{
			if ( is_array( $val ) )
			{
				$val = implode( ' ', $val );
			}

			$val = htmlspecialchars( $val );

			$atrString .= " $key=\"$val\"";
		}
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
	* Add a column decorator to this report column
	*/
	public function &addDecorator( colDecorator $decorator )
	{
		$this->_decorators[] = $decorator;
		return $this;
	}
	//------------------------------------------------------------------------
	public function render( $row, $header, $type )
	{
		if ( $header )
		{
			$content = $this->_ro_caption;
		}
		else
		{
			$content = '';

			foreach( $this->_ro_fields as $fieldPart )
			{
				if ( $row->exists( $fieldPart ) )
				{
					$content .= $row->getVar( $fieldPart );
				}
				else
				{
					$content .= $fieldPart;
				}
			}

			foreach( $this->_decorators as $decorator )
			{
				if ( $header == false && ( $type == ctALL || $decorator->outputType == $type ) )
				{
					$content = $decorator->render( $row, $this, $content );
				}
			}
		}

		return $content;
	}
}
//------------------------------------------------------------------------
// col decorators below
//------------------------------------------------------------------------
abstract class colDecorator extends tgsfBase
{
	protected $_ro_outputType = rotALL;
	abstract public function render( $row, $colDef, $renderedContent );
	//------------------------------------------------------------------------
	/**
	* Sets the output type for the decorator instance
	*/
	public function &outputType( $value )
	{
		$this->_ro_outputType = $value;
		return $this;
	}
}
//------------------------------------------------------------------------
class cdBool extends colDecorator
{
	protected $_ro_trueValue;
	protected $_ro_falseValue;
	protected $_ro_reverse = false;

	public function __construct()
	{
		$this->_ro_trueValue = 'Y';
		$this->_ro_falseValue = 'N';
	}
	//------------------------------------------------------------------------
	/**
	* Sets the value for a true condition
	*/
	public function &trueValue( $value )
	{
		$this->_ro_trueValue = $value;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the output value
	*/
	public function &falseValue( $value )
	{
		$this->_ro_falseValue = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets up whether or not to reverse the value of the column
	*/
	public function &reverse()
	{
		$this->_ro_reverse = true;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets up to use an image for t/f (checkmark/red x if using elegant under images)
	*/
	public function &checkImage()
	{
		$this->_ro_trueValue = tgsfHtmlTag::factory( 'img' )->setAttribute( 'src', image_url( 'elegant/checkmark.png' ) )->cssClass( 'bool-y' )->renderTagOnly();
		
		$this->_ro_falseValue = tgsfHtmlTag::factory( 'img' )->setAttribute( 'src', image_url( 'elegant/x.png' ) )->cssClass( 'bool-n' )->renderTagOnly();

		return $this;
	}
	//------------------------------------------------------------------------
	public function render( $row, $colDef, $content )
	{
		$value = (bool)$row->getVar( $colDef->fieldname ) || $this->_ro_reverse;

		if ( $value )
		{
			return $this->_ro_trueValue;
		}
		return $this->_ro_falseValue;
	}
}
//------------------------------------------------------------------------
class cdUrl extends colDecorator
{
	protected $_ro_urlVars		= array();
	protected $_ro_url			= null;
	protected $_ro_anchorExtras = '';
	//------------------------------------------------------------------------
	public function __construct( $url )
	{
		if ( $url instanceof tgsfUrl )
		{
			$this->_ro_url = clone $url;
		}
		else
		{
			$this->_ro_url = URL( (string)$url );
		}
	}
	//------------------------------------------------------------------------
	public function render( $row, $colDef, $content )
	{
		foreach( $this->_ro_urlVars as $fieldName => $urlVar )
		{
			$this->_ro_url->setVar( $urlVar, $row->[$fieldName] );
		}
		if ( $this->_ro_outputType == rotHTML )
		{
			return $this->_ro_url->anchorTag( $content );
		}
		else
		{
			return (string)$this->_ro_url;
		}
	}
	//------------------------------------------------------------------------
	/**
	* Maps a datasource variable to a url variable.
	*@param The var name in the query results
	*@param The url var that gets the value from the query results
	*/
	public function &mapVar( $dsVar, $urlVar )
	{
		$this->_ro_urlVars[$urlVar] = $dsVar;
		return $this;
	}
}