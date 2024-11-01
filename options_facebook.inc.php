<h3>Facebook Options</h3>

	<table class="form-table">
	<tr valign="top">
		<th scope="row">
			Enable Facebook?
			<br />
			<a href="http://www.facebook.com" target="_blank">Create a Facebook Account</a>
		</th>
		<td>
			<input type="checkbox" name="socialite[facebook_enabled]" value="1" <?= $sl_options["facebook_enabled"] == 1 ? "CHECKED" : ""; ?> />
			<i>If not checked, blog posts will not be published to your Facebook account.</i>
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Facebook Account</th>
		<td>

<?php if($sl_options["facebook_auth_token"] == "") : ?>

			<p>
			A Facebook Authentication Token is needed for Wordpress to interact with your Facebook account.
			This is a Two step process.
			</p>
			<ul>
				<li>
					<strong>Step 1:</strong>
					<?php
					// first
					$fb_url = urlencode("http://www.facebook.com/code_gen.php?xxRESULTTOKENxx&api_key=".SL_Facebook::FB_API_KEY."&v=1.0");
					// second
					$fb_url = urlencode("http://www.facebook.com/connect/prompt_permissions.php?v=1.0&fbconnect=true&display=popup&extern=1&next=$fb_url&next_cancel=$fb_url&api_key=".SL_Facebook::FB_API_KEY."&ext_perm=publish_stream&enable_profile_selector=1");
					?>
					<a href="http://www.facebook.com/login.php?v=1.0&api_key=<?= SL_Facebook::FB_API_KEY; ?>&next=<?= $fb_url; ?>&next_cancel=<?= $fb_url; ?>" target="_blank">Follow this link to log into your Facebook account</a>.
					Once you log in, follow the directions Facebook gives you. At the end you will be asked to generate
					a One Time Code. Enter this One Time Code into the text field in Step 2. The Socialite Facebook module will not
					work without the code.
				</li>
				<li>
					<strong>Step 2:</strong>
					Copy the One Time Code you received from Step 1 into the text box below.
					<br />
					Facebook One Time Code: <input type="text" name="socialite[facebook_auth_token]" value="<?= htmlentities($sl_options["facebook_auth_token"]); ?>" />
				</li>
			</ul>
			
<?php
			else :
				$fb = new SL_Facebook($sl);
				$fb_user_info = $fb->GetUserInfo();

				if(is_array($fb_user_info)) :

					$name = $fb_user_info["name"];
					if($name == "")
						$name = "{$fb_user_info["first_name"]} {$fb_user_info["last_name"]}";

					$img = $fb_user_info["pic_square"];
					if($img == "")
						$img = "http://www.gravatar.com/avatar/ad516503a11cd5ca435acc9bb6523536?s=50";
?>

					<p style="clear: right">
						<img src="<?= $img; ?>" border=0 width=50 height=50 style="float: left; margin: 0px 10px 3px 0px" />
						<div style="clear: right">
							You are logged into Facebook as
							<strong><a href="<?= $fb_user_info["profile_url"]; ?>" target="_blank"><?= $name; ?></a></strong>.
							<br />Your One Time Code: <?= htmlentities($sl_options["facebook_auth_token"]); ?>
							<br />
							<br />Click the button below to reset the login option.
						</div>
						&nbsp;
					</p>

<?php			else : ?>

					<p style="clear: right">
						<img src="<?= $img; ?>" border=0 width=50 height=50 style="float: left; margin: 0px 10px 3px 0px" />
						<div style="clear: right">
							Your login expired. This usually happens because you assigned a one time key to your login
							with another application. Facebook does not permit this and deactivates the old login.
							<br />Click the button below to reset the login option.
						</div>
						&nbsp;
					</p>

<?php			endif; ?>

			<p>
				<input type="hidden" name="socialite[facebook_auth_token]" value="<?= $sl_options["facebook_auth_token"]; ?>" />
				<input type="hidden" name="socialite[facebook_session][session_key]" value="<?= $sl_options["facebook_session"]["session_key"]; ?>" />
				<input type="hidden" name="socialite[facebook_session][uid]" value="<?= $sl_options["facebook_session"]["uid"]; ?>" />
				<input type="hidden" name="socialite[facebook_session][expires]" value="<?= $sl_options["facebook_session"]["expires"]; ?>" />
				<input type="hidden" name="socialite[facebook_session][secret]" value="<?= $sl_options["facebook_session"]["secret"]; ?>" />
				<input type="submit" name="facebook_reset_auth_token" value="Reset Facebook Account Login" onclick="if(confirm('Are you sure you wish to reset your Facebook account? You can always add it back if you reset it and change your mind later.')) { return true; } else { return false ; }" />
			</p>

<?php		endif; ?>

		</td>
	</tr>

<?php if(is_array($fb_user_info)) : ?>
	<tr valign="top">
		<th scope="row">Post to This Facebook Page</th>
		<td>
				<select name="socialite[facebook_post_page_id]" size=1>
				<option value=""><?= $name; ?>'s Profile Page</option>
				<?php
					$page_arr = $fb->GetAdminPagesForUser();
					$group_arr = $fb->GetGroupsForUser();

					if(is_array($page_arr))
					{
						print "<option value=\"\" style=\"font-weight: bold; font-style: italic\">Pages</option>\n";
						foreach($page_arr as $k=>$v)
						{
							if(strlen($v["name"]) > 40)
								$v["name"] = substr($v["name"], 0, 40)."...";

							$selected = strstr($sl_options["facebook_post_page_id"], $v["page_id"]) ? "SELECTED" : "";
							//$selected = $v["page_id"] == $sl_options["facebook_post_page_id"] ? "SELECTED" : "";
							print "<option value=\"{$v["page_id"]}-p\" $selected>{$v["name"]}</option>\n";
						}
					}

					if(is_array($group_arr))
					{
						print "<option value=\"\" style=\"font-weight: bold; font-style: italic\">Groups</option>\n";
						foreach($group_arr as $k=>$v)
						{
							if(strlen($v["name"]) > 40)
								$v["name"] = substr($v["name"], 0, 40)."...";

							$selected = strstr($sl_options["facebook_post_page_id"],$v["gid"]) ? "SELECTED" : "";
							//$selected = $v["gid"] == $sl_options["facebook_post_page_id"] ? "SELECTED" : "";
							print "<option value=\"{$v["gid"]}-g\" $selected>{$v["name"]}</option>\n";
						}
					}
				?>
				</select>
		</td>
	</tr>
<?php		endif; ?>

	<tr valign="top">
		<th scope="row">Add Status Update to the Wall</th>
		<td>
			<input type="checkbox" name="socialite[facebook_new_post_active]" value=1 <?= $sl_options["facebook_new_post_active"] == 1 ? "CHECKED" : ""; ?> />
			Update the Status on the Wall When a Post is Created?<br />
			Wall Post Format <i>(use #title# for the page title, #link# for the permalink)</i>
			<br />
			<input type="text" name="socialite[facebook_new_post_text]" size=40 value="<?= htmlentities($sl_options["facebook_new_post_text"]); ?>" />

			<br /><br />

			<input type="checkbox" name="socialite[facebook_edit_post_active]" value=1 <?= $sl_options["facebook_edit_post_active"] == 1 ? "CHECKED" : ""; ?> />
			Update the Status on the Wall When a Post is Edited?<br />
			Wall Post Format <i>(use #title# for the page title, #link# for the permalink)</i>
			<br />
			<input type="text" name="socialite[facebook_edit_post_text]" size=40 value="<?= htmlentities($sl_options["facebook_edit_post_text"]); ?>" />
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Add Links to the Wall</th>
		<td>
			<input type="checkbox" name="socialite[facebook_new_link_active]" value=1 <?= $sl_options["facebook_new_link_active"] == 1 ? "CHECKED" : ""; ?> />
			Add a Link to the Wall When a Post is Created?<br />
			Link Text Format <i>(use #title# for the page title, #link# for the permalink)</i>
			<br />
			<input type="text" name="socialite[facebook_new_link_text]" size=40 value="<?= htmlentities($sl_options["facebook_new_link_text"]); ?>" />

			<br /><br />

			<input type="checkbox" name="socialite[facebook_edit_link_active]" value=1 <?= $sl_options["facebook_edit_link_active"] == 1 ? "CHECKED" : ""; ?> />
			Add a Link to the Wall When a Post is Edited?<br />
			Link Text Format <i>(use #title# for the page title, #link# for the permalink)</i>
			<br />
			<input type="text" name="socialite[facebook_edit_link_text]" size=40 value="<?= htmlentities($sl_options["facebook_edit_link_text"]); ?>" />
		
		</td>
	</tr>
	<tr valign="top">
		<th scope="row">Shorten URLs?</th>
		<td>
			<input type="checkbox" name="socialite[facebook_short_url]" value="1" <?= $sl_options["facebook_short_url"] == 1 ? "CHECKED" : ""; ?> />
			<i>
				Only takes effect if the #link# tag is used in a template above<br />
				<strong>NOTE:</strong> If you are having problems posting to Facebook, uncheck this option or change the URL Shortening service
				you selected above. Facebook blocks some of the URL Shortening services, preventing Socialite from posting to your Wall.
			</i>
		</td>
	</tr>
	</table>