/*
This code is Copyright (C) by TMLA INC.  ALL RIGHTS RESERVED.
Please view license.txt in /tgsf_core/legal/license.txt or
http://tgWebSolutions.com/opensource/tgsf/license.txt
for complete licensing information.
*/

/* Trim Functions are Public Domain */
/* http://www.somacon.com/p355.php */

/* From the web page above:
This set of Javascript functions trim or remove whitespace from the ends of strings. These functions can be stand-alone or attached as methods of the String object. They can left trim, right trim, or trim from both sides of the string. Rather than using a clumsy loop, they use simple, elegant regular expressions. The functions are granted to the public domain.
*/
String.prototype.trim = function() {
	return this.replace(/^\s+|\s+$/g,"");
}

String.prototype.ltrim = function() {
	return this.replace(/^\s+/,"");
}

String.prototype.rtrim = function() {
	return this.replace(/\s+$/,"");
}
String.prototype.getFloat = function()
{
	return parseFloat( this.replace( /[^0-9.]/g, "" ) );
}
String.prototype.startsWith = function(str){
    return (this.indexOf(str) === 0);
}
/* public domain disabled plugin for jquery */
jQuery.fn.extend({
filterDisabled : function(){ return this.filter(function(){return (typeof(this.disabled)!=undefined)})},
disabled: function(h) {
   if (h!=undefined) return this.filterDisabled().each(function(){this.disabled=h});
   this.filterDisabled().each(function() {h=((h||this.disabled)&&this.disabled)}); return h;
},
toggleDisabled: function() { return this.filterDisabled().each(function(){this.disabled=!this.disabled});}
});

//------------------------------------------------------------------------
/*
The following code was written by Tim Gallagher and is subject to the same conditions as the rest of tgsf
*/
jQuery.fn.extend({
	setLabelError : function( message )
		{
			var id = $(this).attr('id');
			var label = $( "label[for='" + id + "']" );
			label.addClass( 'errorCaption' );
			
			var spans = label.children( 'span' );
			var span;

			if ( spans.length == 0 )
			{
				label.append( '<span class="error_message">' + message + "</span>" );
			}
			else
			{
				// not right
				/* spans[0].html( message );
				spans[0].addClass( 'error_message' ); */
			}
		}
	}
);
//------------------------------------------------------------------------
function tgsfFormFieldFocus( id )
{
	$(document).ready(function()
	{
	    $('#' + id ).focus().select();
	});
}
//------------------------------------------------------------------------
// shuffle from
//http://yelotofu.com/2008/08/jquery-shuffle-plugin/
(function($){
  $.fn.shuffle = function() {
    return this.each(function(){
      var items = $(this).children();
      return (items.length)
        ? $(this).html($.shuffle(items))
        : this;
    });
  }
 
  $.shuffle = function(arr) {
    for(
      var j, x, i = arr.length; i;
      j = parseInt(Math.random() * i),
      x = arr[--i], arr[i] = arr[j], arr[j] = x
    );
    return arr;
  }
})(jQuery);
//------------------------------------------------------------------------
/*
 * jQuery doTimeout: Like setTimeout, but better! - v0.4 - 7/15/2009
 * http://benalman.com/projects/jquery-dotimeout-plugin/
 * 
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($){var a={},c="doTimeout",d=Array.prototype.slice;$[c]=function(){return b.apply(window,[0].concat(d.call(arguments)))};$.fn[c]=function(){var f=d.call(arguments),e=b.apply(this,[c+f[0]].concat(f));return typeof f[0]==="number"||typeof f[1]==="number"?this:e};function b(k){var l=this,g,i={},m=arguments,h=4,f=m[1],j=m[2],o=m[3];if(typeof f!=="string"){h--;f=k=0;j=m[1];o=m[2]}if(k){g=l.eq(0);g.data(k,i=g.data(k)||{})}else{if(f){i=a[f]||(a[f]={})}}i.id&&clearTimeout(i.id);delete i.id;function e(){if(k){g.removeData(k)}else{if(f){delete a[f]}}}function n(){i.id=setTimeout(function(){i.fn()},j)}if(o){i.fn=function(p){o.apply(l,d.call(m,h))&&!p?n():e()};n()}else{if(i.fn){j===undefined?e():i.fn(j===false);return true}else{e()}}}})(jQuery);

/**
 * Cookie plugin
 *
 * Copyright (c) 2006 Klaus Hartl (stilbuero.de)
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 */

/**
 * Create a cookie with the given name and value and other optional parameters.
 *
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Set the value of a cookie.
 * @example $.cookie('the_cookie', 'the_value', { expires: 7, path: '/', domain: 'jquery.com', secure: true });
 * @desc Create a cookie with all available options.
 * @example $.cookie('the_cookie', 'the_value');
 * @desc Create a session cookie.
 * @example $.cookie('the_cookie', null);
 * @desc Delete a cookie by passing null as value. Keep in mind that you have to use the same path and domain
 *       used when the cookie was set.
 *
 * @param String name The name of the cookie.
 * @param String value The value of the cookie.
 * @param Object options An object literal containing key/value pairs to provide optional cookie attributes.
 * @option Number|Date expires Either an integer specifying the expiration date from now on in days or a Date object.
 *                             If a negative value is specified (e.g. a date in the past), the cookie will be deleted.
 *                             If set to null or omitted, the cookie will be a session cookie and will not be retained
 *                             when the the browser exits.
 * @option String path The value of the path atribute of the cookie (default: path of page that created the cookie).
 * @option String domain The value of the domain attribute of the cookie (default: domain of page that created the cookie).
 * @option Boolean secure If true, the secure attribute of the cookie will be set and the cookie transmission will
 *                        require a secure protocol (like HTTPS).
 * @type undefined
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */

/**
 * Get the value of a cookie with the given name.
 *
 * @example $.cookie('the_cookie');
 * @desc Get the value of a cookie.
 *
 * @param String name The name of the cookie.
 * @return The value of the cookie.
 * @type String
 *
 * @name $.cookie
 * @cat Plugins/Cookie
 * @author Klaus Hartl/klaus.hartl@stilbuero.de
 */
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};