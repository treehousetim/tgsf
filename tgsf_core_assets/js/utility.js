/*
This code is copyright 2009 by TMLA INC.  ALL RIGHTS RESERVED.
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
