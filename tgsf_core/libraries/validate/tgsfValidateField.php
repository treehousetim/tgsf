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
	protected	$_ro_valid	= true;
	//------------------------------------------------------------------------
	public		$fieldName;
	//------------------------------------------------------------------------
	/**
	* Adds a new validation rule to this validation field
	* @param Mixed.  If an instance of a validation rule, then the rule has its field set to this rule
	* and is added to this field's rules array.  If a string, then it is the rule type string
	* and is ends up being used to instantiate an object of class tvr_{$ruleType}
	*/
	public function &addRule( $ruleType )
	{
		if ( $ruleType instanceof tgsfValidateRule )
		{
			$this->_rules[] =& $ruleType;
			$ruleType->setField( $this );
			return $ruleType;
		}
		
		$className = 'tvr_' . $ruleType;
		
		if ( ! class_exists( $className ) )
		{
			throw new tgsfValidationException( 'Undefined validation rule type: ' . $ruleType );
		}
		
		$rule = new $className( $this );
		$this->_rules[] =& $rule;
		return $rule;
	}
	//------------------------------------------------------------------------
	/**
	* an alias to addRule
	*/
	protected function &_( $ruleType )
	{
		return $this->addRule( $ruleType );
	}
	//------------------------------------------------------------------------
	/**
	* Sets an error message on the last rule that was set up.  Must be called immediately after creating a new rule to apply the message to that rule.
	* @param String The message to use.
	*/
	public function &error_message( $msg )
	{
		$idx = count( $this->_rules ) -1;
		
		if ( $idx >= 0 && ! empty( $this->_rules[$idx] ) )
		{
			$this->_rules[$idx]->error_message( $msg );
		}
		else
		{
			throw new tgsfValidationException( 'Trying to set validation RULE message when no rule has been defined.' );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	public function execute( &$ds, &$errors )
	{
		foreach ( $this->_rules as &$rule )
		{
			if ( $rule->emptyValueValid === true )
			{
				$value = $ds->_( $this->fieldName );
				if ( empty( $value ) )
				{
					$rule->valid = true;
				}
				else
				{
					$rule->execute( $this->fieldName, $ds );
				}
			}
			else
			{
				$rule->execute( $this->fieldName, $ds );
			}
			
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
	public function &future_date()
	{
		$this->_( vt_future_date );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &date()
	{
		$this->_( vt_date );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &match_field( $field, $fieldCaption, $errorMessage = '' )
	{
		$rule =& $this->_( vt_match_field );
		$rule->field = $field;
		$rule->fieldCaption = $fieldCaption;
		$rule->overrideError = $errorMessage;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &not_match_field( $field, $fieldCaption, $errorMessage = '' )
	{
		$rule =& $this->_( vt_not_match_field );
		$rule->field = $field;
		$rule->fieldCaption = $fieldCaption;
		$rule->overrideError = $errorMessage;
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
	//------------------------------------------------------------------------
	public function &db_exists( $table, $whereField )
	{
		$rule =& $this->_( vt_db_exists );
		$rule->table = $table;
		$rule->whereField = $whereField;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &usa_phone()
	{
		$this->_( vt_usa_phone );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &usa_state()
	{
		$this->_( vt_usa_state );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &usa_zipcode()
	{
		$this->_( vt_usa_zipcode );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &bank_routing()
	{
		$this->_( vt_bank_routing );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &credit_card()
	{
		$this->_( vt_credit_card );
		return $this;
	}
	//------------------------------------------------------------------------
	public function &custom( $callBack )
	{
		$rule =& $this->_( vt_custom );
		$rule->callBack = $callBack;
		return $this;
	}
}
