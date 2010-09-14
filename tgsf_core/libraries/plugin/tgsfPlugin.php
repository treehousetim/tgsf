<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class tgsfPlugin extends tgsfBase
{
	private $_handlers		= array();

	private $_plugins		= array();
	private $_pluginNames	= array();
	private $_loaded		= array();
	//------------------------------------------------------------------------
	public function __construct()
	{
	}
	protected function &_getGroup( $event, $create = false )
	{
		if (
			array_key_exists( $event->type, $this->_handlers ) == false ||
			is_array( $this->_handlers[$event->type] ) == false )
		{
			$this->_handlers[$event->type] = array();
		}

		if ( array_key_exists( $event->event, $this->_handlers[$event->type] ) == false )
		{
			$this->_handlers[$event->type][$event->event] = array();
		}
		
		return $this->_handlers[$event->type][$event->event];
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if a plugin **name** has been registered before.
	* this is used to handle plugins in the app space being used before plugins
	* in the core space.
	*/
	function pluginRegistered( $name )
	{
		return in_array( $name, $this->_pluginNames );
	}
	//------------------------------------------------------------------------
	/**
	* Registers a plugin into the system - only if the file exists
	* @var String The file name
	*/
	function registerPlugin( $file, $name )
	{
		$out = false;
		if ( file_exists(  $file ) )
		{
			if ( ! in_array( $file, array_keys( $this->_plugins ) ) )
			{
				$this->_pluginNames[] = $name;
				$this->_plugins[$file]['name'] = $name;
				$this->_plugins[$file]['file'] = $file;
				$out = true;
				$this->doAction( 'register_plugin', $file );
				$this->doAction( 'register_plugin_' . $name, $file );
			}
		}
		else
		{
			throw new tgsfException( 'Unable to load plugin file for plugin (' . $name . '): ' . $file );
		}
		return $out;
	}
	//------------------------------------------------------------------------
	/**
	* (possibly as early )
	*/
	function getPlugins()
	{
		return $this->_plugins;
	}
	//------------------------------------------------------------------------
	/**
	* Marks a plugin as loaded
	*/
	function markPluginAsLoaded( $plugin, $name = '' )
	{
		$this->_loaded[] = $plugin;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if a plugin is already loaded.
	* This relies on having the markPluginAsLoaded method called
	* @param String The plugin file
	* @see markPluginAsLoaded
	*/
	function pluginAlreadyLoaded( $plugin )
	{
		return in_array( $this->_loaded, $plugin );
	}
	//------------------------------------------------------------------------

	function ectArray( $event )
	{
		$retVal = array();
		$group =& $this->_getGroup( $event );

		if ( $group !== false )
		{
			foreach( $group as $level => $items )
			{
				foreach ( $items as $handler )
				{
					$retVal[] = call_user_func( $handler, $event );
				}
			}
		}

		return $retVal;
	}
	//------------------------------------------------------------------------
	function ectString( $event )
	{
		$content = $event->ds->content;
		$group =& $this->_getGroup( $event );

		if ( $group !== false )
		{
			foreach( $group as $level => $items )
			{
				foreach ( $items as $handler )
				{
					$content = call_user_func( $handler, $event );
					$event->ds->setVar( 'content', $content );
				}
			}
			return $content;
		}

		return $content;
	}
	//------------------------------------------------------------------------
	/**
	* Adds a handler for the specified type of event
	*/
	public function addHandler( $event )
	{
		$handler = $event->handler();
		$level = $event->ds->level;

		if ( is_callable( $handler ) )
		{
			$group =& $this->_getGroup( $event );
			$group[$level][] = $handler;
		}
		else
		{
			throw new tgsfException( "Plugin Handler is not callable.\n" . get_dump( $handler ) );
		}
	}	
}
