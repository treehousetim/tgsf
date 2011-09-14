<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
class tgsfPluginLoader extends tgsfBase
{
	protected $_ro_file;
	protected $_ro_name;
	protected $_ro_loaded = false;
	protected $_ro_registered = false;
	//------------------------------------------------------------------------
	/**
	* sets the filename for this plugin loader
	* @param String The file name to the plugin file.
	*/
	public function &file( $file )
	{
		if ( file_exists(  $file ) == false )
		{
			throw new tgsfException( 'Unable to load plugin file: ' . $file );
		}

		$this->_ro_file = $file;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* names this plugin loader
	* @param String The name of the plugin.
	*/
	public function &name( $name )
	{
		$this->_ro_name = $name;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* registers this plugin loader
	*/
	public function &register()
	{
		if ( tgsfPlugin::getInstance()->pluginRegistered( $this ) == false )
		{
			tgsfPlugin::getInstance()->registerPlugin( $this );
			$this->_ro_registered = true;
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &load( )
	{
		require_once $this->file;
		tgsfPlugin::getInstance()->loadPlugin( $this );
		tgsfEventFactory::action()
			->event( $this->_ro_name . '_init' )
			->setVar( 'plugin', $this )
			->exec();

		return $this;
	}
}