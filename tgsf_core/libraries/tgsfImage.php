<?php
/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/
//--------------------------------------------------------------------
//	Written 03/18/2010 by Wesley Cripe.

enum( 'img', array (
				'PNG' => 'png',
				'JPG' => 'jpg',
				'JPEG' => 'jpg',
				'GIF' => 'gif'
				)
	);

class tgsfImage extends tgsfPlugin
{
	protected $_data = array();
	protected $_background = '';
	protected $_chartType = 'line';
	protected $_xColumns = array();
	protected $_yColumns = array();
	protected $_drawAxis = true;
	protected $_size = array( 'x' => 0, 'y' => 0 );
	protected $_image = null;
	protected $_antialias = true;
	protected $_axisWidth = 2;
	protected $_numXPoints = 10;
	protected $_numYPoints = 5;
	protected $_font = 'Verdana';
	protected $_cache = false;
	protected $_cacheExpires = 600;
	protected $_points = array();

	//------------------------------------------------------------------------
	/**
	*	Determines if the GD library exists and imports a background image and data, if provided.
	*/
	public function __construct( &$data = null, $background = '' )
	{
		if ( !function_exists( 'gd_info' ) )
		{
			throw new tgsfException( 'Cannot initialize Imaging library: Your version of PHP does not support the GD image library.' );
		}
		$this->_font = asset_path( 'fonts', IS_CORE ).'verdana.ttf';
		
		if ( $data )
		{
			$this->_import( $data );
		}

		if ( $background && file_exists( $background ) )
		{
			$this->_background = $background;
		}
		else if ( $background )
		{
			throw new tgsfException( 'Invalid background image path provided.' );
		}
	}
	//------------------------------------------------------------------------
	/**
	*	Gets a new instance of the class
	*/
	public static function &factory()
	{
		$c = __CLASS__;
		$instance = new $c();
		return $instance;
	}
	//------------------------------------------------------------------------
	/**
	*	Renders the line chart image
	*/
	public function &render()
	{
		//	If cache is enabled and a valid cache file exists, use our image cache instead of regenerating the image
		$cachefile = dirname( __FILE__ ).'/cache/'.$this->_cache;
		if ( $this->_cache )
		{
			if ( file_exists( $cachefile ) )
			{
				if ( filemtime( $cachefile ) + $this->_cacheExpires >= time() )
				{
					$this->_image = imagecreatefrompng( $cachefile );
					return $this;
				}
			}
		}

		if ( $this->_data instanceof query )
		{
			$q = $this->_data;
			$this->_data = array();
			
			while ( $row = $q->fetch() )
			{
				$this->_data[] = (array)$row;
			}
		}

		//	Create a blank white image if we're not drawing on an already-existing one
		if ( !$this->_image )
		{
			$this->_image = imagecreatetruecolor( $this->_size['x'], $this->_size['y'] );
			$white = imagecolorallocate( $this->_image, 255, 255, 255 );
			imagefilledrectangle( $this->_image, 0, 0, $this->_size['x'], $this->_size['y'], $white );
		}
		else
		{
			$white = imagecolorallocate( $this->_image, 255, 255, 255 );
		}
		
		imageantialias( $this->_image, true );

		$black = imagecolorallocate( $this->_image, 0, 0, 0 );

		if ( $this->_drawAxis )
		{
			//	Draw X axis
			imagefilledrectangle( $this->_image, 100, $this->_size['y'] - 100, $this->_size['x'] - 50, $this->_size['y'] - 99 - $this->_axisWidth, $black );
			//	Draw Y axis
			imagefilledrectangle( $this->_image, 100, $this->_size['y'] - 100, 99 + $this->_axisWidth, 50, $black );
		}

		$columns = count( $this->_data );
		if ( $columns < $this->_numXPoints )
		{
			$this->_numXPoints = $columns;
		}

		
		$chartPixelWidth = $this->_size['x'] - 150;
		$chartPixelHeight = $this->_size['y'] - 155;
		$chartXStart = 99 + $this->_axisWidth;
		$chartYStart = $this->_size['y'] - 99 - $this->_axisWidth;
		$xInterval = ( $this->_size['x'] - 150 ) / $this->_numXPoints;
		$yInterval = ( $this->_size['y'] - 155 ) / $this->_numYPoints;

		//	Get max, min Y values
		foreach ( $this->_data as $d )
		{
			foreach ( $this->_yColumns as $y => $color )
			{
				if ( !isset( $maxY ) || !isset( $minY ) )
				{
					$maxY = $d[$y];
					$minY = $d[$y];
				}
				else
				{
					$maxY = max( $maxY, $d[$y] );
					$minY = min( $minY, $d[$y] );
				}
			}
		}
		
		
		$yScale = $maxY - $minY;
		
		//	If we have a scale smaller than maxY, the scale should be relative to maxY since the scale should go from 0 to maxY
		if ( $yScale > 0 )
		{
			$yScale = max( $maxY, $yScale );
		}
		else if ( $yScale < 0 )
		{
			throw new tgsfException( 'Minimum Y value greater than maximum Y value.' );
		}
		
		//	If the minY value is less than 0, start the scale there--otherwise, start at 0
		$yStart = min( 0, $minY );
		
		//	Draw y-axis markers
		for ( $iy = 0; $iy <= $this->_numYPoints; $iy++ )
		{
			$yCoord = $chartYStart - $iy * $yInterval;
			$yVal = $yStart + $iy * ( $yScale / $this->_numYPoints );
			$yVal = $this->dispatchFilter( 'core:image:y-axis', $yVal, array() );

			imageline( $this->_image, 95, $yCoord, 105, $yCoord, $black );
			$bounds = imagettfbbox( 8, 0, $this->_font, $yVal );
			imagettftext( $this->_image, 8, 0, 80 - $bounds[2] - $bounds[0], $yCoord - ( $bounds[5] + $bounds[1] ) / 2 - 1, $black, $this->_font, $yVal );
		}

		if ( $yInterval == 0 )
		{
			$yInterval = 1;
		}
		
		//	Set the number of data elements between x-axis markers
		$xEls = floor( $columns / $this->_numXPoints );
				
		if ( $xEls == 0 )
		{
			$xEls = 1;
		}

		$ix = 0;
		$curColMarker = 0;
		$count = 0;

		//	Draw x-axis markers
		foreach ( $this->_data as $d )
		{
			if ( $ix >= $curColMarker - 1 )
			{
				$xCoord = $chartXStart + $count * $xInterval;
				imageline( $this->_image, $xCoord, $this->_size['y'] - 95, $xCoord, $this->_size['y'] - 105, $black );
				$bounds = imagettfbbox( 8, 90, $this->_font, $d[$this->_xColumns[0]] );
				$xVal = $d[$this->_xColumns[0]];
				$xVal = $this->dispatchFilter( 'core:image:x-axis', $xVal, array() );
				imagettftext( $this->_image, 8, 90, $xCoord - ( $bounds[4] - $bounds[2] ) / 2 + 1, $this->_size['y'] - 80 - ( $bounds[3] - $bounds[1] ), $black, $this->_font, $xVal );
				$curColMarker += $xEls;
				$count++;
			}

			$ix++;
		}
		
		//	Draw data lines
		$dataPts = count( $this->_data );
		$xInterval = ( $this->_size['x'] - 150 ) / $dataPts;

		//	Draws each data line, one line at a time
		foreach ( $this->_yColumns as $y => $color )
		{
			$ix = 0;
			//	Allocate the line color
			$c = imagecolorallocate( $this->_image, $color[0], $color[1], $color[2] );
			
			//	Draws each data point for the line
			foreach ( $this->_data as $d )
			{
				//	If we don't have a point from the last piece of data, don't draw a line segment since we don't know where to start
				if ( !isset( $lastPt[$y] ) )
				{
					if ( isset( $d[$y] ) )
					{
						$lastPt[$y] = $d[$y];
					}

					continue;
				}
				
				//	Everything looks in order; draw the line segment
				if ( isset( $d[$y] ) )
				{
					imageline( $this->_image,
						$chartXStart + ( $ix ) * $xInterval, 
						$chartYStart - ( $lastPt[$y] / $maxY ) * $chartPixelHeight,
						$chartXStart + ( $ix + 1 ) * $xInterval,
						$chartYStart - ( $d[$y] / $maxY ) * $chartPixelHeight,
						$c );
					$lastPt[$y] = $d[$y];
				}
				
				$ix++;
			}
		}

		$ix = 0;
		
		//	Draw any points on the graph
		foreach ( $this->_data as $d )
		{
			foreach ( $this->_points as $pt )
			{
				if ( $pt->x == $d[$this->_xColumns[0]] )
				{
					$color = imagecolorallocate( $this->_image, $pt->r, $pt->g, $pt->b );
					imagefilledellipse( $this->_image, $chartXStart + $ix * $xInterval, $chartYStart - ( $pt->y / $maxY ) * $chartPixelHeight, $pt->radius, $pt->radius, $color );
				}
			}

			$ix++;
		}

		//	If we reach this point and caching is enabled, we need to create the cache file since none was available
		if ( $this->_cache )
		{
			imagepng( $this->_image, $cachefile );
		}
		
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Adds an "x" dimension column to look for in the data. In line graphs, only the first is used.
	*/
	public function &x( $col )
	{
		$this->_xColumns[] = $col;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Adds a "y" dimension column to look for in the data. Each y dimension column is drawn as a separate line in line graphs.
	*/
	public function &y( $col, $r = 0, $g = 0, $b = 0 )
	{
		$this->_yColumns[$col] = array( $r, $g, $b );
		return $this;
	}
	
	//------------------------------------------------------------------------
	/**
	*	Add a new point to the graph
	*/
	public function &point( $x, $y, $size = 2, $r = 0, $g = 0, $b = 0 )
	{
		$pt = new tgsfImagePoint();
		$pt->x = $x;
		$pt->y = $y;
		$pt->radius = $size;
		$pt->r = $r;
		$pt->g = $g;
		$pt->b = $b;
		
		$this->_points[] = $pt;

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Enables or disables caching; sets the cache expire time
	*/
	public function &cache( $doCache = true, $expires = 600 )
	{
		$start = microtime();

		//	If a cache tag is provided, $doCache will be something other than true; in which case we don't need to generate a cache tag
		if ( $doCache === true )
		{
			//	The fastest way to get the state of the class is to do a buffer-captured var_dump--serialization is relatively slow
			ob_start();
			var_dump( (array)$this );
			$doCache = md5( ob_get_contents() );
			ob_end_clean();
		}
		
		$this->_cache = $doCache;
		$this->_cacheExpires = $expires;
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Sets the image size, or resizes the image if one is already present
	*/
	public function &size( $x, $y )
	{
		//	Resize an existing image
		if ( $this->_image )
		{
			$oldSize = $this->_size;
			$this->_size = array( 'x' => $x, 'y' => $y );
			if ( !imagecopyresampled( $this->_image, $this->_image, 0, 0, 0, 0, $x, $y, $oldSize[0], $oldSize[1] ) )
			{
				throw new tgsfException( 'Cannot resize image.' );
			}
		}
		//	Set the image size if we're not dealing with an existing image
		else
		{
			$this->_size = array( 'x' => $x, 'y' => $y );
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Import data from a query, array, or object
	*/
	public function &data( $input )
	{
		$this->_import( $input );
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Sets a background image; attempts to autodetect the background image type if none is provided
	*/
	public function &background( $path, $type = null )
	{
		if ( file_exists( $path ) )
		{
			$this->_background = $path;
		}
		else
		{
			throw new tgsfException( 'Invalid background image path provided.' );
		}
		
		if ( !$type )
		{
			$extension = strtolower( substr( $path, 0, -3 ) );
			
			if ( $extension == 'jpg' || $extension == 'jpeg' )
			{
				$type = imgJPG;
			}
			else if ( $extension == 'png' )
			{
				$type = imgPNG;
			}
			else if ( $extension == 'gif' )
			{
				$type = imgGIF;
			}
		}

		if ( $extension == imgJPG )
		{
			$this->_image = imagecreatefromjpeg( $path );
		}
		else if ( $extension == imgPNG )
		{
			$this->_image = imagecreatefrompng( $path );
		}
		else if ( $extension == imgGIF )
		{
			$this->_image = imagecreatefromgif( $path );
		}
		else
		{
			throw new tgsfException( 'Unable to determine image filetype. Try specifying a type.' );
		}

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Adds a filter to the image generator
	*/
	public function &pluginFilter( $event, $handler, $level = 0 )
	{
		$this->addFilter( $event, $handler, $level );

		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Sends the image to the browser in the specified format immediately; halts operation as soon as the image is sent
	*/
	public function output( $format = imgPNG )
	{
		if ( $format == imgPNG )
		{
			header( 'Content-type: ' . IMAGETYPE_PNG );
			imagepng( $this->_image );
		}
		else if ( $format == imgGIF )
		{
			header( 'Content-type: ' . IMAGETYPE_GIF );
			imagegif( $this->_image );
		}
		else if ( $format == imgJPG )
		{
			header( 'Content-type: ' . IMAGETYPE_JPEG );
			imagejpeg( $this->_image );
		}

		imagedestroy( $this->_image );

		exit();
	}
	//------------------------------------------------------------------------
	/**
	*	Saves the image to the specified path with the specified format
	*/
	public function &save( $path, $format = imgPNG )
	{
		if ( $format == imgPNG )
		{
			imagepng( $this->_image, $path );
		}
		else if ( $format == imgGIF )
		{
			imagegif( $this->_image, $path );
		}
		else if ( $format == imgJPG )
		{
			imagejpeg( $this->_image, $path );
		}

		imagedestroy( $this->_image );
		
		return $this;
	}
	//------------------------------------------------------------------------
	/**
	*	Sends debug output from the image object to the browser
	*/
	public function debug()
	{
		$dataRecords = count( $this->_data );

		echo "<pre>-------------------------------\n";
		echo "IMAGE DEBUG\n\n";
		echo "Image x columns: ".implode( ', ', $this->_xColumns )."\n";
		echo "Image y columns: ".implode( ', ', array_keys( $this->_yColumns ) )."\n";
		echo "Image data exists: ".(( $this->_image ) ? 'True' : 'False' )."\n";
		echo "Image width: ".$this->_size['x']."\n";
		echo "Image height: ".$this->_size['y']."\n";
		echo "Font path: ".$this->_font."\n";
		echo "\n";
		echo $dataRecords." data entries\n";
		echo "Cache file location: ".dirname( __FILE__ ).'/cache/'.$this->_cache."\n";

		echo "-------------------------------\n</pre>";
	}
	//------------------------------------------------------------------------
	/**
	*	Internal use only; performs a data input from several different types
	*/
	private function _import( $data )
	{
		if ( !is_array( $data ) && !is_object( $data ) )
		{
			throw new tgsfException( 'Input into Imaging class must be an array, object, or DataSource.' );
		}

		if ( $data instanceof tgsfDataSource )
		{
			$this->_data = $data->dataArray();
		}
		else if ( $data instanceof query )
		{
			$this->_data = $data;
		}
		else
		{
			$this->_data = (array)$data;
		}
	}
}

//------------------------------------------------------------------------
/**
*	Defined to hold defaults for a point on the image
*/
class tgsfImagePoint extends tgsfBase
{
	public $x = 0;
	public $y = 0;
	public $r = 0;
	public $g = 0;
	public $b = 0;
	public $radius = 2;
}