tgsf.URL = function ( purl )
{
	//------------------------------------------------------------------------
	if ( !(this instanceof tgsf.URL) ) 
	{
		return new tgsf.URL( purl );
	}

	if ( ! this.config )
	{
		throw new Error( 'You must call tgsf.URL.setConfig before using URL')
	}

	var _url = purl.trim();
	this.local = true;
	
	var _ds = tgsf.datasource();
	this.setVar = function( name, val ) { _ds.setVar( name, val ); return this; };
	this.getVar = function( name ) { return _ds.getVar( name ); };
	this.exists = function( name ) { return _ds.exists( name ); };
	this.isEmpty = function( name ) { return _ds.isEmpty( name ) };
	this.dataArray = function() { return _ds.dataArray(); };

	//------------------------------------------------------------------------
	this.isLocal = function()
	{
		this.local = true;
		return this;
	}
	//------------------------------------------------------------------------
	this.notLocal = function()
	{
		this.local = false;
		return this;
	}
	//------------------------------------------------------------------------
	this.redirect = function()
	{
		window.document.location.href = this.toString();
		return false;
	}
	//------------------------------------------------------------------------
	this.toString = function()
	{
		if ( this.local == true )
		{
			var url = _url + this.getUrlVars();
			url += this.config.trailingSlash;

			if( url == '/' )
			{
				url = '';
			}

			return this.config.base + url;
		}
		else
		{
			return _url + this.getUrlVars();
		}
	}
	//------------------------------------------------------------------------
	this.getUrlVars = function()
	{
		if ( _ds.getLength() < 1 )
		{
			return '';
		}

		var get_string		= this.config.get_string;
		var get_separator	= this.config.get_separator;
		var get_equals		= this.config.get_equals;

		if ( this.local == false )
		{
			get_string		= '?';
			get_separator	= '&amp;';
			get_equals		= '=';
		}

		var _out = get_string;
		var _pieces = [];
		var _ix = 0;

		var tvars = this.dataArray();

		for ( name in tvars )
		{
			_pieces[_ix] = name + get_equals + tvars[name];
			_ix++;
		}

		return _out + _pieces.join( get_separator );
	}
	//------------------------------------------------------------------------
}

tgsf.URL.setConfig = function( configObj )
{
	tgsf.URL.prototype.config = configObj;
}