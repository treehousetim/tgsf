<?php

// emulate tgsf cli object for command line options.
// duplicated here to keep this file stand alone
$CLI = parseArgv();

// this file uses the information found in the /tgsf_core/config/version.php file
// to automatically increment the build number
// then it creates 2 zip files:
// 1 containing the core files for upgrading, etc
// 1 containing all files

// both files have the subversion folders stripped out.
// the exclusions are managed in the rs_exclude files
// we're using rsync for creating the temporary folder of files to zip up

// you need a developer.php file that contains 2 variables in it:
// $gcUser and $gcPass

include './developer.php';
exec( '../publish/prepare.sh' );

$versionFile = '../tgsf_core/config/version.php';

include $versionFile;

if ( $CLI->increment )
{
	$build++;
}

$out = "<?php\n";
$out .= '$major = ' . $major . ";" . PHP_EOL;
$out .= '$minor = ' . $minor . ";" . PHP_EOL;
$out .= '$build = ' . $build . ";" . PHP_EOL . PHP_EOL;

$out .= '$versionString = "{$major}.{$minor}.{$build}";' . PHP_EOL;
$out .= "define( 'TGSF_VERSION', \$versionString );" . PHP_EOL;
$out .= "define( 'TGSF_VERSION_INT', \$major . \$minor . \$build );" . PHP_EOL;

file_put_contents( $versionFile, $out );
$versionString = "{$major}.{$minor}.{$build}";

echo 'Building tgsf version: ' . $versionString . PHP_EOL;

$coreFolder = 'tgsf-core-' . $versionString;
$fullFolder = 'tgsf-' . $versionString;

runRsync( $coreFolder, 'rs_exclude_core.txt' );
runRsync( $fullFolder, 'rs_exclude_full.txt' );


if ( $CLI->upload )
{
	createZip( $coreFolder, $coreFolder );
	createZip( $fullFolder, $fullFolder );

	echo "\n" . 'Uploading to Google Code';
	uploadToGoogleCode( 'ZIP - Core Files - Use for Upgrading', 'tgsf', $coreFolder . '.zip', $gcUser, $gcPass, 'Featured' );
	uploadToGoogleCode( 'ZIP - Full Framework', 'tgsf', $fullFolder . '.zip', $gcUser, $gcPass, 'Featured' );
}

//------------------------------------------------------------------------
// utility functions below
//------------------------------------------------------------------------
function uploadToGoogleCode( $summary, $project, $file, $user, $pass, $labels = '' )
{
	if ( $labels != '' )
	{
		$labels = '--labels="' . $labels . '" ';
	}

	echo "\n";

	$cmd = './gc_upload.py --summary="' . $summary . '"' . " --project=tgsf --user={$user} --password={$pass} {$labels }\"{$file}\"";
	$ecmd = './gc_upload.py --summary="' . $summary . '"' . " --project=tgsf --user={$user} --password=xxx {$labels }\"{$file}\"";
	echo $ecmd . "\n";
	system( $cmd );
}
//------------------------------------------------------------------------
function runRsync( $dest, $exclude )
{
	$c = '';
	echo "Running RSync\n";
	$exclude= "--exclude-from=" . $exclude;
	$pg = "--no-p --no-g";
	$rsync_options = "-Pa --delete";
	$rsync_local_path = "../";
	$rsync_local_dest = "./" . $dest;

	$cmd = "rsync $rsync_options  $exclude $c $pg $rsync_local_path $rsync_local_dest";
	exec( $cmd );	
}
//------------------------------------------------------------------------
function createZip( $zipName, $folderToZip )
{
	echo "Creating Zip and Tar\n";
	@unlink( $zipName . '.zip' );
	//@unlink( $zipName . '.tar.gz' );
	system( "zip -r9 -q {$zipName}.zip {$folderToZip}" );
	//system( "tar -pczf {$zipName}.tar.gz {$folderToZip}" );
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