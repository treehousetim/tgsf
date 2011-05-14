<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

//------------------------------------------------------------------------
/**
* base abstract report class
*/
abstract class tgsfReport extends tgsfGrid
{
	protected	$_ro_reportTitle	= '';
	protected	$_ro_reportDate		= '';
	protected	$_ro_reportAppName	= '';
	protected	$_ro_reportSubTitle = '';
	protected	$_ro_reportDetails	= '';

	protected	$_params			= array();
	//------------------------------------------------------------------------
	protected function _sort() {} // empty implementation for reports
	abstract protected function _paramSetup();
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct()
	{
		if ( defined( 'REPORT_APP_NAME' ) )
		{
			$this->_ro_reportAppName = REPORT_APP_NAME;
		}

		$this->_ro_reportDate = gmdate( DT_FORMAT_SQL );
		$this->css_class( 'report-grid' );
		parent::__construct();
		$this->_paramSetup();
	}
	//------------------------------------------------------------------------
	/**
	* Available to the _setup method, addParam sets the report up to accept parameters
	*/
	protected function addParam( $name, $defaultValue = null )
	{
		$this->_params = array_merge( array( $name => $defaultValue ), $this->_params );
	}
	//------------------------------------------------------------------------
	/**
	* Sets a parameter on a report
	* @param String The name of the param to set
	* @param Mixed The value to set the param to
	*/
	public function &setParam( $name, $value )
	{
		if ( ! array_key_exists( $name, $this->_params ) )
		{
			throw new tgsfException( 'setParam - No Report Param by that name (' . $name . ') exists for this report: ' . get_class( $this ) );
		}

		$this->_params[$name] = $value;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Returns a named report parameter
	* @param String The param name - must have been set up using addParam
	*/
	public function &getParam( $name )
	{
		if ( ! array_key_exists( $name, $this->_params ) )
		{
			throw new tgsfException( 'getParam - No Report Param by that name (' . $name . ') exists for this report: ' . get_class( $this ) );
		}
		return $this->_params[$name];
	}
	//------------------------------------------------------------------------
	/**
	* Sets the report title
	*/
	public function &reportTitle( $title )
	{
		$this->_ro_reportTitle = $title;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the report sub title
	*/
	public function &reportSubTitle( $title )
	{
		$this->_ro_reportSubTitle = $title;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the report details
	*/
	public function &reportDetails( $details )
	{
		$this->_ro_reportDetails = $details;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the report date
	*/
	public function &reportDate( $date )
	{
		$this->_ro_reportDate = $date;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	* Sets the report App Name
	*/
	public function &reportAppName( $name )
	{
		$this->_ro_reportAppName = $name;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function renderAsGrid()
	{
		return parent::render( grtHTML_TABLE );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function render( $renderType = grtHTML_TABLE, $csvIncludeHeader = false )
	{
		if ( empty( $this->_colDefs ) )
		{
			$this->_setup();
		}

		if ( $this->hasAttribute( 'id' ) === false )
		{
			throw new tgsfException( 'Reports must have an ID set.  Use $this->id( \'report-id\' ); in your setup method.' );
		}

		if ( $renderType == grtHTML_TABLE )
		{
			$div = new tgsfHtmlTag( 'div' );

			$div
				->id( 'report-' . clean_text( strtolower( $this->attributes['id'] ), '-' ) )
				->css_class( 'report_container' );

			$table = $div->addTag( 'table' );
			$table->style = 'width: 100%';

			$table
			->css_class( 'report_container' )
			->addTag( 'thead' )
				->css_class( 'report_header' )
				->addTag( 'tr' )
					->addTag( 'th' )
						->setAttribute( 'colspan', 2 )
						->addTag( 'p' )
							->css_class( 'report_app_name' )
							->content( $this->_ro_reportAppName )
							->parent
						->addTag( 'p' )
							->css_class( 'report_title' )
							->content( $this->_ro_reportTitle )
							->parent
						->parent
					->parent

				->addTag( 'tr' )
					->addTag( 'th' )
						->setAttribute( 'width', '50%' )
						->content( $this->_ro_reportDetails )
						->css_class( 'report-details' )
						->parent

					->addTag( 'th')
						->setAttribute( 'width', '50%' )
						->content( $this->_ro_reportDate )
						->css_class( 'report-date' )
						->parent
					->parent
				->parent

			->addTag( 'tbody' )
				->addTag( 'tr' )
					->addTag( 'td' )
						->setAttribute( 'colspan', 2 )
						->content( parent::render( $renderType ) );

			return $div->render();
		}
		else
		{
			return parent::render( $renderType, $csvIncludeHeader );
		}
	}
}
