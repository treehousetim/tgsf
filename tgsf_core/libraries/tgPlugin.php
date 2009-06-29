<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class tgPlugin
{
	private static $me;
	private $_actions		= array();
	private $_filters		= array();
	private $_plugins		= array();
	private $_pluginNames	= array();
	private $_loaded		= array();

	//------------------------------------------------------------------------
	public function __construct()
	{
		self::$me =& $this;
	}
	//------------------------------------------------------------------------
	public static function &get_instance()
	{
		return self::$me;
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
		if ( file_exists(  $file ) && ! in_array( array_keys( $this->_plugins ), $file ) )
		{
			$this->_pluginNames[] = $name;
			$this->_plugins[$file]['name'] = $name;
			$this->_plugins[$file]['file'] = $file;
			$out = true;
			$this->doAction( 'register_plugin', $file );
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
	/**
	* Processes the specified action
	* @param String the name
	*/
	function doAction( $name, $params )
	{
		if ( is_array( $this->_actions[$name] ) )
		{
			$group =& $this->_actions[$name];
			foreach( $group as $level => $items )
			{
				foreach ( $items as $action )
				{
					$result[] = call_user_func( $action, $params );
				}
			}
		}
		
		return $result;
	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	function doFilter( $name, $content, $params )
	{
		if ( is_array( $this->_filters ) )
		{
			if ( is_array( $this->_filters[$name] ) )
			{
				$group =& $this->_filters[$name];
				foreach( $group as $level => $items )
				{
					foreach ( $items as $action )
					{
						$content = call_user_func( $action, $content, $params );
					}
				}
				return $content;
			}
		}
		return $content;
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds an action handler
	* @param String The name of the action
	* @param Mixed A callback or a string of a function name
	* @param integer The level at which to call 0 to 1000
	*/
	function addAction( $name, $handler, $level )
	{
		if ( is_callable( $handler ) )
		{
			$group =& $this->_actions[$name];
			$group[$level][] = $handler;
		}
		else
		{
			echo "<pre>Problem adding action for $name the handler is not callable - see details below\n";
			var_dump( $handler );
			echo '</pre>';
		}
	}
	
	
	//------------------------------------------------------------------------
	/**
	* Adds a filter handler
	* @param String The name of the filter
	* @param Mixed A callback or a string of a function name
	* @param integer The level at which to call 0 to 1000
	*/
	function addFilter( $name, $handler, $level )
	{
		if ( is_callable( $handler ) )
		{
			$group =& $this->_filters[$name];
			$group[$level][] = $handler;
		}
		else
		{
			echo "<pre>Problem adding filter for $name the handler is not callable - see details below\n";
			var_dump( $handler );
			echo '</pre>';
		}
	}
	
}

//------------------------------------------------------------------------

function &tgPlugin()
{
	return tgPlugin::get_instance();
}
