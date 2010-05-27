<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
function &tPLUGIN()
{
	static $pluginApi = null;
	
	if ( $pluginApi === null )
	{
		$pluginApi = new tgsfPlugin();
	}

	return $pluginApi;
}
//------------------------------------------------------------------------
function do_action( $name )
{
	$args =  tgsfBase::sliceArgs( func_get_args(), 1 );
	return tPLUGIN()->dispatchAction( $name, $args );
}
//------------------------------------------------------------------------
function do_filter( $name, $value )
{
	$args =  tgsfBase::sliceArgs( func_get_args(), 2 );
	return tPLUGIN()->dispatchFilter( $name, $value, $args );
}
//------------------------------------------------------------------------
function register_plugin( $file, $name )
{
	if ( tPLUGIN()->pluginRegistered( $name ) )
	{
		return false;
	}
	
	return tPLUGIN()->registerPlugin( $file, $name );
}
//------------------------------------------------------------------------
function add_action( $name, $handler, $level = 0 )
{
	tPLUGIN()->addAction( $name, $handler, $level );
}
//------------------------------------------------------------------------
function add_filter( $name, $handler, $level = 0 )
{
	tPLUGIN()->addFilter( $name, $handler, $level );
}
//------------------------------------------------------------------------
// TODO: verify that this is needed. or that this is the appropriate place for it.
function tg_head()
{
	do_action( 'head' );
}
//------------------------------------------------------------------------
function load_plugins()
{
	
	$plugins = tPLUGIN()->getPlugins();
	
	foreach ( $plugins as $info )
	{
		extract( $info );
		require_once( $file );
		tPLUGIN()->markPluginAsLoaded( $file, $name );
		tPLUGIN()->doAction( $name . '_init', $info );
	}
}


//------------------------------------------------------------------------
// the oop class
//------------------------------------------------------------------------
class tgsfPlugin extends tgsfBase
{
	private $_actions		= array();
	private $_filters		= array();
	private $_plugins		= array();
	private $_pluginNames	= array();
	private $_loaded		= array();
	//------------------------------------------------------------------------
	public function __construct()
	{
	}
	//------------------------------------------------------------------------	
	protected function &_getFilterGroup( $name )
	{
		$retVal = false;
		
		if ( isset( $this->_filters[$name] ) && is_array( $this->_filters[$name] ) )
		{
			$retVal =& $this->_filters[$name];
		}
		
		return $retVal;
	}
	//------------------------------------------------------------------------
	protected function &_getActionGroup( $name )
	{
		$retVal = false;
		
		if ( isset( $this->_actions[$name] ) && is_array( $this->_actions[$name] ) )
		{
			$retVal =& $this->_actions[$name];
		}
		
		return $retVal;
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
	/**
	*
	*/
	public function doAction( $name )
	{
		$args = $this->sliceArgs( func_get_args(), 1 );
		$this->dispatchAction( $name, $args );
	}
	//------------------------------------------------------------------------
	/**
	* Processes the specified action
	* @param String the name
	*/
	function dispatchAction( $name, $params )
	{
		$retVal = array();
		if ( $actionGroup =& $this->_getActionGroup( $name ) !== false )
		{
			foreach( $actionGroup as $level => $items )
			{
				foreach ( $items as $action )
				{
					$retVal[] = call_user_func_array( $action, $params );
				}
			}
		}

		return $retVal;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function doFilter( $name, $content )
	{
		$args = $this->sliceArgs( func_get_args(), 2 );
		$this->dispatchFilter( $name, $content );
	}
	//------------------------------------------------------------------------
	/**
	* Dispatches a filter invocation by receiving an argument array
	*/
	function dispatchFilter( $name, $content, $params )
	{
		if ( $filterGroup =& $this->_getFilterGroup( $name ) !== false )
		{
			foreach( $filterGroup as $level => $items )
			{
				foreach ( $items as $action )
				{
					$content = call_user_func( $action, $content, $params );
				}
			}
			return $content;
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
			throw new tgsfException( "Action Handler is not callable.\n" . get_dump( $handler ) );
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
			throw new tgsfException( "Filter Handler is not callable.\n" . get_dump( $handler ) );
		}
	}
	
}

//------------------------------------------------------------------------
