<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// version item types
define ( 'vitTABLE',	'table' );
define ( 'vitQUERY',	'query' );
define ( 'vitREG',		'reg' );

// version item results
define ( 'virEXISTS',	'table-exists' );
define( 'virSUCCESS',	true );
define( 'virFAIL',		false );

// we use the registry to manage version numbers
load_library( 'db/tgsfDbRegistry/tgsfDbRegistry', IS_CORE );
class versionItemFactory extends tgsfBase
{
	//------------------------------------------------------------------------
	public static function query( $query )
	{
		return new tgsfVersionItemQuery( $query );
	}
	//------------------------------------------------------------------------
	public static function table( $table )
	{
		return new tgsfVersionItemTable( $table );
	}
	//------------------------------------------------------------------------
	public static function reg( $regObj )
	{
		return new tgsfVersionItemReg( $regObj );
	}
}
//------------------------------------------------------------------------
interface i_tgsfVersionItem
{
	public function exec();
}
//------------------------------------------------------------------------
class tgsfVersionItemQuery extends tgsfVersionItem implements i_tgsfVersionItem
{
	//------------------------------------------------------------------------
	/**
	* The constructor - accepts the query
	*/
	public function __construct( $query )
	{
		parent::__construct();
		$this->query( $query );
	}
}
//------------------------------------------------------------------------
class tgsfVersionItemTable extends tgsfVersionItem implements i_tgsfVersionItem
{
	protected $_ro_tableName;
	//------------------------------------------------------------------------
	/**
	* Sets the table name
	*/
	public function __construct( $table )
	{
		parent::__construct();
		$this->_ro_tableName = $table;
	}
	//------------------------------------------------------------------------
	/**
	* Executes the table item
	*/
	public function exec()
	{
		$result = virFAIL;
		if ( dbm( $this->_ro_database )->tableExists( $this->_ro_tableName ) )
		{
			$result = virEXISTS;
		}
		else
		{
			$result = parent::exec();
		}

		return $result;
	}
}
//------------------------------------------------------------------------
class tgsfVersionItemReg extends tgsfVersionItem implements i_tgsfVersionItem
{
	protected $_regObj;
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( $regObj )
	{
		$this->_regObj = $regObj;
	}
	//------------------------------------------------------------------------
	/**
	* Executes the version item registry object -
	* calls store on the REG() object this version item obj stores
	*/
	public function exec()
	{
		$this->_regObj->store();
		$this->_ro_executed = true;
	}
}
//------------------------------------------------------------------------
class tgsfVersionItem extends tgsfBase
{
	public $parent;

	protected $_ro_query;
	protected $_ro_version;
	protected $_ro_context = contextAPP;
	
	private $_query;
	protected $_ro_database = 'default';
	protected $_ro_description = '';
	protected $_ro_execError = false;
	protected $_ro_execErrorMessage = 'Ok';
	protected $_ro_executed;

	//------------------------------------------------------------------------
	/**
	* the constructor - instantiates a query object;
	*/
	public function __construct()
	{
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
	* Sets the description on a version item
	*/
	public function &description( $description )
	{
		$this->_ro_description = $description;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* sets the query statement, and returns the item object (for chaining)
	* @param Mixed - either a query string or a query object
	*/
	public function &query( $query )
	{
		$this->_ro_query = $query;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function context( $context )
	{
		
	}
	//------------------------------------------------------------------------
	/**
	* Sets the version that this item first appeared in the schema and returns the item object (for chaining)
	* the version number is transformed internally to an integer - you're best off leaving all punctuation off
	* for example: 093 instead of 0.9.3
	*/
	public function &version( $version )
	{
		$this->_ro_version = (int)str_replace( array( '.', ':', ' ', ',' ), '', $version );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function exec()
	{
		$this->_ro_executed = false;

		// could be a table or a query type - we don't care
		$result = virSUCCESS;
		try
		{
			if ( $this->_ro_query instanceOf query )
			{
				$this->_ro_query->exec();
			}
			else
			{
			 	$this->_query->reset()
					->static_query( $this->_ro_query )
					->exec();
			}
			$this->_ro_executed = true;
		}
		catch ( Exception $e )
		{
			$this->_ro_execError = true;
			$this->_ro_execErrorMessage = $e->getMessage();
			$result = virFAIL;
		}

		return $result;
	}
}