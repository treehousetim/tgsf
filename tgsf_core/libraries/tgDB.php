<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class select extends query
{
	private $_selects = array();
	private $_joins = array();
	private $_wheres = array();
	private $_orders = array();
	private $_limit = array();
}

/*
function updateConfig( $subFolder, $host, $port, $prefix )
{
	if ( $port == '80' )
	{
		$port = '';
	}
	
	if ( $port != '' )
	{
		$port = ':' . $port;
	}
	
	$table = $prefix . 'options';
	$root = trim( 'http://' . $host . $port . '/' . $subFolder, '/' );

	try
	{
		$dbh = new PDO( "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASSWORD );
	}
	catch (PDOException $e )
	{
		print ("Could not connect to server.\n");
		print ("getMessage(): " . $e->getMessage () . "\n");
		exit();
	}

	$sth = $dbh->prepare( 'UPDATE ' . $table . " SET option_value = ? WHERE blog_id=0 and ( option_name=? or option_name=? )" );
		$sth->bindValue( 1, $root );
		$sth->bindValue( 2, 'siteurl' );
		$sth->bindValue( 3, 'home' );
	$sth->execute();
	$dbh = null;
}
*/