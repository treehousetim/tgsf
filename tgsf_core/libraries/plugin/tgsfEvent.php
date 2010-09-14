<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
// the factory class to create new events
//------------------------------------------------------------------------
class tgsfEventFactory
{
	/**
	* filter factory
	*/
	public static function &filter()
	{
		$instance = new tgsfFilter();
		$instance->type( eventFILTER );
		$instance->ect( ectSTRING );
		return $instance;
	}
	//------------------------------------------------------------------------
	/**
	* action factory
	*/
	public static function &action()
	{
		$instance = new tgsfAction();
		$instance->type( eventACTION );
		$instance->ect( ectARRAY );
		return $instance;
	}
	//------------------------------------------------------------------------
	/**
	* event handler factory
	*/
	public static function &handler()
	{
		$instance = new tgsfHandler();
		$instance->type( eventHANDLER );
		$instance->ect( ectARRAY );
		return $instance;
	}
}
//------------------------------------------------------------------------
// the interface all events must adhere to
//------------------------------------------------------------------------
interface tgsfBaseEvent
{
	public function &type( $type );
	public function &event( $event );
	public function exec();
	public function &ect( $type );
}
//------------------------------------------------------------------------
interface tgsfBaseEventHandler extends tgsfBaseEvent
{
	public function &func( $name );
	public function &object( &$object );
	public function &level( $level );
	public function handler();
}
//------------------------------------------------------------------------
// the standard event
//------------------------------------------------------------------------
class tgsfEvent extends tgsfBase
{
	public $type = eventUNKNOWN;
	protected $_ro_ds;
	protected $_ro_type;
	protected $_ro_event;
	protected $_ro_ect = ectSTRING;
	//------------------------------------------------------------------------
	public function __construct()
	{
		$this->_ro_ds = tgsfDataSource::factory();
		$this->_ro_ds->setVar( 'event', $this );
	}
	//------------------------------------------------------------------------
	function &type( $type )
	{
		$this->_ro_type = $type;
		return $this;
	}
	//------------------------------------------------------------------------
	public function &event( $event )
	{
		$this->_ro_event = $event;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* set the event collection type
	* used to determine how event handler return values are collected
	* types are in tgsf_core/config/constants.php - ectARRAY, ectSTRING
	* and are either added to an array or concatenated to a string
	*/
	public function &ect( $type )
	{
		$this->_ro_ds->setVar( 'ect', $type );
		$this->_ro_ect = $type;
		return $this;
	}
	//------------------------------------------------------------------------
	public function exec()
	{
		if ( $this->_ro_ect == ectARRAY )
		{
			return tPLUGIN()->ectArray( $this );
		}
		
		return tPLUGIN()->ectString( $this );
	}
}
//------------------------------------------------------------------------
class tgsfFilter  extends tgsfEvent implements tgsfBaseEvent
{
	public function &content( $content )
	{
		$this->_ro_ds->setVar( 'content', $content );
		return $this;
	}
}
//------------------------------------------------------------------------
class tgsfAction  extends tgsfEvent implements tgsfBaseEvent {}
//------------------------------------------------------------------------
class tgsfHandler extends tgsfEvent implements tgsfBaseEventHandler
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct()
	{
		parent::__construct();
		$this->level( 100 );
	}
	//------------------------------------------------------------------------
	/**
	* Sets the function name to execute for handing the event
	*/
	public function &func( $name )
	{
		$this->_ro_ds->setVar( 'func', $name );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the object to execute for handing the event
	*/
	public function &object( &$object )
	{
		$this->_ro_ds->setVar( 'object', $object );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &level( $level )
	{
		$this->_ro_ds->setVar( 'level', $level );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function handler()
	{
		if ( $this->_ro_ds->isEmpty( 'object' ) || $this->_ro_ds->isEmpty( 'func' ) )
		{
			if ( $this->_ro_ds->isEmpty( 'func' ) )
			{
				throw new tgsfException( 'Event does not have a handler set up' );
			}
			else
			{
				return (string)$this->_ro_ds->func;
			}
		}
		return array( &$this->_ro_ds->object, $this->_ro_ds->func );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function exec()
	{
		return tPLUGIN()->addHandler( $this );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function attach()
	{
		$this->exec();
	}
}
//tgsfEventFactory::handler()->func( 'name' )->object( $instance )->exec();