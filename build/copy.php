<?php

// this file uses the information found in the /tgsf_core/config/version.php file
//  creates 2 zip files:
// 1 containing the core files for upgrading, etc
// 1 containing all files

// both files have the subversion folders stripped out.
// the exclusions are managed in the rs_exclude files
// we're using rsync for creating the temporary folder of files to zip up

$versionFile = '../tgsf_core/config/version.php';

include $versionFile;

$coreFolder = 'dev-tgsf-core-' . $versionString;
$fullFolder = 'dev-tgsf-' . $versionString;

runRsync( $coreFolder, 'rs_exclude_core.txt' );
createZip( $coreFolder, $coreFolder );

runRsync( $fullFolder, 'rs_exclude_full.txt' );
createZip( $fullFolder, $fullFolder );


//------------------------------------------------------------------------
// utility functions below
//------------------------------------------------------------------------
function runRsync( $dest, $exclude )
{
	$c = '';
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
