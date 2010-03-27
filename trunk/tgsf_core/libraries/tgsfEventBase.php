<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
/**
* An event-enabled base class. Extend this to get access to event handling in your class.
*/
class tgsfEventBase extends tgsfBase
{
	protected $_eventHandlers = array();

	function &addEvent( $type, $callback, $object = null )
	{
		if ( $object !== null && is_array( $callback ) )
		{
			throw new tgsfException( 'You must not define an object if you wish to pass the callback in array( $object, $callback ) form to an event handler.' );
		}
		else if ( $object !== null )
		{
			$callback = array( $object, $callback );
		}

		if ( !is_callable( $callback ) )
		{
			throw new tgsfException( 'The callback you provided was not a callable function.' );
		}

		$this->_eventHandlers[$type][] = $callback;

		return $this;
	}

	function &removeEvent( $type, $callback, $object = null )
	{
		if ( $object !== null && is_array( $callback ) )
		{
			throw new tgsfException( 'You must not define an object if you wish to pass the callback in array( $object, $callback ) form to an event handler.' );
		}
		else if ( $object !== null )
		{
			$callback = array( $object, $callback );
		}

		if ( isset( $this->_eventHandlers[$type] ) )
		{
			for ( $ix = 0, $max = count( $this->_eventHandlers[$type] ); $ix < $max; $ix++ )
			{
				if ( isset( $this->_eventHandlers[$type][$ix] ) && $this->_eventHandlers[$type][$ix] == $callback )
				{
					unset( $this->_eventHandlers[$type][$ix] );
				}
			}
		}

		return $this;
	}

	function &removeAllEvents( $type )
	{
		if ( isset( $this->_eventHandlers[$type] ) )
		{
			unset( $this->_eventHandlers[$type] );
		}

		return $this;
	}

	protected function triggerEvent( $type, $args = array() )
	{
		if ( !isset( $this->_eventHandlers[$type] ) )
		{
			return $args;
		}

		foreach( $this->_eventHandlers[$type] as $callback )
		{
			if ( !is_array( $args ) )
			{
				$args = array( $args );
			}

			$args = call_user_func_array( $callback, $args );
		}

		return $args;
	}
}

?>