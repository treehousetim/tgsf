$(document).ready( function()
{
	$( 'div.sm_contact_form input[name="first_name"]').hide();
	$( '#contactLoading' ).hide();
	$('div#footer ul.testimonials').shuffle().newsTicker( 7000 );
	$('div#pageBottom').css('display','block');
});
