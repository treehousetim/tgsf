tgsf = {};
tgsf.datasource = function( values )
{
	if ( ! ( this instanceof tgsf.datasource ) ) 
	{
		return new tgsf.datasource( values );
	}
	this._vars = [];

	if ( values )
	{
		this._vars = values;
	}
	//------------------------------------------------------------------------
	this.setVar = function( name, value )
	{
		this._vars[name] = value;
		return this;
	}
	//------------------------------------------------------------------------
	/**
	* Pass a second parameter as a default value if desired, otherwise empty string is returned
	*/
	this.getVar = function( name )
	{
		if ( this.exists( name ) )
		{
			return this._vars[name];
		}

		if ( arguments.length == 2 )
		{
			return arguments[1];
		}

		return '';
	}
	//------------------------------------------------------------------------
	this.length = (function()
	{
		//return this._vars.length();
	})();
	//------------------------------------------------------------------------
	this.exists = function( name )
	{
		return this._vars[name] != undefined;
	}
	//------------------------------------------------------------------------
	this.isEmpty = function ( name )
	{
		if ( this.exists( name ) == false )
		{
			return true;
		}
		
		return this._vars[name] == '' || this._vars[name] == 0 || this._vars[name] == false;
	}
	//------------------------------------------------------------------------
	this.dataArray = function()
	{
		// may be worthless - I'm attempting to protect the original from modification
		var _ret = this._vars;
		return _ret;
	}
}