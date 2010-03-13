tgsf = {};
tgsf.datasource = function( values )
{
	if ( ! ( this instanceof tgsf.datasource ) )
	{
		return new tgsf.datasource( values );
	}
	var _vars = [];

	if ( values )
	{
		_vars = values;
	}
	//------------------------------------------------------------------------
	this.setVar = function( name, value )
	{
		_vars[name] = value;
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
			return _vars[name];
		}

		if ( arguments.length == 2 )
		{
			return arguments[1];
		}

		return '';
	}
	//------------------------------------------------------------------------
	this.getLength = function()
	{
		var ix = 0;
		var val;
		for( val in _vars )
		{
			ix++;
		}
		return ix;
	};

	//------------------------------------------------------------------------
	this.exists = function( name )
	{
		return _vars[name] != undefined;
	}
	//------------------------------------------------------------------------
	this.isEmpty = function ( name )
	{
		if ( this.exists( name ) == false )
		{
			return true;
		}
		
		return _vars[name] == '' || _vars[name] == 0 || _vars[name] == false;
	}
	//------------------------------------------------------------------------
	this.dataArray = function()
	{
		return _vars;
	}
}
