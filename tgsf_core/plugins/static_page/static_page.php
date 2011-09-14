<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// you must configure the plugin - example below:
/*
tgsfPlugin::loaderFactory()
	->file( plugin( 'static_page/static_page', IS_CORE ) )
	->name( 'static_page' )
	->register();

tgsfEventFactory::handler()
	->event( 'static_page_init' )
	->func( 'config_static_page' )
	->attach();

function config_static_page( $name )
{
	global $config;
	$config['static_page_minRole'] = roleADMIN;
	$config['static_page_view'] = 'page_editor';
}
*/

tgsfEventFactory::actionHandler()
	->event( 'static_page_init' )
	->func( 'static_page_setup' )
	->attach();
//------------------------------------------------------------------------
function static_page_setup( $file )
{
	$class = new staticPage();
	tgsfEventFactory::actionHandler()
		->event( 'pre_404' )
		->func( 'pre_404' )
		->object( $class )
		->attach();
}
//------------------------------------------------------------------------
function staticPageGetSlug( $slug )
{
	$staticPage = new staticPage;
	return $staticPage->model->fetch( $slug );
}
//------------------------------------------------------------------------
class staticPage extends tgsfBase
{
	public $model;

	function __construct()
	{
		$this->model = load_cloned_object( path( 'plugins/static_page', IS_CORE ), 'model' );
	}
	//------------------------------------------------------------------------
	function adminAjax()
	{
		$minRole = config( 'static_page_minRole' );

		if ( $minRole !== false )
		{
			if ( ! AUTH()->hasRole( $minRole ) )
			{
				// we display a 404 with an empty string to avoid stack recursion - display_404 calls the 404 action which we've hooked to get here.
				display_404( '' );
			}
		}

		if ( GET()->dataPresent && !GET()->isEmpty( 'page' ) )
		{
			$page = $this->model->fetch( GET()->_( 'page' ) );
			echo json_encode( $page );
			exit();
		}
	}
	//------------------------------------------------------------------------
	function pre_404( $event )
	{
		$slug = $event->page;

		if ( $slug == 'ajax/admin/rte' )
		{
			$this->adminAjax();
			return;
		}

		if ( $slug == 'admin/rte' )
		{
			$this->adminController();
			exit();
		}

		$row = $this->model->fetch( $slug );

		if ( $row === false || $row->page_published == false )
		{
			// we have no page for this url, return and let the core handle the 404
			return;
		}

		if ( empty( $row->page_window_title ) )
		{
			$row->page_window_title = $row->page_title;
		}

		if ( $row->page_template == '' || file_exists( view( $row->page_template ) ) === false )
		{
			include view( 'static_page' );
			exit();
		}
		else
		{
			include view( $row->page_template );
			exit();
		}
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function adminController( )
	{
		$minRole = config( 'static_page_minRole' );

		if ( $minRole === false || ! AUTH()->hasRole( $minRole ) )
		{
			// we display a 404 with an empty string to avoid stack recursion - display_404 calls the 404 action which we've hooked to get here.
			display_404( '' );
		}

		$form = load_cloned_object( path( 'plugins/static_page', IS_CORE ), 'form' );
		AUTH()->minRole( roleADMIN );

		//------------------------------------------------------------------------

		$form->processor( URL( 'admin/rte' ) );

		if ( POST()->dataPresent )
		{
			$ds = clone POST();
			$ds->setVar( 'page_published', $ds->exists( 'page_published' ) );

			$form->ds( $ds );

			if ( $form->validate() )
			{
				if ( $ds->isEmpty( 'page_slug' ) === false )
				{
					$this->model->update( $ds );

					URL( 'admin/rte' )->setVar( 'msg', 'page_saved' )->redirect();
				}
				elseif ( $ds->isEmpty( '_page_slug' ) === false )
				{
					$ds->remap( array( '_page_slug' => 'page_slug' ) );
					$this->model->insert( $ds );

					URL( 'admin/rte' )->setVar( 'msg', 'page_saved' )->redirect();
				}
			}
		}

		$formHtml  = $form->render();
		$formValid = $form->valid;

		$view = config( 'static_page_view' );
		ob_start();
		$this->outputJS( 'ajax/admin/rte' );
		$js = ob_get_clean();

		if ( $view !== false )
		{
			include view( $view );
		}

	}
	//------------------------------------------------------------------------

	public function outputJS( $ajaxController )
	{
		?>
		<style type="text/css">
		@import url( '<?=URL( relative_path( 'assets/js/jquery/rte', IS_CORE_PATH ) )?>jquery.rte.css' );
		</style>

		<script type="text/javascript" src="<?=URL( relative_path( 'assets/js/jquery/rte', IS_CORE_PATH ) )?>jquery.rte.js"></script>
		<script type="text/javascript" src="<?=URL( relative_path( 'assets/js/jquery/rte', IS_CORE_PATH ) )?>jquery.rte.tb.js"></script>
		<script type="text/javascript" src="<?=URL( relative_path( 'assets/js/jquery/rte', IS_CORE_PATH ) )?>jquery.ocupload-1.1.4.js"></script>
		<script type="text/javascript">

		$(document).ready( function()
		{
			var rte = $("textarea#page_content").rte(
			{
				controls_rte:  rte_toolbar,
				controls_html: html_toolbar
			});

			$("select#page_slug").change( function()
			{
				if ( $(this).val() )
				{
					$("input#_page_slug").attr( 'disabled', 'disabled' );
					$.getJSON( String( tgsf.URL( "<?= $ajaxController . config( 'get_string' ); ?>page<?= config( 'get_equals' ); ?>" + $(this).val() ) ), function(json)
					{
						if ( !json.error )
						{
							for ( prop in json )
							{
								$( "#" + prop ).val( json[prop] );
							}
							$( "#_page_slug" ).val( json.page_slug );
							$( "#page_published" ).attr( 'checked', json.page_published == 1 )
							rte.page_content.set_content( json.page_content );
						}
					});
				}
				else
				{
					$( "#page_published" ).attr( 'checked', false )
					$("input#_page_slug").removeAttr( 'disabled' );
					$("form#page input"   ).not( ".submit" ).val( "" );
					$("textarea#page_content" ).val( "" );
					rte.page_content.set_content( '' );
				}

			});

			$( "input#_page_slug" ).change( function()
			{
				$("select#page_slug" ).attr( 'disabled', 'disabled' );
			});

		});
		</script>
		<?php
	}
}

