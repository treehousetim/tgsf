<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
class tgsfValidateField extends tgsfBase
{
	protected	$_ds;
	protected	$_rules = array();
	protected	$_ro_valid	= false;
	//------------------------------------------------------------------------
	public		$fieldName;
	//------------------------------------------------------------------------
	protected function &_( $ruleType )
	{
		$className = 'tvr_' . $ruleType;
		
		if ( ! class_exists( $className ) )
		{
			throw new tgsfValidationException( 'Undefined validation rule type: ' . $ruleType );
		}
		
		$rule = new $className( $field );
		$this->_rules[] =& $rule;
		return $rule;
	}
	//------------------------------------------------------------------------
	public function execute( &$ds, &$errors )
	{
		foreach ( $this->_rules as &$rule )
		{
			$rule->execute( $this->fieldName, $ds );
			if ( ! $rule->valid )
			{
				$errors[$this->fieldName][] = $rule->errorMessage;
				$this->_ro_valid = false;
			}
		}
		
		return $this->_ro_valid;
	}
	//------------------------------------------------------------------------
	public function &alpha()
	{
		$this->_( vt_alpha );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &alpha_numeric()
	{
		$this->_( vt_alpha_numeric );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &required()
	{
		$this->_( vt_required );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &min_len( $min )
	{
		$rule =& $this->_( vt_min_len );
		$rule->minLen = $min;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &max_len( $max )
	{
		$rule =& $this->_( vt_max_len );
		$rule->maxLen = $max;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &int()
	{
		$this->_( vt_int );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &clean()
	{
		$this->_( vt_clean );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &gt( $value )
	{
		$rule =& $this->_( vt_gt );
		$rule->value = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &gte( $value )
	{
		$rule =& $this->_( vt_gte );
		$rule->value = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &lt( $value )
	{
		$rule =& $this->_( vt_lt );
		$rule->value = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &lte( $value )
	{
		$rule =& $this->_( vt_lte );
		$rule->value = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &neq( $value )
	{
		$rule =& $this->_( vt_neq );
		$rule->value = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &date()
	{
		$this->_( vt_date );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &match_field( $field, $fieldCaption )
	{
		$rule =& $this->_( vt_match_field );
		$rule->field = $field;
		$rule->fieldCaption = $fieldCaption;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &match_value( $value )
	{
		$rule =& $this->_( vt_match_value );
		$rule->value = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &db_unique( $table, $whereField )
	{
		$rule =& $this->_( vt_db_unique );
		$rule->table = $table;
		$rule->whereField = $whereField;
		return $this;
	}
}
