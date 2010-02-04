<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
function ENCRYPT( $key = '' )
{
	return tgsfCrypt::get_instance( $key );
}
//------------------------------------------------------------------------
class tgsfCrypt extends tgsfBase
{
	private static	$_instance			= null;

	public $cipher = MCRYPT_RIJNDAEL_256;
	public $key    = '';
	public $mode   = MCRYPT_MODE_ECB;
	protected $_iv  = null;

	//------------------------------------------------------------------------
	/**
	* protected to enforce singleton pattern
	*/
	protected function __construct()
	{
	}
	//------------------------------------------------------------------------
	/**
	* Sets the key used by encrypt and decrypt
	* @param String The key to use
	*/
	public function setKey( $key )
	{
		if ( $key != '' )
		{
			$this->key = $key;
		}
	}

	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance( $key )
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}

		self::$_instance->setKey( $key );

		return self::$_instance;
	}
	//------------------------------------------------------------------------
	/**
	* Prevent users from cloning the instance
	*/
	public function __clone()
	{
		throw new tgsfException( 'Cloning a singleton (tgsfCrypt) is not allowed. Use the ENCRYPT() function to get its instance.' );
	}
	//------------------------------------------------------------------------
	public function iv()
	{
		if ( $this->_iv === null )
		{	
    		$iv_size = mcrypt_get_iv_size( $this->cipher, $this->mode );
    		$this->_iv = mcrypt_create_iv( $iv_size, MCRYPT_RAND );
    	}

    	return $this->_iv;
	}
	//------------------------------------------------------------------------
	public function encode( $data )
	{
		if ( empty( $this->key ) )
		{
			throw new tgsfEncryptionException( 'No key has been set.' );
		}
		return mcrypt_encrypt( $this->cipher, $this->key, $data, $this->mode, $this->iv() );
	}
	//------------------------------------------------------------------------
	public function decode( $data )
	{
		if ( empty( $this->key ) )
		{
			throw new tgsfEncryptionException( 'No key has been set.' );
		}
		$data = mcrypt_decrypt( $this->cipher, $this->key, $data, $this->mode, $this->iv() );
		return str_replace( "\x0", '', $data );
	}
}