<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class model extends table
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( $name, $which = 'default' )
	{
		parent::__construct( $name, $which );
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function pkGet()
	{
		$q = new query();
		$q->select( '*' )->from( $this->_name );
		$this->wherePK( $q );
		echo $q->generate();
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	function wherePK( &$query )
	{
		if ( ! is_object( $query ) || ! $query instanceof query )
		{
			throw new tgsfDbException( 'wherePK expects to receive an object that is an instance of the query class' );
		}
		
		foreach( $this->_primaryKey as $field )
		{
			$query->and_where( $field->getWhereParamString() );
		}
	}
}


/*
load_model( 'login' );
//load_form( 'profileForm' );
$login->login_id = 123;

$login->get( array( 'login_id' => 123 ) );
$login->SetFromModel( $profileForm );
$errors = array();
$login->validate( $errors ); // last ditch effort to catch errors - the formModel should do its own validation
$login->insert();
$id = $login->lastInsertId();
$login->update();
*/
