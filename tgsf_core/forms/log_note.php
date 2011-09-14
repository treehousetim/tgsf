<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class logNoteForm extends tgsfForm
{
	public function __construct()
	{
		parent::__construct();
		$this->useTemplate( 'top', IS_CORE );
	}
	//------------------------------------------------------------------------
	protected function _setup()
	{
		$this->id( 'logNoteForm' );
		$this->autocomplete( FORM_AUTOCOMPLETE_OFF );
		
		$this->_( fftHidden )
			->name( 'log_note_log_id' );

		$this->_( fftTextArea )->caption( 'Add a note' )
			->name( 'log_note_content' );

		//------------------------------------------------------------------------
		// BUTTONS
		//------------------------------------------------------------------------
		$this->_( fftSubmit )
			->caption( 'Save Note' )
			->name( 'go' );
	}
	//------------------------------------------------------------------------
	protected function _setupValidate( &$v )
	{
		$v->_( 'log_note_content'  )->required();
		$v->_( 'log_note_log_id' )->required()->gt( 0 );
	}
}

return new logNoteForm();