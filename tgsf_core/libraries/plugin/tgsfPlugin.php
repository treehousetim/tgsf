<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class tgsfPlugin extends tgsfBase
{
	protected static $instance;
	private $_handlers		= array();

	private $_plugins		= array();
	private $_pluginNames	= array();
	private $_loaded		= array();
	//------------------------------------------------------------------------
	protected function __construct()
// private
	{
//		parent::__construct();
	}
	//------------------------------------------------------------------------
	public static function getInstance()
	{
		if ( tgsfPlugin::$instance === null )
		{
			tgsfPlugin::$instance = new tgsfPlugin();
		}

		return tgsfPlugin::$instance;
	}
	//------------------------------------------------------------------------
	public static function loaderFactory()
	{
		return new tgsfPluginLoader();
	}
	//------------------------------------------------------------------------
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
	* Returns true/false if a plugin has been registered before.
	* this is used to handle plugins in the app space being used before plugins
	* in the core space.
	* @param object::tgsfPluginLoader The plugin loader object
	*/
	function pluginRegistered( $plugin )
	{
		return in_array( $plugin->name, $this->_pluginNames );
	}
	//------------------------------------------------------------------------
	/**
	* Registers a plugin into the system - only if the file exists
	* @param object::tgsfPluginLoader The plugin loader object
	*/
	function registerPlugin( $plugin )
	{
		$out = false;

		if ( ! in_array( $plugin->file, array_keys( $this->_plugins ) ) )
		{
			$this->_pluginNames[] = $plugin->name;
			$this->_plugins[$plugin->file] = $plugin;
			$out = true;
			tgsfEventFactory::action()
				->event( 'register_plugin' )
				->setVar( 'plugin', $plugin )
				->exec();

			tgsfEventFactory::action()
				->event( 'register_plugin_' . $plugin->name )
				->setVar( 'plugin', $plugin )
				->exec();
		}

		return $out;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function loadPlugins()
	{
		foreach ( $this->_plugins as $plugin )
		{
			if ( $this->pluginLoaded( $plugin ) == false )
			{
				$plugin->load();
			}
		}
	}
	//------------------------------------------------------------------------
	/**
	* Marks a plugin as loaded
	* @param object::tgsfPluginLoader The plugin loader object
	*/
	function loadPlugin( $plugin )
	{
		$this->_loaded[] = $plugin->file;
	}
	//------------------------------------------------------------------------
	/**
	* Returns true/false if a plugin is already loaded.
	* This relies on having the markPluginAsLoaded method called
	* @param object::tgsfPluginLoader The plugin loader object
	* @see markPluginAsLoaded
	*/
	function pluginLoaded( $plugin )
	{
		return in_array( $plugin->file, $this->_loaded );
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
		$content = $event->content;
		$group =& $this->_getGroup( $event );

		if ( $group !== false )
		{
			foreach( $group as $level => $items )
			{
				foreach ( $items as $handler )
				{
					$content = call_user_func( $handler, $event );
					$event->setVar( 'content', $content );
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

		$level = $event->level;

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
