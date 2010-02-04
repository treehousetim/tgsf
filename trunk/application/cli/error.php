<?php defined( 'BASEPATH' ) or die( 'Restricted' );
echo PHP_EOL;
echo 'An error has occurred:' . PHP_EOL . PHP_EOL;

echo 'Error Message: ' . $message . PHP_EOL;

if ( $exception instanceof Exception )
{
	echo $exception->getMessage() . PHP_EOL;
	echo 'File: ' . $exception->getFile() . PHP_EOL;
	echo 'Line: ' . $exception->getLine() . PHP_EOL;
	echo $exception->getTraceAsString() . PHP_EOL;
}
