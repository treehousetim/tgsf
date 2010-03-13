<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$windowTitle = $row->page_window_title;
$metaDescription = $row->page_meta_description;

include view( 'header' );
?>
<h1><?= $row->page_title; ?></h1>

<div id="page-content">
	<?= $row->page_content; ?>
</div>

<? include view( 'footer' ); ?>
