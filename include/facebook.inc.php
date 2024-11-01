<?php
/**
 * @package	Socialite
 * @author	Ryan Gilfether <ryan@gilfether.com>
 * @license	http://www.gnu.org/copyleft/gpl.html GPL V3
 * @version	0.1 Beta
 * @link	http://www.gilfether.com/socialite
 * @date	March 7, 2009
 */

include_once(dirname(__FILE__)."/facebook-platform/php/facebook.php");
include_once(dirname(__FILE__)."/facebook-platform/php/facebook_desktop.php");
include_once(dirname(__FILE__)."/http.inc.php");

class SL_Facebook
{
	/*
	 * we should be allowed to distribute secret keys for apps like Socialite
	 * read http://wiki.developers.facebook.com/index.php/Open_Source_Applications_Terms_Of_Service_Problem
	 */
	const FB_APP_ID = "56181454677";
	const FB_API_KEY = "b5b0dd8f263d746a5ccdea9da03cb56f";
	const FB_SECRET_KEY = "c7966de0c2aeb637c637131981f18a77";

	private $sl_obj = null;
	private $fb_client = null;
	private $fb_user_info = "";


	public function __construct(&$socialite)
	{
		$this->sl_obj = &$socialite;
		$this->fb_client = new FacebookDesktop(self::FB_API_KEY, self::FB_SECRET_KEY);

		// we need to manually set the permanent $session keys we are given when the user
		// goes through the Socialite Facebook registration
		$sess = $this->sl_obj->GetOptions("facebook_session");
		if(is_array($sess))
		{
			$this->fb_client->api_client->session_key = $sess["session_key"];
			$this->fb_client->api_client->secret = $sess["secret"];
		}
	}

	/*
	 * Publish the wall post and/or the link to a facebook profile
	 */
	public function Publish($publish_opts)
	{
		try
		{
			$wp_post = $this->sl_obj->GetWPPost();
			$post_link = $wp_post->permalink;
			$post_title = $wp_post->post_title;

			if($this->sl_obj->GetOptions("facebook_short_url"))
				$post_link = $this->sl_obj->ShortenPermalink($post_link);


			// a newly published post, either immediate or is future published
			if($publish_opts['publish'] == 'Publish' || $publish_opts['publish_future'] === true)
			{
				// if active, post to wall
				if($this->sl_obj->GetOptions("facebook_new_post_active") == 1)
				{ 
					// setup the status to send to facebook
					$fb_post = $this->sl_obj->GetOptions("facebook_new_post_text");
					$fb_post = str_replace('#title#', $post_title, $fb_post);
					$fb_post = str_replace('#link#', $post_link, $fb_post);

					self::SetStatus($fb_post);
				}

				// if active, post a link to profile
				if($this->sl_obj->GetOptions("facebook_new_link_active") == 1)
				{
					// setup the link to post
					$fb_post = $this->sl_obj->GetOptions("facebook_new_link_text");
					$fb_post = str_replace('#title#', $post_title, $fb_post);
					$fb_post = str_replace('#link#', $post_link, $fb_post);
					self::AddLink($post_link, $fb_post);
				}
			}
			else if($publish_opts['original_post_status'] == 'publish' || $publish_opts['prev_status'] == 'publish')
			{
				// editing an existing published post. original_post_status - wp 2.7, prev_status- pre wp 2.7

				if($this->sl_obj->GetOptions("facebook_edit_post_active") == 1)
				{
					// setup the status to send to facebook
					$fb_post = $this->sl_obj->GetOptions("facebook_edit_post_text");
					$fb_post = str_replace('#title#', $post_title, $fb_post);
					$fb_post = str_replace('#link#', $post_link, $fb_post);

					self::SetStatus($fb_post);
				}

				// if active, post a link to profile
				if($this->sl_obj->GetOptions("facebook_edit_link_active") == 1)
				{
					// setup the link to post
					$fb_post = $this->sl_obj->GetOptions("facebook_edit_link_text");
					$fb_post = str_replace('#title#', $post_title, $fb_post);
					$fb_post = str_replace('#link#', $post_link, $fb_post);
					self::AddLink($post_link, $fb_post);
				}
			}
			else // this was some other status, ignore
				return false;

		}
		catch(Exception $e)
		{

		}

		return false;
	}

	/*
	 * Get the logged in Facebook User's account info, used for display in the Socialite
	 * Configuration Admin page
	 */
	public function GetUserInfo()
	{
		try
		{
			if($this->fb_user_info == "")
			{
				$sess = $this->sl_obj->GetOptions("facebook_session");
				$uid = array($sess["uid"]);
				$fields = array('uid','first_name','last_name','name','locale',
						'affiliations','pic_square','profile_url');

				$this->fb_user_info = $this->fb_client->api_client->users_getInfo($uid, $fields);
			}

			return $this->fb_user_info[0];
		}
		catch(Exception $e)
		{

		}

		return false;
	}


	/*
	 * Updates the user's status to notify that they just added Socialite.
	 * Do this only when the user saves or changes their login information.
	 */
	public function SetStatus($status = "")
	{
		try
		{
			$has_perm = $this->fb_client->api_client->users_hasAppPermission('status_update');
			if($has_perm)
			{
				if($status != "")
				{
					$page_id = $this->sl_obj->GetOptions("facebook_post_page_id");

					if(!($page_id > 0)) // post to user's page
						$res = $this->fb_client->api_client->stream_publish($status);
					else // post to a fan page
						$res = $this->fb_client->api_client->stream_publish($status, null, null, null, $page_id);
				}
				return true;
			}
		}
		catch(Exception $e)
		{

		}

		return false;
	}


	/*
	 * Adds a link to college-pages.com. Do this only once so we don't annoy the user.
	 * Called when we save the user's login information for the first time
	 */
	public function AddLink($url = "", $txt = "")
	{
		try
		{
			$wp_post = $this->sl_obj->GetWPPost();
			$has_perm = $this->fb_client->api_client->users_hasAppPermission('share_item');

			if($has_perm && $url != "")
			{
				$page_id = $this->sl_obj->GetOptions("facebook_post_page_id");
				$user_info = $this->GetUserInfo();

				// remove the http(s):// from the beginning of the domain
				$domain = preg_replace("@^https?\://@i", "", get_bloginfo("url"));

				// remove the trailing [...] placed at the end of an excerpt by WP
				// remove any htmlentities that may for what ever reason be in the excerpt
				$description = preg_replace("/\[\.+?\]$/", "...", $wp_post->post_excerpt);
				$description = preg_replace("/\&.+?;/", " ", $description);

				$action_link = array(array('text' => $domain, 'href' => $url));
				$attachment = array(
					"name" => $wp_post->post_title,
					"href" => $url,
					"caption" => "Source: $domain",
					"description" => $description
				);

				$id_parts = explode("-", $page_id);

				if(!($page_id > 0)) // post to user's page
					$res = $this->fb_client->api_client->stream_publish($txt, $attachment, $action_link);
				else if($id_parts[1] == "g") // post to a group
					$res = $this->fb_client->api_client->stream_publish($txt, $attachment, $action_link, $id_parts[0]);
				else // post link to fan page
					$res = $this->fb_client->api_client->stream_publish($txt, $attachment, $action_link, null, $id_parts[0]/*$page_id*/);

				return true;
			}
		}
		catch(Exception $e)
		{
			
		}

		return false;
	}


	/*
	 * Get the authorization session settings, used when saving a new login for
	 * the first time
	 */
	public function GetAuthSession($opts)
	{
		try
		{
			$ret = $this->fb_client->api_client->auth_getSession($opts["facebook_auth_token"]);
			return $ret;
		}
		catch(Exception $e)
		{

		}

		return false;
	}


	public function ResetLogin()
	{
		try
		{
			// for the user's page
			$this->fb_client->api_client->auth_revokeExtendedPermission("publish_stream");
			$this->fb_client->api_client->auth_revokeAuthorization();

			// for the fan page the user had given permission to
			$page_id = $this->sl_obj->GetOptions("facebook_post_page_id");
			if($page_id != "")
			{
				$this->fb_client->api_client->auth_revokeExtendedPermission("publish_stream", $page_id);
				$this->fb_client->api_client->auth_revokeAuthorization($page_id);
			}

			//$this->fb_client->api_client->auth_expireSession();
		}
		catch(Exception $e)
		{

		}

		$this->sl_obj->ResetOption("facebook_enabled", 0);
		$this->sl_obj->ResetOption("facebook_session");
		$this->sl_obj->ResetOption("facebook_auth_token");
	}


	public function SaveLogin()
	{
		$opts = $this->sl_obj->GetOptions();

		// if the One Time Code (Authentication Token) is set, lets grab the
		// facebook login session and store it, allowing us to re-use it indefinitly
		if($opts["facebook_auth_token"] != "" && !is_array($opts["facebook_session"]))
		{
			$opts["facebook_session"] = $this->GetAuthSession($opts);

			// if we haven't yet set the facebook status, lets do it. we set the status
			// so it only happens once and we don't spam the user all the time with it
			if($opts["facebook_set_status"] != 1)
			{
				// we have to create a new instance of SL_Facebook because
				// we just updated options
				$this->SetStatus("Added the Socialite Wordpress Plugin for Facebook");
				$opts["facebook_set_status"] = 1;
			}
		}

		$this->sl_obj->SaveOptions($opts);
	}


	/*
	 * Get all pages a user is an admin of, this will allow them to post to these pages
	 */
	public function GetAdminPagesForUser()
	{
		$ui = $this->GetUserInfo();

		// get the list of pages a user is an admin of
		$query = "SELECT page_id FROM page_admin  WHERE uid={$ui["uid"]}";
		$result = $this->fb_client->api_client->fql_query($query);

		if(!is_array($result))
			return false;

		// now get the page info for each of the pages queried above
		$query = "SELECT page_id,name FROM page WHERE ";
		foreach($result as $k=>$v)
			$query .= "page_id=".$v["page_id"]." OR ";

		$query  = preg_replace("/ OR $/i", "", $query);
		$query .= " ORDER BY name ASC";

		$result = $this->fb_client->api_client->fql_query($query);

		if(!is_array($result))
			return false;

		return $result;
	}

	public function GetGroupsForUser()
	{
		$ui = $this->GetUserInfo();

		// get the list of pages a user is an admin of
		$query  = "SELECT gid,name FROM group WHERE gid IN (SELECT gid FROM group_member ";
		$query .= "WHERE uid={$ui["uid"]}) ORDER BY name ASC";

		$result = $this->fb_client->api_client->fql_query($query);

		if(!is_array($result))
			return false;

		return $result;
	}
}
?>