<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
* form field class
*/
class tgsfFormField extends tgsfBase
{
	protected	$_optionList	= array();
	protected	$_caption		= '';
	protected	$_desc			= '';
	protected	$_error			= array();
	protected	$_type			= fftText;
	protected	$_selected		= array();
	protected	$_template		= null;
	protected	$_name			= '';
	protected	$_atr			= array();
	protected	$_value			= '';
	protected	$_group			= '';
	protected	$_rawHTML		= '';
	protected	$_ro_tag		= null;
	protected	$_ro_label		= null;
	protected	$_ro_valueSet	= false;
	protected	$_ro_labelAttributes = array();
	protected	$_ro_fieldAttributes = array();
	//------------------------------------------------------------------------
	public		$form			= null;
	//------------------------------------------------------------------------

	public function __construct( $type )
	{
		$this->_type = $type;
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __get( $name )
	{
		if ( isset( $this->{'_'.$name} ) )
		{
			return $this->{'_'.$name};
		}
		
		if ( isset( $this->{'_ro_'.$name} ) )
		{
			return $this->{'_ro_'.$name};
		}
	
		throw new tgsfFormException( 'No field variable named "' . $name . '"' );

	}
	
	//------------------------------------------------------------------------
	/**
	* Sets the error message(s) for the field.
	* @param array The error message(s) for the current field.
	*/
	public function setError( $error )
	{
		if ( isset( $error[$this->_name] ) )
		{
			$this->_error = (array)$error[$this->_name];
		}
	}
	
	//------------------------------------------------------------------------
	/**
	* Sets the value for this field using the form's datasource
	*/
	public function setValue()
	{
		if ( $this->_ro_valueSet === false && $this->form->ds !== null )
		{
			$this->_ro_valueSet = true;
			
			$this->_value = $this->form->ds->_( $this->_name );
			if ( $this->_type === fftDropDown )
			{
				$this->_selected[] = $this->_value;
			}
		}
	}

	//------------------------------------------------------------------------
	/**
	* Alias to $this->name()
	*@ param String The name of this field
	*/

	public function &_( $name ) { return $this->name( $name ); }
	//------------------------------------------------------------------------

	/**
	* Alias to $this->name()
	*@ param String The name of this field
	*/
	public function &name( $name )
	{
		$this->_name = $name;
		$this->form->_addByName( $this );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &caption( $caption )
	{
		$this->_caption = $caption;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &desc( $desc )
	{
		$this->_desc = $desc;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &useGroup( $group )
	{
		$this->_group = $group;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &useTemplate( $template )
	{
		if ( is_object( $template ) )
		{
			$this->_template = $template;
		}
		else
		{
			$this->_template = load_template_library( $template );
		}
		
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the list for a listbox or dropdown or radio group
	*/
	public function &optionList( $list )
	{
		$this->_optionList = $list;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &selected( $values )
	{
		$values = (array)$values;
		$this->_selected = array_merge( $this->_selected, $values );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function rawHTML( $rawHTML )
	{
		$this->_rawHTML = $rawHTML;
	}
	//------------------------------------------------------------------------
	/**
	* Sets an attribute for the label
	* @param String The name of the attribute
	* @param String The value of the attribute
	*/
	public function &setLabelAttribute( $name, $value )
	{
		$this->_ro_labelAttributes[$name] = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* creates and returns $this->label
	*/
	public function getLabelTag()
	{
		$this->setValue();
		if ( $this->_ro_label === null )
		{
			$label = new tgsfHtmlTag( 'label' );
			$label->setAttributes( $this->_ro_labelAttributes );
			$label->css_class( $this->_type );
			$this->_ro_label =& $label;
			
			$label->_('')->content( $this->caption );
			$label->addAttribute( 'for', $this->_name );

			if ( ! empty( $this->_error ) )
			{
				$label->css_class( 'errorCaption' );
				$label->_( 'span' )->css_class( "error_message" )->content( implode( ' and ', $this->_error ) );
			}
			$this->form->onLabel( $this->_ro_label );
		}
		return $this->_ro_label;
	}
	//------------------------------------------------------------------------
	/**
	* Sets an attribute for the field
	* @param String The name of the attribute
	* @param String The value of the attribute
	*/
	public function &setFieldAttribute( $name, $value )
	{
		$this->_ro_fieldAttributes[$name] = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns and sets $this->tag (read only) as a new tgsfHtmlTag object
	*/
	public function getFieldTag()
	{
		$this->setValue();
		
		if ( $this->_ro_tag === null )
		{
			$tag = new tgsfHtmlTag( 'input' );
			$tag->setAttributes( $this->_ro_fieldAttributes );
			$this->_ro_tag =& $tag;
			
			$tag->addAttribute( 'name', $this->_name, SINGLE_ATTR_ONLY );
			$tag->id( $this->_name );
			$tag->value( $this->value );
			$tag->css_class( $this->_type );
		
			switch ( $this->_type )
			{
			case fftText:
				$tag->addAttribute( 'type', 'text', SINGLE_ATTR_ONLY );
				break;
			
			case fftHidden:
				$tag->addAttribute( 'type', 'hidden', SINGLE_ATTR_ONLY );
				break;

			case fftCheck:
				$tag->addAttribute( 'type', 'checkbox', SINGLE_ATTR_ONLY );
				$tag->value( 1 );
				if ( $this->value != 0 && $this->value != '' )
				{
					$tag->addAttribute( 'checked', 'checked', SINGLE_ATTR_ONLY );
				}
				break;

			case fftRadio:
				$tag->addAttribute( 'type', 'radio', SINGLE_ATTR_ONLY );
				$tag->addAttribute( 'name', $this->_group, SINGLE_ATTR_ONLY );
				break;

			case fftSubmit:
				$tag->addAttribute( 'type', 'submit', SINGLE_ATTR_ONLY );
				$tag->value( $this->caption );
				break;

			case fftReset:
				$tag->addAttribute( 'type', 'reset', SINGLE_ATTR_ONLY );
				break;

			case fftPassword:
				$tag->addAttribute( 'type', 'password', SINGLE_ATTR_ONLY );
				break;

			case fftImage:
				$tag->addAttribute( 'type', 'image', SINGLE_ATTR_ONLY );
				break;

			case fftFile:
				$tag->addAttribute( 'type', 'file', SINGLE_ATTR_ONLY );
				break;

			case fftTextArea:
				$tag->changeTag( 'textarea' );
				$tag->removeAttribute( 'value' );
				$tag->_( NON_TAG_NODE )->content( $this->_value ); // create a child for content only.
				break;

			case fftDropDown:
			case fftList:
				$tag->changeTag( 'select' );
				
				foreach ( $this->_optionList as $optVal => $caption )
				{
					$option = $tag->_( 'option' );
					$option->content( $caption );
					$option->addAttribute( 'value', $optVal );
					if ( in_array( $optVal, $this->_selected ) )
					{
						$option->addAttribute( 'selected', 'selected' );
					}
					unset( $option );
				}
				break;
			
			case fftButton:
				$tag->changeTag( 'button' );
				$tag->content( $this->_caption );
				break;
				
			case fftStatic:
				$tag->changeTag( 'p' );
				$tag->content( $this->_value );
				break;
			}
			$this->form->onField( $this->_ro_tag );
		}
		return $this->_ro_tag;
	}
	//------------------------------------------------------------------------
	public function render( &$container )
	{
		if ( is_null( $this->_template ) )
		{
			throw new tgsfFormException( 'No rendering template has been set.' );
		}

		$this->getLabelTag();
		$this->getFieldTag();

		$this->_template->{$this->_type}( $this, $container );
	}
}
