<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
This code is copyright 2009-2010 by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

// you are responsible for writing your own controller that is used to edit content
// consult the google code web site for examples if you need help.

add_action( 'static_page_init', 'static_page_setup' );
function static_page_setup( $file )
{
	$class = new staticPage();
	add_action( 'pre_404', array( &$class, 'pre404' ) );
}


class staticPage
{
	public $model;

	function __construct()
	{
		$this->model = load_cloned_object( path( 'plugins/static_page', IS_CORE ), 'model' );
	}
	//------------------------------------------------------------------------
	function pre404( $slug )
	{
		$row = $this->model->fetch( $slug );
		
		if ( $row === false )
		{
			// we have no page for this url, return and let the core handle the 404
			return;
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
					$.getJSON( URL( '<?= $ajaxController . config( 'get_string' ); ?>page<?= config( 'get_equals' ); ?>' + $(this).val() ), function(json)
					{
						if ( !json.error )
						{
							$("input#page_template"   ).val( json.page_template );
							$("input#page_title"      ).val( json.page_title    );
							$("textarea#page_content" ).val( json.page_content  );
							rte.page_content.set_content( json.page_content );
						}
					});
				}
				else
				{
					$("input#_page_slug").removeAttr( 'disabled' );
					$("input#page_template"   ).val( "" );
					$("input#page_title"      ).val( "" );
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

