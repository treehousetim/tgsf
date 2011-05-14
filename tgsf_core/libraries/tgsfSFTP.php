<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// @Requires php_ssh.dll

class tgsfSFTP extends tgsfBase
{
	protected $_connection;
	protected $_sftp;

	protected $_server;
	protected $_port;
	protected $_user;
	protected $_pass;
	protected $_fingerprint;

	//------------------------------------------------------------------------
	public function __construct()
	{
	}
	//------------------------------------------------------------------------
	public function connect( $param )
	{
		$this->_server		= $param['server'];
		$this->_port		= $param['port'];
		$this->_user		= $param['user'];
		$this->_pass		= $param['pass'];
		$this->_fingerprint	= $param['fingerprint'];

		$this->_connection = @ssh2_connect( $this->_server, $this->_port);

		if ( $this->_connection === false )
		{
			throw new Exception( 'Could not connect via SSH2: ' . $this->_server );
		}

		if ( isset($this->_user) && isset($this->_pass) && strlen($this->_user) != 0 && strlen($this->_pass) != 0 )
		{
			$this->auth_password();
		}

		$this->startSFTP();

		if ( isset($this->_fingerprint) && strlen($this->_fingerprint) != 0 )
		{
			$this->verify();
		}
	}
	//------------------------------------------------------------------------
	public function auth_password()
	{
		$this->_auth= @ssh2_auth_password($this->_connection, $this->_user, $this->_pass );

		if ( $this->_auth === false )
		{
			throw new Exception( 'Could not authenticate: ' . $this->_user . '@' . $this->_server );
		}
	}
	//------------------------------------------------------------------------
	public function startSFTP()
	{
		$this->_sftp = ssh2_sftp($this->_connection);

		if ( $this->_sftp === false )
		{
			throw new Exception( 'Could not start SFTP: ' . $this->_server );
		}
	}
	//------------------------------------------------------------------------
	public function getFileNames( $dir )
	{
		$files = array();

		$h = opendir( "ssh2.sftp://" . $this->_sftp . "/" . $dir );

		// List all the files
		while ( $file = readdir( $h ) )
		{
			if( is_dir( $file ) === false )
			{
				$files[$file] =  '/' . $dir . '/' . $file;
			}
		}

		closedir( $h );

		return $files;
	}
	//------------------------------------------------------------------------
	public function disconnect()
	{
	}
	//------------------------------------------------------------------------
	public function verify()
	{
		$remoteFingerPrint = @ssh2_fingerprint($this->_connection);

		if ( $remoteFingerPrint != $this->_fingerprint )
		{
			throw new Exception( "Fingerprint mismatch in verify: " . $remoteFingerPrint . ' : ' . $this->_fingerprint );
		}
	}
	//------------------------------------------------------------------------
	public function getFile( $remoteFile, $localFile )
	{
		$data = file_get_contents( "ssh2.sftp://" . $this->_sftp . $remoteFile );

		if ( $data === false )
		{
			throw new Exception("Could not open remote file: " . $remoteFile );
		}

		file_put_contents ($localFile, $data );
	}
	//------------------------------------------------------------------------
	public function deleteFile( $remoteFile )
	{
		unlink( "ssh2.sftp://" . $this->_sftp . $remoteFile );
	}

}

/*
// STAT info on a file

	$statinfo = ssh2_sftp_lstat($sftp, '/path/to/symlink');
	$filesize = $statinfo['size'];
	$group = $statinfo['gid'];
	$owner = $statinfo['uid'];
	$atime = $statinfo['atime'];
	$mtime = $statinfo['mtime'];
	$mode = $statinfo['mode'];

*/
