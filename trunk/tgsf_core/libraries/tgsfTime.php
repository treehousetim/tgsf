<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2011 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$GLOBALS['customTs'] = null;

class time extends tgsfBase
{
	/**
	* Returns the current time in $tz
	* @param String The timezone string
	*/
	static public function currentTs()
	{
// Do NOT Merge this Code - for DEMO only - to get a custom date set $GLOBALS['customTs'] to mktime(...);

		if ( $GLOBALS['customTs'] != null )
		{
			return $GLOBALS['customTs'];
		}

//end.
		$ts = time();

		return $ts;
	}
}