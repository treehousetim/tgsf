<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class rteForm extends tgsfForm
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	protected function _setup()
	{
		$this->id( 'page' );
		
		$this->useTemplate( 'top' );

		// page_slug
		$staticPage = new staticPage;
		$list = array ( '' => '--' );
		$list = array_merge( $list, $staticPage->model->fetchForDropdown() );
		
		$this->_( fftDropDown )
		     ->caption( 'Choose Existing Page or... ' )
		     ->name( 'page_slug' )
		     ->optionList( $list );
		
		$this->_( fftText )
			->caption( 'Type in a new page slug (partial URL)' )
			->name( '_page_slug' );

		// page_title
		$this->_( fftText )
		     ->caption( 'Title' )
		     ->name( 'page_title' )
		     ->setFieldAttribute( 'style', 'width: 50%;' );
		
		$this->_( fftCheck )
			->caption( 'Published' )
			->name( 'page_published' );
		
		// window title
		$this->_( fftText )
		     ->caption( 'Window Title' )
		     ->name( 'page_window_title' )
		     ->setFieldAttribute( 'style', 'width: 50%;' );
		
		// meta description
		$this->_( fftText )
		     ->caption( 'Description (SEO)' )
		     ->name( 'page_meta_description' )
		     ->setFieldAttribute( 'style', 'width: 50%;' );
		
		// page_content
		$this->_( fftTextArea )
		     ->caption( 'Content' )
		     ->name( 'page_content' )
		     ->setFieldAttribute( 'style', 'width: 97%; height: 20em;' );
		
		// page_template
		$this->_( fftText )
		     ->caption( 'Template' )
		     ->name( 'page_template' )
		     ->setFieldAttribute( 'style', 'width: 50%;' );
		
		$this->startGroup( '_buttons' );

		$this->_( fftSubmit )->caption( 'Save' )->name( 'go' );
	}

	//------------------------------------------------------------------------

	protected function _setupValidate( &$v )
	{
		$v->_( 'page_slug'     )->required()->max_len( 255 );
		$v->_( '_page_slug'    )->required()->max_len( 255 );
		$v->_( 'page_template' )->max_len( 255 );
		$v->_( 'page_title'    )->required()->max_len( 255 );
		$v->_( 'page_window_title' )->max_len( 255 );
		$v->_( 'page_meta_description' )->max_len( 255 );
		$v->_( 'page_content'  )->required();
	}
	//------------------------------------------------------------------------
	public function validateForm( &$ds, &$v )
	{
		$this->forceValid(
			$v->page_slug->valid ||
			$v->_page_slug->valid 
			);
	}
}

return new rteForm();