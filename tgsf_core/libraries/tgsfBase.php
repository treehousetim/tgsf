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
		if ( isset( $this->{'_ro_' . $name} ) )
		{
			return $this->{'_ro_' . $name};
		}
		else
		{
			throw new tgsfException( 'GET: Undeclared class variable ' . $name . "\nYou must declare all variables you'll be using in the class definition." );
		}
	}
	
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
// see tgsf_core/legal/phpmanual.txt for why this is included here
// from php manual
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
		return "<pre class=\"error\">\n\n" . get_class( $this ) . ":\n{$this->message}\n\nin {$this->file}({$this->line})\n"
			. "{$this->getTraceAsString()}</pre>";
	}
}
//------------------------------------------------------------------------
function fatalErrorBacktrace()
{
	$t = debug_backtrace();
	foreach ( $t as $lineInfo )
	{
		echo $lineInfo['file'] . ':' . $lineInfo['line'] . ':' . $lineInfo['function'];
		if ( isset( $lineInfo['args'] ) )
		{
			foreach ( $lineInfo['args'] as $argName => $argValue )
			{
				$value = $argValue;
				
				if ( is_object( $argValue ) )
				{
					ob_start();
					var_dump( $argValue );
					$value = ob_get_clean();
				}
				echo $argName . ' = ' . $value . '<br>';
			}
		}
		echo '<hr>';
	}
}
//------------------------------------------------------------------------
set_error_handler( create_function( '$a, $b, $c, $d', 'if ( $a==2) { fatalErrorBacktrace(); }; throw new ErrorException( $b, 0, $a, $c, $d ); return false;' ), E_ALL );
//------------------------------------------------------------------------
// end phpmanual code
//------------------------------------------------------------------------

//------------------------------------------------------------------------
// The custom class types for different errors.
//------------------------------------------------------------------------
class tgsfException extends CustomException { }
class tgsfDbException extends tgsfException { }
class tgsfHtmlException extends tgsfException { }
class tgsfFormException extends tgsfException { }
class tgsfValidationException extends tgsfException { }
class appException extends tgsfException{ }