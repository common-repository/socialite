<?php
/*
Plugin Name: Socialite
Plugin URI: http://www.gilfether.com/socialite
Description: Publishes your Wordpress posts to Twitter, Facebook, and Myspace. <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=3944609" target="_blank">Donate to help development of this plugin</a> | <a href="http://www.gilfether.com/socialite" target="_blank">Official Socialite Homepage</a>
Version: 0.2.5.2
Author: Ryan Gilfether <ryan@gilfether.com>
Author URI: http://www.gilfether.com

Copyright 2009  Ryan Gilfether  (email : ryan@gilfether.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// if a PHP vesion less than 5.0 is running, use a subset of the functions so the
// error message in socialite_manager.php is displayed
if(version_compare(PHP_VERSION, '5.0.0', '<'))
{
	/*
	* if needed, initializes the default values for socialite.
	* Displays the Socialite Manager page for configuring Socialite
	*/
	function socialite_options()
	{
		include(dirname(__FILE__)."/socialite_manager.php");
	}

	/*
	* sets up the menu link under 'Settings', and to call the socialite_options() function
	* when that link is clicked
	*/
	function socialite_menu()
	{
		add_options_page('Socialite Configuration', 'Socialite', 10, __FILE__, 'socialite_options');
	}

	add_action('admin_menu', 'socialite_menu');
}
else // we have a supported version of PHP
{
	include_once(dirname(__FILE__)."/include/socialite.inc.php");
	include_once(dirname(__FILE__)."/include/twitter.inc.php");
	include_once(dirname(__FILE__)."/include/facebook.inc.php");
	include_once(dirname(__FILE__)."/include/myspace.inc.php");

	$sl = new Socialite();

	/*
	* When a post is published immediately or is published into the future
	*/
	function socialite_post($post_id, $is_future = false)
	{
		global $sl;
		$publish_opts = array();

		$sl->SetWPPost(get_post($post_id));

		// set the type of publish this is, immediate publish, future publish, or editing old post
		// because posting into the future does not send the $_POST variable, we need to create
		// our own value to send to the functions below
		$is_future ? $publish_opts = array("publish_future"=>true) : $publish_opts = $_POST;

		// if Twitter is enabled
		if($sl->GetOptions("twitter_enabled") == "1")
		{
			$twit = new SL_Twitter($sl);
			$twit->SendUpdate($publish_opts);
			unset($twit);
		}

		// if Facebook is enabled
		if($sl->GetOptions("facebook_enabled") == "1" && is_array($sl->GetOptions("facebook_session")))
		{
			$fb = new SL_Facebook($sl);
			$fb->Publish($publish_opts);
			unset($fb);
		}

		// if MySpace is enabled
		if($sl->GetOptions("myspace_enabled") == "1")
		{
			$ms = new SL_MySpace($sl);
			$ms->PublishPost($publish_opts);
			unset($ms);
		}
	}


	/*
	* Called when a post is set to publish into the future and it's publish
	* date has arrived
	*/
	function socialite_post_future($post_id)
	{
		socialite_post($post_id, true);
	}


	/*
	* if needed, initializes the default values for socialite.
	* Displays the Socialite Manager page for configuring Socialite
	*/
	function socialite_options()
	{
		global $sl;

		// only check to see if socialite is initialized before viewing the
		// socialite_manager page, this avoids checks occuring all the time
		socialite_initialize();

		include(dirname(__FILE__)."/socialite_manager.php");
	}


	/*
	* Initializes the default values for socialite
	*/
	function socialite_initialize()
	{
		global $sl;

		if($sl->GetOptions("initialized") != '1')
		{
			$arr = array(
				"initialized" => 1,
				"short_url_service" => "ur.ly",
				"bit_ly_login" => "",
				"bit_ly_api_key" => "",
				"twitter_new_post_active" => 1,
				"twitter_new_post_text" => "Published: #title# @ #link#",
				"twitter_edit_post_active" => 1,
				"twitter_edit_post_text" => "Edited: #title# @ #link#",
				"twitter_added_friend" => 0,
				"twitter_short_url" => 1,
				"facebook_post_page_id" => "",
				"facebook_new_post_active" => 1,
				"facebook_edit_post_active" => 1,
				"facebook_new_post_text" => "Published: #title# @ #link#",
				"facebook_edit_post_text" => "Edited: #title# @ #link#",
				"facebook_new_link_active" => 0,
				"facebook_edit_link_active" => 0,
				"facebook_new_link_text" => "Published: #title# @ #link#",
				"facebook_edit_link_text" => "Edited: #title# @ #link#",
				"facebook_set_status" => 0,
				"facebook_short_url" => 0,
				"myspace_category" => 0,
				"myspace_allow_comments" => "",
				"myspace_privacy" => 0,
				"myspace_new_post_active" => 1,
				"myspace_new_post_title" => "Published: #title#",
				"myspace_new_post_body" => "<h1><a href=\"#link#\" target=\"_blank\">#title#</a></h1>\n#content#\n<p>\n<a href=\"#link#\" target=\"_blank\">View the original post</a> | <a href=\"".get_bloginfo("siteurl")."\" target=\"_blank\">Visit ".get_bloginfo("name")."</a>\n</p>",
				"myspace_edit_post_active" => 1,
				"myspace_edit_post_title" => "Edited: #title#",
				"myspace_edit_post_body" => "<h1><a href=\"#link#\" target=\"_blank\">#title#</a></h1>\n#excerpt#\n<p>\n<a href=\"#link#\" target=\"_blank\">Read the complete post</a> | <a href=\"".get_bloginfo("siteurl")."\" target=\"_blank\">Visit ".get_bloginfo("name")."</a>\n</p>", 
				"myspace_short_url" => 0
			);
			$sl->SaveOptions($arr);
		}
	}


	/*
	* sets up the menu link under 'Settings', and to call the socialite_options() function
	* when that link is clicked
	*/
	function socialite_menu()
	{
		add_options_page('Socialite Configuration', 'Socialite', 10, __FILE__, 'socialite_options');
	}


	// if we have requeste to reset the facebook auth key
	if(array_key_exists("facebook_reset_auth_token", $_POST))
	{
		$fb = new SL_Facebook($sl);
		$fb->ResetLogin();
		unset($fb);
	}
	else if($_POST['action'] == 'update' && $_POST["socialite"]["initialized"] == 1)
	{
		// else we are saving the Socialite options from socialite_manager.php
		$sl->SaveOptions($_POST["socialite"]);

		// save the facebook login options so we have a permanent login
		$fb = new SL_Facebook($sl);
		$fb->SaveLogin();
		unset($fb);

		// attempt to add us as a friend, the function will only do it once
		// and will ignore multiple attempts
		$twit = new SL_Twitter($sl);
		$twit->AddFriend();
		unset($twit);
	}



	// capture the wordpress actions
	add_action('publish_post', 'socialite_post',1,1); // should be fired only if a post is actually published/edited, but not just saved
	add_action('future_to_publish', 'socialite_post_future',1,1); // should be fired only if a future post is actually published
	add_action('admin_menu', 'socialite_menu');


	// initialize socialite when the plugin is activated
	// CAUSES AN ERROR, LEAVE COMMENTED!
	//register_activation_hook(__FILE__, 'socialite_initialize');
}
?>