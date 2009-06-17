/*
10/28/2008 Version 2.1

This is not free SOFTWARE.

This SOFTWARE is protected by copyright laws and international copyright treaties,
as well as other intellectual property laws and treaties.
The SOFTWARE is not sold, and instead is only licensed for use,
strictly in accordance with the license that accompanied the purchase of this product.

If you don't have a copy of this license, you may view it online:
http://www.thephppro.com/products/ajax/license.php

I am a dad who enjoys spending time with his wife and children.
When I sell software I am able to spend more time with them than I would otherwise.

By purchasing a license to use this software instead of copying it and using it
you accomplish two things.

1. You do the right thing by not stealing.
2. You give me time with my family - this is a noble thing.

You may purchase this ajax framework from http://www.thephppro.com/products/ajax/
My prices are reasonable and fair and include varying levels of support.

Thank you for respecting my hard work.

Tim Gallagher

THE ONLY RIGHTS YOU HAVE WITHOUT PURCHASE ARE TO ALLOW WEB SITES YOU VISIT TO INVOKE A DOWNLOAD AS PART
OF THEIR NORMAL OPERATIONS INSIDE A WEB PAGE ON WEB SITES THAT HAVE PURCHASED A COPY OF THIS CODE AND TO
ALLOW THIS CODE TO EXECUTE AS PART OF WEB SITE SERVICES THAT YOU ARE THE RECIPIENT OF 

DO NOT REMOVE THE PRECEEDING NOTICE - DOING SO VIOLATES THE TERMS OF THE LICENSE AGREEMENT

*/

var tppAJAXTimeout = 60000;
var tppAJAXNoAJAXSupportMessage = 'Your browser does not support AJAX';
var tppAJAXTimeoutMessage = 'Unable to communicate with server - operation aborted.';
var tppAJAXAutoLoading = false; // set to true to use automatic loading divs
var tppAJAXAutoLoadingModal = true; // true/false for modal layer covering document

var ajaxFactory = (
function()
{
	var pool = [];
	var poolSize = 2;
	 
	var emptyFunc = function() {}; // used for an empty event handler
	 
	// private static methods
 
	function createRequestor()
	{
        if ( typeof XMLHttpRequest != 'undefined')
		{
			xmlhttp = new XMLHttpRequest();
        }
        else
        {
            var xmlhttp = false;

            /*@cc_on @*/
            /*@if ( @_jscript_version >= 5 )
            try
            {
                //xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
                xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
            }
            catch (E)
            {
                try
                {
                    xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (E)
                {
                    xmlhttp = false;
                }
            }
            @end @*/
        }

		
		return xmlhttp;
    }

	// create poolSize requestors to have avoid the overhead of creating them later
	for ( var ix = 0; ix < poolSize; ix++ )
	{
		pool.push( createRequestor() );
	}
 
	// shared instance methods
	return (
	{
		release:function( xmlhttp )
		{
			xmlhttp.onreadystatechange = emptyFunc;
			pool.push( xmlhttp );
		},
		
		getRequestor:function()
		{
			if ( pool.length < 1 )
			{
				return createRequestor();
			}
			else
			{
				return pool.pop();
			}
		},
		
		toString:function()
		{
			return "pool size = " + stack.length;
		}
	}
	);
}

)();
// ----------------------------------------------------------------------------------------
function tppAJAXCore()
{
    this.xmlhttp                    = ajaxFactory.getRequestor();
}
// ----------------------------------------------------------------------------------------
tppAJAXCore.prototype.getData =
function ( myNode, myNodeName )
{
    var subNodes = myNode.childNodes;

    for ( j=0; j < subNodes.length; j++ )
    {
        var subNode = subNodes.item(j);
        
        if ( subNode.nodeName == myNodeName )
        {
            // this is cross browser ( ie and firefox ) compatible
            return subNode.childNodes.item(0).nodeValue;
        }
    }
    return "";
}
// ----------------------------------------------------------------------------------------
tppAJAXCore.prototype.checkReadyState =
function ()
{
    var result = true;

    if ( this.xmlhttp.readyState == 1 )
    {
        if ( document.getElementById( 'globalError' ) )
        {
            document.getElementById( 'globalError' ).style.display = 'inline';
            document.getElementById( 'globalError' ).innerHTML = 'Previous operation has not finished, please wait one moment.';
        }
        else
        {
            alert( 'Previous operation has not finished, please wait one moment.' );
        }
        result = false;
    }

    return result;
}
// ----------------------------------------------------------------------------------------
tppAJAXCore.prototype.processXmlResponse =
function ( response )
{
    if ( ! response )
    {
        dotppAJAXAutoUnLoad();
        return;
    }
    nodes = response.getElementsByTagName('itm');
    if ( ! nodes )
    {
        alert( 'Malformed XML - no items to replace!' );
    }

    // get html to apply to html containers
    // via the getElementById function
    for ( i=0; i < nodes.length; i++ )
    {
        node = nodes.item(i);
        try
        {
            var el =document.getElementById( this.getData( node, 'id' ) );
            if ( el.type != undefined )
            {
                if ( el.type == 'text' )
                {
                    el.value = this.getData( node, 'dat' );
                }
            }
            else
            {
                el.innerHTML = this.getData( node, 'dat' );
            }
        }
        catch ( err ) {}
        
    }

    // get javascript commands to execute
    nodes = response.getElementsByTagName('js');

    for ( i=0; i < nodes.length; i++ )
    {
        node = nodes.item(i);
        eval( this.getData( node, 'dat' ) );
    }

    // get properties to set on document
    // i.e. document.title = 'Title Bar Title'
    nodes = response.getElementsByTagName('doc');

    for ( i=0; i < nodes.length; i++ )
    {
        node = nodes.item(i);
        code = "document." + this.getData( node, 'id' ) + " = '" + this.getData( node, 'dat' ) + "'";
        eval( code );
    }
}
// ----------------------------------------------------------------------------------------
tppAJAXCore.prototype.createFormQuery =
function ( elements )
{
    var queryVars = [1][1];
    var checkQueryVars = [10];

    var curVar			= 0;
    var strQuery		= "";

    for ( ix = 0; ix < elements.length; ix++ )
    {
        element = elements.item(ix);

        type			= element.type;
        if ( ! type )
        {
            continue;
        }

        name			= element.name;
        value			= element.value;
        radioItemCnt	= 0;

        if (type.substr( 0, 6 ) == 'select' )
        {
            type = 'select';
        }

        switch ( type )
        {
        
        case 'select':
            if ( element.multiple )
            {
                for ( iy = 0; iy < element.options.length; iy++ )
                {
                    if ( element.options[iy].selected )
                    {
                        if ( element.options[iy].value == '' )
                        {
                            element.options[iy].value = element.options[iy].text;
                        }

                        strQuery = strQuery + name + "[]=" + escape( element.options[iy].value ) + "&";
                    }
                }
            }
            else
            {
                strQuery = strQuery + name + "=" + escape( value ) + "&";
            }
            break;
        
        case 'checkbox':
        case 'radio':
            if ( element.checked === true )
            {
                strQuery = strQuery + name + "=" + escape( value ) + "&";
            }
            break;

        default:
            strQuery = strQuery + name + "=" + escape( value ) + "&";
        };

    };

    return strQuery;
}
// ----------------------------------------------------------------------------------------
// this is intended to be over-ridden by the end user
tppAJAXCore.prototype.stateChange =
function ( xmlhttp, beforeAfter ) {  }
// ----------------------------------------------------------------------------------------
tppAJAXCore.prototype.setupCommunication =
function ( method, processor, query, callback, isJSON )
{
    this.lastMethod = method;
    this.lastProcessor = processor;
    this.lastQuery = query;
    this.lastCallback = callback;
    this.lastIsJSON = isJSON;

    var stateChangeHandler = this.stateChange;
    var xmlhttp = this.xmlhttp;
    var tppAJAX = this;

	if ( typeof( xmlhttp) == "object" )
	{
        if( this.checkReadyState() )
		{
			xmlhttp.open( method, processor, true );

			var requestTimer = setTimeout(
                function()
                {
                    xmlhttp.abort();
                    hideModalLoadingIndicator();
                    alert( tppAJAXTimeoutMessage );
                },
            tppAJAXTimeout );

			if ( method == 'POST' )
			{
				xmlhttp.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
			}

			xmlhttp.onreadystatechange = 
			function()
            {
                stateChangeHandler( xmlhttp, 'before' );

				if ( xmlhttp.readyState == 4 && ( xmlhttp.status == 200 || xmlhttp.status === 0 ) )
				{
					clearTimeout( requestTimer );

                    if ( isJSON )
                    {
                        if ( xmlhttp.responseText )
                        {
                            var txt = xmlhttp.responseText;
                            if ( txt.substr(0,9) == "while(1);" )
                            {
                                txt = txt.substring(9);
                            }

                            jsonObject = eval( "(" + txt + ")" );
                            
                            if ( callback && typeof callback == 'function' )
                            {
                                callback( jsonObject );
                            }
                            else
                            {
                                alert( "No Callback to handle JSON data\n" + xmlhttp.responseText );
                                dotppAJAXAutoUnLoad();
                            }
                        }
                        else
                        {
                            tppAJAX.processXmlResponse( xmlhttp.responseXML );
                        }
                    }
                    else
                    {
                        if ( xmlhttp.responseText && ( xmlhttp.responseXML === '' || xmlhttp.responseXML === null ) )
                        {
                            alert( xmlhttp.responseText );
                            dotppAJAXAutoUnLoad();
                        }
                        tppAJAX.processXmlResponse( xmlhttp.responseXML );
                    }

					ajaxFactory.release( xmlhttp );
				}
				else if( xmlhttp.readyState == 4 )
				{
					clearTimeout( requestTimer );

                    if ( isJSON )
                    {
                        alert( "There was a problem retrieving the JSON data:\n" + xmlhttp.statusText );
                    }
                    else
                    {
                        alert( "There was a problem retrieving the XML data:\n" + xmlhttp.statusText );
                    }
                    dotppAJAXAutoUnLoad();
					ajaxFactory.release( xmlhttp );
				}
                stateChangeHandler( xmlhttp, 'after' );
			};
		   
			xmlhttp.send( query );
		}
	}
	else
	{
		alert( tppAJAXNoAJAXSupportMessage );
	}
}
// ----------------------------------------------------------------------------------------
// simple public api
// ----------------------------------------------------------------------------------------
function dotppAJAXAutoLoad()
{
    if ( tppAJAXAutoLoading )
    {
        if ( tppAJAXAutoLoadingModal )
        {
            showModalLoadingIndicator();
        }
        else
        {
            showLoadingIndicator();
        }
    }
}
// ----------------------------------------------------------------------------------------
function dotppAJAXAutoUnLoad()
{
    if ( tppAJAXAutoLoading )
    {
        if ( tppAJAXAutoLoadingModal )
        {
            hideModalLoadingIndicator();
        }
        else
        {
            hideLoadingIndicator();
        }
    }
}
// ----------------------------------------------------------------------------------------
function getJSON( callback, serverPage )
{
    dotppAJAXAutoLoad();
    var tppAJAX = new tppAJAXCore();
    return tppAJAX.setupCommunication( 'GET', serverPage, null, callback, true );
}
// ----------------------------------------------------------------------------------------
function sendFormJSON( callback, formID, formProcessor )
{
    dotppAJAXAutoLoad();
	var form = document.getElementById( formID );
	var elements = form.elements;

    var tppAJAX = new tppAJAXCore();

    query = tppAJAX.createFormQuery( form.elements );

	tppAJAX.setupCommunication( 'POST', formProcessor, query, callback, true );

    return tppAJAX;
}
// ----------------------------------------------------------------------------------------
function updatePage( serverPage )
{
    dotppAJAXAutoLoad();
    var tppAJAX = new tppAJAXCore();
    tppAJAX.setupCommunication( 'GET', serverPage, null, null, false );
}
// ----------------------------------------------------------------------------------------
function sendForm ( formID, formProcessor )
{
    dotppAJAXAutoLoad();

	var form = document.getElementById( formID );
	var elements = form.elements;

    var tppAJAX = new tppAJAXCore();

    query = tppAJAX.createFormQuery( form.elements );

	tppAJAX.setupCommunication( 'POST', formProcessor, query, null, false );

    return tppAJAX;
}
// ----------------------------------------------------------------------------------------
function setupModalLoadingIndicator( sourceDiv )
{
    var cover = document.getElementById( 'tppAJAXModalCover' );

    if ( ! cover )
    {
        cover = document.createElement( 'div' );
        cover.setAttribute( 'id', 'tppAJAXModalCover' );
        cover.style.display = 'none';
    }

    cover.innerHTML = '&nbsp;';
    document.body.insertBefore( cover, document.body.firstChild);
    setupLoadingIndicator (sourceDiv );
}
// ----------------------------------------------------------------------------------------
function setupLoadingIndicator (sourceDiv )
{
    var loadingIndicator = document.getElementById( 'tppAJAXLoadingContent' );
    var source = document.getElementById( sourceDiv );

    if ( source )
    {
        if ( ! loadingIndicator )
        {
            loadingIndicator = document.createElement( 'div' );
            loadingIndicator.setAttribute( 'id', 'tppAJAXLoadingContent' );
            loadingIndicator.style.display = 'none';
        }

        loadingIndicator.innerHTML = source.innerHTML;
        document.body.insertBefore( loadingIndicator, document.body.firstChild);
    }
}
// ----------------------------------------------------------------------------------------
function showModalLoadingIndicator()
{
    document.getElementById( 'tppAJAXModalCover' ).style.display = 'block';
    showLoadingIndicator();
}
// ----------------------------------------------------------------------------------------
function hideModalLoadingIndicator()
{
    document.getElementById( 'tppAJAXModalCover' ).style.display = 'none';
    hideLoadingIndicator();
}
// ----------------------------------------------------------------------------------------
function showLoadingIndicator()
{
    document.getElementById( 'tppAJAXLoadingContent' ).style.display = 'inline';
    var d = document.createElement( 'div' );
    d.setAttribute( 'id', 'tppAJAXDeleting' );
    document.body.insertBefore( d, document.body.lastChild );
    document.body.removeChild( d );
    delete d;
}
// ----------------------------------------------------------------------------------------
function hideLoadingIndicator()
{
    document.getElementById( 'tppAJAXLoadingContent' ).style.display = 'none';
    document.createElement( 'div' );
}
// ----------------------------------------------------------------------------------------
