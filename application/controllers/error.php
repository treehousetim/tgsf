<?php defined( 'BASEPATH' ) or die( 'Restricted' );

$windowTitle = 'An error has occured';

//------------------------------------------------------------------------

$errorMessage = '<p>' . $message .'</p>';
$errorMessage = '<div class="ui-alert ui-alert-error">' . $errorMessage . '</div>';

//------------------------------------------------------------------------

$exceptionMessage = '';

if ( $exception !== null && config( 'debug_mode' ) === true )
{
	$exceptionMessage .= '<p>' . $exception->getMessage() . '</p>';
	$trace = str_replace( BASEPATH, '', $exception->getTraceAsString() );
	$exceptionMessage .= '<textarea cols="90" rows = "10" wrap="off">' . $trace . '</textarea>';
	$exceptionMessage = '<div class="ui-alert ui-alert-info ui-exception">' . $exceptionMessage . '</div>';
}

//------------------------------------------------------------------------

$isError = true;
include view( 'template/header' );
include view( 'error' );
include view( 'template/footer' );
