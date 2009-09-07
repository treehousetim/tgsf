<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
function current_base_url_path()
{
	list( $base_url_path ) = explode( '/index.php', $_SERVER['SCRIPT_NAME'] );
	$out = trim( $base_url_path, '/' ) . '/';
	
	if ( $out == '/' )
	{
		$out = '';
	}
	return $out;
}
//------------------------------------------------------------------------
function current_protocol()
{
	$protocol = 'http';
	
	if ( ! empty( $_SERVER['https'] ) && $_SERVER['https'] === 'on' )
	{
		$protocol = 'https';
	}
	
	return $protocol;
}
//------------------------------------------------------------------------
function current_has_www()
{
	list( $host ) = explode( ':', $_SERVER['HTTP_HOST'] );
	return starts_with( $host, 'www.' );
}
//------------------------------------------------------------------------
function current_host()
{
	list( $host ) = explode( ':', $_SERVER['HTTP_HOST'] );

	if ( current_has_www() )
	{
		$host = substr( $host, 4 );
	}
	
	// if the host is not localhost, and it is the live host (as defined in the app config)
	// and we have host_www turned on (as defined in the app config)
	// then add www. to the hostname.
	if ( $host != 'localhost' && $host == config( 'live_host' ) && config( 'host_www' ) == true )
	{
		$host = 'www.' . $host;
	}

	return $host;
}
//------------------------------------------------------------------------
function current_port()
{
	$port = '';
	
	if ( ! isset( $_SERVER['SERVER_PORT'] ) )
	{
		list( $host, $port ) = explode( ':', $_SERVER['HTTP_HOST'] );
		$port = trim( $port );
	}
	else
	{
		$port = $_SERVER['SERVER_PORT'];
	}
	
	if ( $port == '80' )
	{
		$port = '';
	}
	else
	{
		$port = ':' . $_SERVER['SERVER_PORT'];
	}
	
	return $port;
}

//------------------------------------------------------------------------
function current_base_url()
{
	$url  = current_protocol() . '://';
	$url .= current_host();
	$url .= current_port() . '/';
	$url .= current_base_url_path();

	return $url;
}
