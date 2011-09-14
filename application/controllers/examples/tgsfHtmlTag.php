<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$windowTitle = 'tgsfHtmlTag Examples';
$extraCss[] = css_path() . 'examples.css';

include view( 'header' );

$anchor = new tgsfHtmlTag( 'a' );
$anchor->addAttribute( 'href', 'http://www.example.com/' );
$anchor->content( 'Click Here - Example 1' );
echo $anchor;

echo '<br />';

echo tgsfHtmlTag::factory( 'a' )
    ->addAttribute( 'href', 'http://www.example.com/' )
    ->content( 'Click Here - Example 2' );

//------------------------------------------------------------------------
$table = tgsfHtmlTag::factory( 'table' )
	->id( 'example-grid' )
	->cssClass( 'grid' );

for ( $row = 1; $row < 11; $row++ )
{
	$tr = $table->addTag( 'tr' );
	for ( $col = 1; $col < 5; $col++ )
	{
		$td = $tr->addTag( 'td' )
			->cssClass( 'row' . $row )
			->cssClass( 'col' . $col )
			->content( $row . ', ' . $col );
	}
}

echo $table;

echo '<br />';
echo tgsfHtmlTag::factory( 'span' )
	->addAttribute( 'title', 'My Title' )
	->content( 'In a span' );

echo '<br />';
$a = tgsfHtmlTag::factory( 'a' )
	->content( 'Click Me' );

$a->rel = 'nofollow';
$a->href = 'http://google.com/';
$a->onClick = "alert( 'Clicked!' );";

echo $a;
echo '<br />';
echo tgsfHtmlTag::factory( 'div' )
	->cssClass( 'example-divs' )
	->addTag( 'p' )
		->content( 'This is paragraph Text' )
		->cssClass( 'example-paragraphs' )
	->parent;

echo '<br />';
$p = tgsfHtmlTag::factory( 'p' )
	->content( 'This is paragraph Text' )
	->cssClass( 'example-paragraphs' );

echo tgsfHtmlTag::factory( 'div' )
	->cssClass( 'example-divs' )
	->addTag( $p )->parent;

echo '<br />';
$tag = tgsfHtmlTag::factory( 'div' )
	->contentFilter( 'strtoupper' );

echo $tag->content( 'uppercase text' );

include view( 'footer' );