<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
/**
* The base class.  This prevents objects from setting or getting undeclared variables.
* To avoid exceptions from being thrown, you need to override these functions in your extending class, or
* declare all variables you'll be using in the class definition.
*/
class tgsfBase
{
	public function __get( $name )
	{
		if ( property_exists( $this, '_ro_' . $name ) )
		{
			return $this->{'_ro_' . $name};
		}

		throw new tgsfException( 'Undefined class member "' . $name . "\"\nYou must declare all variables you'll be using in the class definition." );
	}
	//------------------------------------------------------------------------
	public function __set( $name, $value )
	{
		ob_start();
		var_dump( $value );
		$vd = ob_get_contents();
		ob_end_clean();
		throw new tgsfException( 'SET: Undeclared class variable ' . $name . ' : value: ' . $vd . "\nYou must declare all variables you'll be using in the class definition." );
	}
}
//------------------------------------------------------------------------
// from php manual
// If you intend on creating a lot of custom exceptions, you may find this code useful.
// I've created an interface and an abstract exception class that ensures that all
// parts of the built-in Exception class are preserved in child classes.  It also
// properly pushes all information back to the parent constructor ensuring that nothing is lost.
// This allows you to quickly create new exceptions on the fly.
// It also overrides the default __toString method with a more thorough one.
// ask at nilpo dot com
//------------------------------------------------------------------------
interface IException
{
    /* Protected methods inherited from Exception class */
    public function getMessage();                 // Exception message
    public function getCode();                    // User-defined Exception code
    public function getFile();                    // Source filename
    public function getLine();                    // Source line
    public function getTrace();                   // An array of the backtrace()
    public function getTraceAsString();           // Formated string of trace

    /* Overrideable methods inherited from Exception class */
    public function __toString();                 // formated string for display
    public function __construct( $message = null, $code = 0 );
}
//------------------------------------------------------------------------
abstract class CustomException extends Exception implements IException
{
	protected $message = 'Unknown exception';		// Exception message
	private   $string;								// Unknown
	protected $code    = 0;							// User-defined exception code
	protected $file;								// Source filename of exception
	protected $line;								// Source line of exception
	private   $trace;								// Unknown
	protected $errno;								// error number aka error level - integer

	//------------------------------------------------------------------------
	public function __construct( $message = null, $code = 0 )
	{
		if ( ! $message )
		{
			throw new $this( 'Unknown '. get_class( $this ) );
		}

		parent::__construct( $message, $code );
	}

	//------------------------------------------------------------------------
	public function __toString()
	{
		$htmlPre  = '<pre class=\"error\">';
		$htmlPost = '</pre>';

		if ( TGSF_CLI )
		{
			$htmlPre  = '';
			$htmlPost = '';
		}

		return $htmlPre . PHP_EOL . PHP_EOL . get_class( $this ) .
			":\n{$this->message}\n\nin {$this->file}({$this->line})\n" .
			"{$this->getTraceAsString()}" . $htmlPost;
	}
}
//------------------------------------------------------------------------
function fatalErrorBacktrace()
{
	$msg = 'Fatal Error.  An administrator has been notified with the details.';
	
	if ( in_debug_mode() )
	{
		fb( $msg, FirePHP::ERROR );
	}

	$btrace = array();

	$t = debug_backtrace();
	
	array_shift( $t );

	$errorDetails = $t[0]['args'][1];
	array_shift( $t );

	foreach ( $t as $lineInfo )
	{
		if ( in_debug_mode() )
		{
			fb( $lineInfo, $lineInfo['function'] . '() : ' . basename($lineInfo['file']) . ' : ' . $lineInfo['line'], FirePHP::INFO );
		}
		
		$btrace[] = 'Function: ' . $lineInfo['function'];
		$btrace[] = '    File: '     . $lineInfo['file'];
		$btrace[] = '    Line: '     . $lineInfo['line'];
		
		if ( isset( $lineInfo['args'] ) )
		{
			foreach ( $lineInfo['args'] as $argName => $argValue )
			{
				$btrace[] = "        " . $argName . ' = ' . get_dump( $value );
			}
		}

		$btrace[] = '';
	}
	
	$btrace = implode( PHP_EOL, $btrace );
	
	if ( function_exists( 'LOGGER' ) )
	{
		LOGGER()->log( $errorDetails . PHP_EOL . $btrace, 'fatal' );
	}
	else
	{
		log_error( $errorDetails . PHP_EOL . $btrace );
	}

	if ( in_debug_mode() )
	{
		$open = TGSF_CLI?'':'<pre>';
		$close = TGSF_CLI?'':'</pre>';
		echo $msg . $open . $errorDetails . PHP_EOL . $btrace . $close;
	}
	else
	{
		echo $msg;
	}

	exit();
}
//------------------------------------------------------------------------
set_error_handler( create_function( '$a, $b, $c, $d', 'if ( $a==2) { fatalErrorBacktrace(); }; throw new ErrorException( $b, 0, $a, $c, $d ); return false;' ), E_ALL );
//------------------------------------------------------------------------
// end ask at nilpo dot com code
//------------------------------------------------------------------------

//------------------------------------------------------------------------
// The custom class types for different errors.
//------------------------------------------------------------------------
class tgsfException extends CustomException { }
class tgsfDbException extends tgsfException { }
class tgsfGridException extends tgsfException { }
class tgsfHtmlException extends tgsfException { }
class tgsfFormException extends tgsfException { }
class tgsfValidationException extends tgsfException { }
class appException extends tgsfException{ }
