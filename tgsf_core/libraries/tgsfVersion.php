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

// this api is beta as of 2010-07-21

// version item types
define ( 'vitTABLE', 'table' );
define ( 'vitQUERY', 'query' );

// version item results
define ( 'virEXISTS',	'table-exists' );
define( 'virSUCCESS',	true );
define( 'virFAIL',		false );

// we use the registry to manage version numbers
load_library( 'db/tgsfDbRegistry/tgsfDbRegistry', IS_CORE );


class tgsfVersionItem extends tgsfBase
{
	protected $_ro_type;
	protected $_ro_tableName;
	protected $_ro_ddl;
	protected $_ro_version;
	protected $_parent;
	private $_query;
	protected $_ro_database = 'default';
	protected $_ro_description = '';
	protected $_ro_execError = false;
	protected $_ro_execErrorMessage = '';
	

	//------------------------------------------------------------------------
	/**
	* Creates the object - pass the tgsfVersion instance as the parent for method chaining.
	*/
	public function __construct( $parent, $version = null )
	{
		if ( $version !== null )
		{
			$this->version( $version );
		}

		$this->_parent =& $parent;
		$this->_query = query::factory();
	}
	//------------------------------------------------------------------------
	/**
	* Changes what database we perform this operation on
	*/
	public function &changeDb( $which = 'default' )
	{
		$this->_ro_database = $which;
		$this->_query->changeDb( $which );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &description( $description )
	{
		$this->_ro_description = $description;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets up the item as a query item, adds in the query statement, and returns the item object (for chaining)
	*/
	public function &query( $query )
	{
		$this->_ro_type = vitQUERY;
		$this->ddl( $query );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets $this->tableName and returns the item object (for chaining)
	*/
	public function &table( $tableName )
	{
		$this->_ro_type = vitTABLE;
		$this->_ro_tableName = $tableName;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* adds the ddl for this item and returns the item object (for chaining)
	*/
	public function &ddl( $ddl )
	{
		$this->_ro_ddl = $ddl;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the version that this item first appeared in the schema and returns the item object (for chaining)
	*/
	public function &version( $version )
	{
		$this->_ro_version = str_replace( '.', '', $version );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the parent tgsfVersion object - useful for method chaining
	*/
	public function &parent()
	{
		return $this->_parent;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function exec()
	{
		$result = virFAIL;
		if ( $this->_ro_type == vitTABLE && dbm( $this->_ro_database )->tableExists( $this->_ro_tableName ) )
		{
			$result = virEXISTS;
		}
		else
		{
			// could be a table or a query type - we don't care
			$result = virSUCCESS;
			try
			{
				 $this->_query->reset()
					->static_query( $this->_ro_ddl )
					->exec();
			}
			catch ( Exception $e )
			{
				$this->_ro_execError = true;
				$this->_ro_execErrorMessage = $e->getMessage();
			}

			if ( $this->_query == false )
			{
				$result = virFAIL;
			}
		}

		return $result;
	}
}
//------------------------------------------------------------------------
//------------------------------------------------------------------------
//------------------------------------------------------------------------
class tgsfVersion extends tgsfBase
{
	protected $_ro_codeVersion;
	protected $_ro_codeDisplayVersion;
	protected $_items;
	protected $_ro_addingVersion = NULL;
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
	public function detectDbVersion()
	{
		$table = coreTable( 'registry' );
		$this->_ro_dbVersion = 0;
		if ( dbm()->tableExists( $table ) )
		{
			load_library( 'db/tgsfDbRegistry/tgsfDbRegistry', IS_CORE );
			REG( $table );
			$this->_ro_dbVersion = REG_VALUE()->name( 'version' )->context( $this->_ro_context )->fetch()->registry_value;
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function setVersion( $version )
	{
		$this->_ro_addingVersion = str_replace( '.', '', $version );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &addItem( $description = '' )
	{
		$item = $this->_items[] = new tgsfVersionItem( $this, $this->_ro_addingVersion );
		$item->description( $description );
		return $item;
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
		}
		
		return $this;
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
		
		if ( $this->_ro_errorExists == false )
		{
			return $table->addTag( 'tr' )->addTag( 'td' )->addAttribute( 'colspan', '2' )->content( 'Install/Upgrade was successful.' );
		}

		$table->addTag( 'tr' )
			->addTag( 'th' )->content( 'Description' )
			->parent
			->addTag( 'th' )->content( 'Error' );

		foreach( $this->_items as &$item )
		{
			if ( $item->execError )
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
		}
		
		return $table;
	}
}
