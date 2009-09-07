<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
* Cleans out possible email header injection attacks.  Spammers try to inject new line characters as delimiters
* so they can put in their own headers in an email message.  We're simply removing them.  these characters
* should not exist in email addresses or in email subjects.  don't use this on message bodies.
* @param String The text to clean (something like an email address that was typed in)
*/
function clean_for_email( $inbound )
{
    return str_replace( array( "\n", "\r" ), "", $inbound );
}
//------------------------------------------------------------------------
/**
* Trims an array - removes empty and null elements
* @param String The entire string to compare against
* @param String The snippet to test for at the beginning of $compare
*/
function trimArray( $inArray )
{
    foreach ( $inArray as $key => $value )
    {
        if ( trim( $value ) !="" )
        {
            if ( is_int( $key ) )
            {
                $outArray[] = trim( $value );
            }
            elseif ( is_string( $key ) )
            {
                $outArray[$key] = trim( $value );
            }
        }
    }
    
    return $outArray;
}
//------------------------------------------------------------------------
/**
* Returns true if the string to compare starts with the snippet
* @param String The entire string to compare against
* @param String The snippet to test for at the beginning of $compare
*/
function starts_with( $subject, $snippet )
{
	if ( is_array( $snippet ) )
	{
		$out = false;
		foreach ( $snippet as $value )
		{
			if ( $value == substr( $subject, 0, strlen( $value ) ) )
			{
				$out = true;
				break;
			}
		}
	}
	else
	{
		$out = $snippet === substr( $subject, 0, strlen( $snippet ) );
	}
	
	return $out;
}

//------------------------------------------------------------------------
/**
* Returns true if the string to compare ends with the snippet
* @param String The entire string to compare against
* @param String The snippet to test for at the end of $compare
*/
function ends_with( $subject, $snippet )
{
	if ( is_array( $snippet ) )
	{
		$out = false;
		foreach ( $snippet as $value )
		{
			if ( $value == substr( $subject, -1 * strlen( $value ) ) )
			{
				$out = true;
				break;
			}
		}
	}
	else
	{
		$out = $snippet == substr( $subject, -1 * strlen( $snippet ) );
	}
	
	return $out;
}
//------------------------------------------------------------------------
/**
* Returns the specified number of tab characters - a silly function
* that only serves to create pretty looking code.
* @param Int The number of tab characters to return.
*/
function tab( $repeat )
{
	return str_repeat( "\t", $repeat );
}
//------------------------------------------------------------------------
/**
* Attempts to replicate the C language enum construct by creating defines
* for the array items passed in.
* @param String The name of the group/prefix for the enum'd values. eg. qt or QUERY_TYPE_
* @param Array The array of items to define values for.  If an array key is non-numeric  then that becomes the define name.
* @param bool Should enum use the value for the defined value or use the given array key 
* example: $arrayExample['DEF'] = 'value'; enum( 'example', $arrayExample ); creates this define:
* define( 'exampleDEF', 'value' );
*/
function enum( $prefix, $items, $useValueForDefine = false )
{
	if ( $useValueForDefine )
	{
		foreach ( $items as $key => $value )
		{
			define( $prefix . $value, $value );
		}
	}
	else
	{
		foreach ( $items as $key => $value )
		{
			if ( is_numeric( $key ) )
			{
				define( $prefix . $value, $key );
			}
			else
			{
				define( $prefix . $key, $value );
			}
		}
	}
}
//------------------------------------------------------------------------
/**
* if the passed argument is already an array then nothing is done.
* if the passed argument is not an array then an a
* @param Mixed The variable to test for arrayness
* @param Array The return variable 
*/
function arrayify( &$in, &$out )
{
	if ( ! is_array( $in ) )
	{
		$out = array();
		$out[] = $in;
	}
	else
	{
		$out = array();
		$out = $in;
	}
}
//------------------------------------------------------------------------
/**
* Determines if a string is a local file based on whether or not it
* begins with http:// or https://
* @param String The file or path to check
*/
function is_local( $file )
{
	// return if the file does not start with
	return ! starts_with( $file, array( 'http://', 'https://' ) );
}
//------------------------------------------------------------------------
function must_end_with( &$subject, $ending )
{
	if ( ! ends_with( $subject, $ending ) )
	{
		$subject .= $ending;
	}
}