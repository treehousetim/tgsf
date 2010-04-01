<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
enum( 'qt',
	array(
		'NONE'   => NULL,
		'STATIC' => 'STATIC',
		'SELECT' => 'SELECT',
		'INSERT' => 'INSERT',
		'UPDATE' => 'UPDATE',
		'DELETE' => 'DELETE'
		)
	);

define( 'qiDUP_CHECK', true );
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
	protected $_stmHandle			= null;
	protected $_executed			= false;
	protected $_insertTable			= '';
	protected $_updateTable			= '';
	protected $_whereList			= array( "1=1" );
	protected $_type				= qtNONE;
	protected $_table				= '';
	protected $_limit				= '';
	
	protected $_staticQuery			= '';

	protected $_selectList			= array();
	protected $_orderByList			= array();
	protected $_groupByList			= array();
	protected $_joinList			= array();
	protected $_fromList			= array();
	protected $_setList				= array();
	protected $_literalSet			= array();
	protected $_insertList			= array();
	protected $_tableList			= array();

	protected $_params				= array();
	protected $_paramTypes			= array();
	protected $_currentParamType	= ptSTR;
	protected $_ro_lastInsertId		= false;
	protected $_ro_rowCount			= false;
	protected $_generated_sql       = '';

	protected $_duplicateKeyUpdate	= false;
	protected $_duplicateKeyUpdateString = '';
	
	//------------------------------------------------------------------------
	public static function &factory( $which = 'default' )
	{
		$c = __CLASS__;
		$instance = new $c( $which );
		return $instance;
	}

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
		return ' ' . implode( ' ', $out ) . ' ';
	}

	//------------------------------------------------------------------------
	/**
	* generates the select list (fields) when we render the query
	*/
	protected function _select()
	{
		return 'SELECT ' . implode( ',', $this->_selectList ) . ' ';
	}

	//------------------------------------------------------------------------
	/**
	* Generates the order by clause for select queries
	*/
	public function _orderBy()
	{
		if ( count( $this->_orderByList ) )
		{
			return 'ORDER BY ' . implode( ',', $this->_orderByList ) . ' ';
		}

		return '';
	}
	
	//------------------------------------------------------------------------
	/**
	* Generates the group by clause for select queries
	*/
	public function _groupBy()
	{
		if ( count( $this->_groupByList ) )
		{
			return 'GROUP BY ' . implode( ',', $this->_groupByList ) . ' ';
		}

		return '';
	}

	//------------------------------------------------------------------------
	/**
	* Generates the limit option for select, update, and delete queries
	*/
	public function _limit()
	{
		if ( $this->_limit )
		{
			return "LIMIT {$this->_limit} ";
		}

		return '';
	}

	//------------------------------------------------------------------------
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
	* returns a SQL string to handle duplicate key updates
	*/
	protected function _duplicateKeyUpdate()
	{
		$values = array();

		if ( $this->_duplicateKeyUpdateString )
		{
			$values[] = $this->_duplicateKeyUpdateString;
		}
		else
		{
			foreach( $this->_insertList as $value )
			{
				$values[] = $value . " = VALUES({$value})";
			}
		}

		return ' ON DUPLICATE KEY UPDATE ' . implode( ',', $values ); 
	}

	//------------------------------------------------------------------------
	/**
	* returns the SET field=:field,field1=:field1 portion of an update query
	* if you use the table.field pattern, the parameter will become table_field
	*/
	protected function _set()
	{
		$tmp = array();
		foreach ( $this->_setList as $item )
		{
			$citem = clean_text( $item );
			$tmp[] = $item . " = :{$citem}";
		}

		foreach ( $this->_literalSet as $str )
		{
			$tmp[] = $str;
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
		$this->_stmHandle			= null;
		$this->_executed			= false;
		$this->_insertTable			= '';
		$this->_updateTable			= '';
		$this->_whereList			= array( "1=1" );
		$this->_type				= qtNONE;
		$this->_table				= '';
		$this->_limit				= '';
		$this->_staticQuery			= '';

		$this->_selectList			= array();
		$this->_orderByList			= array();
		$this->_fromList			= array();
		$this->_joinList			= array();
		$this->_setList				= array();
		$this->_literalSet			= array();
		$this->_insertList			= array();
		$this->_tableList			= array();

		$this->_params				= array();
		$this->_paramTypes			= array();
		$this->_currentParamType	= ptSTR;
		$this->_ro_rowCount			= false;
		$this->_ro_lastInsertId		= false;

		$this->_generated_sql       = '';

		$this->_duplicateKeyUpdate = false;
		$this->_duplicateKeyUpdateString = '';
	}
	//------------------------------------------------------------------------
	public function &static_query( $query )
	{
		$this->_type = qtSTATIC;
		$this->_staticQuery = $query;
		return $this;
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
	public function &bindValDs( $name, $ds, $type )
	{
		return $this->bindValue( $name, $ds->_( $name ), $type );
	}
	//------------------------------------------------------------------------
	public function &clearBoundParams()
	{
		$this->_params = array();
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the query params as a datasource
	*/
	public function paramsAsDs()
	{
		$ds = tgsfDataSource::factory();
		foreach ( $this->_params as $name => $param )
		{
			$ds->setVar( $name, $param->value );
		}
		
		return $ds;
	}
	//------------------------------------------------------------------------
	/**
	* Change the database connection being used for this query.
	* @param String The Logical database connection name - defaults to default.
	*/
	public function changeDB( $which )
	{
		$this->_stmHandle = null;
		$this->_conn = null;
		$this->_conn =& dbm()->connect( $which );
		$this->_handle = $this->_conn->handle();
	}
	//------------------------------------------------------------------------
	// where public methods
	//------------------------------------------------------------------------
	/**
	* Adds static text to the where stack
	* @param String The text to add
	*/
	public function staticWhere( $str )
	{
		$this->_whereList[] = $str;
	}
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
	//------------------------------------------------------------------------
	/**
	* Adds an AND condition for a like - only if the datasource contains a non-empty value for $name
	*/
	public function where_like( $name, $ds, $valuePrefix = '%', $valuePostfix = '%', $booleanOp = 'AND ' )
	{
		if ( $ds->{$name} != '' )
		{
			$this->_whereList[] = $booleanOp . $name . ' LIKE :' . $name;
			$val = $valuePrefix . trim( $ds->_( $name ) ) . $valuePostfix;

			$this->bindValue( $name, $val, ptSTR );
		}

	}
	//------------------------------------------------------------------------
	/**
	* Adds an OR condition for a like - only if the datasource contains a non-empty value for $name
	*/
	public function or_where_like( $name, $ds, $valuePrefix = '%', $valuePostfix = '%' )
	{
		$this->where_like( $name, $ds, $valuePrefix, $valuePostfix, 'OR ' );
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
		$this->_tableList[] = $table;
		$this->_fromList[] = $table;
		$this->_table = $table;
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
	public function &insert_into( $table, $update = UPDATE_OFF )
	{
		$this->_type = qtINSERT;
		$this->_insertTable = $table;
		$this->_table = $table;
		$this->_duplicateKeyUpdate = $update;
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	* Inserts into the specified table
	* @param SQL string indicating what fields to update and their values in the event of a duplicate key
	* @return $this - current instance for method chaining.
	*/
	public function &update_action( $actionString )
	{
		$this->_duplicateKeyUpdate = true;
		$this->_duplicateKeyUpdateString = $actionString;
		return $this;
	}

	//------------------------------------------------------------------------
	/**
	* Used to create an insert query - pass in the list of field names
	* then use bind later on to bind a value or param into the query
	* You can send a list of field names to this function ignoring the 2 params
	* documented below.  No duplicate checking is performed when using this form.
	* @param Mixed String/Array - either a string of a single field name
	* or an array of fields
	* @param Bool Check for and do not add duplicate field names.  setting this to true will slow things down
	* you should only use this if you have the potential or know you will be trying to set the same fieldname more than once
	* @return $this - current instance for method chaining.
	*/
	public function &insert_fields( $fields, $dupCheck = false )
	{
		if ( ! is_array( $fields ) && func_num_args() > 1 && is_bool( func_get_arg( 1 ) ) === false )
		{
			$fields = func_get_args();
			$dupCheck = false;
		}
		else
		{
			$fields = (array)$fields;
		}


		if ( $dupCheck === false )
		{
			foreach ( $fields as $field )
			{
				$fieldName = (string)$field;
				$this->_insertList[] = $fieldName;
				$this->_paramTypes[$fieldName] = $this->_currentParamType;
			}
		}
		else
		{
			foreach( $fields as $field )
			{
				$field = (string)$field;
				if ( ! in_array( $field, $this->_insertList ) )
				{
					$this->_insertList[] = $field;
					$this->_paramTypes[$field] = $this->_currentParamType;
				}
			}
		}

		return $this;
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
	public function &select( $fieldList = '*' )
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

	/**
	* A shortcut function that aliases ->select( 'count(*)' );
	* @param String The field to put inside the count function
	*/
	public function &count( $fieldList = '*' )
	{
		return $this->select( 'count(' . $fieldList . ')' );
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
		$this->_tableList[] = $table;
		$this->_joinList[] = new queryJoin( $type, $table, $clause );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &order_by( $clause )
	{
		$this->_orderByList[] = $clause;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &group_by( $field )
	{
		$this->_groupByList[] = $field;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a limit on a query - mysql specific
	*/
	public function &limit( $limit )
	{
		$this->_limit = (string)$limit;
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
	public function &update( $table )
	{
		$this->_type = qtUPDATE;
		$this->_updateTable = $table;
		$this->_table = $table;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Used to create an update query - pass in the list of field names
	* then use bind later on to bind a value or param into the query
	* You can send a list of field names to this function ignoring the 2 params
	* documented below.  No duplicate checking is performed when using this form.
	* @param Mixed String/Array - either a string of a single field name
	* or an array of fields
	* @param Bool Check for and do not add duplicate field names.  setting this to true will slow things down
	* you should only use this if you have the potential or know you will be trying to set the same fieldname more than once
	* @return Returns the query object for method chaining
	*/
	public function &set( $fields, $dupCheck = false )
	{
		if ( ! is_array( $fields ) && func_num_args() > 1 && is_bool( func_get_arg( 1 ) ) === false )
		{
			$fields = func_get_args();
			$dupCheck = false;
		}
		else
		{
			$fields = (array)$fields;
		}

		if ( $dupCheck === false )
		{
			foreach ( $fields as $field )
			{
				$field = (string)$field;
				$this->_setList[] = $field;
				$this->_paramTypes[$field] = $this->_currentParamType;
			}
		}
		else
		{
			foreach( $fields as $field )
			{
				$field = (string)$field;
				if ( ! in_array( $field, $this->_setList ) )
				{
					$this->_setList[] = $field;
					$this->_paramTypes[$field] = $this->_currentParamType;
				}
			}
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Allows a literal string to be used when updating.
	* example:
	* $q->setLiteral( 'field=field+1' )
	* @param String The text to literally put in the query
	*/
	public function &setLiteral( $str )
	{
		$this->_literalSet[] = $str;
		return $this;
	}
	// end of update public methods
	//------------------------------------------------------------------------
	/**
	* Sets the internal variable $this->_currentParamType to a param type value (see /tgsf_core/libraries/db/enum.php)
	* used to populate an internal param types array for different params in an update or insert query
	* use of this is required for autoBind to work.
	* No need to call this if all fields are ptSTR's
	* @param Int - A param type
	*/
	public function &pt( $paramType )
	{
		$this->_currentParamType = $paramType;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Automatically binds values to the query from a datasource.
	* relies on the use of calling $this->pt( paramtype );
	* @param Object::tgsfDatasource
	*/
	public function &autoBind( $ds )
	{
		foreach ( $this->_paramTypes as $field => $pt )
		{
			$this->bindValue( $field, $ds->$field, $pt );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* creates and returns a new unionized query
	*/
	public function union( $type = 'ALL' )
	{
		throw new tgsfException( 'Not Implemented' );
	}
	//------------------------------------------------------------------------
	public function &getSQL( &$sql )
	{
		$sql = $this->_generated_sql;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Generates the SQL for a query
	*/
	public function generate()
	{
		$out = '';

		switch ( $this->_type )
		{
		case qtSTATIC:
			$out = $this->_staticQuery;
			break;

		case qtSELECT:
			$out = $this->_select() . $this->_from() . $this->_join() . $this->_where() . $this->_groupBy() . $this->_orderBy() . $this->_limit();
			break;

		case qtUPDATE:
			if ( ( count( $this->_literalSet ) > 0 || count( $this->_setList ) > 0 ) === false )
			{
				throw new tgsfDbException( "Can't Update when no field values have been set." );
			}

			$out = $this->_update() . $this->_join() . $this->_set() . $this->_where() . $this->_limit();
			break;

		case qtINSERT:
			if ( count( $this->_insertList ) == 0 )
			{
				throw new tgsfDbException( "Can't Insert with no fields - use insert_fields()" );
			}

			$out = $this->_insert() . $this->_insertList() . $this->_insertParams();

			if ( $this->_duplicateKeyUpdate )
			{
				$out .= $this->_duplicateKeyUpdate();
			}
			break;

		case qtDELETE:
			$out = 'DELETE ' . $this->_from() . $this->_where() . $this->_limit();
			break;

		default:
			throw new tgsfDbException( 'Query has not been set up.' );
		}


		$this->_generated_sql = $out;

		return $out;
	}
	//------------------------------------------------------------------------
	/**
	* Returns the generated sql
	*/
	public function sql()
	{
		if ( $this->_generated_sql == '' )
		{
			$this->generate();
		}
		return $this->_generated_sql;
	}
	//------------------------------------------------------------------------
	/**
	* Prepares the query so it's ready for execution.
	*/
	public function prepare()
	{
		$this->_stmHandle = $this->_handle->prepare( $this->generate() );
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
			$this->prepare();
		}

		$this->_doBindValues( $this->_stmHandle );
		$success = $this->_stmHandle->execute();
		$this->_ro_rowCount = $this->_stmHandle->rowCount();

		if ( $success === false )
		{
			if ( function_exists( 'LOGGER' ) )
			{
				LOGGER()->queryError( $this->sql(), $this->_stmHandle->errorInfo(), $this->paramsAsDs() );
			}
			else
			{
				log_query_error( $this->_stmHandle->errorInfo() . PHP_EOL . $this->generate() . get_dump( $this->paramsAsDs()->dataObject() ) );
			}

			throw new tgsfDbException( 'Error executing query - error is: ' . implode( "\n", $this->_stmHandle->errorInfo() ) );
		}
		$this->_executed = true;

		if ( $this->_type == qtINSERT )
		{
			$this->_ro_lastInsertId = dbm()->lastInsertId();
		}
		return $this;
	}

	//------------------------------------------------------------------------

	public function &log( $pk1 = '', $event = '' )
	{
		$login_id = null;
		if ( function_exists( 'AUTH' ) )
		{
			$login_id = AUTH()->getLoginId();
		}

		$ds = new dbDataSource();
		$ds->set( array(
			'log_login_id'	=> $login_id,
		    'log_table'		=> $this->_table,
		    'log_type'		=> $this->_type,
		    'log_pk1'		=> $pk1,
		    'log_sql'		=> $this->generate(),
			'log_app_event'	=> $event,
		    'log_params' => serialize( $this->_params )
		) );

		$q = new query();

		$q->insert_into( 'db_log' )
		  ->pt( ptSTR )
		  ->insert_fields( array(
		    	'log_table',
		    	'log_type',
		    	'log_pk1',
		    	'log_sql',
				'log_app_event',
		    	'log_params'
		    ))

		  ->pt( ptINT )
		  ->insert_fields( array(
				'log_login_id' ))

		  ->autoBind( $ds )
		  ->exec();

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Outputs debugging information for a query after executing
	*/
	public function &debug()
	{
		$eol = str_repeat( "\n", 2 );
		
		if ( in_debug_mode() || TGSF_CLI === true )
		{
			echo "<pre>\n";
			echo "-------------------------------\n";
			echo 'QUERY DEBUG' . $eol;
			
			if ( $this->_type == qtINSERT )
			{
				echo 'Last Insert ID: ' . $this->_ro_lastInsertId . $eol;
			}
			
			echo '$this->rowCount: ' . $this->_ro_rowCount . $eol;
			
			$query = $this->sql();
			
			foreach( $this->_params as $param )
			{
				if ( $param->type == PDO::PARAM_STR || $param->type == PDO::PARAM_LOB )
				{
					$value = "'" . $param->value . "'";
				}
				else
				{
					if ( $param->value === null )
					{
						$value = 'NULL';
					}
					elseif( is_bool( $param->value ) && $param->type == PDO::PARAM_BOOL )
					{
						$value = $param->value?'TRUE':'FALSE';
					}
					else
					{
						$value = $param->value;
					}
				}
				
				$query = str_replace( ':' . $param->name, $value, $query );
			}
			
			echo $this->sql() . $eol;
			echo $query . $eol;
			echo 'Param Values' . $eol;
			$this->paramsAsDs()->debug();
			echo "-------------------------------\n";
			echo "</pre>" . $eol;
		}

		return $this;
	}
	
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//--------------------------- filter methods -----------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	
	//------------------------------------------------------------------------
	/**
	* Outputs debugging information for a query after executing
	*/
	public function &filter( $callback )
	{
		$args = func_get_args();

		if ( is_array( $args ) )
		{
			array_shift( $args );
		}
		else
		{
			$args = array();
		}

		array_unshift( $args, $this );

		call_user_func_array( $callback, $args );
		return $this;
	}
	
	//------------------------------------------------------------------------
	
	public function filterSetIf( &$q, $bool, $pt, $key, $value = false )
	{
		if ( $bool === true )
		{
			switch ( $this->_type )
			{
				case qtINSERT:
					$q->pt( $pt )->insert_fields( $key );
					break;
					
				case qtUPDATE:
				default:
					$q->pt( $pt )->set( $key );
			}
			
			if ( $value !== false )
			{
				$q->bindValue( $key, $value, $pt );
			}
		}
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
	public function &fetch_ds( $cursor_orientation = PDO::FETCH_ORI_NEXT, $offset = 0 )
	{
		$ds = new dbDataSource();
		$ds->setTables( $this->_tableList );
		$result = $this->fetch( PDO::FETCH_ASSOC, $cursor_orientation, $offset );
		if ( $result === false )
		{
			return $result;
		}
		$ds->set( $result );
		return $ds;
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

		if ( $style == PDO::FETCH_COLUMN )
		{
			return $this->_stmHandle->fetchAll( $style, $col );
		}

		return $this->_stmHandle->fetchAll( $style );

	}

	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &fetchAll_ds( $style = PDO::FETCH_OBJ, $col = 0 )
	{
		$ds = new dbDataSource();
		$ds->setTables( $this->_tableList );
		$ds->setRows( $this->fetchAll( $style, $col ) );
		return $ds;
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
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &pluginAction( $name,  )
	{
		$args =  $this->sliceArgs( func_get_args(), 1 );
		array_unshift( $args, $this );
		tPLUGIN()->dispatchAction( $name, $args );

		return $this;
	}
}
