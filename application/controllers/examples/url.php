<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// basic usage
echo URL( 'login' )->anchorTag( 'Caption' );
echo '<br>' . PHP_EOL;

// expanded anchor tag demo - using tgsfHtmlTag features
echo URL( 'login' )
    ->anchorTag( 'Caption' )
        ->css_class( 'nav-link' );
echo '<br>' . PHP_EOL;

// using notLocal()
echo URL( 'http://example.com/test' );
echo '<br>' . PHP_EOL;
echo URL( 'http://example.com/test' )->notLocal();