<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//------------------------------------------------------------------------
// IDD generator Requires idd Table - see idd.sql
//------------------------------------------------------------------------
function &IDD( $table = null )
{
	return tgsfIDD::get_instance( $table );
}
//------------------------------------------------------------------------
// why not extend model?
class tgsfIDD extends tgsfBase
{
	/* Region: Singleton */
	private static $_instance;
	protected $_ro_table;

	//------------------------------------------------------------------------
	public static function &get_instance( $table )
	{
		if ( self::$_instance === null )
		{
			if ( $table === null )
			{
				throw new tgsfException( 'Table name is required when calling IDD for the first time.' );
			}
			$c = __CLASS__;
			self::$_instance = new $c( $table );
		}

		return self::$_instance;
	}

	/* End Region: Singelton */
	//------------------------------------------------------------------------
	/**
	* Singleton's don't have public constructors
	*/
	protected function __construct( $table )
	{
		$this->_ro_table = $table;
	}
	//------------------------------------------------------------------------
	function getNextID( $key, $defaultInitialValue = 1 )
	{
		$query = new query();
		dbm()->beginTransaction();
		$rows = $query
				->select( 'idd_nextid' )
				->from( $this->table )
				->where( 'idd_id=:idd_id')
				->bindValue( 'idd_id', $key, ptINT )
				->exec()
				->fetchAll();

		if ( count( $rows ) )
		{
			$query->reset();
			$result = $rows[0]->idd_nextid;

			$query
				->update( $this->table )
				->setLiteral( 'idd_nextid=idd_nextid+1' )
				->where( 'idd_id=:idd_id' )
				->bindValue( 'idd_id', $key, ptSTR )
				->exec();
		}
		else
		{
			$result = $defaultInitialValue;
			$query->reset();

			$query
				->insert_into( $this->table )
				->insert_fields( 'idd_id', 'idd_nextid' )
				->bindValue( 'idd_id', $key, ptSTR )
				->bindValue( 'idd_nextid', $result+1, ptINT )
				->exec();
		}
		dbm()->commit();
		return $result;
	}


	/**
	 * Good check digit function based on UPC check digits
	 */
	function getCheckDigit($digits)
	{
		$odd_total  = 0;
		$even_total = 0;

		for( $i=0; $i < strlen($digits); $i++)
		{
			if((($i+1)%2) == 0)
			{
				/* Sum even digits */
				$even_total += $digits[$i];
			}
			else
			{
				/* Sum odd digits */
				$odd_total += $digits[$i];
			}
		}

		$sum = (3 * $odd_total) + $even_total;

		/* Get the remainder MOD 10*/
		$check_digit = $sum % 10;

		/* If the result is not zero, subtract the result from ten. */
		return ($check_digit > 0) ? 10 - $check_digit : $check_digit;
	}
}
