<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
-----------------------------------------------------------------------------
| This file is copyright 2012 by TMLA INC ALL RIGHTS RESERVED.
|----------------------------------------------------------------------------
| A report factory for all sorts of different objects
|----------------------------------------------------------------------------
| Date			| Person		| Change Description
|----------------------------------------------------------------------------
| 2012-02-22	| TGallagher	| Created
-----------------------------------------------------------------------------
*/

class reportFactory
{
	public static function col()
	{
		$args = func_get_args();
		return new tgsfReportColBase( $args );
	}
}
//------------------------------------------------------------------------
class reportOutputFactory
{
	public static function browser()
	{
		return new reportOutputHtml();
	}
	//------------------------------------------------------------------------
	public static function html( $filename )
	{
		$object = new reportOutputHtml( $filename );
	}
	//------------------------------------------------------------------------
	public static function csv( $filename )
	{
		return new reportOutputCsv( $filename );
	}
}
//------------------------------------------------------------------------
class decoratorFactory
{
	public static function url( $url )
	{
		return new cdUrl( $url );
	}
	//------------------------------------------------------------------------
	public static function bool()
	{
		return new cdBool();
	}
}