<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
 *  Simple Test Framework : a beginning
 *
 *  tgsfTest takes a formatter to send it's output to so that it is output agnostic.
 *
 */

//------------------------------------------------------------------------------
/**
 *  CLI Output formatter (PHP_EOL and : delimiters)
 */
class tgsfCLIOutput extends tgsfBase
{
	public function __construct()
	{
	}

	public function line()
	{
		echo "--------------------------------------------------------------------------------";
	}

	public function display( $text, $newline = false )
	{
		echo $text;

		if ( $newline )
		{
			echo PHP_EOL;
		}
	}

	public function delimeter()
	{
		echo ': ';
	}
}
/**
 *  HLTM Output formatter
 */
class tgsfHTMLOutput extends tgsfBase
{
	public function __construct()
	{
	}

	public function line()
	{
		echo "<HR>";
	}

	public function display( $text, $newline = false )
	{
		echo $text;

		if ( $newline )
		{
			echo "<BR />";
		}
	}

	public function delimeter()
	{
		echo '&nbsp;&nbsp;&nbsp;';
	}
}
//------------------------------------------------------------------------------
/**
 *  Test Class - for executing tests
 */
class tgsfTest extends tgsfBase
{
	protected $_ro_output;

	public function __construct( $out )
	{
		$this->_ro_output = $out;
	}

	public function line()
	{
		$this->_ro_output->line();
	}

	// Check Database Connection
	// Check arguments
	function assertFalse( $name, $results )
	{
		return $this->assertEquals( $name, $results, false );
	}

	function assertTrue( $name, $results )
	{
		return $this->assertEquals( $name, $results, true );
	}

	function assertEquals( $name, $testThis, $equalsThis )
	{
		$this->_ro_output->display('Execute Test' );
		$this->_ro_output->delimeter();

		$this->_ro_output->display($name);
		$this->_ro_output->delimeter();

		if ( $testThis === $equalsThis )
		{
			$this->_ro_output->display('Passed', true);
			return true;
		}
		else
		{
			$this->_ro_output->display('Failed');
			$this->_ro_output->delimeter();

			$this->_ro_output->display( 'Expected value' );
			$this->_ro_output->delimeter();

			$this->_ro_output->display( $equalsThis . ' ' . gettype($equalsThis));
			$this->_ro_output->delimeter();

			$this->_ro_output->display( 'Got value' );
			$this->_ro_output->delimeter();

			$this->_ro_output->display( $testThis . ' ' . gettype($testThis), true );

			return false;
		}
	}
}
//------------------------------------------------------------------------------
