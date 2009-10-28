<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/


function js( $jsFiles, $group = null )
{
	$loopFiles = array();
	$groupFiles = array();
	$files = array();

	arrayify( $jsFiles, $loopFiles );

	foreach ( $loopFiles as $jsFile )
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
			$groupFiles[] = "'{$jsFile}'";
		}
	}

	if ( count( $groupFiles ) > 0 )
	{
		if ( is_null( $group ) )
		{
			$group = md5( implode( '', $groupFiles ) );
		}

		$contents = implode( ",\n", $groupFiles );
		$content = "<?php return array( 'js_$group' => array( $contents ) );";

		file_put_contents( path( 'assets/minify_groups', IS_CORE_PATH ) . 'js_' . $group . PHP, $content );
		echo "\t" . '<script type="text/javascript" src="' .url_path( '3rd_party/min', IS_CORE_PATH ) . '?g=js_' . $group . '"></script>' . "\n";

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
	echo "\t" . '<link type="text/css" href="' . $prefix . $file . $suffix . '" rel="Stylesheet" />	' . "\n";
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
	$group = str_replace( '/', '_', $group );
	$loopFiles = array();
	$groupFiles = array();
	$files = array();

	arrayify( $cssFiles, $loopFiles );

	foreach ( $loopFiles as $cssFile )
	{
		if ( ! is_local( $cssFile ) )
		{
			$atr = array();
			$atr['type'] = 'text/css';
			$content = '@import url(' . $cssFile . ');';
			echo html_tag( 'style', $atr, $content );

			echo "\t" . '<style type="text/css">@import url(' . $cssFile . ');</style>' . "\n";
		}
		else
		{
			if ( ! file_exists( $cssFile )  )
			{
				throw new tgsfException( 'File Does Not Exist when trying to create an imported CSS tag: ' . $cssFile );
			}
			$groupFiles[] = "'{$cssFile}'";
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

		$atr = array();
		$atr['type'] = 'text/css';
		$content = '@import url(' . url_path( '3rd_party/min', IS_CORE_PATH ) . '?g=' . $group . ');';
		echo html_tag( 'style', $atr, $content ) . "\n";
	}
}
//------------------------------------------------------------------------
function css_import_ie( $file, $if = 'if IE' )
{
	echo '<!--[' . $if . ']>';
	css_import( $file );
	echo '<![endif]-->';
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
	$atr = array();
	$atr['type']	= 'text/javascript';

	$content  = 'function url( url )';
	$content .= '{ url=url.trim();';

	if ( defined( 'tgTrailingSlash' ) && tgTrailingSlash === true )
	{
		$content .= "url=url+'/';";
	}

	$content .= "if(url=='/'){url=''};";
	$content .= "return '" . current_base_url() . "' + url;";
	$content .= '}';
	echo html_tag( 'script', $atr, $content ) . "\n";
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
        require_once controller( 'error' );
    }
	exit();
}
//------------------------------------------------------------------------
// HTML Generation functions
//------------------------------------------------------------------------
function favicon( $url, $type = 'image/jpeg' )
{
	$atr = array();
	$atr['rel']		= 'icon';
	$atr['href']	= $url;
	$atr['type']	= $type;
	echo html_tag( 'link', $atr );
}
//------------------------------------------------------------------------
function html_inline_style( $content )
{
	if ( $content == '' )
	{
		return;
	}

	$atr = array();
	$atr['type']	= 'text/css';
	echo html_tag( 'style', $atr, $content );
}
//------------------------------------------------------------------------
function html_title( $title )
{
	$atr = array();
	echo html_tag( 'title', $atr, $title );
}
//------------------------------------------------------------------------
function content_type( $type )
{
	$atr = array();
	$atr['content']		= $type;
	$atr['http-equiv']	= 'Content-Type';
	echo html_tag( 'meta', $atr );
}
//------------------------------------------------------------------------
function html_attributes( $attributes )
{
	$out = (string)$attributes;
	if ( is_array( $attributes ) )
	{
		$out = count($attributes)?' ':'';

		foreach( $attributes as $atr => $value )
		{
			$out .= " $atr=\"$value\"";
		}
	}

	return $out;
}
//------------------------------------------------------------------------
function html_tag( $tag, $attributes = '', $content = null )
{
	$atr = html_attributes( $attributes );

	$out = "<{$tag}{$atr}";

	if ( ! is_null( $content ) )
	{
		$out .= ">{$content}</{$tag}>";
	}
	else
	{
		$out .= '>';
	}

	return $out;
}
//------------------------------------------------------------------------
function html_form_options( $options, $selecteds )
{
	if ( ! is_array( $options ) )
	{
		throw new tgsfHtmlException( 'Options for form fields must be in array format.' );
	}

	arrayify( $selecteds, $selected );

	$out = '';
	$atr = array();

	foreach ( $options as $optVal => $caption )
	{
		/*
		if ( is_int( $optVal ) )
		{
			$optVal = $caption;
		}
		*/

		$atr['value'] = $optVal;
		if ( in_array( $optVal, $selected ) )
		{
			$atr['selected'] = 'selected';
		}
		else
		{
			unset( $atr['selected'] );
		}
		$out .= html_tag( 'option', $atr, $caption );
	}
	return $out;
}
//------------------------------------------------------------------------
function html_form_dropdown( $attributes, $options, $selectedValues = null )
{
	$out = '';
	$optionHtml = html_form_options( $options, $selectedValues );
	$out = html_tag( 'select', $attributes, $optionHtml );
	return $out;
}
//------------------------------------------------------------------------
function html_form_listbox( $attributes, $options, $selectedValues = null )
{
	return html_form_dropdown( $attributes, $options, $selectedValues );
}
//------------------------------------------------------------------------
function html_form_text( $attributes )
{
	$attributes['type'] = 'text';

	return html_tag( 'input', $attributes );
}
//------------------------------------------------------------------------
function html_form_textarea( $attributes, $text )
{
	return html_tag( 'textarea', $attributes, $text );
}
//------------------------------------------------------------------------
function html_form_checkbox( $attributes, $checked = false )
{
	if ( $checked )
	{
		$attributes['checked'] = 'CHECKED';
	}
	$attributes['type'] = 'checkbox';
	return html_tag( 'input', $attributes );
}
//------------------------------------------------------------------------
function html_form_radio( $attributes, $selected = false )
{
	if ( $selected )
	{
		$attributes['checked'] = 'CHECKED';
	}

	$attributes['type'] = 'radio';

	return html_tag( 'input', $attributes );
}
//------------------------------------------------------------------------
function html_form_hidden( $name, $value )
{
	$attributes['type'] = 'hidden';
	$attributes['name'] = $name;
	$attributes['value'] = $value;

	return html_tag( 'input', $attributes );
}
//------------------------------------------------------------------------
function html_form_file( $attributes )
{
	$attributes['type'] = 'file';

	return html_tag( 'input', $attributes );

}
//------------------------------------------------------------------------
function html_form_submit( $attributes )
{
	$attributes['type'] = 'submit';

	return html_tag( 'input', $attributes );
}
//------------------------------------------------------------------------
function html_form_reset( $attributes )
{
	$attributes['type'] = 'reset';

	return html_tag( 'input', $attributes );
}
//------------------------------------------------------------------------
function html_form_button( $attributes, $content = null )
{
	$attributes['type'] = 'button';

	return html_tag( 'button', $attributes, $content );
}
//------------------------------------------------------------------------
function html_form_password( $attributes )
{
	if ( isset( $attributes['value'] ) )
	{
		unset( $attributes['value'] );
	}

	$attributes['type'] = 'password';

	return html_tag( 'input', $attributes );
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
	static $cnt = 0;
	static $items = array();

	$args = func_get_args();
	$argCnt = count($args);

	if ( ! ( $items === $args ) || $argCnt == 0 )
	{
		$current = 0;
		$cnt = $argCnt;
		$items = $args;
	}
	else
	{
		$current++;
	}

	return $argCnt>0?$args[$current % $argCnt]:'';
}