<?php

// emulate tgsf cli object for command line options.
// duplicated here to keep this file stand alone
$CLI = parseArgv();

// this file uses the information found in the /tgsf_core/config/version.php file

exec( '../publish/prepare.sh' );

$versionFile = '../tgsf_core/config/version.php';

include $versionFile;

echo PHP_EOL;
echo 'tgsf v.' . $versionString . ' Deployment Tool.' . PHP_EOL;

$target = new SplFileInfo( $CLI->target );

if ( ( $target->isReadable() && $target->isDir() && ( $target->getFilename() != '.' && $target->getFilename() != '..' ) ) == false )
{
	echo PHP_EOL;
	die( 'Target does not exist.' . PHP_EOL );
}

$directory = new DirectoryIterator( $CLI->target );

if ( iterator_count( $directory ) != 2 )
{
	if ( empty( $CLI->overwrite ) || $CLI->overwrite != true )
	{
		echo PHP_EOL;
		die( 'Target is not empty' . PHP_EOL );
	}
}

runRsync( $CLI->target );

echo 'Deployed tgsf v.' . $versionString . ' to ' . $CLI->target . PHP_EOL;
echo PHP_EOL;
echo 'Done.';
//------------------------------------------------------------------------
// utility functions below
//------------------------------------------------------------------------
function runRsync( $dest )
{
	GLOBAL $CLI;

	if ( empty( $CLI->upgrade ) || $CLI->upgrade != true )
	{
		$exclude = 'rs_exclude_full.txt';
		echo 'Full deployment of all components.' . PHP_EOL;
	}
	else
	{
		$exclude = 'rs_exclude_core.txt';
		echo 'Upgrade of core components only.' . PHP_EOL;
	}

	$c = '';
	$exclude= "--exclude-from=" . $exclude;
	$pg = "--no-p --no-g";
	$rsync_options = "-Pa --delete";
	$rsync_local_path = "../";
	$rsync_local_dest = $dest;

	$cmd = "rsync $rsync_options  $exclude $c $pg $rsync_local_path $rsync_local_dest";
	exec( $cmd );	
}
//------------------------------------------------------------------------
function parseArgv()
{
	$out = array( 'upload' => 0, 'increment' => 0 );
	global $argv;
	$tmpArgv = $argv;

	// remove script name
	array_shift( $tmpArgv );

	$unnamed = array();

	foreach( $tmpArgv as $arg )
	{
		if ( substr( $arg, 0, 2 ) == '--' )
		{
			$eqPos = strpos( $arg, '=' );

			if ( $eqPos === false )
			{
				$key   = substr( $arg, 2 );
				
				$value = 1;
				if ( array_key_exists( $key, $out ) )
				{
					$value = $out[$key];
				}

				$out[$key] = $value;
			}
			else
			{
				$key = substr( $arg, 2, $eqPos-2 );
				$out[$key] = substr( $arg, $eqPos + 1 );
			}
		}
		elseif ( substr( $arg, 0, 1 ) == '-' )
		{
			if (substr( $arg, 2, 1 ) == '=' )
			{
				$key = substr( $arg, 1, 1 );
				$out[$key] = substr( $arg, 3 );
			}
			else
			{
				$chars = str_split( substr( $arg, 1 ) );
				foreach( $chars as $char )
				{
					$key = $char;
					$value = 1;
					if ( array_key_exists( $key, $out ) )
					{
						$value = $out[$key];
					}

					$out[$key] = $value;
				}
			}
		}
	}
	return (object)$out;
}