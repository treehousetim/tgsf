<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
abstract class tgsfValidateRule extends tgsfBase
{
	public		$emptyValueValid	= false;
	public		$valid				= false;
	public		$errorMessage		= '';
	protected	$_params			= array();
	protected	$_field				= null;

	//------------------------------------------------------------------------
	abstract public function execute( $fieldName, $ds );
	public function jsCode() { return ''; }
	//------------------------------------------------------------------------
	/**
	* The constructor - accepts a validation field object
	*/
	public function __construct( &$field )
	{
		$this->setField( $field );
	}
	//------------------------------------------------------------------------
	/**
	* Sets the field this rule belongs to
	* @param Object The field object this rule belongs to.
	*/
	public function setField( &$field )
	{
		$this->_field =& $field;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getField()
	{
		return $this->_field;
	}
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
	//------------------------------------------------------------------------
	public function &error_message( $msg )
	{
		return $this->errorMessage( $msg );
	}
	//------------------------------------------------------------------------
	/**
	* Sets the javascript code for
	*/
	public function getJs()
	{
		return $this->jsCode();
	}
}

//------------------------------------------------------------------------
//========================================================================
//------------------------------------------------------------------------

class tvr_alpha extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must contain only letters';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[a-z\s]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
	//------------------------------------------------------------------------
	public function jsCode()
	{
		$code = <<< alphaJS

		if ( $( this ).value().match( /^[a-z\s]+$/ ) == false )
		{
			valid = false;
			errorMsg = " and $this->errorMessage";
		}
alphaJS;
		return $code;
	}
}
//------------------------------------------------------------------------
class tvr_alpha_numeric extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must contain only letters and numbers';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[0-9a-z\s]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_alphanum_extended extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' may not contain / \ ? or = characters.';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '%^[^/\\\\?=]+$%', $ds->_( $fieldName ) );
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
		$this->valid = trim( (string)$value ) != '';
		return $this->valid;
	}
	//------------------------------------------------------------------------
	public function jsCode()
	{
		$code = <<< alphaJS

		if ( $( this ).val().trim() == '' )
		{
			valid = false;
			//errorMsg = " and $this->errorMessage";
			$(this).setLabelError( '$this->errorMessage' )
		}
alphaJS;
		return $code;
	}
}
//------------------------------------------------------------------------
class tvr_min_len extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be at least ' . $this->minLen . ' letters long';
		$this->valid = strlen( trim( $ds->_( $fieldName ) ) ) >= $this->minLen;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_max_len extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must not be longer than ' . $this->maxLen . ' letters';
		$this->valid = strlen( trim( $ds->_( $fieldName ) ) ) <= $this->maxLen;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_int extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a whole number (no decimal point)';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[0-9]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_numeric extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a number';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match('/^[+-]?[0-9]*[.]?+[0-9]*$/', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_clean extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' may only contain: A-Z, 0-9, dashes, underscores and periods.';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[0-9a-z.,_\-\' ]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_clean_address extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a properly formatted address.';
	public function execute( $fieldName, $ds )
	{
		// dash must be last in this expression
		$this->valid = preg_match('/^[A-Z0-9@#*(),.:& -]+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_clean_question extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must only contain letters, spaces, numbers, dashes, underscores, periods, commas, slashes and question marks';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^[\/\'0-9a-z._\-? ]+$/i', $ds->_( $fieldName ) );
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
		if ( empty( $value ) )
		{
			return $this->valid = false;
		}

		$this->valid = (int)$value > (int)$this->value || (float)$value > (float)$this->value;
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
		if ( empty( $value ) )
		{
			return $this->valid = false;
		}

		$this->valid = (int)$value >= (int)$this->value || (float)$value >= (float)$this->value;
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
		if ( empty( $value ) )
		{
			return $this->valid = false;
		}

		$this->valid = (int)$value < (int)$this->value || (float)$value < (float)$this->value;
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

		$this->valid = (int)$value <= (int)$this->value || (float)$value <= (float)$this->value;
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_lte_float extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be less than or equal to ' . $this->value;

		$value = $ds->_( $fieldName );

		$this->valid = (float)$value <= (float)$this->value;
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
class tvr_email extends tgsfValidateRule
{
	// loose validation - matches any characters before and after an @
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid email address';
	public function execute( $fieldName, $ds )
	{
		$this->valid = preg_match( '/^.+@.+$/i', $ds->_( $fieldName ) );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_date extends tgsfValidateRule
{
	public $emptyValueValid = false;
	public $errorMessage = ' must be a valid date';
	public function execute( $fieldName, $ds )
	{
		$value = trim( $ds->_( $fieldName ) );

		if ( $value == '' )
		{
			$this->valid = true;
			return true;
		}

		$pieces = preg_split( '%[-/.]%i', $value );

		if ( count( $pieces ) != 3 )
		{
			$this->valid = false;
		}
		elseif ( strlen( $pieces[0] ) == 4 )
		{
			// yyyy-mm-dd
			$year	= (int)$pieces[0];
			$month	= (int)$pieces[1];
			$day	= (int)$pieces[2];
			$this->valid = checkdate( $month, $day, $year );
		}
		elseif ( strlen( $pieces[2] ) == 4 )
		{
			// mm-dd-yyyy
			$month	= (int)$pieces[0];
			$day	= (int)$pieces[1];
			$year	= (int)$pieces[2];
			$this->valid = checkdate( $month, $day, $year );
		}

		return $this->valid;
	}
}

//------------------------------------------------------------------------
/*
 * Ensure a date is is a specific number of days in the future
 */
class tvr_future_date extends tvr_date
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be a valid date ' . (int)$this->numDays . ' day(s) after today';

		parent::execute( $fieldName, $ds );

		if ( $this->valid )
		{
			$date = strtotime( tz_gmdate_start( DT_FORMAT_SQL, tz_strtotime( $ds->{$fieldName}, $this->tz ), $this->tz ) );
			$now  = strtotime( tz_gmdate_start( DT_FORMAT_SQL, time() + ( intval( $this->numDays ) * DT_TIME_DAY ), $this->tz ) );
			$this->valid = $date >= $now;
		}

		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_match_field extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		if ( $this->overrideError !== '' )
		{
			$this->errorMessage = $this->overrideError;
		}
		else
		{
			$this->errorMessage = ' must match ' . $this->fieldCaption;
		}
		$this->valid = $ds->_( $fieldName ) == $ds->_( $this->field );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_not_match_field extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		if ( $this->overrideError !== '' )
		{
			$this->errorMessage = $this->overrideError;
		}
		else
		{
			$this->errorMessage = ' must not be the same as ' . $this->fieldCaption;
		}
		$this->valid = $ds->_( $fieldName ) != $ds->_( $this->field );
		return $this->valid;
	}
}
//------------------------------------------------------------------------
class tvr_match_value extends tgsfValidateRule
{
	public function execute( $fieldName, $ds )
	{
		$this->errorMessage = ' must be ' . $this->value ;
		return $this->valid = $ds->_( $fieldName ) == $this->value;
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
		return $this->valid = $cnt == 0;
	}
}
//------------------------------------------------------------------------
class tvr_db_exists extends tgsfValidateRule
{
	public $errorMessage = ' doesn\'t exist';
	public function execute( $fieldName, $ds )
	{
		$q = new query();
		$q->select( 'count(*)' )->from( $this->table )->where( $this->whereField . '=:dbuwp' )->bindValue( 'dbuwp', $ds->_( $fieldName ), ptSTR );
		$cnt = $q->exec()->fetchColumn();
		return $this->valid = $cnt > 0;
	}
}
//------------------------------------------------------------------------
class tvr_usa_phone extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid US phone number example: 123-456-7890 - no extensions allowed';
	public function execute( $fieldName, $ds )
	{
		return $this->valid = preg_match('/^\\s*\\(?\\s*[0-9]{3}\\s*\\)?[-. ]?[0-9]{3}[-. ]?[0-9]{4}/', $ds->_( $fieldName ) );
	}
}
//------------------------------------------------------------------------
class tvr_usa_state extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid US state';
	public function execute( $fieldName, $ds )
	{
		load_config( 'us_states', IS_CORE_CONFIG );
		$stateList = config( 'us_states' );
		$value = $ds->_( $fieldName );
		return $this->valid = in_array( strtoupper( $value ), $stateList );
	}
}
//------------------------------------------------------------------------
class tvr_usa_canada_state extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid US state or Canadian Province';
	public function execute( $fieldName, $ds )
	{
		load_config( 'us_canada_states', IS_CORE_CONFIG );
		$stateList = config( 'us_canada_states' );
		$value = $ds->_( $fieldName );
		return $this->valid = in_array( strtoupper( $value ), $stateList );
	}
}

//------------------------------------------------------------------------
class tvr_usa_zipcode extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid zip code';
	public function execute( $fieldName, $ds )
	{
		$value = $ds->_( $fieldName );
		return $this->valid = preg_match( '/^[0-9]{5}(?:-[0-9]{4})?$/', $value );
	}
}
//------------------------------------------------------------------------
class tvr_bank_routing extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid 9-digit routing number';
	public function execute( $fieldName, $ds )
	{
		/*
			Routing Number: (http://en.wikipedia.org/wiki/Routing_number#Number_format_and_standards)
				Strip spaces for numbers such as "12 3456789"
				The first two digits of the nine digit ABA number must be in the ranges 00 through 12, 21 through 32, 61 through 72, or 80.
				The entire number must pass checksum calc: (3 * (d1 + d4 + d7) + 7 * (d2 + d5 + d8) + d3 + d6 + d9 ) % 10 == 0
		*/

		$value = $ds->_( $fieldName );
		$value = str_replace( ' ', '', $value );

		if ( preg_match( '/^[0-9]+$/i', $value ) && strlen($value) == 9 )
		{
			$pair = (int)substr( $value, 0, 2 );

			if ( ($pair < 0) ||
			     ($pair > 12 && $pair < 21) ||
			     ($pair > 32 && $pair < 61) ||
			     ($pair > 32 && $pair < 61) ||
			     ($pair > 72 && $pair < 80) ||
			     ($pair > 80) )
			{
				return $this->valid = false;
			}

			$d1 = (int)substr( $value, 0, 1 );
			$d2 = (int)substr( $value, 1, 1 );
			$d3 = (int)substr( $value, 2, 1 );
			$d4 = (int)substr( $value, 3, 1 );
			$d5 = (int)substr( $value, 4, 1 );
			$d6 = (int)substr( $value, 5, 1 );
			$d7 = (int)substr( $value, 6, 1 );
			$d8 = (int)substr( $value, 7, 1 );
			$d9 = (int)substr( $value, 8, 1 );

			return $this->valid = ((3 * ($d1 + $d4 + $d7) + 7 * ($d2 + $d5 + $d8) + $d3 + $d6 + $d9 ) % 10 == 0);
		}

		return $this->valid = false;
	}
}
//------------------------------------------------------------------------
class tvr_credit_card extends tgsfValidateRule
{
	public $emptyValueValid = true;
	public $errorMessage = ' must be a valid credit card number';
	public function execute( $fieldName, $ds )
	{
		return $this->valid = false; /* WIP */
	}
}
//------------------------------------------------------------------------
class tvr_custom extends tgsfValidateRule
{
	public $errorMessage = ' YOU MUST MANUALLY SET YOUR ERRORS IN A CUSTOM VALIDATION RULE';
	public function execute( $fieldName, $ds )
	{
		return $this->valid = call_user_func( $this->callBack, $ds, $this );
	}
}
