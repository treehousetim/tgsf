<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//$this->dropdown( 'name' )->
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
		)
	);

enum( 'ffTpl',
	array(
		'Top',
		'Left',
		'Right'
		)
	);
//------------------------------------------------------------------------

//------------------------------------------------------------------------
/**
* form class
*/
abstract class tgsfForm extends tgsfBase
{
	protected	$_fields	= array();
	protected	$_template	= null;
	protected	$_validator	= null;
	protected	$_ds		= null;
	protected	$_processor	= '';
	protected	$_id		= '';
	protected	$_ro_valid	= true;
	protected	$_groupName	= '_main';
	//------------------------------------------------------------------------
	public		$errors		= array();
	/**
	* not used yet - why do we need to reuse a form object?
	* also, _setup is called from the constructor.
	*/
 	private function _reset()
	{
		throw new tgsfFormException( 'Form resetting not allowed.' );
		/*
		$this->_fields		= array();
		$this->_template	= null;
		$this->_validator	= null;
		$this->_errors		= array();
		$this->_ds			= null;
		$this->_id			= '';
		$this->_processor	= '';
		*/
	}
	//------------------------------------------------------------------------
	abstract protected function _setup();
	abstract protected function _setupValidate( &$v );
	//------------------------------------------------------------------------

	public function __construct()
	{
		$this->_setup();
	}

	//------------------------------------------------------------------------

	protected function useTemplate( $template )
	{
		if ( is_object( $template ) )
		{
			$this->_template = $template;
		}
		else
		{
			$this->_template = load_template_library( 'form/' . $template );
		}
	}

	//------------------------------------------------------------------------

	protected function &_getValidator()
	{
		load_library( 'validate/tgsfValidate',			IS_CORE_LIB );
		load_library( 'validate/tgsfValidateField',		IS_CORE_LIB );
		load_library( 'validate/tgsfValidateRule',		IS_CORE_LIB );
		
		if ( $this->_validator === null )
		{
			$this->_validator = new tgsfValidate();
		}

		return $this->_validator;
	}

	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	
	//------------------------------------------------------------------------
	/**
	* Sets a datasource for this form.
	* @param a datasource object - either a db datasource or an http post datasource
	*/
	public function ds( &$ds )
	{
		$this->_ds =& $ds;
	}
	//------------------------------------------------------------------------
	public function &_( $type )
	{
		$field = new tgsfFormField( $type );
		$field->useTemplate( $this->_template );
		$field->useGroup( $this->_groupName );
		$this->_fields[] =& $field;
		return $field;
	}
	//------------------------------------------------------------------------
	/**
	* Keeps track of the current group - template renderers can make choices based on groups
	*/
	public function startGroup( $name )
	{
		$this->_groupName = $name;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function processor( $url )
	{
		$this->_processor = $url;
	}
	//------------------------------------------------------------------------
	public function render( $returnOnly = true )
	{
		$atr['method']	= 'POST';
		$atr['action']	= $this->_processor;

		if ( ! empty( $this->_id ) )
		{
			$atr['id']		= $this->_id;
		}
		
		$curGroup = $this->_fields[0]->group;

		$out  = $this->_template->formTag( $atr, $this );
		$out .= $this->_template->beforeFields( $this, $curGroup );

		foreach ( $this->_fields as &$field )
		{
			if ( $field->group != $curGroup )
			{
				$out .= $this->_template->afterFields( $this );
				$out .= $this->_template->beforeFields( $this, $field->group );
			}
			
			$curGroup = $field->group;
			if ( ! is_null( $this->_ds ) )
			{
				$field->setValue( $this->_ds );
			}
			$field->setError( $this->errors );
			$out .= $field->render();
		}
		$out .= $this->_template->afterFields( $this );
		$out .= $this->_template->closeForm( $this );
		$out .= '</form>';

		if ( $returnOnly === false )
		{
			echo $out;
		}

		return $out;
	}

	//------------------------------------------------------------------------

	public function validate()
	{
		if ( is_null( $this->_ds ) )
		{
			throw new tgsfFormException( 'The form has no datasource to validate with.' );
		}
		
		$v = $this->_getValidator();
		$this->_setupValidate( $v );
		if ( $this->_validator->execute( $this->_ds ) === false )
		{
			$this->errors = $this->_validator->errors;
			$this->_ro_valid = false;
		}
	}
}

//------------------------------------------------------------------------
// Abstract form template class defines the methods a form template class must implement
//------------------------------------------------------------------------

abstract class tgsfFormTemplate extends tgsfBase
{
	protected $_field;
	//------------------------------------------------------------------------

	public function hidden( &$field )
	{
		return html_form_hidden( $field->name, $field->value );
	}
	
	abstract public function formTag( $atr, &$form );
	abstract public function closeForm( &$form );
	abstract public function beforeFields(  &$form, $group = '' );
	abstract public function afterFields( &$form );
	
	abstract public function dropdown( &$field );
	//abstract public function optionList( &$field, $atr );
	abstract public function file( &$field  );
	abstract public function text( &$field );
	abstract public function textArea( &$field );
	abstract public function radio( &$field );
	abstract public function checkbox( &$field );
	abstract public function image( &$field );
	abstract public function button( &$field );
	abstract public function submit( &$field );
	abstract public function reset( &$field );
	abstract public function password( &$field );
	abstract public function other( &$field );
}