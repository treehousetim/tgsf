<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//------------------------------------------------------------------------
// TODO: Add error handling to this class
//------------------------------------------------------------------------
/**
* An empty base class for future use - all core classes extend this.
*/
class tgsfBase
{
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
	protected $message = 'Unknown exception';     // Exception message
	private   $string;                            // Unknown
	protected $code    = 0;                       // User-defined exception code
	protected $file;                              // Source filename of exception
	protected $line;                              // Source line of exception
	private   $trace;                             // Unknown

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
		return get_class( $this ) . " '{$this->message}' in {$this->file}({$this->line})\n"
			. "{$this->getTraceAsString()}";
	}
}
//------------------------------------------------------------------------
// end phpmanual code
//------------------------------------------------------------------------
class tgsfException extends CustomException { }
class tgsfDbException extends tgsfException { }