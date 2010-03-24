<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// this simply presents a url view of a CLI invoked controller.

echo "URL = " . (string) CLI();
echo PHP_EOL . PHP_EOL;
CLI()->debug();
echo PHP_EOL;
