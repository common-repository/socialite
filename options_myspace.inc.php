<h3>MySpace Options</h3>

	<table class="form-table">

<?php if(!function_exists("curl_init")) : ?>

	<tr valign="top">
		<td style="background-color: #C44; color: #000 ;">
			<strong>Warning!</strong> Socialite's MySpace module requires cURL to be installed.
			<a href="http://www.php.net/manual/en/curl.setup.php" target="_blank">Please read
			for more information</a>
		</td>
	</tr>

<?php else: ?>

	<tr valign="top">
		<th scope="row">
			Enable MySpace?
			<br />
			<a href="http://www.myspace.com" target="_blank">Create a MySpace Account</a>
		</th>
		<td>
			<input type="checkbox" name="socialite[myspace_enabled]" value="1" <?= $sl_options["myspace_enabled"] == 1 ? "CHECKED" : ""; ?> />
			<i>If not checked, blog posts will not be published to your MySpace account.</i>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Login Email</th>
		<td><input type="text" name="socialite[myspace_login]" value="<?= htmlentities($sl_options["myspace_login"]); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">Login Password</th>
		<td><input type="password" name="socialite[myspace_password]" value="<?= htmlentities($sl_options["myspace_password"]); ?>" /></td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Update the MySpace Blog When a Post is Created
			<br /><br />

			<i>Use the following formatting tags to customize your blog layout:</i>
			<ul class="directions">
				<li>#title# </li>
				<li>#content#</li>
				<li>#excerpt#</li>
				<li>#link# </li>
			</ul>
		</th>
		<td>
			<input type="checkbox" name="socialite[myspace_new_post_active]" value=1 <?= $sl_options["myspace_new_post_active"] == 1 ? "CHECKED" : ""; ?> />
			Post to your MySpace Blog When a Post is Created?
			<br /><br />

			Title Template<br />
			<input type="text" name="socialite[myspace_new_post_title]" value="<?= htmlentities($sl_options["myspace_new_post_title"]); ?>" size=40 />
			<i>MySpace sets a max length of <?= SL_MySpace::MS_TITLE_MAX_LEN; ?> characters</i>
			<br />

			Body Template<br />
			<textarea name="socialite[myspace_new_post_body]" cols=45 rows=6><?= htmlentities($sl_options["myspace_new_post_body"]); ?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">
			Update the MySpace Blog When a Post is Edited
			<br /><br />

			<i>Use the following formatting tags to customize your blog layout:</i>
			<ul class="directions">
				<li>#title# </li>
				<li>#content#</li>
				<li>#excerpt#</li>
				<li>#link# </li>
			</ul>
		</th>
		<td>
			<input type="checkbox" name="socialite[myspace_edit_post_active]" value=1 <?= $sl_options["myspace_edit_post_active"] == 1 ? "CHECKED" : ""; ?> />
			Post to your MySpace Blog When a Post is Edited?
			<br /><br />

			Title Template<br />
			<input type="text" name="socialite[myspace_edit_post_title]" value="<?= htmlentities($sl_options["myspace_edit_post_title"]); ?>" size=40 />
			<i>MySpace sets a max length of <?= SL_MySpace::MS_TITLE_MAX_LEN; ?> characters</i>
			<br />

			Body Template<br />
			<textarea name="socialite[myspace_edit_post_body]" cols=45 rows=6><?= htmlentities($sl_options["myspace_edit_post_body"]); ?></textarea>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Blog Category for Posts</th>
		<td>
			<select name="socialite[myspace_category]" size=1>
				<option value="0" <?= $sl_options["myspace_category"] == 0 ? "SELECTED" : ""; ?>>None</option>
				<option value="1" <?= $sl_options["myspace_category"] == 1 ? "SELECTED" : ""; ?>>Art and Photography</option>
				<option value="4" <?= $sl_options["myspace_category"] == 4 ? "SELECTED" : ""; ?>>Automotive</option>
				<option value="2" <?= $sl_options["myspace_category"] == 2 ? "SELECTED" : ""; ?>>Blogging</option>
				<option value="6" <?= $sl_options["myspace_category"] == 6 ? "SELECTED" : ""; ?>>Dreams and the Supernatural</option>
				<option value="3" <?= $sl_options["myspace_category"] == 3 ? "SELECTED" : ""; ?>>Fashion, Style, Shopping</option>
				<option value="7" <?= $sl_options["myspace_category"] == 7 ? "SELECTED" : ""; ?>>Food and Restaurants</option>
				<option value="8" <?= $sl_options["myspace_category"] == 8 ? "SELECTED" : ""; ?>>Friends</option>
				<option value="9" <?= $sl_options["myspace_category"] == 9 ? "SELECTED" : ""; ?>>Games</option>
				<option value="10" <?= $sl_options["myspace_category"] == 10 ? "SELECTED" : ""; ?>>Goals, Plans, Hopes</option>
				<option value="11" <?= $sl_options["myspace_category"] == 11 ? "SELECTED" : ""; ?>>Jobs, Work, Careers</option>
				<option value="12" <?= $sl_options["myspace_category"] == 12 ? "SELECTED" : ""; ?>>Life</option>
				<option value="14" <?= $sl_options["myspace_category"] == 14 ? "SELECTED" : ""; ?>>Movies, TV, Celebrities</option>
				<option value="15" <?= $sl_options["myspace_category"] == 15 ? "SELECTED" : ""; ?>>Music</option>
				<option value="16" <?= $sl_options["myspace_category"] == 16 ? "SELECTED" : ""; ?>>MySpace</option>
				<option value="17" <?= $sl_options["myspace_category"] == 17 ? "SELECTED" : ""; ?>>News and Politics</option>
				<option value="18" <?= $sl_options["myspace_category"] == 18 ? "SELECTED" : ""; ?>>Parties and Nightlife</option>
				<option value="19" <?= $sl_options["myspace_category"] == 19 ? "SELECTED" : ""; ?>>Pets and Animals</option>
				<option value="26" <?= $sl_options["myspace_category"] == 26 ? "SELECTED" : ""; ?>>Podcast</option>
				<option value="20" <?= $sl_options["myspace_category"] == 20 ? "SELECTED" : ""; ?>>Quiz/Survey</option>
				<option value="21" <?= $sl_options["myspace_category"] == 21 ? "SELECTED" : ""; ?>>Religion and Philosophy</option>
				<option value="13" <?= $sl_options["myspace_category"] == 13 ? "SELECTED" : ""; ?>>Romance and Relationships</option>
				<option value="22" <?= $sl_options["myspace_category"] == 22 ? "SELECTED" : ""; ?>>School, College, Greek</option>
				<option value="23" <?= $sl_options["myspace_category"] == 23 ? "SELECTED" : ""; ?>>Sports</option>
				<option value="24" <?= $sl_options["myspace_category"] == 24 ? "SELECTED" : ""; ?>>Travel and Places</option>
				<option value="5" <?= $sl_options["myspace_category"] == 5 ? "SELECTED" : ""; ?>>Web, HTML, Tech</option>
				<option value="25" <?= $sl_options["myspace_category"] == 25 ? "SELECTED" : ""; ?>>Writing and Poetry</option>
			</select>
			<i>These are the categories given by MySpace when publishing a new blog entry
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Allow Comments?</th>
		<td>
			<input type="checkbox" name="socialite[myspace_allow_comments]" value=1 <?= $sl_options["myspace_allow_comments"] == 1 ? "CHECKED" : ""; ?> />
			Yes
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Privacy</th>
		<td>
			<select name="socialite[myspace_privacy]" size=1>
			<option value="0" <?= $sl_options["myspace_privacy"] == 0 ? "SELECTED" : ""; ?>>Public</option>
			<option value="1" <?= $sl_options["myspace_privacy"] == 1 ? "SELECTED" : ""; ?>>Diary</option>
			<option value="2" <?= $sl_options["myspace_privacy"] == 2 ? "SELECTED" : ""; ?>>Friends</option>
			<option value="3" <?= $sl_options["myspace_privacy"] == 3 ? "SELECTED" : ""; ?>>Preferred List</option>
			</select>
			<i>Privacy settings are created by MySpace, please read your MySpace blog page for more info</i>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Shorten URLs?</th>
		<td>
			<input type="checkbox" name="socialite[myspace_short_url]" value="1" <?= $sl_options["myspace_short_url"] == 1 ? "CHECKED" : ""; ?> />
			<i>Only takes effect if the #link# tag is used in a template above</i>
		</td>
	</tr>

<?php endif; ?>

	</table>