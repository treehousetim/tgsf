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
		'match_value',
		'db_unique'
		), ENUM_USE_VALUE
	);

class tgsfValidate extends tgsfBase
{
	protected $_fields		= array();
	protected $_ds			= null;
	protected $_ro_errors	= array();
	protected $_ro_valid	= true;
	//------------------------------------------------------------------------
	protected function &_getField( $name )
	{
		if ( ! isset( $this->_fields[$name] ) )
		{
			$this->_fields[$name] = new tgsfValidateField( $name );
			$this->_fields[$name]->fieldName = $name;
		}
		
		return $this->_fields[$name];
	}
	//------------------------------------------------------------------------
	public function &_( $fieldName )
	{
		return $this->_getField( $fieldName );
	}
	//------------------------------------------------------------------------
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