$(document).ready(
function()
{
	$('#login-box' ).dialog(
	{
		modal: true,
		closeOnEscape: false,
		draggable: false,
		title: 'System Login',
		open: function(event, ui)
		{ 
			//hide close button.
			$(this).parent().children().children('.ui-dialog-titlebar-close').hide();
			$( "#user_login_username" ).focus().select();
		}
	}
	);

});