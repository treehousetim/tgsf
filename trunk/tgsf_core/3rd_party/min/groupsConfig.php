<?php
$file = realpath( dirname( __FILE__ ) . '/../../../' ) . '/tgsf_core_assets/minify_groups/'. $_GET['g'] . '.php';

if ( file_exists( $file ) )
{
	return require_once( $file );
}
else
{
    header("Status: 403" );
	echo 'Forbidden';
    exit();
}
