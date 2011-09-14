<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class logSeverityForm extends tgsfForm
{
	public function __construct()
	{
		parent::__construct();
		$this->useTemplate( 'top', IS_CORE );
	}
	//------------------------------------------------------------------------
	protected function _setup()
	{
		$this->id( 'logSeverityForm' );
		
		//------------------------------------------------------------------------
		// HIDDEN
		//------------------------------------------------------------------------

		// log_id
		$this->_( fftHidden	)->name( 'log_id' );

		//------------------------------------------------------------------------
		// Fields
		//------------------------------------------------------------------------
		$this->_( fftDropDown )
		     ->caption( 'Severity' )
		     ->name( 'log_severity' )
		     ->optionList(
				array(
						'unreviewed'		=> 'Unreviewed',
						'ignore'			=> 'Ignore',
						'minor'				=> 'Minor',
						'big'				=> 'Big',
						'critical'			=> 'Critical',
						'immediate action'	=> 'Immediate action'
					)
				);

		//------------------------------------------------------------------------
		// BUTTONS
		//------------------------------------------------------------------------
		$this->_( fftSubmit )->caption( 'Update Severity' )->name( 'go' );
	}
	//------------------------------------------------------------------------
	protected function _setupValidate( &$v )
	{
		$v->_( 'log_id' )->required()->gt( 0 );
	}
}

return new logSeverityForm();