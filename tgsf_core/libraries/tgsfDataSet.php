<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class tgsfDataSetDef
{
	public $type = 'string';
	public $subType;

	public $name;
	public $strict = false;
	public $whitelist;
	public $pt = ptSTR;
	//------------------------------------------------------------------------
	public function type( $type )
	{
		$this->type = $type;
		return $this;
	}
	//------------------------------------------------------------------------
	public function name( $name )
	{
		$this->name = $name;
		return $this;
	}
	//------------------------------------------------------------------------
	public function strict( $strict )
	{
		$this->type = $strict;
		return $this;
	}
	//------------------------------------------------------------------------
	public function whitelist()
	{
		$this->whitelist = func_get_args();
		return $this;
	}
	//------------------------------------------------------------------------
	public function pt( $pt )
	{
		$this->pt = $pt;
		return $this;
	}
}
//------------------------------------------------------------------------
abstract class tgsfDataSet
{
	protected	$_defList			= array();
	private		$_data 				= array();
	public		$parent				= null;

	//------------------------------------------------------------------------
	abstract protected function _setup();
	//------------------------------------------------------------------------
	public function __construct( $parent = null )
	{
		if ( ! $parent == null )
		{
			$this->parent = $parent;
		}
		$this->_setup();
	}
	//------------------------------------------------------------------------
	public function define( $name )
	{
		if ( array_key_exists( $name, $this->_defList ) )
		{
			throw new Exception( $name . ' has already been defined on this dataset.' );
		}

		$this->_defList[$name] = new tgsfDataSetDef();
		return $this->_defList[$name];
	}
	//------------------------------------------------------------------------
	public function removeVar( $name )
	{
		if ( $this->exists( $name ) )
		{
			unset( $this->_defList[$name] );
			$this->unsetVar( $name );
		}
	}
	//------------------------------------------------------------------------
	public function __get( $name )
	{
		if ( ! $this->exists( $name ) )
		{
			throw new Exception( $name . ' is not a defined member of this data set.' );
		}

		if ( array_key_exists( $name, $this->_data ) )
		{
			return $this->_data[$name];
		}

		if ( $this->_defList[$name]->strict )
		{
			throw new Exception( 'value for ' . $name . ' has not been set.' );
		}

		return '';
	}
	//------------------------------------------------------------------------
	public function __set( $name, $value )
	{
		if ( array_key_exists( $name, $this->_defList ) )
		{
			$type = $this->_defList[$name]->type;

			$valType = gettype( $value );

			if ( $valType == 'object' )
			{
				if ( !$value instanceOf $type )
				{
					throw new Exception( get_class( $value ) . ' Is not an Instance of ' . type );
				}
			}
			elseif ( $valType != $type )
			{
				throw new Exception( $valType . ' is not of type: ' . $type );
			}
		}
		else
		{
			throw new Exception( $name . ' is not a defined member of this data set.' );
		}

		$this->setValue( $name, $value );
		return $this;
	}
	//------------------------------------------------------------------------
	public function __call( $name, $value )
	{
		if ( $value == null )
		{
			if ( ! $this->exists( $name ) )
			{
				throw new Exception( $name . ' is not a defined member of this data set.' );
			}

			$type = $this->_defList[$name]->type;

			if ( class_exists( $type ) )
			{
				if ( ! $this->isEmpty( $name ) )
				{
					$value = $this->{$name};
				}
				else
				{
					if ( is_subclass_of( $type, 'tgsfDataSet' ) )
					{
						$value = new $type();
						$value->parent = $this;
					}
					else
					{
						$value = new $type();
					}
					$this->setValue( $name, $value );
				}

				return $value;
			}
		}

		$this->setValue( $name, $value[0] );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets a value in the data set
	*/
	public function &setValue( $name, $value )
	{
		if ( $this->exists( $name ) )
		{
			$type = $this->_defList[$name]->type;

			$valType = gettype( $value );
			if ( $valType == 'object' )
			{
				if ( !$value instanceOf $type )
				{
					throw new Exception( get_class( $value ) . ' Is not an Instance of ' . type );
				}
			}
			elseif ( $valType != $type )
			{
				throw new Exception( $valType . ' is not of type: ' . $type );
			}
		}

		if (
			( $this->_defList[$name]->type == 'string' &&
			$this->_defList[$name]->whitelist != '' ) && !
			in_array( $value, $this->_defList[$name]->whitelist )
			)
		{
			throw new Exception( 'the value for ' . $name . ' ( ' . $value . ') is not in the white list: [' . implode( ',', $this->_defList[$name]->whitelist ) . ']' );
		}

		$this->_data[$name] = $value;
		return $this;
	}
/*
	//------------------------------------------------------------------------
	public function dataArray()
	{
		foreach( $this->_defList as $name => $def )
		{
			
		}
		return (array)$this->_data;
	}
	//------------------------------------------------------------------------
	public function dataObj()
	{
		return (stdclass)$this->_data;
	}*/

	//------------------------------------------------------------------------
	/**
	* This completely resets the data that is available in the dataset.
	*/
	public function &reset()
	{
		$this->_data = array();
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* checks to see if a particular data element is empty, using the php construct empty.
	* @param String The name of the data element
	*/
	public function isEmpty( $name )
	{
		return empty( $this->_data[$name] );
	}
	//------------------------------------------------------------------------
	/**
	* Checks to see if a particular data element exists.  this is different than empty
	* because this will return true even if an element is empty as long as
	* it is present in the datasource
	* @return bool true if exists
	*/
	public function exists( $name )
	{
		return array_key_exists( $name, $this->_defList );
	}
	//------------------------------------------------------------------------
	/**
	* Checks to see if a particular data element (must be valid) has been set
	* using array_key_exists.  If you need to check 'empty', use isEmpty
	*/
	public function varSet( $name )
	{
		return array_key_exists( $name, $this->_data );
	}
	//------------------------------------------------------------------------
	/**
	* Manually unset a member of the data source
	*/
	public function &unsetVar( $varName )
	{
		if ( array_key_exists( $varName, $this->_data ) )
		{
			unset( $this->_data[$varName] );
		}
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Pushes values onto defined array vars
	*/
	public function push( $name, $value )
	{
		if ( ! $this->exists( $name ) )
		{
			throw new Exception( $name . ' is not a defined member of this data set.' );
		}

		if ( $this->_defList[$name]->type != 'array' )
		{
			throw new Exception( $name . ' is not defined as an array' );
		}

		if ( ! $this->varSet( $name ) || ! $this->_data[$name] )
		{
			$this->_data[$name] = array();
		}

		return array_push( $this->_data[$name], $value );
	}
	//------------------------------------------------------------------------
	/**
	* Pops values off of defined array vars
	*/
	public function pop( $name )
	{
		if ( ! $this->exists( $name ) )
		{
			throw new Exception( $name . ' is not a defined member of this data set.' );
		}

		if ( $this->_defList[$name]->type != 'array' )
		{
			throw new Exception( $name . ' is not defined as an array' );
		}

		if ( ! $this->_data[$name] )
		{
			$this->_data[$name] = array();
		}

		return array_pop( $this->_data[$name] );
	}
	//------------------------------------------------------------------------
	/**
	* Shift an element off the beginning of a defined array var
	*/
	public function shift( $name )
	{
		if ( ! $this->exists( $name ) )
		{
			throw new Exception( $name . ' is not a defined member of this data set.' );
		}

		if ( $this->_defList[$name]->type != 'array' )
		{
			throw new Exception( $name . ' is not defined as an array' );
		}

		if ( ! $this->_data[$name] )
		{
			$this->_data[$name] = array();
		}

		return array_shift( $this->_data[$name] );
	}
	//------------------------------------------------------------------------
	/**
	* Prepend one or more elements to the beginning of a defined array var
	*/
	public function unshift( $name, $value )
	{
		if ( ! $this->exists( $name ) )
		{
			throw new Exception( $name . ' is not a defined member of this data set.' );
		}

		if ( $this->_defList[$name]->type != 'array' )
		{
			throw new Exception( $name . ' is not defined as an array' );
		}

		if ( ! $this->_data[$name] )
		{
			$this->_data[$name] = array();
		}

		return array_unshift( $this->_data[$name], $value );
	}
}
