<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
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
	protected	$_ro_anchorTag;
	protected	$_ro_addTrailingSlash = false;
	protected	$_ro_ignoreApp = false;

	//------------------------------------------------------------------------
	/**
	* The constructor sets the datasource type and the url.  initializes the url to be local
	*/
	protected function __construct( $url, $core )
	{
		$this->isLocal();
		$this->_ro_ignoreApp = starts_with( $url, '/' );

		$url = trim( $url, "\t\n\r /\\" ); // remove leading/trailing whitespace and slashes( back and forward)
		$this->_ro_core = (bool)$core;
		
		$this->_ro_addTrailingSlash = ( defined( 'tgTrailingSlash' ) && tgTrailingSlash === true ) && config( 'get_string' ) != '?';

		if ( strpos( $url, '?' ) !== false )
		{
			$this->_ro_addTrailingSlash = false;
		}

		$this->_ro_url = $url;
		$this->_ro_anchorTag = new tgsfHtmlTag( 'a' );
		$this->_ro_anchorTag->href = $this->__toString();
		
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
			// array / __tgsf_vars issue
			if ( ! is_array( $value ) )
			{
				$vars[] = $name . $this->_ro_equals . urlencode( $value );
			}
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

		$url = '';

		if ( $this->_ro_ignoreApp == false )
		{
			$url = APP_URL_FOLDER;
		}

		$url .= $this->_ro_url . $this->getUrlVars();

		if ( $this->_ro_addTrailingSlash )
		{
			$url .= '/';
		}

		if ( $url == APP_URL_FOLDER . '/' )
		{
			$url = APP_URL_FOLDER;
		}

		if ( TGSF_CLI === false )
		{
			$url = current_base_url() . $url;
		}
		
		return tgsfEventFactory::filter()->event( 'generate_url' )->content( $url )->exec();

		return $url;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a url to be local.  Changes the variable bits and pieces to the standard
	* tgsf style of /_/varname/varvalue (by default - uses config vars)
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
	public function &anchorTag( $caption = '' )
	{
		//$tag = clone $this->_ro_anchorTag;
		$tag = $this->_ro_anchorTag;
		
		$tag->content( $caption );
		$tag->href = $this->__toString();

		return $tag;
	}
	//------------------------------------------------------------------------
	/**
	* A temporary redirect
	* @param Bool Exit after redirecting?  Send the define DO_NOT_EXIT to continue script execution
	*/
	public function &redirect( $exit = true )
	{
		if ( TGSF_CLI === true )
		{
			if ( $exit ) exit();
			return $this;
		}

		header( "HTTP/1.1 303 See Other" );

		$urlStr = $this->__toString();

		if ( can_plugin() )
		{
			$urlStr = tgsfEventFactory::filter()->event( 'temp_redirect_url' )->content( $urlStr )->exec();
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
		if ( TGSF_CLI === true )
		{
			if ( $exit ) exit();
			return $this;
		}

		header( "HTTP/1.1 301 Moved Permanently" );
		$urlStr = $this->__toString();

		if ( can_plugin() )
		{
			$urlStr = tgsfEventFactory::filter()->event( 'perm_redirect_url' )->content( $urlStr )->exec();
		}
		header( 'Location: ' . $urlStr );
		
		if ( $exit )
		{
			exit();
		}

		return $this;
	}
}
