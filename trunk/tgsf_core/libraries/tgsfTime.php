<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
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
		if ( $GLOBALS['customTs'] != null )
		{
			return $GLOBALS['customTs'];
		}

		$ts = time();

		return $ts;
	}
}