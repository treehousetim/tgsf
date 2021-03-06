<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/**
* a template class
*/
class formTop extends tgsfFormTemplate
{
	//------------------------------------------------------------------------
	protected function _wrapField( &$field, &$container )
	{
		$tag = $container->_( 'dd' );
		$tag->css_class( $field->name . '_field' );
		$tag->addTag( $field->tag );

		if ( $field->desc != '' )
		{
			$tag->addTag( 'p' )->content( $field->desc )->id( $field->name . '_desc' );
		}
	}
	//------------------------------------------------------------------------
	protected function _caption( &$field, &$container )
	{
		$caption = $container->_( 'dt' );
		$caption->css_class( $field->name . '_caption' );
		$caption->css_class( $field->type );
		$caption->addTag( $field->label );
	}
	//------------------------------------------------------------------------
	public function fieldContainer( &$form, $group = '' )
	{
		$cls = trim( strtolower( clean_text( $group ) ), ' _' );

		$fs = $form->addTag( 'fieldset' )->css_class( $cls );

		if ( ! starts_with( $group, '_' ) )
		{
			$fs->addTag( 'legend' )->content( $group );
		}

		$dl = $fs->_( 'dl' )->css_class( $cls );

		return $dl;
	}
	//------------------------------------------------------------------------
	public function statictext( &$field, &$container )
	{
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function dropdown( &$field, &$container )
	{
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function file( &$field, &$container )
	{
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function text( &$field, &$container )
	{
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function password( &$field, &$container )
	{
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function textArea( &$field, &$container )
	{
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function checkbox( &$field, &$container )
	{
		// this is to make it so that checkboxes always exist in the POST datasource
		$hidden = $container->_( 'input' );
		$hidden->setAttribute( 'type', 'hidden' );
		$hidden->value( '0' );
		$hidden->setAttribute( 'name', $field->name );
		$hidden->setAttribute( 'id', $field->name . '_default' );
		
		$this->_caption( $field, $container );
		$this->_wrapField( $field, $container );
	}
	//------------------------------------------------------------------------
	public function radio( &$field, &$container )
	{
		throw new tgsfException( 'debug output from form radio groups.' );

		// $out = $this->_caption( $field );
		//
		// $list = new tgsfHtmlTag( 'dl' );
		//
		// foreach ( $field->optionList as $value => $caption )
		// {
		// 	$id = md5( 'rg' . $field->name . $value );
		// 	$container = new tgsfHtmlTag( 'dd' );
		// 	$radio = $container->addTag( $field->tag );
		// 	$radio->addAttribute( 'value', $value, SINGLE_ATTR_ONLY );
		// 	$radio->id( $id );
		//
		// html_form_radio has been removed from the framework.
		// 	$out .= html_tag( 'dd', '', html_form_radio( $atr ) . ' ' . html_tag( 'label', array('for'=>$id), $caption ) );
		// }
		//
		// return $this->_wrapField( $field, $out );
	}
	//------------------------------------------------------------------------
	public function image( &$field, &$container )
	{
		$fld_container = $container->_( 'dt' );
		$fld_container->content( '&nbsp;' );
		$fld_container->_( 'dd' )->addTag( $field->tag );
	}
	//------------------------------------------------------------------------
	public function button( &$field, &$container )
	{
		$fld_container = $container->_( 'dt' );
		$fld_container->content( '&nbsp;' );
		$fld_container->_( 'dd' )->addTag( $field->tag );
	}
	//------------------------------------------------------------------------
	public function submit( &$field, &$container )
	{
		$fld_container = $container->_( 'dt' );
		$fld_container->content( '&nbsp;' );
		$fld_container->css_class( $field->name . '_caption' );

		$dd = $fld_container->_( 'dd' );
		$dd->addTag( $field->tag );
		$dd->css_class( $field->name . '_field' );
	}
	//------------------------------------------------------------------------
	public function reset( &$field, &$container )
	{
		$fld_container = $container->_( 'dt' );
		$fld_container->content( '&nbsp;' );
		$fld_container->_( 'dd' )->addTag( $field->tag );
	}
	//------------------------------------------------------------------------
	public function other( &$field, &$container )
	{
		$container->_( NON_TAG_NODE )->content( $field->rawHTML );
	}
	//------------------------------------------------------------------------
	public function span( &$field, &$container )
	{
		$container->_( 'dd' )
			->css_class( $field->name . '_field' )
			->addTag( $field->tag );
	}
}

return new formTop();
