<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class field extends tgsfBase
{
	public $name		= '';
	public $type		= '';
	public $size		= '';
	public $quoted		= false;
	public $enum		= false;
	public $enumList	= array();
	public $blob		= false;
	public $options		= array();
	public $primaryKey	= false;
	public $comment		= '';

	// Used for inserts/updates
	public $value		= null;

	public function __construct( $name, $type, $size = '' )
	{
		$this->name = $name;
		$this->type = strtolower( $type );
		$this->size = $size;

		$quoted = ends_with( $this->type, 'text' );
		$quoted = $quoted || ends_with( $this->type, 'char' ); //char and varchar
		$quoted = $quoted || ends_with( $this->type, 'binary' ); //varbinary and binary
		$quoted = $quoted || ends_with( $this->type, 'enum' );
		$quoted = $quoted || ends_with( $this->type, 'set' );

		$this->quoted = $quoted;
		$this->blob = ends_with( $this->type, 'blob' );

		$this->enum = ends_with( $this->type, 'enum' );
	}
	//------------------------------------------------------------------------
	public function setOptions( $options )
	{
		$this->options = $options;
	}
	//------------------------------------------------------------------------
	public function enum( $list )
	{
		enum( $this->name . '_', $list, ENUM_USE_VALUE );
		$this->enumList = $list;
		$this->enum = true;
	}
	//------------------------------------------------------------------------
	public function setValue( $value )
	{
		$this->value = $value;
	}
	//------------------------------------------------------------------------
	public function generateDDL()
	{
		$out = array();

		$out[] = str_pad( $this->name, 35 );

		if ( $this->enum === true )
		{
			$out[] = str_pad( strtoupper( $this->type ) . "( '" . implode( "','", $this->enumList ) . "' )",35 );
		}
		else if ( ! is_null( $this->size ) )
		{
			$out[] = str_pad( strtoupper( $this->type ) . '(' . $this->size . ')', 35 );
		}
		else
		{
			$out[] = str_pad( strtoupper( $this->type ), 35 );
		}

		if ( count( $this->options ) > 0 )
		{
			$out = array_merge( $out, $this->options );
		}

		$out[count($out)-1] = trim($out[count($out)-1]);

		return implode( ' ', $out );
	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	function getWhereParamString()
	{
		return $this->name . '=:' . $this->name;
	}
}
