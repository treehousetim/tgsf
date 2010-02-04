<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
function SESSION()
{
	return tgsfSession::get_instance();
}
//------------------------------------------------------------------------
class tgsfSession extends tgsfBase
{
	private static	$_instance			= null;
	protected		$_ro_started		= false;
	
	//------------------------------------------------------------------------
	/**
	* protected to enforce singleton pattern
	*/
	protected function __construct()
	{

	}
	
	//------------------------------------------------------------------------
	/**
	* Static function that returns the singleton instance of this class.
	*/
	public static function &get_instance()
	{
		if ( self::$_instance === null )
		{
			$c = __CLASS__;
			self::$_instance = new $c;
		}
		
		return self::$_instance;
	}
	//------------------------------------------------------------------------
	/**
	* Prevent users from cloning the instance
	*/
	public function __clone()
	{
		throw new tgsfException( 'Cloning a singleton (tgsfSession) is not allowed. Use the SESSION() function to get its instance.' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function &start()
	{
		if ( TGSF_CLI )
		{
			return $this;
		}
		
		if ( $this->_ro_started === false )
		{
			load_config( 'session' );
			ini_set( 'session.use_trans_sid',		false					);
			ini_set( 'url_rewriter.tags',			''						);
			ini_set( 'session.use_cookies',			true					);
			ini_set( 'session.use_only_cookies',	true					);
			ini_set( 'session.name', 				sha1( current_domain()	) );
			ini_set( 'session.cookie_lifetime',		0 						); // until browser is closed.  We implement this server side below

			ini_set( 'session.cache_expire',			config( 'session/page-cache-expire'	) );
			ini_set( 'session.hash_function',			config( 'session/hash_function'		) );
			ini_set( 'session.hash_bits_per_character',	config( 'session/hash_bits'			) );
			ini_set( 'session.cookie_domain',			config( 'session/cookie_domain'		) );
			ini_set( 'session.cookie_path',				config( 'session/cookie_path'		) );
			ini_set( 'session.cache_limiter',			config( 'session/cache_limiter'		) );
			ini_set( 'session.cookie_httponly', 		config( 'session/httponly'			) );
			ini_set( 'session.hash_function',			config( 'session/hash_function'		) );
			
			session_start();
			$this->_ro_started = true;
		}

		// now we manually expire the session if necessary
		
		$lifetime = config( 'session/lifetime' );
		if ( empty( $_SESSION['tgsf-last-access'] ) )
		{
			$_SESSION['tgsf-last-access'] = time();
		}
		elseif ( time() - $_SESSION['tgsf-last-access'] >= $lifetime )
		{
			$this->_ro_started = false;
			$_SESSION = array();
			if ( isset( $_COOKIE[session_name()] ) )
			{
				setcookie( session_name(), '', time()-42000, config( 'session/cookie_path' ) );
			}
			session_destroy();
			
			
			$this->start(); // restart the session.
		}

		$_SESSION['tgsf-last-access'] = time();

		return $this;
	}
}