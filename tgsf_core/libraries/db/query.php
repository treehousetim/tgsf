<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
* simple container class
*/
class queryParam extends tgsfBase
{
	public $name;
	public $value;
	public $type;
	
	public function __construct( $name, $value, $type )
	{
		$this->name		= $name;
		$this->value	= $value;
		$this->type		= $type;
	}
}
//------------------------------------------------------------------------
class query extends tgsfBase
{
	protected $_conn;
	protected $_handle;
	
	protected $_stmHandle		= null;
	
	protected $_selectList		= array();
	protected $_joinList		= array();
	protected $_fromList		= array();
	protected $_setList			= array();
	protected $_insertList		= array();
	protected $_params			= array();
	
	protected $_insertTable		= '';
	protected $_updateTable		= '';
	protected $_whereList		= array( "1=1" );
	protected $_type			= qtNONE;
	protected $_executed		= false;

	//------------------------------------------------------------------------

	public function __construct( $which = 'default' )
	{
		$this->changeDB( $which );
	}

	//------------------------------------------------------------------------	
	protected function _table()
	{
		return implode( ',', $this->_table );
	}

	/**
	* Used in rendering the query
	* this is used to render select queries - the from table list.
	*/
	protected function _from()
	{
		return ' FROM ' . implode( ',', $this->_fromList ) . ' ';
	}

	//------------------------------------------------------------------------
	/**
	* Returns a string containing the entire where clause
	*/
	protected function _where()
	{
		return ' WHERE ' . implode( ' ', $this->_whereList ) . ' ';
	}

	//------------------------------------------------------------------------
	/**
	* generates the sql for the joins on a select query
	*/
	protected function _join()
	{
		$out = array();
		
		foreach ( $this->_joinList as &$join )
		{
			$out[] = $join->generate();
		}
		return ' ' . implode( ',', $out ) . ' ';
	}
	
	//------------------------------------------------------------------------
	/**
	* generates the select list (fields) when we render the query
	*/
	protected function _select()
	{
		return 'SELECT ' . implode( ',', $this->_selectList ) . ' ';
	}
	
	
	/**
	* generates the update table for rendering the query
	*/
	protected function _update()
	{
		return "UPDATE {$this->_updateTable} ";
	}
	
	//------------------------------------------------------------------------
	/**
	* generates the insert into for rendering the query
	*/
	protected function _insert()
	{
		return "INSERT INTO {$this->_insertTable} ";
	}
	
	//------------------------------------------------------------------------
	/**
	* returns the list of insert field names
	*/
	protected function _insertList()
	{
		return '(' . implode( ',', $this->_insertList ) . ')';
	}
	
	//------------------------------------------------------------------------
	/**
	* returns the VALUES(...) portion of an insert query
	*/
	protected function _insertParams()
	{
		foreach( $this->_insertList as $field )
		{
			$o[] = ":{$field}";
		}
		
		return ' VALUES(' . implode( ',', $o ) . ')';
	}
	
	//------------------------------------------------------------------------
	/**
	* returns the SET field=:field,field1=:field1 portion of an update query
	*/
	protected function _set()
	{
		$tmp = array();
		foreach ( $this->_setList as $item )
		{
			$tmp[] = $item . " = :{$item}";
		}
		return "SET " . implode( ',', $tmp );
	}

	//------------------------------------------------------------------------
	/**
	* Automatically binds values to the query using values that have been bound using
	* $this->bindValue()
	* @param Object::PDOStatement The statement to bind values into
	*/
	protected function _doBindValues( &$statement )
	{
		if ( count( $this->_params ) > 0 )
		{
			foreach ( $this->_params as &$param )
			{
				$statement->bindValue( ":{$param->name}", $param->value, $param->type );
			}
		}
	}


	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//---------------------------    Public    -------------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------


	/**
	* Resets the query - calling this is just like destroying a query object
	* and creating a new one
	*/
	public function reset()
	{
		$this->_selectList		= array();
		$this->_fromList		= array();
		$this->_joinList		= array();
		$this->_insertList		= array();
		$this->_setList			= array();
		$this->_params			= array();
		
		$this->_insertTable		= '';
		$this->_updateTable		= '';
		$this->_whereList		= array( "1=1" );
		$this->_type			= qtNONE;

		$this->_stmHandle		= null;
		$this->_executed		= false;
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds a value to bind later on when the query is executed to this class
	* this creates a queryParam object for storage of the 3 bits of data
	* and stores it in the _params class variable
	* @param String The name of the 
	*/
	public function &bindValue( $name, $value, $type )
	{
		$qp = new queryParam( $name, $value, $type );
		$this->_params[$name] =& $qp;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Change the database connection being used for this query.
	* @param String The Logical database connection name - defaults to default.
	*/
	public function changeDB( $which )
	{
		$this->_conn = null;
		$this->_conn =& dbm()->connect( $which );
		$this->_handle = $this->_conn->handle();
	}

	// where public methods
	//------------------------------------------------------------------------
	/**
	* Adds an AND section to the where clause
	* @param String The AND section to add.
	*/
	public function &where( $where )
	{
		return $this->and_where( $where );
	}

	//------------------------------------------------------------------------
	/**
	* Adds an AND section to the where clause
	* @param String The AND section to add.
	*/
	public function &and_where( $where )
	{
		$this->_whereList[] = 'AND ' . $where;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Adds an OR section to the WHERE clause
	* @param String The OR section to add.
	*/
	public function &or_where( $where )
	{
		$this->_whereList[] = 'OR ' . $where;
		return $this;
	}
	// end of where public methods
	//------------------------------------------------------------------------
	
	//------------------------------------------------------------------------
	/**
	* Adds a table name to the from list for a select query
	* @return $this - current instance for method chaining.
	*/
	public function &from( $table )
	{
		$this->_fromList[] = $table;
		return $this;
	}
	
	// public delete methods
	//------------------------------------------------------------------------
	/**
	* Used to create a delete query from the specified table
	* @return $this - current instance for method chaining.
	*/
	public function &delete_from( $table )
	{
		$this->from( $table );
		$this->_type = qtDELETE;
		return $this;
	}
	
	// public insert methods
	//------------------------------------------------------------------------
	/**
	* Inserts into the specified table
	* @param String The table name
	* @return $this - current instance for method chaining.
	*/
	public function &insert_into( $table )
	{
		$this->_type = qtINSERT;
		$this->_insertTable = $table;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Used to create an insert query - pass in the list of field names
	* then use bind later on to bind a value or param into the query
	* @param Mixed String/Array - either a string of a single field name
	* or an array of fields
	* @param Bool Check for and do not add duplicate field names.  setting this to true will slow things down
	* you should only use this if you have the potential or know you will be trying to set the same fieldname more than once
	* @return $this - current instance for method chaining.
	*/
	public function &insert_fields( $fields, $dupCheck = false )
	{
		$loopFields = array();
		arrayify( $fields, $loopFields );
		
		if ( $dupCheck === false )
		{
			foreach ( $loopFields as $field )
			{
				$this->_insertList[] = (string) $field;
			}
		}
		else
		{
			foreach( $loopFields as $field )
			{
				$field = (string)$field;
				if ( ! in_array( $field, $this->_setList ) )
				{
					$this->_insertList[] = $field;
				}
			}
		}
		
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function insert_fields_ds( $ds )
	{
		foreach( $this->_insertList as $fieldName )
		{
			//$this->bind
		}
	}
	// end of public insert methods
	//------------------------------------------------------------------------
	
	// public select methods
	//------------------------------------------------------------------------
	/**
	* Add fields to the field list for a select query
	* Also marks this query as a select query;
	* @param Mixed Array or String of the fields to include in the query
	* @return $this - current instance for method chaining.
	*/
	public function &select( $fieldList )
	{
		$this->_type = qtSELECT;
		if ( is_array( $fieldList ) )
		{
			$this->_selectList = array_merge( $this->_selectList, $fieldList );
		}
		else
		{
			$this->_selectList[] = $fieldList;
		}

		return $this;
	}
	
	// including joins in select method section because joins are typically 
	// used in selects, but this is in no way ignoring the fact that joins can be used
	// in other types of queries
	
	//------------------------------------------------------------------------
	/**
	* Set a join for a select query
	* @param String The table to join
	* @param String The clause for the join - used in the "ON"
	* @param String The type of join.  Defaults to LEFT OUTER JOIN, but can be any type.
	* No error checking is done to ensure the join type is correct
	* @return $this - current instance for method chaining.
	*/
	public function &join( $table, $clause, $type = 'LEFT OUTER JOIN' )
	{
		$this->_joinList[] = new queryJoin( $type, $table, $clause );
		return $this;
	}
	// end of public select methods
	//------------------------------------------------------------------------

	// public update methods
	//------------------------------------------------------------------------
	/**
	* Sets query type to update.  Sets the update query table
	* This is required for update queries.
	* @param String The name of the table we're updating
	* @return $this - current instance for method chaining.
	*/
	public function &update( $tableName )
	{
		$this->_updateTable = $tableName;
		$this->_type = qtUPDATE;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Used to create an update query - pass in the list of field names
	* then use bind later on to bind a value or param into the query
	* @param Mixed String/Array - either a string of a single field name
	* or an array of fields
	* @param Bool Check for and do not add duplicate field names.  setting this to true will slow things down
	* you should only use this if you have the potential or know you will be trying to set the same fieldname more than once
	* @return Returns the query object for method chaining
	*/
	public function &set( $fields, $dupCheck = false )
	{
		$loopFields = array();
		arrayify( $fields, $loopFields );
		
		if ( $dupCheck === false )
		{
			foreach ( $loopFields as $field )
			{
				$this->_setList[] = (string) $field;
			}
		}
		else
		{
			foreach( $loopFields as $field )
			{
				$field = (string)$field;
				if ( ! in_array( $field, $this->_setList ) )
				{
					$this->_setList[] = $field;
				}
			}
		}
		
		return $this;
	}
	// end of update public methods
	//------------------------------------------------------------------------


	//------------------------------------------------------------------------
	/**
	* Generates the SQL for a query
	*/
	public function generate()
	{
		$out = '';
		
		switch ( $this->_type )
		{
		case qtSELECT:
			$out = $this->_select() . $this->_from() . $this->_join() . $this->_where();
			break;

		case qtUPDATE:
			if ( count( $this->_setList ) == 0 )
			{
				throw new tgsfDbException( "Can't Update when no field values have been set." );
			}

			$out = $this->_update() . $this->_set() . $this->_where();
			break;
			
		case qtINSERT:
			if ( count( $this->_insertList ) == 0 )
			{
				throw new tgsfDbException( "Can't Insert with no fields - use insert_fields()" );
			}

			$out = $this->_insert() . $this->_insertList() . $this->_insertParams();
			break;
			
		case qtDELETE:
			$out = 'DELETE ' . $this->_from() . $this->_where();
			break;
		
		default:
			throw new tgsfDbException( 'Query has not been set up.' );
		}
		
		return $out;
	}

	//------------------------------------------------------------------------
	/**
	* Executes the query that has been set up.  If a query has not been defined
	* an exception is thrown.
	*/
	public function &exec()
	{
		$success = false;
		if ( is_null( $this->_stmHandle ) )
		{
			$this->_stmHandle = $this->_handle->prepare( $this->generate() );
		}

		$this->_doBindValues( $this->_stmHandle );
		$success = $this->_stmHandle->execute();

		if ( $success === false )
		{
			throw new tgsfDbException( 'Error executing query - error is: ' . implode( "\n", $this->_stmHandle->errorInfo() ) );
		}
		$this->_executed = true;
		return $this;
	}
	
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//--------------------------- result methods -----------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	
	/**
	* Fetches the next row from a result set.  Leaving all parameters set to their defaults and simply calling
	* ->fetch(); will return the next row from a query as an object.
	* @param PDO::FETCH_* - Typically ignorable - Controls how the next row will be returned to the caller. This value must be one of the PDO::FETCH_* constants, defaulting to PDO::FETCH_OBJ
	* @param PDO::FETCH_ORI_* - Typically ignorable - For a PDOStatement object representing a scrollable cursor, this value determines which row will be returned to the caller. This value must be one of the PDO::FETCH_ORI_* constants, defaulting to PDO::FETCH_ORI_NEXT. To request a scrollable cursor for your PDOStatement object, you must set the PDO::ATTR_CURSOR attribute to PDO::CURSOR_SCROLL when you prepare the SQL statement with PDO::prepare().
	* @param Int - Typically ignorable. For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to PDO::FETCH_ORI_ABS, this value specifies the absolute number of the row in the result set that shall be fetched. For a PDOStatement object representing a scrollable cursor for which the cursor_orientation parameter is set to PDO::FETCH_ORI_REL, this value specifies the row to fetch relative to the cursor position before PDOStatement::fetch() was called.
	*/
	public function fetch( $style = PDO::FETCH_OBJ, $cursor_orientation = PDO::FETCH_ORI_NEXT, $offset = 0 )
	{
		if ( $this->_executed == false )
		{
			throw new tgsfDbException( 'Unable to fetch on a query that has not been executed.' );
		}
		
		return $this->_stmHandle->fetch( $style, $cursor_orientation, $offset );
	}
	//------------------------------------------------------------------------
	/**
	* Performs a fetch on a query result and returns a dbDataSource object.
	* This gives no control over the fetch style since the return value
	* is a custom data source object that is native only to tgsf.
	* For a description of the parameters, please see fetch()
	* @see fetch()
	*/
	public function fetch_ds( $cursor_orientation = PDO::FETCH_ORI_NEXT, $offset = 0 )
	{
		$ds = new dbDataSource();
		$ds->set( $this->fetch( PDO::FETCH_ASSOC, $cursor_orientation, $offset ) );
	}
	
	//------------------------------------------------------------------------
	/**
	* Fetches all rows from a query result.
	*
	*/
	public function fetchAll( $style = PDO::FETCH_OBJ, $col = 0 )
	{
		if ( $this->_executed == false )
		{
			throw new tgsfDbException( 'Unable to fetch_all on a query that has not been executed.' );
		}
		return $this->_stmHandle->fetchAll( $style, $col );
	}
	
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function fetchAll_ds( $style = PDO::FETCH_OBJ, $col = 0 )
	{
		$ds = new dbDataSource();
		$ds->setRows( $this->fetchAll( $style, $col ) );
	}
	//------------------------------------------------------------------------
	public function fetchColumn( $col = 0 )
	{
		if ( $this->_executed == false )
		{
			throw new tgsfDbException( 'Unable to fetch_column on a query that has not been executed.' );
		}
		
		return $this->_stmHandle->fetchColumn( $col );
	}
}
