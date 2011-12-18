<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
query::factory()
    ->select()
    ->from( 'user' )
    ->where( 'id=:id' )
    ->bindValue( 'id', 123, ptINT )
    ->debug();

query::factory()
    ->select()
    ->from( 'user' )
    ->debug();

echo PHP_EOL;

query::factory()
    ->select()
    ->from( 'user' )
    ->join( 'user_clubs', 'user.id=user_clubs.user_id' )
    ->where( 'id=:id' )
    ->bindValue( 'id', 123, ptINT )
    ->debug();

echo PHP_EOL;
echo 'Group By';
query::factory()
    ->select()
    ->from( 'user' )
    ->group_by( 'user_type' )
    ->where( 'id=:id' )
    ->bindValue( 'id', 123, ptINT )
    ->debug();

echo PHP_EOL;
echo 'Insert';

query::factory()
    ->insert_into( 'user' )
    ->insert_fields( 'username','email' )
    ->bindValue( 'username', 'example', ptSTR )
	->bindValue( 'email', 'example@example.com', ptSTR )
    ->debug();


query::factory()
    ->update( 'user' )
    ->set( 'username' )
    ->bindValue( 'username', 'example', ptSTR )
    ->where( 'user.id=:user_id' )
    ->bindValue( 'user_id', 123, ptINT )
    ->debug();

query::factory()
    ->delete_from( 'user' )
    ->where( 'user.id=:user_id' )
    ->bindValue( 'user_id', 122, ptINT )
    ->debug();

class example
{
	public function fetchAll( $enabledOnly = true )
	{
	    return query::factory()
	        ->select( '*' )
	        ->from( 'user' )
			->filter( array( $this, 'filterEnabled' ), $enabledOnly )
			->debug()
	        //->exec()
			//->fetchAll()
			;
	}
	
	public function filterEnabled( $q, $enabledOnly )
	{
		if ( $enabledOnly )
		{
			$q->where( 'user_enabled = true' );
		}
	}
}
$e = new example();
$e->fetchAll();
$e->fetchAll( false );
