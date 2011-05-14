$(document).ready(
function()
{
	if ( top.location != location )
	{
		top.location.href = document.location.href ;
	}
	
});

function scrollToTop()
{
	$("html, body").animate( { scrollTop: 0 }, 800 );
}
