<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// this will go away soon - ignore this file.

$pageFile = config( 'page_path' ) . $page . EXT;

if ( file_exists( $pageFile ) )
{
    ob_start();
    include $pageFile;

	if ( isset( $noTypography ) && $noTypography == true )
	{
		$out = ob_get_contents();
	}
	else
	{
		$out = ob_get_contents();
		//$out = auto_typography( trim( ob_get_contents() ) );
	}

    ob_end_clean();
    echo $out;

    $content = ob_get_contents();
    ob_end_clean();
    include view( 'header' );
	echo $content;
    include view( 'footer' );
}
else
{
	echo '404 - ' . $pageFile;
}