<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
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
	protected function _standardAtr( &$field )
	{
		$atr = array();
		$atr['name'] = $field->name;
		$atr['id'] = $field->name;
		$atr['value'] = $field->value;
		return $atr;
	}
	//------------------------------------------------------------------------
	protected function _caption( &$field )
	{
		$labelAtr['for'] = $field->name;
		return "\n" . html_tag( 'dt', '', html_tag( 'label', $labelAtr, $field->caption ) ) . "\n";
	}
	//------------------------------------------------------------------------
	public function dropdown( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return $this->_caption( $field ) .
		html_tag( 'dd', '',
		html_form_dropdown( $atr, $field->list, $field->selected ) );
	}
	//------------------------------------------------------------------------
	public function file( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return $this->_caption( $field ) .
		html_tag( 'dd', '',
		html_form_file( $atr ) );
	}
	//------------------------------------------------------------------------
	public function text( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return $this->_caption( $field ) .
		html_tag( 'dd', '',
		html_form_text( $atr ) );
	}
	//------------------------------------------------------------------------
	public function textArea( &$field )
	{
		$atr = $this->_standardAtr( $field );
		$text = '';
		if ( isset( $atr['value'] ) )
		{
			$text = $atr['value'];
			unset( $atr['value'] );
		}
		
		return $this->_caption( $field ) .
		html_tag( 'dd', '',
		html_form_textarea( $atr, $text ) );
	}
	//------------------------------------------------------------------------
	public function radio( &$field )
	{
		$atr = $this->_standardAtr( $field );
		$out = $this->_caption( $field );
		
		foreach ( $field->optionList as $value => $caption )
		{
			$atr['value'] = $value;
			$id = md5( 'rg' . $field->name . $value );
			$atr['id'] = $id;
			$out .= html_tag( 'dd', '', html_form_radio( $atr ) . ' ' . html_tag( 'label', array('for'=>$id), $caption ) );
		}
		return html_tag( 'dd', '', $out );
	}
	//------------------------------------------------------------------------
	public function checkbox( &$field )
	{
		$atr = $this->_standardAtr( $field );
		$atr['value'] = '1';
		$value = $field->value;
		
		if ( $value == 0 || $value == '' )
		{
			// nothing
		}
		else
		{
			$atr['checked'] = 'checked';
		}
		
		return $this->_caption( $field ) .
		html_tag( 'dd', '',
		html_form_checkbox( $atr ) );	
	}
	//------------------------------------------------------------------------
	public function image( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return 'n/i';
	}
	//------------------------------------------------------------------------
	public function button( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return "\n" . html_tag( 'dt', '', html_form_button( $atr, $field->caption ) ) . "\n<dd></dd>";
	}
	//------------------------------------------------------------------------
	public function submit( &$field )
	{
		$atr = $this->_standardAtr( $field );
		$atr['value'] = $field->caption;
		return "\n" . html_tag( 'dt', '', html_form_submit( $atr ) ) . "\n<dd></dd>";
	}
	//------------------------------------------------------------------------
	public function reset( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return 'n/i';
	}
	//------------------------------------------------------------------------
	public function password( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return $this->_caption( $field ) .
		html_tag( 'dd', '',
		html_form_text( $atr ) );
	}
	//------------------------------------------------------------------------
	public function other( &$field )
	{
		$atr = $this->_standardAtr( $field );
		return 'n/i';
	}
}

return new formTop();