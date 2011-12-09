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

$release++;

$out = "<?php\n";
$out .= '$major = ' . $major . ";" . PHP_EOL;
$out .= '$minor = ' . $minor . ";" . PHP_EOL;
$out .= '$build = ' . $build . ";" . PHP_EOL;
$out .= '$release = ' . $release . ";" . PHP_EOL . PHP_EOL;

$out .= '$versionString = "{$major}.{$minor}.{$build}-r{$release}";' . PHP_EOL;
$out .= "define( 'TGSF_VERSION', \$versionString );" . PHP_EOL;
$out .= "define( 'TGSF_VERSION_INT', \$major . \$minor . \$build . \$release );" . PHP_EOL;

file_put_contents( $versionFile, $out );
include $versionFile;

$coreFolder = 'tgsf-core-' . $versionString;
$fullFolder = 'tgsf-' . $versionString;

runRsync( $coreFolder, 'rs_exclude_core.txt' );
createZip( $coreFolder, $coreFolder );

runRsync( $fullFolder, 'rs_exclude_full.txt' );
createZip( $fullFolder, $fullFolder );


//echo "\n" . 'Uploading to Google Code';
//uploadToGoogleCode( 'ZIP - Core Files - Use for Upgrading', 'tgsf', $coreFolder . '.zip', $gcUser, $gcPass, 'Featured' );
//uploadToGoogleCode( 'ZIP - Full Framework', 'tgsf', $fullFolder . '.zip', $gcUser, $gcPass, 'Featured' );


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
	$c - '';
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
