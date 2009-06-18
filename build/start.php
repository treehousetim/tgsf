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
$out .= '$major = ' . $major . ";\n";
$out .= '$minor = ' . $minor . ";\n";
$out .= '$nano = ' . $nano . ";\n\n";
$out .= '$build = ' . $build . ";\n\n";
$out .= '$versionString = "{$major}.{$minor}.{$nano}.{$build}";';

file_put_contents( $versionFile, $out );
include $versionFile;

file_put_contents( 'version.sh', "set version='{$versionString}'" );

chmod( 'build.sh', 0755 );
chmod( 'version.sh', 0755 );

$coreFolder = 'tgsf-core-' . $versionString;
$fullFolder = 'tgsf-' . $versionString;

runRsync( $coreFolder, 'rs_exclude_core.txt' );
createZip( $coreFolder, $coreFolder );
remove_dir( $coreFolder );

runRsync( $fullFolder, 'rs_exclude_full.txt' );
createZip( $fullFolder, $fullFolder );
remove_dir( $fullFolder );

echo 'Uploading to Google Code';

uploadToGoogleCode( 'ZIP - Core Files - Use for Upgrading', 'tgsf', $coreFolder . '.zip', $gcUser, $gcPass );
uploadToGoogleCode( 'GZIP - Core Files - Use for Upgrading', 'tgsf', $coreFolder . '.tar.gz', $gcUser, $gcPass );

uploadToGoogleCode( 'ZIP - Full Framework', 'tgsf', $fullFolder . '.zip', $gcUser, $gcPass );
uploadToGoogleCode( 'GZIP - Full Framework', 'tgsf', $fullFolder . '.tar.gz', $gcUser, $gcPass );


//------------------------------------------------------------------------
// utility functions below
//------------------------------------------------------------------------
function uploadToGoogleCode( $summary, $project, $file, $user, $pass )
{
	$cmd = './gc_upload.py --summary="' . $summary . '"' . " --project=tgsf --user={$user} --password={$pass} \"{$file}\"";
	system( $cmd );
}
//------------------------------------------------------------------------
function runRsync( $dest, $exclude )
{
	$exclude= "--exclude-from=" . $exclude;
	$pg = "--no-p --no-g";
	$rsync_options = "-Pav";
	$rsync_local_path = "../";
	$rsync_local_dest = "./" . $dest;

	$cmd = "rsync $rsync_options  $exclude $c $pg $rsync_local_path $rsync_local_dest";
	system( $cmd );	
}
//------------------------------------------------------------------------
function createZip( $zipName, $folderToZip )
{
	unlink( $zipName . '.zip' );
	unlink( $zipName . '.tar.gz' );
	system( "zip -r9 {$zipName}.zip {$folderToZip}" );
	system( "tar -pvczf {$zipName}.tar.gz {$folderToZip}" );
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
