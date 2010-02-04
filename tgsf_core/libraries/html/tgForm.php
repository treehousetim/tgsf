<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//------------------------------------------------------------------------
// form enums

// form field type
enum( 'fft',
	array(
		'Hidden'	=> 'hidden',
		'Text'		=> 'text',
		'TextArea'	=> 'textarea',
		'File'		=> 'file',
		'DropDown'	=> 'dropdown',
		'List'		=> 'list',
		'Radio'		=> 'radio',
		'Check'		=> 'checkbox',
		'Image'		=> 'image',
		'Button'	=> 'button',
		'Submit'	=> 'submit',
		'Reset'		=> 'reset',
		'Password'	=> 'password',
		'OtherTag'	=> 'other',
		'Static'	=> 'statictext'
		)
	);
define( 'FORM_AUTOCOMPLETE_ON', true );
define( 'FORM_AUTOCOMPLETE_OFF', false );

load_library( 'html/tgsfHtmlTag', IS_CORE_LIB );
//------------------------------------------------------------------------
/**
* form class
*/
abstract class tgsfForm extends tgsfHtmlTag
{
	protected	$_fields		= array();
	protected	$_fieldsByName	= array();
	protected	$_template		= null;
	protected	$_validator		= null;
	protected	$_ro_ds			= null;
	protected	$_processor		= '';
	protected	$_ro_valid		= true;
	protected	$_groupName		= '_main';
	protected	$_ro_setup		= false;
	protected	$_ro_autocomplete	= false;
	//------------------------------------------------------------------------
	public		$errors			= array();
	/**
	* not used yet - why do we need to reuse a form object?
	*/
 	private function _reset()
	{
		throw new tgsfFormException( 'Form resetting not allowed.' );
		/*
		$this->_fields			= array();
		$this->_fieldsByName	= array()
		$this->_template		= null;
		$this->_validator		= null;
		$this->_errors			= array();
		$this->_ro_ds			= null;
		$this->_id				= '';
		$this->_processor		= '';
		$this->_ro_setup		= false;
		*/
	}
	//------------------------------------------------------------------------
	abstract protected function _setup();
	abstract protected function _setupValidate( &$v );
	/* abstract */ public function onLabel( &$label ){}
	/* abstract */ public function onField( &$field ){}
	/* abstract */ public function onSelectOption( &$field ){}
	/* abstract */ public function onGroupContainer( &$container, $groupName ){}

	//------------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct( 'form' );
		$this->setAttribute( 'method', 'POST' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function autocomplete( $on = true )
	{
		$this->removeAttribute( 'autocomplete' );
		$this->_ro_autocomplete = $on;
		if ( $on === false )
		{
			$this->addAttribute( 'autocomplete', 'off' );
		}
	}
	//------------------------------------------------------------------------
	protected function useTemplate( $template, $core = true )
	{
		if ( is_object( $template ) && $template instanceof tgsfFormTemplate )
		{
			$this->_template = $template;
		}
		else
		{
			$this->_template = load_template_library( 'form/' . $template, $core );
		}
	}
	//------------------------------------------------------------------------
	protected function &_getValidator()
	{
		if ( $this->_validator === null )
		{
			load_library( 'validate/tgsfValidate',			IS_CORE_LIB );
			load_library( 'validate/tgsfValidateField',		IS_CORE_LIB );
			load_library( 'validate/tgsfValidateRule',		IS_CORE_LIB );
			$this->_validator = new tgsfValidate();
			$this->_validator->setForm( $this );
			$this->_setupValidate( $this->_validator );
		}

		return $this->_validator;
	}
	//------------------------------------------------------------------------
	public function &validator()
	{
		return $this->_getValidator();
	}
	//------------------------------------------------------------------------
	/**
	* Sets a datasource for this form.
	* @param a datasource object - either a db datasource or an http post datasource
	*/
	public function &ds( &$ds )
	{
		$this->_ro_ds =& $ds;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &_( $type )
	{
		$field = new tgsfFormField( $type );
		$field->useTemplate( $this->_template );
		$field->useGroup( $this->_groupName );
		$field->form =& $this;
		$this->_fields[] =& $field;
		return $field;
	}
	//------------------------------------------------------------------------
	/**
	* Not a public API, only to be used by the field object to link itself back when its name has been set
	*/
	public function _addByName( &$field )
	{
		$this->_fieldsByName[$field->name] =& $field;
		return false; // trickery
	}
	//------------------------------------------------------------------------
	/**
	* Returns a field object for the given name
	* @param String The name of the field.  Using $this->_( fftText )->name( 'fieldName' ); is required.
	* in other words, you must name a field for it to be available to this function.
	* Using this allows you to output a form one field at a time.
	* Example: $form = load_form( 'example' );
	* echo $form->fieldByName( 'example_field' )->getLabelTag()->render();
	* echo $form->fieldByName( 'example_field' )->getFieldTag()->render();
	*/
	public function &fieldByName( $name )
	{
		if ( $this->_ro_setup === false )
		{
			$this->_setup();
			$this->_ro_setup = true;
		}

		return $this->_fieldsByName[$name];
	}
	//------------------------------------------------------------------------
	/**
	* Keeps track of the current group - template renderers can make choices based on groups
	* @param String The name of a group
	*/
	public function startGroup( $name )
	{
		$this->_groupName = $name;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the processing URL
	* @param String The url of the form's processor
	*/
	public function processor( $url )
	{
		$this->setAttribute( 'action', $url );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderTagOnly()
	{
		if ( $this->_ro_setup === false )
		{
			$this->_setup();
			$this->_ro_setup = true;
		}
		return parent::renderTagOnly();
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __toString()
	{
		return $this->render();
	}
	//------------------------------------------------------------------------
	/*
	* Renders a form
	*@param Bool True = only return html
	*/
	public function render()
	{
		if ( $this->_ro_setup === false )
		{
			$this->_setup();
			$this->_ro_setup = true;
		}

		if ( empty($this->_fields) )
		{
			throw new tgsfFormException( 'The form has no fields.' );
		}

		$curGroup = $this->_fields[0]->group;
		$container = $this->_template->fieldContainer( $this, $curGroup );
		$this->onGroupContainer( $container, $curGroup );

		foreach ( $this->_fields as &$field )
		{
			if ( $field->group != $curGroup )
			{
				$container = $this->_template->fieldContainer( $this, $field->group );
				$this->onGroupContainer( $container, $field->group );
			}
			$curGroup = $field->group;

			$field->setError( $this->errors );
			$field->render( $container );
		}

	 	$html = parent::render();
		$this->_children = array();
		$this->_ro_content = '';
		//$html .= $this->renderJsValidation();
		return $html;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderJsValidation()
	{
		$v = $this->_getValidator();

		$s = new tgsfHtmlTag( 'script' );
		$s->type = "text/javascript";
		$v->jsOutput( $s );
		return $s;
	}
	//------------------------------------------------------------------------
	/**
	* Forces a valid/invalid condition on the form.
	* @param Bool - Use defines from validate/tgsfValidate.php  FORCE_VALID, FORCE_INVALID
	*/
	protected function forceValid( $value )
	{
		$this->_ro_valid = $value;
		$this->errors = array();
	}
	//------------------------------------------------------------------------
	public function validate()
	{
		if ( $this->_ro_setup === false )
		{
			$this->_setup();
			$this->_ro_setup = true;
		}

		if ( $this->_ro_ds === null )
		{
			throw new tgsfFormException( 'The form has no datasource to validate with.' );
		}

		$v = $this->_getValidator();

		if ( $this->_validator->execute( $this->_ro_ds ) === false )
		{
			$this->errors = $this->_validator->errors;
			$this->_ro_valid = false;
		}

		$this->validateForm( $this->_ro_ds, $v );
		
		return $this->_ro_valid;
	}
	//------------------------------------------------------------------------
	/**
	* Overwrite this function in extending classes to do a form-level validation
	*/
	public function validateForm( &$ds, &$v )
	{
		// empty
	}
}
//------------------------------------------------------------------------
// Abstract form template class defines the methods a form template class must implement
//------------------------------------------------------------------------
abstract class tgsfFormTemplate extends tgsfBase
{
	protected $_field;
	//------------------------------------------------------------------------

	public function hidden( &$field, &$container )
	{
		$container->addTag( $field->tag );
	}

	abstract public function fieldContainer(  &$form, $group = '' );

	abstract public function dropdown(	&$field, &$container );
	abstract public function file(		&$field, &$container );
	abstract public function text(		&$field, &$container );
	abstract public function textArea(	&$field, &$container );
	abstract public function radio(		&$field, &$container );
	abstract public function checkbox(	&$field, &$container );
	abstract public function image(		&$field, &$container );
	abstract public function button(	&$field, &$container );
	abstract public function submit(	&$field, &$container );
	abstract public function reset(		&$field, &$container );
	abstract public function password(	&$field, &$container );
	abstract public function other(		&$field, &$container );
}