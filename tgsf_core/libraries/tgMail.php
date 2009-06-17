<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
* Cleans out possible email header injection attacks.  Spammers try to inject new line characters as delimiters
* so they can put in their own headers in an email message.  We're simply removing them.  these characters
* should not exist in email addresses or in email subjects.  don't use this on message bodies.
* @param String The text to clean (something like an email address that was typed in)
*/
function clean_for_email( $inbound )
{
    return str_replace( array( "\n", "\r" ), "", $inbound );
}