<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
// unlike some other datasources in tgsf, the URL datasource isn't a singleton
// but it does protect its constructor in favor of the use of a factory
//------------------------------------------------------------------------
// used when redirecting
// define( 'DO_NOT_EXIT', false );

function &URL( $url, $core = false )
{
	return tgsfUrl::factory( $url, $core );
}
//------------------------------------------------------------------------
class tgsfUrl extends tgsfDataSource
{
	protected	$_ro_url		= '';
	protected	$_ro_core		= false;
	protected	$_ro_prefix;
	protected	$_ro_separator;
	protected	$_ro_equals;
	protected	$_ro_local;

	//------------------------------------------------------------------------
	/**
	* The constructor sets the datasource type and the url.  initializes the url to be local
	*/
	protected function __construct( $url, $core )
	{
		$this->isLocal();

		$url = trim( $url, "\t\n\r /\\" ); // remove leading/trailing whitespace and slashes( back and forward)

		$this->_ro_core = (bool)$core;
		$this->_ro_url = $url;

		parent::__construct( dsTypeAPP );
	}
	//------------------------------------------------------------------------
	/**
	* returns a string of the url variables (if any)
	*/
	protected function getUrlVars()
	{
		$varArray = $this->dataArray();
		if ( count( $varArray ) < 1 )
		{
			return '';
		}
		$vars = array();
		
		foreach ( $varArray as $name => $value )
		{
			$vars[] = $name . $this->_ro_equals . $value;
		}
		return $this->_ro_prefix . implode( $this->_ro_separator, $vars );
	}
	//------------------------------------------------------------------------
	/**
	* Static factory that creates new url instances
	*/
	public static function &factory( $url, $core )
	{
		$c = __CLASS__;
		$instance = new $c( $url, $core );
		return $instance;
	}
	//------------------------------------------------------------------------
	public function __toString()
	{
		// if this is not a local url, then return the url itself with any vars appended.
		if ( $this->_ro_local === false )
		{
			return $this->_ro_url . $this->getUrlVars();
		}
		
		$url = $this->_ro_url . $this->getUrlVars();

		if ( defined( 'tgTrailingSlash' ) && tgTrailingSlash === true )
		{
			$url .= '/';
		}

		if ( $url == '/' )
		{
			$url = '';
		}
		
		$url = current_base_url() . $url;

		$url = do_filter( 'generate_url', $url );

		return $url;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a url to be local.  Changes the variable bits and pieces to the standard
	* tgsf style of /_/varname/varvalue
	* _/ / and /
	*/
	public function &isLocal()
	{
		$this->_ro_local = true;
		$this->_ro_prefix		= config( 'get_string' )!=''?config( 'get_string' ):'/_/';
		$this->_ro_separator	= config( 'get_separator')!=''?config( 'get_separator' ):'/';
		$this->_ro_equals		= config( 'get_equals' )!=''?config( 'get_equals' ):'/';
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a url to be not local.  This also resets the variable bits and pieces
	* back to a standard http get setup.
	* ? &amp; and =
	*/
	public function &notLocal()
	{
		$this->_ro_local = false;
		$this->_ro_prefix		= '?';
		$this->_ro_separator	= '&amp;';
		$this->_ro_equals		= '=';
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &anchorTag()
	{
		$a = new tgsfHtmlTag( 'a' );
		$a->href = $this->__toString();
		return $a;
	}
	//------------------------------------------------------------------------
	/**
	* A temporary redirect
	* @param Bool Exit after redirecting?  Send the define DO_NOT_EXIT to continue script execution
	*/
	public function &redirect( $exit = true )
	{
		header( "HTTP/1.1 303 See Other" );

		$urlStr = $this->__toString();

		if ( can_plugin() )
		{
			$urlStr = do_filter( 'temp_redirect_url', $urlStr );
		}

		header( 'Location: ' . $urlStr );

		if ( $exit )
		{
			exit();
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* A permanent redirect
	* @param Bool Exit after redirecting?  Send the define DO_NOT_EXIT to continue script execution
	*/
	public function &permRedirect( $exit = true )
	{
		header( "HTTP/1.1 301 Moved Permanently" );
		$urlStr = $this->__toString();

		if ( can_plugin() )
		{
			$urlStr = do_filter( 'perm_redirect_url', $urlStr );
		}
		header( 'Location: ' . $urlStr );
		
		if ( $exit )
		{
			exit();
		}

		return $this;
	}
}