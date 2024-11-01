
function sl_short_url_options()
{
	opt = $F('short_url_service');

	if(opt == "bit.ly")
	{
		if($('bitly_options').style.display == "none")
			new Effect.SlideDown('bitly_options');
	}
	else
	{
		if($('bitly_options').style.display != "none")
			new Effect.SlideUp('bitly_options');
	}
}

Event.observe(window, 'load', sl_short_url_options);