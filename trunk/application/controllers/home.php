<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// see the config/config.php for the site name that is referenced in views/header.php
$windowTitle = 'Welcome';


include view( 'header' );
?>
<body style="margin: 1em;">
<h2 style="font-size: 2em;">Welcome to tgsf v<?=TGSF_VERSION; ?></h2>
<p>Congratulations!  If you don't see any error messages then you have successfully installed tgsf.</p>
<br>
<p>We have worked hard to make tgsf a framework that is useful, robust and easy to use and understand.</p>
<p>Our goal is for you to work with tgsf the same way as you're already working with PHP only with a few time-saving features.</p>
<p>You can visit our <a href="http://code.google.com/p/tgsf/">Google Code</a> project page</a> for more information</p>
<br>
<p>There are some simple <?= URL( 'examples' )->anchorTag( 'examples.' ); ?></p>
<br>
<p>You should not put your content directly in controllers like we've done with our sample home page.  Please use views instead of putting HTML output directly in your controller.  You'll be happier, we'll be happier, and everyone else will be happier and will give you a hearty thump on the back and congratulate you.</p>
<br>
<p>You really should view the document in tgsf_core/legal/bundled.txt</p>


<?php

include view( 'footer' );
