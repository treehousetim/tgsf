<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
* @ENUM
* the validation types
*/
enum( 'vt_',
	array(
		'alpha',
		'alpha_numeric',
		'required',
		'min_len',
		'max_len',
		'int',
		'clean',
		'gt',
		'lt',
		'gte',
		'lte',
		'neq',
		'date',
		'match_field',
		'not_match_field',
		'match_value',
		'db_unique',
		'db_exists',
		'usa_phone',
		'usa_state',
		'usa_zipcode',
		'bank_routing',
		'credit_card',
		'custom'
		), ENUM_USE_VALUE
	);
define( 'FORCE_VALID', true );
define( 'FORCE_INVALID', false );

class tgsfValidate extends tgsfBase
{
	protected $_fields		= array();
	protected $_ds			= null;
	protected $_ro_errors	= array();
	protected $_ro_valid	= true;
	//------------------------------------------------------------------------
	/**
	* an alias to addField
	*/
	public function &_( $fieldName )
	{
		return $this->addField( $fieldName );
	}
	//------------------------------------------------------------------------
	/**
	* Adds a new field to this validate object
	* @param String The name of the field to add.
	*/
	public function &addField( $name )
	{
		if ( ! isset( $this->_fields[$name] ) )
		{
			$this->_fields[$name] = new tgsfValidateField( $name );
			$this->_fields[$name]->fieldName = $name;
		}
		
		return $this->_fields[$name];
	}
	//------------------------------------------------------------------------
	/**
	* Allows a field object to be retrieved using ->fieldName instead of a function call
	* If no field exists for the provided member var name, we pass this off to the parent
	* @param String The name of the field.
	*/
	public function __get( $name )
	{
		try
		{
			return parent::__get( $name );
		}
		catch ( Exception $e )
		{
			if ( isset( $this->_fields[$name] ) )
			{
				return $this->_fields[$name];
			}
		}

		throw new tgsfException( 'No validation Rule' );
	}
	//------------------------------------------------------------------------
	/**
	* gets a validation field object by name
	* @param String The name of the field
	*/
	public function &getField( $name )
	{
		if ( ! isset( $this->_fields[$name] ) )
		{
			throw new tgsfException( 'No validation rule exists for field "' . $name . '" when calling getField' );
		}
		return $this->_fields[$name];
	}
	//------------------------------------------------------------------------
	/**
	* Executes the validation for all fields
	* @param Object::tgsfDataSource The datasource to use for getting values to validate with.
	*/
	public function execute( &$ds )
	{
		$this->_ds =& $ds;
		foreach ( $this->_fields as &$field )
		{
			if ( $field->execute( $ds, $this->_ro_errors ) === false )
			{
				$this->_ro_valid = false;
			}
		}
		return $this->_ro_valid;
	}
}