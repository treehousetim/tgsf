<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$anchor = new tgsfHtmlTag( 'a' );
$anchor->addAttribute( 'href', 'http://www.example.com/' );
$anchor->content( 'Click Here' );
echo $anchor;

echo tgsfHtmlTag::factory( 'a' )
    ->addAttribute( 'href', 'http://www.example.com/' )
    ->content( 'Click Here' );

