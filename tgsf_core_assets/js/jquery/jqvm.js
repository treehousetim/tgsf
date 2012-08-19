/*
	jQuery View Manager
	@author Dwight Brown <dwightb@construct7.com>
	@version 1.1
	@date 2009-12-08 20:17 CST
	@requires jQuery 1.3
*/
(function()
{
	window.JQVM = {};
	JQVM = function($)
	{
		var _options =
		    {
		    	view          : "",
		    	node          : 0,
		    	nodeSelector  : "#page-",
		    	defaultNode   : 1,
		    	defaultView   : "",
		    	scrollToTop   : true,
		    	classLoading  : "jqvm-loading",
		    	cache         : true
		    };

		$(document).on( 'click', '[node]', null,
		function()
		{
			if ( ! $( this ).attr( "href" ) ) return;
		
			var cache = _options.cache;
			if ( $(this).attr("cache") )
			{
				cache = ($(this).attr("cache") == "no") ? false : true;
			}
			
			JQVM.load( $(this).attr("href"), $(this).attr("node"), {cache:cache} );
			return false;
		});
		
		return {
			set: function( option, value )
			{
				_options[option] = value;
			},
			get: function( option )
			{
				return _options[option];
			},
			load: function( href, node, options )
			{
				var before = JQVM.before( href, node, options );
				if ( before === false ) return false;
				before = before || {};
				
				var options       = before.options || options || {};
				var href          = before.href || href || options.view;
				var node          = before.node || node || options.node  || _options.defaultNode;
				var nodeSelector  = options.nodeSelector  || _options.nodeSelector;
				var scrollToTop   = options.scrollToTop   || _options.scrollToTop;
				var classLoading  = options.classLoading  || _options.classLoading;
				var defaultNode   = options.defaultNode   || _options.defaultNode;
				var defaultView   = options.defaultView   || _options.defaultView;
				var cache         = (typeof options.cache == "boolean") ? options.cache : _options.cache;
				
				if ( !href && location.params.view )
				{
					href = location.params.view + location.search;
				}
				else
				{
					href = href || defaultView;
				}
				
				_options.view = href;
				_options.node = node;
				
				var nodeEl = $(nodeSelector + node);				
				if ( nodeEl.length == 0 ) return;
				
				if ( !cache || nodeEl.html() == "" || nodeEl.attr( "loaded" ) != "yes" )
				{
					nodeEl.attr( "loaded", "no" );
					
					nodeEl.addClass( classLoading );
					
					$.get( href, function( data )
					{
						nodeEl.removeClass( classLoading );
						nodeEl.html( data );
						nodeEl.attr( "loaded", "yes" );
						
						var after = JQVM.after( href, node, options );
						if ( after === false ) return false;
						
						if ( scrollToTop ) $("html, body").animate( { scrollTop: 0 }, "slow" );
					});
				}
				else
				{
					var after = JQVM.after( href, node, options );
					if ( after === false ) return false;
					
					if ( scrollToTop ) $("html, body").animate( { scrollTop: 0 }, "slow" );
				}
			},
			after: function( href, node, options )
			{
				return {};
			},
			before: function( href, node, options )
			{
				return {};
			}
		};
	}(jQuery);
}());

//------------------------------------------------------------------------

location.params = function()
{
	var params = {};
	
	if ( location.search != "" )
	{
		var _qs = location.search.slice(1).split( '&' );
		
		for ( p in _qs )
		{
			_qs[p] = _qs[p].split('=');
			params[_qs[p][0]] = _qs[p][1];
		}
	}
	
	return params;
}();

//------------------------------------------------------------------------

window.log = window.log || function()
{
	var logger = $( "#logger" );
	if ( logger )
	{
		var text = logger.val() + "\n"
		
		for( var i = 0; i < arguments.length; ++i )
		{
	    	text += arguments[i] + ", ";
	    }
	    
	    logger.val( text );
	}
	else if ( window.console )
	{
		console.log( arguments );
	}
};

//------------------------------------------------------------------------

if ( !window.console )
{
	window.console = {};
	window.console.log = window.log;
}
