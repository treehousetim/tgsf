<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
function &FORMAT()
{
	return tgsfFormat::get_instance();
}
//------------------------------------------------------------------------
class tgsfFormat extends tgsfBase
{
	private static	$_instance			= null;
	protected $listCache				= array();

	//------------------------------------------------------------------------
	/**
	* protected to make a singleton instance
	*/
	protected function __construct()
	{
		// do nothing
	}

	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance()
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}

		return self::$_instance;
	}

	//------------------------------------------------------------------------
	/**
	* Prevent users from cloning the instance
	*/
	public function __clone()
	{
		throw new tgsfException( 'Cloning a singleton (tgsfGet) is not allowed. Use the FORMAT() function to get its instance.' );
	}

	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	public function translateListEntry( $listName, $entryValue )
	{
		if ( array_key_exists( $listName, $this->listCache ) == false )
		{
			if ( config( $listName ) == '' )
			{
				load_config( 'lists/' . $listName );
				$this->listCache[$listName] = config( $listName );
			}
		}

		if ( ! is_array( $this->listCache[$listName] ) )
		{
			throw new tgsfException( 'Lists must be arrays when translating list entries.' );
		}

		$list =& $this->listCache[$listName];

		if ( array_key_exists( $entryValue, $list ) == false )
		{
			throw new tgsfException( 'No Entry found in list: ' . $listName . ' for value: ' . $entryValue );
		}
		return $list[$entryValue];
	}
	//------------------------------------------------------------------------
	public function usa_phone( $text, $formatWithParens = false )
	{
		$pattern = '\\1-\\2-\\3';
		if ( $formatWithParens )
		{
			$pattern = '(\\1) \\2-\\3';
		}
		return trim( preg_replace('/\\(?([0-9]{3})\\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})/', $pattern, $text ) );
	}
	//------------------------------------------------------------------------
	/**
	* Formats a date
	*/
	public function date( $text, $format = DT_FORMAT_UI_DATE, $tz = 'UTC' )
	{
		if ( empty( $text ) )
		{
			$ts = time::currentTs();
		}
		else
		{
			$ts = strtotime( $text );
		}

		if ( $ts === false )
		{
			return '';
		}

		$date = new Zend_Date( $ts, Zend_Date::TIMESTAMP );
		$date->setTimezone( $tz );
		return $date->toString( $format ); //  . '(' . $text . ')';
	}
	//------------------------------------------------------------------------
	/**
	* Formats a raw date - no time zone is considered
	*/
	public function raw_date( $text, $format = DT_FORMAT_UI_DATE )
	{
		if ( empty( $text ) )
		{
			$ts = time::currentTs();
		}
		else
		{
			$ts = strtotime( $text );
		}

		if ( $ts === false )
		{
			return '';
		}

		$date = new Zend_Date( $ts, Zend_Date::TIMESTAMP );
//		$date->setTimezone( $tz );
		return $date->toString( $format );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function datetime( $text, $tz = 'UTC' )
	{
		return $this->date( $text, DT_FORMAT_UI_SHORT, $tz );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function mysqlDate( $text )
	{
		throw new tgsfException( 'mysqlDate() has been deprecated.' );
		return $this->date( $text, DT_FORMAT_SQL_DATE );
	}
	//------------------------------------------------------------------------
	/**
	* Formats a currency amount
	*/
	public function currency( $amount )
	{
		setlocale( LC_MONETARY, 'en_US' );

		// money_format is NOT supported under windows
		// return money_format( '%n', (float)$amount );

        // english notation with thousands seperator
        return number_format($amount, 2, '.', ',');
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function last_four( $data, $chr = '*' )
	{
		return $this->obfuscate( $data, 4, $chr );
	}
	//------------------------------------------------------------------------
	/**
	* Mask data such as account numbers, etc
	*/
	public function obfuscate( $data, $len = 4, $chr = '*' )
	{
		$repeat = strlen( trim( $data ) ) - (int)$len;

		if ( $repeat < 1 )
		{
			$repeat = ceil( strlen($data) / 2 );
		}

		$stars = $repeat;
		if ( $stars > 3 )
		{
			$stars = 3;
		}

		return str_repeat( $chr, $stars ) . substr( $data, $repeat );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function boolToYN( $value )
	{
		return (bool)$value?'Yes':'No';
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function boolToTF( $value )
	{
		return (bool)$value?'True':'False';
	}
	//------------------------------------------------------------------------
	public function boolCheck( $value )
	{
		$trueImg = tgsfHtmlTag::factory( 'img' )->setAttribute( 'src', image_url( 'elegant/checkmark.png' ) )->cssClass( 'bool-y' )->renderTagOnly();
		$falseImg = tgsfHtmlTag::factory( 'img' )->setAttribute( 'src', image_url( 'elegant/x.png' ) )->cssClass( 'bool-n' )->renderTagOnly();

		return ($value)? $trueImg:$falseImg;
	}
	//------------------------------------------------------------------------
	public function numbers_only( $value )
	{
		return preg_replace( '/[^\d]/', '', $value );
	}
	//------------------------------------------------------------------------
	public function slugToCssClasses( $slug )
	{
		$pieces = explode( '/', $slug );

		foreach( $pieces as $piece )
		{
			$out[] = $piece;
		}
		
		return implode( '-', $out ) . ' ' . implode( ' ', $out );
	}
}
