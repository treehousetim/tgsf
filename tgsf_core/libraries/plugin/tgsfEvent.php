<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2011 by TMLA INC.  ALL RIGHTS RESERVED.
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
	* action event handler factory
	*/
	public static function actionHandler()
	{
		return tgsfEventFactory::handler( eventACTION );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function filterHandler()
	{
		return tgsfEventFactory::handler( eventFILTER );
	}
	//------------------------------------------------------------------------
	/**
	* event handler factory
	*/
	protected static function &handler( $type )
	{
		$instance = new tgsfHandler();
		$instance->type( $type );

		if ( $type == eventACTION )
		{
			$instance->ect( ectARRAY );
		}
		else
		{
			$instance->ect( ectSTRING );
		}

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
class tgsfEvent extends tgsfDataSource
{
	protected $_ro_type = eventUNKNOWN;
	protected $_ro_event;
	protected $_ro_ect = ectSTRING;
	//------------------------------------------------------------------------
	public function __construct()
	{
		//parent::__construct();
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
		$this->setVar( 'ect', $type );
		$this->_ro_ect = $type;
		return $this;
	}
	//------------------------------------------------------------------------
	public function exec()
	{
		$result = false;

		try
		{
			if ( $this->_ro_ect == ectARRAY )
			{
				$result = tgsfPlugin::getInstance()->ectArray( $this );
			}
			else
			{
				$result = tgsfPlugin::getInstance()->ectString( $this );
			}
		}
		catch( Exception $e )
		{
			throw $e;
		}

		return $result;
	}
}
//------------------------------------------------------------------------
class tgsfFilter extends tgsfEvent implements tgsfBaseEvent
{
	public function &content( $content )
	{
		$this->setVar( 'content', $content );
		return $this;
	}
}
//------------------------------------------------------------------------
class tgsfAction extends tgsfEvent implements tgsfBaseEvent {}
//------------------------------------------------------------------------
class tgsfHandler extends tgsfEvent implements tgsfBaseEventHandler
{
	protected $_ro_callbackType;
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct()
	{
		$this->_ro_callbackType = cbtFUNCTION;
		parent::__construct();
		$this->level( 100 );
	}
	//------------------------------------------------------------------------
	/**
	* Sets the function name to execute for handing the event
	*/
	public function &func( $name )
	{
		$this->setVar( 'func', $name );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the object to execute for handing the event
	*/
	public function &object( &$object )
	{
		$this->_ro_callbackType = cbtOBJECT;
		$this->setVar( 'object', $object );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the class to execute with a static method - also call func
	* @param String The name of a class
	*/
	public function &static_class( $class )
	{
		$this->_ro_callbackType = cbtCLASS;
		$this->setVar( 'class', $class );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the level or order to execute this handler
	* @param Int The level or order
	*/
	public function &level( $level )
	{
		$this->setVar( 'level', $level );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns a handler that can be used with call_user_func
	*/
	public function handler()
	{
		switch ( $this->_ro_callbackType )
		{
		case cbtFUNCTION:
			return (string)$this->func;
			break;
		case cbtOBJECT:
			return array( $this->getVar( 'object' ), $this->func );
			break;
		case cbtCLASS:
			return array( $this->getVar( 'class' ), $this->func );
			break;
		default:
			throw new tgsfException( 'Event does not have a handler set up' );
		}
	}
	//------------------------------------------------------------------------
	/**
	* Alias to attach
	*/
	public function exec()
	{
		$this->attach();
	}
	//------------------------------------------------------------------------
	/**
	* Adds the handler to the core plugin system
	*/
	public function attach()
	{
		return tgsfPlugin::getInstance()->addHandler( $this );
	}
}