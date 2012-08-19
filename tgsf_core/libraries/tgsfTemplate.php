<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


function js( $jsFiles, $group = null )
{
	$group = APP_URL_FOLDER . $group;
	$jsFiles = (array)$jsFiles;
	$group = str_replace( '/', '_', $group );
	$groupFiles = array();

	foreach ( $jsFiles as $jsFile )
	{
		if ( ! is_local( $jsFile ) )
		{
			echo "\t" . '<script type="text/javascript" src="' . $jsfile . '"></script>' . "\n";
		}
		else
		{
			if ( ! file_exists( $jsFile )  )
			{
				throw new tgsfException( 'File Does Not Exist when trying to create a script tag: ' . $jsFile );
			}

			if ( config( 'debug_mode' ) === true )
			{
				echo "\t" . '<script type="text/javascript" src="' . basepathToUrl( $jsFile ) . '?' . time() . '"></script>' . PHP_EOL;
			}
			else
			{
				$groupFiles[] = "'{$jsFile}'";
			}
		}
	}

	if ( count( $groupFiles ) > 0 )
	{
		if ( is_null( $group ) )
		{
			$group = md5( implode( '', $groupFiles ) );
		}

		$group = 'js_' . $group;

		$contents = implode( ",\n", $groupFiles );
		$content = "<?php return array( '$group' => array( $contents ) );";

		file_put_contents( path( 'assets/minify_groups', IS_CORE_PATH ) . $group . PHP, $content );

		echo "\t" . '<script type="text/javascript" src="' . $url = URL( '_minify' )->setVar( 'g', $group ) . '"></script>' . PHP_EOL;

	}
}
//------------------------------------------------------------------------
function css( $file, $local = true )
{
	$prefix = '';
	$suffix = '';
	if ( $local )
	{
		$prefix = config( 'css_url' );
		$suffix = '.css';
	}

	$tag = new tgsfHtmlTag( 'link' );
	$tag->type = 'text/css';
	$tag->href = $prefix . $file . $suffix;
	$tag->rel = 'Stylesheet';

	echo $tag;
}
//------------------------------------------------------------------------
/**
* Outputs one or more style tags with an @import rule
* This function also integrates with the bundled minify to create groups of minified CSS
* in which case it outputs a single style tag with an @import rule pointing to /tgsf_core/3rd_party/min/?g=example
* @param Mixed Either a string or an array of css files to include.
* If the name does not start with http:// or https:// then it is considered local and will be put through minify
* @param The name of the minify group - files will not be minified unless the group name is provided.
* and is
*/
function css_import( $cssFiles, $group = null )
{
	echo PHP_EOL;

	$group = APP_URL_FOLDER . $group;
	$cssFiles = (array)$cssFiles;
	$group = str_replace( '/', '_', $group );
	$groupFiles = array();

	foreach ( $cssFiles as $cssFile )
	{
		if ( ! is_local( $cssFile ) )
		{
			$tag = new tgsfHtmlTag( 'style' );
			$tag->type = 'text/css';
			$tag->content( '@import url(' . $cssFile . ');' );
			echo $tag;
			unset( $tag );
		}
		else
		{
			if ( ! file_exists( $cssFile )  )
			{
				throw new tgsfException( 'File Does Not Exist when trying to create an imported CSS tag: ' . $cssFile );
			}

			if ( config( 'debug_mode' ) === true )
			{
				$tag = new tgsfHtmlTag( 'style' );
				$tag->type = 'text/css';
				$tag->content( '@import url(' . basepathToUrl( $cssFile ) . '?' . time() . ');' );
				echo "\t" . $tag . PHP_EOL;
			}
			else
			{
				$groupFiles[] = "'{$cssFile}'";
			}
		}
	}

	if ( count( $groupFiles ) > 0 )
	{
		if ( is_null( $group ) )
		{
			$group = md5( implode( '', $groupFiles ) );
		}

		$contents = implode( ",\n", $groupFiles );
		$content = "<?php return array( '$group' => array( $contents ) );";

		file_put_contents( path( 'assets/minify_groups', IS_CORE_PATH ) . $group . PHP, $content );
		$tag = new tgsfHtmlTag( 'style' );
		$tag->type = 'text/css';
		$tag->content( '@import url(' . ltrim( URL( '_minify' )->setVar( 'g', $group ), '/' ) . ');' );
		echo $tag . PHP_EOL;
	}
}
//------------------------------------------------------------------------
function css_import_ie( $file, $if = 'if IE' )
{
	static $ie = 1;
	echo '<!--[' . $if . ']>';
	css_import( $file, 'ie-' . $ie );
	echo '<![endif]-->';
	$ie++;
}
//------------------------------------------------------------------------
function css_import_ie_x( $file, $version = '6', $local = true )
{
	echo "<!--[if IE $version]>";
	css_import( $file, $local );
	echo '<![endif]-->';
}
//------------------------------------------------------------------------
function output_css_properties( $array )
{
	foreach ( $array as $prop => $value )
	{
		echo $prop . ': ' . $value . '; ';
	}
}
//------------------------------------------------------------------------
function js_output_url_func()
{
	$config['trailingSlash'] = (defined( 'tgTrailingSlash' ) && tgTrailingSlash === true )?'/':'';
	$config['base'] = current_base_url();

	$config['get_string']		= config( 'get_string', '/_/' );
	$config['get_separator']	= config( 'get_separator', '/' );
	$config['get_equals']		= config( 'get_equals', '/' );

	$content = "\n" . 'tgsf.URL.setConfig(' . json_encode( $config ) . ');';

	echo tgsfHtmlTag::factory( 'script' )
		->setAttribute( 'type', 'text/javascript' )
		->content( $content );
}
//------------------------------------------------------------------------
/**
* This loads the error controller and then exits script execution.
* @var String The error message to display.
*/
function show_error( $message, $exception = null)
{
	if ( function_exists( 'LOGGER' ) )
	{
		if ( $exception instanceof Exception )
		{
			LOGGER()->exception( $exception, $message );
		}
		else
		{
			LOGGER()->app( $message );
		}
	}
	else
	{
		if ( $exception === null )
		{
			log_error( $message );
		}
		else
		{
			log_exception( $exception );
		}
	}
	global $page;

    if ( TGSF_CLI )
    {
        require_once cli_controller( 'error' );
    }
    else
    {
		// for debug mode uncomment as any startup errors produce new errors.
		//
		// echo 'error:' . $message . ', ' . $exception;

        require_once controller( 'error' );
    }
	exit();
}
//------------------------------------------------------------------------
// HTML Generation functions
//------------------------------------------------------------------------
function favicon( $url, $type = 'image/x-icon' )
{
	$tag = tgsfHtmlTag::factory( 'link' )
		->setAttribute( 'rel', 'icon' )
		->setAttribute( 'href', $url )
		->setAttribute( 'type', $type );

	echo $tag;
	$tag->setAttribute( 'rel', 'shortcut icon' );

	echo $tag;
}
//------------------------------------------------------------------------
function html_inline_style( $content )
{
	if ( $content == '' )
	{
		return;
	}
	echo tgsfHtmlTag::factory( 'style' )
		->setAttribute( 'type', 'text/css' )
		->content( $content );
}
//------------------------------------------------------------------------
function html_title( $title )
{
 	echo tgsfHtmlTag::factory( 'title' )
		->content( $title );
}
//------------------------------------------------------------------------
function meta_description( $text )
{
	echo tgsfHtmlTag::factory( 'meta' )
		->setAttribute( 'content', $text )
		->setAttribute( 'name', 'description' );
}
//------------------------------------------------------------------------
function content_type( $type )
{
	echo tgsfHtmlTag::factory( 'meta' )
		->setAttribute( 'content', $type )
		->setAttribute( 'http-equiv', 'Content-Type' );
}
//------------------------------------------------------------------------
function brNotEmpty( $var )
{
	echo $var;
	if ( ! empty( $var ) )
	{
		echo '<br>';
	}
}
//------------------------------------------------------------------------
function getArrayFirstLastCssClass( $current, $count, $middleClass = '', $firstClass = 'first', $finalClass = 'last' )
{
	if ( $current == $count - 1 )
	{
		return $finalClass;
	}

	if ( $current == 0 )
	{
		return $firstClass;
	}

	return $middleClass;
}
//------------------------------------------------------------------------
/**
* Call with a set of arguments and this function will return them based on a modulus of the quantity of invocations
* in other words, each time you call this function it will return the next item in the list of parameters.
* to reset, call with no arguments, or with different arguments
*/
function alternate()
{
	static $current = 0;
	static $items = array();

	$args = func_get_args();
	$argCnt = count($args);

	if ( ! ( $items === $args ) || $argCnt == 0 )
	{
		$current = 0;
		$items = $args;
	}
	else
	{
		$current++;
	}

	return $argCnt>0?$args[$current % $argCnt]:'';
}
//------------------------------------------------------------------------
/**
* Alternate with a namespace
* The first argument is a namespace to enable multiple calls in a single loop
* Call with a set of arguments and this function will return them based on a modulus of the quantity of invocations
* in other words, each time you call this function it will return the next item in the list of parameters.
* to reset, call with a namespace and different arguments
*/
function alternateNs()
{
	$args = func_get_args();
	$ns = array_shift( $args );
	$argCnt = count($args);

	if ( empty( $ns ) )
	{
		throw new tgsfException( 'alternateNs called without a namespace' );
	}

	static $cache = array();

	if ( empty( $cache[$ns] ) )
	{
		$cache[$ns]['current'] = 0;
		$cache[$ns]['items'] = array();
	}

	$items =& $cache[$ns]['items'];
	$current =& $cache[$ns]['current'];

	if ( ! ( $items === $args ) || $argCnt == 0 )
	{
		$current = 0;
		$items = $args;
	}
	else
	{
		$current++;
	}

	return $argCnt>0?$args[$current % $argCnt]:'';
}
//------------------------------------------------------------------------
/**
* This creates a menu using an unordered list with links in each list item.
* <ul class="url_menu">
*	<li><a href... > </li>
* </ul>
*
* It returns the ul as a tgsfHtmlTag so you can further add visual changes or content changes.
* You pass it an array of URL objects:
* $menu['Click Here'] = URL( 'you_are_a_winner/view' );
* @param Array The array of url objects
*/
function urlMenu( $array, $subMenuCaptionElement = 'h2', $forceCurrent = '' )
{
	$ul = new tgsfHtmlTag( 'ul' );
	$ul->css_class( 'url_menu' );
	if ( is_array( $array ) )
	{
		$cnt = count( $array );
		$icnt = 0;

		foreach ( $array as $caption => $link )
		{
			$icnt++;
			if ( $icnt == 1 )
			{
				$class = 'first';
			}
			elseif ( $icnt == $cnt )
			{
				$class = 'last';
			}
			else
			{
				$class = 'middle';
			}

			if ( is_array( $link ) )
			{
				$ul->addTag( 'li' )
					->addTag( $subMenuCaptionElement )->content( $caption )->parent
					->addTag( urlMenu( $link, $subMenuCaptionElement, $forceCurrent ) );
			}
			else
			{
				if ( ( $link instanceOf tgsfUrl && $forceCurrent == $link->url ) || $link == $forceCurrent )
				{
					$class .= ' current';
				}
				
				if ( $link instanceOf tgsfUrl )
				{
					$ul->_( 'li' )->content( $link->anchorTag( $caption )->cssClass( $class ) );
				}
				else
				{
					$ul->_( 'li' )->content( $link )->cssClass( $class );
				}
			}
		}
	}

	return $ul;
}
//------------------------------------------------------------------------
function tgsfJqAjaxInputTimeout( $input, $message, $turl, $delay = 500 )
{
	if ( ! $turl instanceof tgsfUrl )
	{
		$url = URL( $turl );
	}
	else
	{
		$url = $turl;
	}

	// jquery code taken from: http://jqueryfordesigners.com/using-ajax-to-validate-forms/
	ob_start();
	?>
	<script type="text/javascript">

	$(document).ready(
	function ()
	{
		var messageElement = $('<?= $message ?>');

		$('<?= $input ?>').keyup(
		function ()
		{
			var t = this;
			if ( this.value != this.lastValue )
			{
				if ( this.timer )
				{
					clearTimeout(this.timer);
				}

				messageElement.removeClass( 'error' ).html('<img src="<?= image_url( 'ajax-loader.gif', IMAGE_URL_RELATIVE, IS_CORE ) ?>"> checking...');
                $( t ).removeAttr('halt');

				this.timer = setTimeout(function ()
				{
					$.ajax({
						url: '<?= $url ?>',
						data: 'action=tgsfAjaxInputTimeout&fieldValue=' + t.value,
						dataType: 'json',
						type: 'post',
						success:
							function ( j )
							{
								if ( j.error == true )
								{
									messageElement.addClass( 'error' );
                                    $( t ).attr( 'halt', 'yes' );
								}
								messageElement.html( j.msg );
							}
					});
				}, <?= $delay ?> );

				this.lastValue = this.value;
			}
		});
	});
	</script>
	<?php

	return ob_get_clean();
}
//------------------------------------------------------------------------
/**
*
*/
function debugMarker( $text )
{
	if ( config( 'debug_mode' ) || TGSF_CLI == true )
	{
		if ( TGSF_CLI )
		{
			echo $text;
		}
		else
		{
			echo '<p>' . $text . '</p>';
		}
	}
	else
	{
		//echo '<!--' . $text . '-->';
	}
}
//------------------------------------------------------------------------
/**
* Takes underline and dash separated words and puts a space between
*/
function toSpaces( $text, $fromChars = array( '_', '-' ) )
{
	return str_replace( $fromChars, ' ', $text );
}
//------------------------------------------------------------------------
/**
* Uppercase to spaces
*/
function underscore_to_spaces( $text, $fromChars = array( '_', '-' ) )
{
	return ucwords( toSpaces( $text, $fromChars ) );
}
//------------------------------------------------------------------------
function imageTag( $url )
{
	return tgsfHtmlTag::factory( 'img' )->setAttribute( 'src', image_url( $url ) );
}
