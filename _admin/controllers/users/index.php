<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//AUTH()->minRole( roleADMIN );

$c

$metaDescription = 'User Management';
$windowTitle = 'Manage Users';

//------------------------------------------------------------------------

include view( 'header' );
?>
<h2 style="font-size: 2em;">Welcome to tgsf v<?=TGSF_VERSION; ?></h2>
<p>This is your admin dashboard.</p>

<p>You really should view the document in tgsf_core/legal/bundled.txt</p>


<?php include view( 'footer' );
