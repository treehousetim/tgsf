<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class date extends tgsfBase
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getDayForDate( $date, $tz = TZ_DEFAULT  )
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
	* Returns the current datetime, format defaults to date and time
	* @param String The format to use for returning
	* @param String The timezone string
	*/
	static public function currentDatetime( $format = DT_FORMAT_SQL, $tz = TZ_DEFAULT )
	{
		$dt = new DateTime( tz_date(DT_FORMAT_SQL, time::currentTs(), $tz), new DateTimeZone( $tz ) );
		return $dt->format( $format );
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
	* Adds x days to the supplied date - if you only pass a date it simply adds 1 day
	* @param String The date to add days to
	* @param Int The number of days to add (no default, othersiwe it is not obvious what is happening)
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
	* Formats the provided date
	* @param String The date to format
	*/
	static public function format( $date, $format = DT_FORMAT_SQL )
	{
		$dt = new DateTime(  $date );
		return $dt->format( $format );
	}
	//------------------------------------------------------------------------
	static public function formatShortFileDate( $date )
	{
		if ( strlen($date) == 6 )
		{
			$date = '20' . substr($date,0,2) . '-' . substr($date, 2,2) . '-' . substr($date,4,2);
		}

		return $date;
	}
	//------------------------------------------------------------------------
	static public function formatShortFileTime( $time )
	{
		if ( strlen($time) == 4 )
		{
			$time = substr($time,0,2) . ':' . substr($time, 2,2);
		}

		return $time;
	}
	//------------------------------------------------------------------------
	static public function currentMonthBeg()
	{
		return gmdate( DT_FORMAT_SQL, tz_strtotime( gmdate( 'Y-m-1 00:00:00', time::currentTs() ), TZ_DEFAULT ) );
	}
	//------------------------------------------------------------------------
	static public function currentMonthEnd()
	{
		return gmdate( DT_FORMAT_SQL, tz_strtotime( gmdate( 'Y-m-t 23:59:59', time::currentTs() ), TZ_DEFAULT ) );
	}
	//------------------------------------------------------------------------
	static public function jsDateObject( $date )
	{
		$dt = new DateTime( $date );
		return 'new Date( ' . $dt->format( 'Y, n-1, j, G, i, s' ) . ')';
	}
	//------------------------------------------------------------------------
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
