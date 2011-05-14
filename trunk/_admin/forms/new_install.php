<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2010-2011 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

class newInstallForm extends tgsfForm
{
	protected function _setup()
	{
		$this->id( 'new_install_form' );
		$this->useTemplate( 'top' );
		
		$this->_( fftHidden )
			->name( 'new' );

		$this->_( fftText )
			->caption( 'Username' )
			->name( 'user_login_username'	);

		$this->_( fftPassword )
			->caption( 'Password' )
			->name( 'user_login_password'	);

		$this->_( fftPassword )
			->caption( 'Password (again)' )
			->name( '_user_login_password'	);

		$this->_( fftSubmit )
			->caption( 'Begin Installation'	)
			->name( 'go' );
	}
	//------------------------------------------------------------------------
	protected function _setupValidate( &$v )
	{
		$v->_( 'user_login_username' )->required();
		$v->_( 'user_login_password' )->required();
		$v->_( '_user_login_password' )->required()->match_field( 'user_login_password', 'Password' );
	}
}

return new newInstallForm();