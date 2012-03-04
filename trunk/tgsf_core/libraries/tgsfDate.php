<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class date
{
	//------------------------------------------------------------------------
	/**
	* Either returns the logged in user's timezone or the value of TZ_DEFAULT
	*/
	static public function getTimezone()
	{
		return function_exists( 'AUTH' )?AUTH()->getLoginTimeZone():TZ_DEFAULT;
	}
	//------------------------------------------------------------------------
	/*
	 * Convert a date string for a specific timezone into a timestamp
	 * @param Str The date string
	 * @param Str The timezone of the date string
	 * @return Int The timestamp result
	 */
	static public function tz_strtotime( $text, $tz = 'UTC' )
	{
		if ( empty( $text )  )
		{
			return time::currentTs();
		}

		$ts = strtotime( $text . ' ' . $tz );

		return $ts;
	}

	//------------------------------------------------------------------------
	/*
	* $ts is expected to be a time stamp.  You can pass a string, but the string
	* must already be in UTC - the timezone here is only used for output
	* not for translating a passed $ts string
	*/
	static public function tz_gmdate_start( $format, $ts, $tz )
	{
		$ts = tz_strtotime( tz_date( DT_FORMAT_SQL_START, $ts, $tz ), $tz );
		return gmdate( $format, $ts );
	}
	//------------------------------------------------------------------------
	/*
	* $ts is expected to be a time stamp.  You can pass a string, but the string
	* must already be in UTC - the timezone here is only used for output
	* not for translating a passed $ts string
	*/
	static public function tz_gmdate_end( $format, $ts, $tz )
	{
		$ts = tz_strtotime( tz_date( DT_FORMAT_SQL_END, $ts, $tz ), $tz );
		return gmdate( $format, $ts );
	}
	
	
	/**
	* wraps the getdate php function and honors the supplied timezone
	* sets the php default timezone to UTC when done
	*/
	static public function getdate( $ts, $tz = TZ_DEFAULT )
	{
		$ts = is_int( $ts )?$ts : strtotime($ts);

		date_default_timezone_set( $tz );

		$result = getdate( $timestamp );

		date_default_timezone_set('UTC');

		return $result;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	static public function getDayForDate( $date, $tz = TZ_DEFAULT  )
	{
		$dt = new DateTime( $date, new DateTimeZone( $tz ) );
		return $dt->format( 'd' );
	}
	//------------------------------------------------------------------------
	/**
	* Converts a date string between timezones
	* @param String The date to convert
	* @param String The timezone the date is in (converting from)
	* @param String The timezone desired for the return value (converting to)
	* @param String The format to return - defaults to DT_FORMAT_SQL
	*/
	static public function convertTz( $date, $fromTz, $toTz, $format = DT_FORMAT_SQL )
	{
		$dt = new DateTime( $date, new DateTimeZone( $fromTz ) );
		$dt->setTimezone( new DateTimeZone( $toTz ) );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	/**
	* Returns true if the two dates are on the same date of the year.
	* @param String date1
	* @param String date2
	*/
	static public function sameDate( $date1, $date2 )
	{
		// build DateTime objects
		$d1 = new DateTime( $date1, new DateTimeZone( TZ_DEFAULT ) );
		$d2 = new DateTime( $date2, new DateTimeZone( TZ_DEFAULT ) );

		// set to 0 time
		$d1->setTime( 0, 0, 0 );
		$d2->setTime( 0, 0, 0 );

		return $d1 == $d2;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current datetime, format defaults to date and time
	* @param String The format to use for returning
	* @param String The timezone string
	*/
	static public function currentDatetime( $format = DT_FORMAT_SQL, $tz = TZ_DEFAULT )
	{
		return date::convertTz( '@' . time::currentTs(), 'UTC', $tz, $format );
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current date, format defaults to date only
	* @param String The format to use for returning
	* @param String The timezone string
	*/
	static public function currentDate( $format = DT_FORMAT_SQL_DATE, $tz = TZ_DEFAULT )
	{
		return date::currentDatetime( $format, $tz );
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current date with the supplied time of day
	*/
	static public function currentDateForTime( $tod, $format = DT_FORMAT_SQL, $tz = TZ_DEFAULT )
	{
		$dt = new DateTime( date::currentDate(DT_FORMAT_SQL_DATE, 'UTC') . ' ' . $tod, new DateTimeZone( $tz ) );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	static public function tzDate( $ts, $format = DT_FORMAT_SQL, $tz = 'UTC' )
	{
		$tzo = new DateTimeZone( $tz );
		$ts = is_int( $ts )?$ts : strtotime($ts);
		$dt = new DateTime( '@' . $ts, $tzo );
		$dt->setTimezone( $tzo );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	/**
	* returns the current date offset by the days
	* @param int The number of days to offset
	* @param string The operator either + or -
	* @param String The format to use for returning
	* @param String The timezone string - defaults to TZ_DEFAULT
	*/
	static public function currentDateOffsetDays( $days, $operator = '+', $format = DT_FORMAT_SQL_DATE, $tz = TZ_DEFAULT )
	{
		$dt = new DateTime( date::currentDate() . ' ' . $operator . $days . ' days', new DateTimeZone( $tz ) );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current date, format defaults to date only
	* @param String The format to use for returning
	*/
	static public function UTCcurrentDatetime( $format = DT_FORMAT_SQL )
	{
		return date::currentDatetime( $format, 'UTC' );
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current date, format defaults to date only
	* @param String The format to use for returning
	*/
	static public function UTCcurrentDate( $format = DT_FORMAT_SQL_DATE )
	{
		return date::currentDatetime( $format, 'UTC' );
	}
	//------------------------------------------------------------------------
	/**
	* Adds x days to the supplied date
	* Use positive numbers to add, negative to subtract.
	* @param String The date to work with
	* @param Int The number of days to add (no default, otherwise it is not obvious what is happening)
	* @param String The date format - defaults to DT_FORMAT_SQL_DATE
	* @param String The timezone - defaults to TZ_DEFAULT
	*/
	static public function addDays( $date, $days, $format = DT_FORMAT_SQL_DATE, $tz = TZ_DEFAULT )
	{
		$dt = new DateTime(  $date . ' +' . $days . ' days', new DateTimeZone( $tz ) );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	/**
	* Simply formats the supplied date with the given format.
	* @param String The date to format
	*/
	static public function format( $date, $format = DT_FORMAT_SQL )
	{
		$dt = new DateTime(  $date );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	/**
	* Shortcut helper function.
	*/
	static public function formatShortFileDate( $date )
	{
		if ( strlen($date) == 6 )
		{
			$date = '20' . substr($date,0,2) . '-' . substr($date, 2,2) . '-' . substr($date,4,2);
		}

		return $date;
	}
	//------------------------------------------------------------------------
	/**
	* Shortcut helper function.
	*/
	static public function formatShortFileTime( $time )
	{
		if ( strlen($time) == 4 )
		{
			$time = substr($time,0,2) . ':' . substr($time, 2,2);
		}

		return $time;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current month's starting date.
	*/
	static public function currentMonthBegin()
	{
		return gmdate( DT_FORMAT_SQL, date::tz_strtotime( gmdate( 'Y-m-1 00:00:00', time::currentTs() ), TZ_DEFAULT ) );
	}
	//------------------------------------------------------------------------
	/**
	* Returns the current month's ending date.
	*/
	static public function currentMonthEnd()
	{
		return gmdate( DT_FORMAT_SQL, date::tz_strtotime( gmdate( 'Y-m-t 23:59:59', time::currentTs() ), TZ_DEFAULT ) );
	}
	//------------------------------------------------------------------------
	/**
	* Returns a snippet of javascript code that creates a new javascript date object for the given php date.
	* @param String - a date
	*/
	static public function jsDateObject( $date )
	{
		$dt = new DateTime( $date );
		return 'new Date( ' . $dt->format( 'Y, n-1, j, G, i, s' ) . ')';
	}
	//------------------------------------------------------------------------
	/**
	* Compares two dates using the supplied operator
	* @param Date 1
	* @param Date 2
	* @param the operator - one of: ==,=>,<=,<,>
	*/
	static public function compare( $date1, $date2, $operator )
	{
		$dt1 = new DateTime( $date1 );
		$dt2 = new DateTime( $date2 );
		
		switch ( $operator )
		{
		case '=':
		case '==':
		case '===':
			return $dt1 == $dt2;
			break;

		case '=>':
		case '>=':
			return $dt1 >= $dt2;
			break;

		case '<=':
		case '=<':
			return $dt1 <= $dt2;
			break;

		case '<':
			return $dt1 < $dt2;
			break;

		case '>':
			return $dt1 > $dt2;
			break;

		default:
			throw new tgsfException( 'Illegal Compare operator in tgsfDate - ' . $operator );
		}
	}
}
