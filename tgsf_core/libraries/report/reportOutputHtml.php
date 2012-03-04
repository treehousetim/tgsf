<?php defined( 'BASEPATH' ) or die( 'Restricted' );
/*
-----------------------------------------------------------------------------
| This file is copyright 2012 by TMLA INC ALL RIGHTS RESERVED.
|----------------------------------------------------------------------------
| A report output handler for browsers (html tables)
|----------------------------------------------------------------------------
| Date			| Person		| Change Description
|----------------------------------------------------------------------------
| 2012-02-07	| TGallagher	| Created
-----------------------------------------------------------------------------
*/

class reportOutputHTML extends tgsfReportOutput
{
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function __construct( $filename = null )
	{
		$this->type = rotHTML;

		if ( $filename == null )
		{
			$filename = 'php://output';
		}

		$this->stream = new SplFileObject( $filename, 'w' );
	}
	//------------------------------------------------------------------------
	/**
	*
	*/
	public function line( tgsfDataSource $row, $header )
	{
		$tag = 'td';
		if ( $header )
		{
			$tag = 'th';
		}

		$report_columns			= $this->report->columns;
		$this->stream->fwrite( "\n\t\t\t\t\t<tr>" );

		foreach( $report_columns as $colType => $columns )
		{
			if ( $colType == ctALL || $colType == ctHTML || $colType == ctBROWSER )
			{
				foreach ( $columns as $column )
				{
					$this->stream->fwrite( "\n\t\t\t\t\t\t" );
					$this->stream->fwrite( '<' . $tag . $column->getHtmlTagExtras() . ">" );
					$this->stream->fwrite( $column->render( $row, $header, $this->type ) );
					$this->stream->fwrite( '</' . $tag .'>' );
				}
			}
		}
		$this->stream->fwrite( "\n\t\t\t\t\t</tr>" );
	}
	//------------------------------------------------------------------------
	public function header( $formDs )
	{
		ob_start();

		$reportSettings		= $this->report->settings;
		$htmlSettings		= $this->report->htmlSettings;
		$columns			= $this->report->columns;
		?>
		<div id="report-<?= $htmlSettings->id; ?>" class="report_container <?= $htmlSettings->id; ?>-container">
			<table style="width: 100%" class="report_container" >
				<thead class="report_header">
					<tr>
						<th colspan="2">
							<p class="report_app_name"><?= config( 'reportAppName' ); ?></p>
							<p class="report_title"><?= $this->report->title; ?></p>
						</th>
					</tr>
					<?php
						if ( is_array( $reportSettings ) )
						{
							echo "\t\t\t<tr>\n";

							if ( count( $reportSettings ) % 2 != 0 )
							{
								$reportSettings[] = '&nbsp;';
							}

							for ( $ix = 0; $ix < count( $details ); $ix=$ix+2 )
							{
								echo "\t\t\t\t";
								?><th width="50%" class="report-details"><?= $reportSettings[$ix]; ?></th><?
								echo "\n\t\t\t\t";
								?><th width="50%" class="report-details"><?= $reportSettings[$ix+1]; ?></th><?
							}

							echo "\t\t\t</tr>\n";
						}
					?>
				</thead>
				<tbody>
				<tr>
					<td colspan="2">
					<table class="report-grid grid" id="<?= $htmlSettings->id; ?>">
						<thead>

							<? $this->line( dsFactory::ds(), true ); // output header ?>

						</thead>
						<tbody>

		<?php
		$this->stream->fwrite( ob_get_clean() );
				
	}
	//------------------------------------------------------------------------
	public function footer( $formDs )
	{
		ob_start();
		?>
						</tbody>
					</table>
					</td>
				</tr>
			</tbody>
			</table>
		</div>
		<?php
		$this->stream->fwrite( ob_get_clean() );
	}
}