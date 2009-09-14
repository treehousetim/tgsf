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
	protected	$_error			= '';
	protected	$_type			= fftText;
	protected	$_selected		= array();
	protected	$_template		= null;
	protected	$_name			= '';
	protected	$_atr			= array();
	protected	$_value			= '';
	protected	$_group			= '';
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
			$this->_error = $error[$this->_name];
		}
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function setValue( $ds )
	{
		$this->_value = $ds->_( $this->_name );
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
		return $this;
	}
	//------------------------------------------------------------------------
	public function &caption( $caption )
	{
		$this->_caption = $caption;
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

	public function &selected( $value )
	{
		$values = array();
		arrayify( $value, $values );
		$this->_selected = array_merge( $this->_selected, $values );
		return $this;
	}

	//------------------------------------------------------------------------

	public function render()
	{
		if ( is_null( $this->_template ) )
		{
			throw new tgsfFormException( 'No rendering template has been set.' );
		}

		return $this->_template->{$this->_type}( $this );
	}
}
