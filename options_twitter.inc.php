<h3>Twitter Options</h3>

<input type="hidden" name="socialite[twitter_added_friend]" value="<?= $sl_options["twitter_added_friend"]; ?>" />
<table class="form-table">
<tr valign="top">
	<th scope="row">
		Enable Twitter?
		<br />
		<a href="http://www.twitter.com" target="_blank">Create a Twitter Account</a>
	</th>
	<td>
		<input type="checkbox" name="socialite[twitter_enabled]" value="1" <?= $sl_options["twitter_enabled"] == 1 ? "CHECKED" : ""; ?> />
		<i>If not checked, blog posts will not be published to your Twitter account.</i>
	</td>
</tr>
<tr valign="top">
	<th scope="row">Username or Email Address</th>
	<td><input type="text" name="socialite[twitter_login]" value="<?= htmlentities($sl_options["twitter_login"]); ?>" /></td>
</tr>
<tr valign="top">
	<th scope="row">Password</th>
	<td><input type="password" name="socialite[twitter_password]" value="<?= htmlentities($sl_options["twitter_password"]); ?>" /></td>
</tr>
<tr valign="top">
	<th scope="row">Update Twitter when Post is Created</th>
	<td>
		<input type="checkbox" name="socialite[twitter_new_post_active]" value=1 <?= $sl_options["twitter_new_post_active"] == 1 ? "CHECKED" : ""; ?> />
		Update Twitter When a Post is Created?<br />
		Text to Tweet <i>(use #title# for the page title, #link# for the permalink)</i>
		<br />
		<input type="text" name="socialite[twitter_new_post_text]" size=40 value="<?= htmlentities($sl_options["twitter_new_post_text"]); ?>" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">Update Twitter When an Post is Edited</th>
	<td>
		<input type="checkbox" name="socialite[twitter_edit_post_active]" value=1 <?= $sl_options["twitter_edit_post_active"] == 1 ? "CHECKED" : ""; ?> />
		Update Twitter When a Post is Edited?<br />
		Text to Tweet <i>(use #title# for the page title, #link# for the permalink)</i>
		<br />
		<input type="text" name="socialite[twitter_edit_post_text]" size=40 value="<?= htmlentities($sl_options["twitter_edit_post_text"]); ?>" />
	</td>
</tr>
<tr valign="top">
	<th scope="row">Shorten URLs?</th>
	<td>
		<input type="checkbox" name="socialite[twitter_short_url]" value="1" <?= $sl_options["twitter_short_url"] == 1 ? "CHECKED" : ""; ?> />
		<i>Recommended. Only takes effect if the #link# tag is used in a template above</i>
	</td>
</tr>
</table>