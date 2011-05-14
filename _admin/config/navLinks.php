<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
$config['nav_links'] = array(
	'Dashboard' => array(
		'Home' => URL( '' ),
	),

	'Content'	=> array( 
		'Pages'			=> URL( 'content/pages' ),
		'Categories'	=> URL( 'content/categories' )
		),
	'Users'		=> URL( 'users' ),
	'Settings'	=> URL( 'settings' )

	);