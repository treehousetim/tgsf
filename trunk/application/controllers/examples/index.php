<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// see the config/config.php for the site name that is referenced in views/header.php
$windowTitle = 'Examples';


include view( 'header' );
?>
<body style="margin: 1em;">
<h2 style="font-size: 2em;">tgsf v<?=TGSF_VERSION; ?> - Examples</h2>
<?php
$menu['query'] = URL( 'examples/query' );
$menu['urls'] = URL( 'test/test-url' );
echo urlMenu( $menu );

include view( 'footer' );
