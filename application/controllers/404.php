<?php defined( 'BASEPATH' ) or die( 'Restricted' );

header( 'HTTP/1.0 404 Not Found' );

$windowTitle = 'An Error has occured.';
$title = $windowTitle;

include view( 'header' );

?>
<body>
<h2>404 - Page Not Found</h2>
<p>We were unable to find the page you requested.</p>
<br><br>
<?
include view( 'footer' );