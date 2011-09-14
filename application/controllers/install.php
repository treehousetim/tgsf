<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

load_library( 'tgsfVersion', IS_CORE );

$blocks[] = load_install_file( 'all', IS_CORE );
$blocks[] = load_install_file( 'all' );


$windowTitle = 'tgsf Installer';
include view( 'header' );
include view( 'install/index' );
include view( 'footer' );