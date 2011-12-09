<?php defined( 'BASEPATH' ) or die( 'Restricted' );

/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// see the config/config.php for the site name that is referenced in views/header.php
$metaDescription = 'A Fresh Install of tgsf';
$windowTitle = 'Welcome';

//------------------------------------------------------------------------

include view( 'template/header' );
include view( 'index' );
include view( 'template/footer' );
