<?php

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

$versionFile = '../tgsf_core/config/version.php';

include $versionFile;

$build++;

$out = "<?php\n";
$out .= '$major = ' . $major . ";" . PHP_EOL;
$out .= '$minor = ' . $minor . ";" . PHP_EOL;
$out .= '$nano = ' . $nano . ";" . PHP_EOL . PHP_EOL;
$out .= '$build = ' . $build . ";" . PHP_EOL . PHP_EOL;
$out .= '$versionString = "{$major}.{$minor}.{$nano}.{$build}";' . PHP_EOL;
$out .= "define( 'TGSF_VERSION', \$versionString );" . PHP_EOL;

file_put_contents( $versionFile, $out );
include $versionFile;

$coreFolder = 'tgsf-core-' . $versionString;
$fullFolder = 'tgsf-' . $versionString;

runRsync( $coreFolder, 'rs_exclude_core.txt' );
createZip( $coreFolder, $coreFolder );
//remove_dir( $coreFolder );

runRsync( $fullFolder, 'rs_exclude_full.txt' );
createZip( $fullFolder, $fullFolder );
//remove_dir( $fullFolder );

echo "\n" . 'Uploading to Google Code';

uploadToGoogleCode( 'ZIP - Core Files - Use for Upgrading', 'tgsf', $coreFolder . '.zip', $gcUser, $gcPass, 'Featured' );
//uploadToGoogleCode( 'GZIP - Core Files - Use for Upgrading', 'tgsf', $coreFolder . '.tar.gz', $gcUser, $gcPass, 'Featured' );

uploadToGoogleCode( 'ZIP - Full Framework', 'tgsf', $fullFolder . '.zip', $gcUser, $gcPass, 'Featured' );
//uploadToGoogleCode( 'GZIP - Full Framework', 'tgsf', $fullFolder . '.tar.gz', $gcUser, $gcPass, 'Featured' );


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
	echo "Running RSync\n";
	$exclude= "--exclude-from=" . $exclude;
	$pg = "--no-p --no-g";
	$rsync_options = "-Pa";
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
// this function comes from the PHP manual notes
// from this url: http://php.net/manual/en/function.rmdir.php
// it is included here based on the fact that manual notes
// become the property of the PHP Documentation Group
// and that the documentation is covered under the following license
// Creative Commons Attribution 3.0 License
// this note serves as the attribution needed for inclusion
// 3RD PARTY LICENSE: Creative Commons Attribution 3.0 License
function remove_dir( $path )
{
	// this function is toxic since it has great power.
	// i'm not using it right now and instead manually removing the folders after running this process.
	return;
	if ( ! file_exists( $path ) )
	{
		return;
	}
	
	$f1 = glob( $path . "/*" );
	$f2 = glob( $path . "/.*" );
	$files = array_merge( $f1, $f2 );
	
	foreach( $files as $file )
	{
		echo $file . "\n\n";
		if ( basename( $file ) == '.' || basename( $file ) == '..' )
		{
			continue;
		}
		
		if ( is_dir( $file ) )
		{
			echo $file . "\n\n";
			remove_dir( $file );
		}
		else
		{
			unlink( $file );
		}
	}
	rmdir( $path );
}
