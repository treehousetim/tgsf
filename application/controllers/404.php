<?php defined( 'BASEPATH' ) or die( 'Restricted' );

header( 'HTTP/1.0 404 Not Found' );

$windowTitle = 'An Error has occured.';
$title = $windowTitle;
include view( 'template/header' );

?>

<h2>404 - Page Not Found</h2>
<p>We were unable to find the page you requested.</p>
<p>Please visit our <a href="<?= URL( '' ) ?>">Home Page</a> to start over.</p>
<br><br><br>

<?
include view( 'template/footer' );
