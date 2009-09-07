<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
abstract class tgsfValidateRule extends tgsfBase
{
	public		$valid			= false;
	public		$errorMessage	= '';
	protected	$_params		= array();

	//------------------------------------------------------------------------
	abstract public function execute( $fieldName, $ds );
	//------------------------------------------------------------------------
	public function __set( $name, $value )
	{
		$this->_params[$name] = $value;
	}
	//------------------------------------------------------------------------
	public function __get( $name )
	{
		return $this->_params[$name];
	}
	//------------------------------------------------------------------------
	public function __isset( $name )
	{
		return isset( $this->_params[$name] );
	}
	//------------------------------------------------------------------------
	public function __unset( $name )
	{
		unset( $this->_params[$name] );
	}
	//------------------------------------------------------------------------
	public function getError()
	{
		$retVal = '';
		if ( $this->valid === false )
		{
			$retVal = $this->errorMessage;
		}
		return $retVal;
	}
	//------------------------------------------------------------------------
	public function &errorMessage( $msg )
	{
		$this->errorMessage = $msg;
		return $this;
	}
}
//------------------------------------------------------------------------
class tvr_alpha extends tgsfValidateRule
{
	public $errorMessage = ' must contain only letters';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[a-z\s]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_alpha_numeric extends tgsfValidateRule
{
	public $errorMessage = ' must contain only letters and numbers';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[0-9a-z\s]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_required extends tgsfValidateRule
{
	public $errorMessage = ' is required';
	public function execute( $fieldName, $ds )
	{
		$value = $ds->_( $fieldName );
		$this->valid = (string)$value != '';
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_min_len extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be at least ' . $this->min . ' letters long';
		$this->valid = strlen( $ds->_( $fieldName ) ) >= $this->minLen;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_max_len extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must not be longer than ' . $this->maxLen . ' letters';
		$this->valid = strlen( $ds->_( $fieldName ) ) <= $this->maxLen;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_int extends tgsfValidateRule
{
	public $errorMessage = ' must be an integer (no decimal point)';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[0-9]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_clean extends tgsfValidateRule
{
	public $errorMessage = ' must only contain letters, spaces, numbers, dashes, underscores and periods';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[0-9a-z._\- ]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_gt extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be greater than ' . $this->value;
		
		$value = $ds->_( $fieldName );
		$this->valid = int($value) > (int)$this->value || (float)$value > (float)$this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_gte extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be greater than or equal to ' . $this->value;
		
		$value = $ds->_( $fieldName );
		$this->valid = int($value) >= (int)$this->value || (float)$value >= (float)$this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_lt extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be less than ' . $this->value;
		
		$value = $ds->_( $fieldName );
		$this->valid = int($value) < (int)$this->value || (float)$value < (float)$this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_lte extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be less than or equal to ' . $this->value;
		
		$value = $ds->_( $fieldName );
		$this->valid = int($value) <= (int)$this->value || (float)$value <= (float)$this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_neq extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must not equal ' . $this->value;
		$value = $ds->_( $fieldName );
		$this->valid = $value != $this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_date extends tgsfValidateRule
{
	public $errorMessage = ' must be a valid date';
	public function execute( $fieldName, $ds )
	{
		$pieces = preg_split( '%[-/.]%i', $ds->_( $fieldName ) );

		if ( count( $pieces ) != 3 )
		{
			$this->valid = false;
		}
		elseif ( strlen( $pieces[0] ) == 4 )
		{
			// yyyy-mm-dd
			$year	- (int)$pieces[0];
			$month	= (int)$pieces[1];
			$day	= (int)$pieces[2];
			$this->valid = checkdate( $month, $day, $year );
		}
		elseif ( strlen( $pieces[2] ) == 4 )
		{
			// mm-dd-yyyy
			$month	= (int)$pieces[0];
			$day	= (int)$pieces[1];
			$year	- (int)$pieces[2];
			$this->valid = checkdate( $month, $day, $year );
		}
		
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_match_field extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must match ' . $this->fieldCaption;
		$this->valid = $ds->_( $fieldName ) == $ds->_( $this->field );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_match_value extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be ' . $this->valid ;
		$this->valid = $ds->_( $fieldName ) == $this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_db_unique extends tgsfValidateRule
{
	public $errorMessage = ' must be unique';
	public function execute( $fieldName, $ds )
	{
		$q = new query();
		$q->select( 'count(*)' )->from( $this->table )->where( $this->whereField . '=:dbuwp' )->bindValue( 'dbuwp', $ds->_( $fieldName ), ptSTR );
		$cnt = $q->exec()->fetchColumn();
		$this->valid = $cnt == 0;
	}
}