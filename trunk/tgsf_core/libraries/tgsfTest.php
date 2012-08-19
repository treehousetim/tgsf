<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
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

abstract class tgsfTestOutput extends tgsfBase
{
	public $verbose;
	public function __construct( $verbose = true )
	{
		$this->verbose = $verbose;
	}
	abstract public function line();
	abstract public function display( $text, $newline = false );
	abstract public function delimiter();
}
//------------------------------------------------------------------------------
/**
 *  CLI Output formatter (PHP_EOL and : delimiters)
 */
class tgsfCLIOutput extends tgsfTestOutput
{
	//------------------------------------------------------------------------------
	public function line()
	{
		if ( ! $this->verbose )
		{
			return;
		}
		echo "--------------------------------------------------------------------------------" . PHP_EOL;
	}
	//------------------------------------------------------------------------------
	public function display( $text, $newline = false )
	{
		if ( ! $this->verbose )
		{
			return;
		}
		echo $text;

		if ( $newline )
		{
			echo PHP_EOL;
		}
	}
	//------------------------------------------------------------------------------
	public function delimiter()
	{
		if ( ! $this->verbose )
		{
			return;
		}
		echo ': ';
	}
}
//------------------------------------------------------------------------
/**
 *  HLTM Output formatter
 */
class tgsfHTMLOutput extends tgsfTestOutput
{
	//------------------------------------------------------------------------------
	public function line()
	{
		if ( ! $this->verbose )
		{
			return;
		}
		echo "<hr>";
	}
	//------------------------------------------------------------------------------
	public function display( $text, $newline = false )
	{
		if ( ! $this->verbose )
		{
			return;
		}

		echo $text;

		if ( $newline )
		{
			echo "<br>";
		}
	}
	//------------------------------------------------------------------------------
	public function delimiter()
	{
		if ( ! $this->verbose )
		{
			return;
		}

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

	//------------------------------------------------------------------------------
	public function __construct( tgsfTestOutput $out )
	{
		$this->_ro_output = $out;
	}
	//------------------------------------------------------------------------------
	public function line()
	{
		$this->_ro_output->line();
	}
	//------------------------------------------------------------------------------
	function assertFalse( $name, $results )
	{
		return $this->assertEquals( $name, $results, false );
	}
	//------------------------------------------------------------------------------
	function assertTrue( $name, $results )
	{
		return $this->assertEquals( $name, $results, true );
	}
	//------------------------------------------------------------------------------
	function assertEquals( $name, $testThis, $equalsThis )
	{
		$this->_ro_output->display('------ Execute Test' );
		$this->_ro_output->delimiter();

		$this->_ro_output->display($name);
		$this->_ro_output->delimiter();

		if ( $testThis === $equalsThis )
		{
			$this->_ro_output->display('Passed', true);
			return true;
		}
		else
		{
			$this->_ro_output->display('Failed');
			$this->_ro_output->delimiter();

			$this->_ro_output->display( 'Expected value' );
			$this->_ro_output->delimiter();

			$this->_ro_output->display( trim(get_dump($equalsThis)) . ' ' . gettype($equalsThis));
			$this->_ro_output->delimiter();

			$this->_ro_output->display( 'Got value' );
			$this->_ro_output->delimiter();

			$this->_ro_output->display( trim(get_dump($testThis)) . ' ' . gettype($testThis), true );

			return false;
		}
	}
}
//------------------------------------------------------------------------------
/**
 *  Test Case - the base class for all test cases
 */
class tgsfTestCase extends tgsfTest
{
	public $testCaseManager;
	//------------------------------------------------------------------------------
	public function getDependencies()
	{
		return array();
	}
	//------------------------------------------------------------------------------
	public final function run()
	{

		$this->checkDependencies();

		if ( ! $this->testCaseManager || ! ( $this->testCaseManager instanceof tgsfTestCaseManager ) )
		{
			throw new tgsfException( 'Attempted to run() a test case without a testCaseManager.' );
		}
		$methods = get_class_methods( get_class( $this ) );

		if ( $methods )
		{
			foreach ( $methods as $method )
			{
				if ( starts_with( $method, 'test' ) )
				{
					$this->_ro_output->display( '---Running test ' . $method, true );
					$this->$method();
				}
			}
		}
	}
	//------------------------------------------------------------------------------
	protected final function checkDependencies()
	{
		foreach ( $this->getDependencies() as $dependency => $location )
		{
			if ( !isset( $this->testCaseManager->testCases[$dependency] ) )
			{
				$this->testCaseManager->loadTestCase( $dependency, $location );

				if ( !isset( $this->testCaseManager->testCases[$dependency] ) )
				{
					throw new tgsfException( 'The test case "' . get_class( $this ) . '" depends on the test case "' . $dependency . '" which has not been loaded in the test case manager.' );
				}
			}
		}
	}
}
//------------------------------------------------------------------------------
/**
 *  Test Case Manager - for executing tests
 */
class tgsfTestCaseManager extends tgsfBase
{

	public $testFiles;
	public $skipTests;
	public $exceptionList;
	public $testBasePath = '';
	public $testCases = array();

	public $_ro_output;

	// resetAfterRun tells run() whether or not to reset(), destroying all references to test case objects after running
	// If you aren't concerned with garbage collection, leave this false, otherwise set to true
	public $resetAfterRun = false;

	public function __construct( $out )
	{
		$this->_ro_output = $out;
	}
	//------------------------------------------------------------------------------
	public function init()
	{
		$this->testFiles = array();
		$this->skipTests = array();
		$this->exceptionList = array();
		// testCases is not reinitialized, as we want to be able to add extra tests to the queue before running
	}

	//------------------------------------------------------------------------------
	public function reset()
	{
		$this->init();
		$this->testCases = array();  // Fully reinitialize the tests (destroys all test cases to clear up memory)
	}

	//------------------------------------------------------------------------------
	public function loadTestsFromPath( $path )
	{
		$path = $this->testBasePath . $path . '/';

		$this->init();

		if ( ! file_exists( $path ) )
		{
			throw new tgsfException( 'Unable to locate tests: The folder "' . $path . '" does not exist.' );
		}

		if ( ! is_dir( $path ) )
		{
			throw new tgsfException( 'The path provided, "' . $path . '", is not a directory.' );
		}

		if ( ! $handle = opendir( $path ) )
		{
			throw new tgsfException( 'Unable to read the directory "' . $path . '".' );
		}

		while ( false !== ( $entry = readdir( $handle ) ) )
		{
			// If its '.', '..', or starts with '.', skip it
			if ( substr( $entry, 0, 1 ) == '.' )
			{
				continue;
			}
			$this->testFiles[] = $entry;
		}

		foreach ( $this->testFiles as $entry )
		{
			require_once( $path . $entry );
			$className = str_replace( '.php', '', $entry );
			if ( class_exists( $className ) )
			{
				$testCase = new $className( $this->_ro_output );
				$testCase->testCaseManager = $this;
				$this->testCases[ $className ] = $testCase;
			}
			else
			{
				throw new tgsfException( 'Unable to find class ' . $className . ' in test case file "' . $path . $entry . '".' );
			}
		}

	}
	//------------------------------------------------------------------------------
	public function run()
	{
		$this->init();

		foreach ( $this->testCases as $className => $testCase )
		{
			if ( get_parent_class( $className ) == 'tgsfTestCase' )
			{
				$this->_ro_output->display( 'Found test ' . $className );
				if ( in_array( $className, $this->skipTests ) )
				{
					$this->_ro_output->display( "\t" . '[SKIPPING] ', true );
				}
				else
				{
					$this->_ro_output->display( "\t" . '[RUNNING] ', true );
				}
				try
				{
					$testCase->run();
				}
				catch ( Exception $e )
				{
					$this->exceptionList[] = $e;
				}

			}
		}

		if ( $this->exceptionList )
		{
			return false;
		}

		if ( $this->resetAfterRun )
		{
			// reset() here will destroy all references to test case objects, freeing them for garbage collection.
			$this->reset();
		}

		return true;
	}

	//------------------------------------------------------------------------------
	public function runTestCase( $testCaseName )
	{
		if ( ! isset( $this->testCases[ $testCaseName ] ) )
		{
			throw new tgsfException( 'The test case "' . $testCaseName . '" has not been loaded into this test case manager.' );
		}

		$this->testCases[ $testCaseName ]->run();
	}
	//------------------------------------------------------------------------------
	public function addTestCase( tgsfTestCase $testCase, $allowOverride = false )
	{
		$testCaseName = get_class( $testCase );
		if ( ! $allowOverride && isset( $this->testCases[ $testCaseName ] ) )
		{
			throw new tgsfException( 'The test case "' . $testCaseName . '" already exists in this test case manager.' );
		}

		$testCase->testCaseManager = $this;
		$this->testCases[ $testCaseName ] = $testCase;
	}
	public function loadTestCase( $testCaseName, $testCaseFile )
	{
		if ( isset( $this->testCases[ $testCaseName ] ) )
		{
			return;
		}
		require_once( $this->testBasePath . $testCaseFile . '.php' );
		$testCase = new $testCaseName( $this->_ro_output );
		$this->addTestCase( $testCase );
	}

}
