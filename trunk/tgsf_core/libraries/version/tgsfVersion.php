<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
// this file contains classes and code to faciliate updating a database according to a specific version
// it is intended to be used so that a project can specify the changes made to a database in code
// and have it automatically updated to the current schema - even if intermediate updates are skipped
// assuming that all the updates are coded up according to the api

load_library( 'version/tgsfVersionItem', IS_CORE );
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
class tgsfVersion extends tgsfBase
{
	protected $_ro_codeVersion;
	protected $_ro_codeDisplayVersion;
	protected $_items;
	protected $_ro_version = NULL;
	protected $_ro_dbVersion;
	protected $_ro_errorExists = false;
	protected $_ro_title;
	protected $_ro_context;

	//------------------------------------------------------------------------
	/**
	* The constructor automatically sets the current version based on the define in the core
	* @param String A title for this version item - used for end-user communication
	*/
	protected function __construct( $title = '' )
	{
		load_config( 'version', IS_CORE );
		$this->_ro_codeVersion = TGSF_VERSION_INT;
		$this->_ro_codeDisplayVersion = TGSF_VERSION;
		$this->detectDbVersion( contextCORE );
		$this->_ro_title = $title;
	}
	//------------------------------------------------------------------------
	/**
	* factory to return new instances
	* @param String A title for this version item - used for end-user communication
	*/
	public static function &factory( $title = '' )
	{
		$c = __CLASS__;
		$instance = new $c( $title );
		return $instance;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &context( $context )
	{
		$this->_ro_context = $context;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Attempts to detect the database version from the database
	* either call context before calling this or pass a context to this function
	* @param String App context - used for getting data out of the registry table
	*/
	public function detectDbVersion( $context )
	{
		$table = coreTable( 'registry' );
		$this->_ro_dbVersion = 0;
		if ( dbm()->tableExists( $table ) )
		{
			load_library( 'db/tgsfDbRegistry/tgsfDbRegistry', IS_CORE );
			REG( $table );
			$this->_ro_dbVersion = REG_VALUE()
				->key( 'version' )
				->group( 'version' )
				->context( $context )
				->fetch();
		}
		
		return $this->_ro_dbVersion;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &setDbVersion( $version, $context )
	{
		$table = coreTable( 'registry' );
		load_library( 'db/tgsfDbRegistry/tgsfDbRegistry', IS_CORE );
		REG( $table );
		REG_VALUE()
			->context( $context )
			->name( 'version' )
			->value( $version )
			->store();
			
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &version( $version )
	{
		$this->_ro_version = $version;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &addItem( &$item )
	{
		$this->_items[] =& $item;
		$item->version( $this->_ro_version );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &exec()
	{
		$this->_ro_errorExists = false;
		foreach( $this->_items as &$item )
		{
			if ( $item->version > $this->_ro_dbVersion )
			{
				$item->exec();
				if ( $item->execError )
				{
					$this->_ro_errorExists = true;
				}
			}
			elseif ( $this-> )
			{
				$this->skipping = true;
			}
		}
		
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getBlockDescriptions()
	{
		$table = tgsfHtmlTag::factory( 'table' )->css_class( 'install_error_list grid' );
		
		$table->beforeTag( $this->getTitle() );

		$table->addTag( 'tr' )
			->addTag( 'th' )->content( 'Description' );

		foreach( $this->_items as &$item )
		{

			$row = $table->addTag( 'tr' );
			$row->addTag( 'td' )->content( $item->description );
		}
		
		return $table;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getTitle()
	{
		if ( $this->_ro_title == '' )
		{
			return '';
		}

		return tgsfHtmlTag::factory( 'h2' )->content( $this->_ro_title );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &title( $title )
	{
		$this->_ro_title = $title;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function getMessages()
	{
		$table = tgsfHtmlTag::factory( 'table' )->css_class( 'install_error_list grid' );
		
		$table->beforeTag( $this->getTitle() );

		$table->addTag( 'tr' )
			->addTag( 'th' )->content( 'Description' )
			->parent
			->addTag( 'th' )->content( 'Message' );

		foreach( $this->_items as &$item )
		{

			$row = $table->addTag( 'tr' );
			if ( $item->description == '' )
			{
				$row->addTag( 'td' )->content( $item->execErrorMessage )->addAttribute( 'colspan', '2' );
			}
			else
			{
				$row->addTag( 'td' )->content( $item->description );
				$row->addTag( 'td' )->content( $item->execErrorMessage );
			}
		}
		
		return $table;
	}
}
