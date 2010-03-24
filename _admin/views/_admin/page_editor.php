<?php defined( 'BASEPATH' ) or die( 'Restricted' ); 
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

$windowTitle = 'Page Editor';
include view( 'header' );
?>
<h2>Page Editor</h2>
<?= BREADCRUMB( 'Page Editor' )->render(); ?>

<?php if ( $formValid == false ) : ?>
	<p class="ui-alert ui-alert-error">There were problems with the information you submitted.  Please review the form below.</p>
<?php endif; ?>

<?= $formHtml; ?>
<?= $js; ?>

<? include view( 'footer' ); ?>