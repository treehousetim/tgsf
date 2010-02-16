tgsf.URL = 
function ( purl )
{
	//------------------------------------------------------------------------
	if ( !(this instanceof tgsf.URL) ) 
	{
		return new tgsf.URL( purl );
	}

	if ( ! this.config )
	{
		throw new Error( 'You must call tgsf.URL.config on URL')
	}

	var _url = purl.trim()
	var _local = true;

	//------------------------------------------------------------------------
	this.isLocal = function()
	{
		_local = true;
		return this;
	}
	//------------------------------------------------------------------------
	this.notLocal = function()
	{
		_local = false;
		return this;
	}
	//------------------------------------------------------------------------
	/**
	* Property getter for local
	*/
	this.local = (function()
	{
		return _local == true; // this way we don' return a pointer to our protected var
	})();
	//------------------------------------------------------------------------
	this.redirect = function()
	{
		window.document.location.href = this.toString();
		return false;
	}
	//------------------------------------------------------------------------
	this.toString = function()
	{
		if ( _local == true )
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
	this.getUrlVarString = function()
	{
		return '';
	}
	//------------------------------------------------------------------------
	this.getUrlVars = function()
	{
		/*
		if ( ! this.ds instanceof tgsf.datasource )
				{
					return '';
				}*/
		
		
		if ( this.length <= 0 )
		{
			return '';
		}
		
		var get_string		= this.config.get_string;
		var get_separator	= this.config.get_separator;
		var get_equals		= this.config.get_equals;

		if ( _local == false )
		{
			get_string		= '?';
			get_separator	= '&amp;';
			get_equals		= '=';
		}

		var _out = get_string;
		var _pieces = [];
		var _ix = 0;

		for ( var name in this._vars )
		{
			_pieces[_ix] = name + get_equals + this._vars[name];
			_ix++;
		}
		return _out + _pieces.join( get_separator );
	}
	//------------------------------------------------------------------------
}
// inherit from the datasource object
tgsf.URL.prototype = new tgsf.datasource();

tgsf.URL.prototype.setConfig = function( configObj )
{
	tgsf.URL.prototype.config = configObj;
};
