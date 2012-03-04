<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
function searchEngineDetect()
{
	return tgsfSearchEngineDetect::getInstance();
}

class tgsfSearchEngineDetect extends tgsfBase
{
	private static	$_instance			= null;
	protected $_ro_terms;
	protected $_ro_engine;
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance()
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}

		return self::$_instance;
	}
	
	protected function __construct()
	{
	}
	//------------------------------------------------------------------------
	function detect()
	{
		if ( array_key_exists( 'HTTP_REFERER', $_SERVER ) )
		{
			$url = $_SERVER['HTTP_REFERER'];
		}

		$parts = parse_url( $url );

		$query = isset($parts_url['query']) ? $parts_url['query'] : (isset($parts_url['fragment']) ? $parts_url['fragment'] : '');
		if(!$query) {
		return '';
		}

		parse_str( $parts['query'], $query );

		$search_engines = array
		(
			'about' => 'terms',
			'alltheweb' => 'q',
			'aol' => 'q',
			'aol.' => 'query',
			'aol..' => 'encquery',
			'answers' => 's',
			'aolsearch' => 'q',
			'ask' => 'q',
			'baidu'	 => 'wd',
			'bing' => 'q',
			'cnn' => 'query',
			'google' => 'q',
			'google.se' => 'as_q',
			'images.google' => 'q',
			'live' => 'q',
			'lycos' => 'query',
			'msn' => 'q',
			'netscape' => 'query',
			'onet' => 'qt',
			'yahoo' => 'p',
		);
		preg_match( '/(' . implode('|', array_keys($search_engines)) . ')\./', $parts['host'], $matches );

		if ( isset( $matches[1] ) )
		{

		}
		return isset($matches[1]) && isset( $query[$search_engines[$matches[1]]] ) ? trim( $query[$search_engines[$matches[1]]] ):false;
	}
}