<h3>General Options</h3>

<table class="form-table">
<tr valign="top">
	<th scope="row">URL Shortenting</th>
	<td>
		<select name="socialite[short_url_service]" id="short_url_service" size=1 onchange="sl_short_url_options()">
		<option value="zz.gd" <?= $sl_options["short_url_service"] == "zz.gd" ? "selected" : ""; ?>>zz.gd</option>
		<option value="is.gd" <?= $sl_options["short_url_service"] == "is.gd" ? "selected" : ""; ?>>is.gd</option>
		<option value="ur.ly" <?= $sl_options["short_url_service"] == "ur.ly" ? "selected" : ""; ?>>ur.ly</option>
		<option value="bit.ly" <?= $sl_options["short_url_service"] == "bit.ly" ? "selected" : ""; ?>>bit.ly</option>
		<option value="tinyurl.com" <?= $sl_options["short_url_service"] == "tinyurl.com" ? "selected" : ""; ?>>tinyurl.com</option>
		</select>
		<br />
		If you select the URL Shortening option for any Social Networking site below, the service you select here
		will be used.
		<br />
		<strong>zz.gd</strong> - Default. This is the easiest option, and gives the shortest possible URLs.<br />
		<strong>is.gd</strong> - Simple and easy to use, gives the shortest possible URLs.<br />
		<strong>ur.ly</strong> - Simple and easy to use, gives the shortest possible URLs.<br />
		<strong>bit.ly</strong> - Allows for tracking statistics on your links. Bit.ly login and API key required.<br />
		<strong>tinyurl.com</strong> - A popular service, however the URLs are longer than most other services.<br />
		<i>	
			<strong>NOTE:</strong> If you are having problems posting to Facebook, change the URL Shortening service	you selected above
			or uncheck the URL Shortening checkbox in the Facebook options section of this configuration page.
			Facebook blocks some of the URL Shortening services, preventing Socialite from posting to your Wall.
		</i>
	</td>
</tr>
<tr valign="top" id="bitly_options" style="display:none">
	<th scope="row">Bit.ly Options</th>
	<td>
		Bit.ly requires you to get an API key. Follow these steps to get your API key:
		<ol>
			<li><a href="http://bit.ly/account/register?rd=/" target="_blank">Sign up for a bit.ly account.</a></li>
			<li><a href="http://bit.ly/account/login?rd=/" target="_blank">Login to your bit.ly account.</a></li>
			<li><a href="http://bit.ly/account/your_api_key" target="_blank">View your bit.ly API key, and enter it below.</a></li> 
		</ol>

		<table border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td>Bit.ly Login</td>
			<td><input type="text" name="socialite[bitly_login]" value="<?= htmlentities($sl_options["bitly_login"]); ?>" /></td>
		</tr>
		<tr>
			<td>Bit.ly API Key</td>
			<td><input type="text" name="socialite[bitly_api_key]" value="<?= htmlentities($sl_options["bitly_api_key"]); ?>" /></td>
		</tr>
		</table>
	<td>
</tr>
</table>