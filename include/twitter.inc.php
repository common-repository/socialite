<?php
/**
 * @package	Socialite
 * @author	Ryan Gilfether <ryan@gilfether.com>
 * @license	http://www.gnu.org/copyleft/gpl.html GPL V3
 * @version	0.1 Beta
 * @link	http://www.gilfether.com/socialite
 * @date	March 7, 2009
 */

include_once(dirname(__FILE__)."/http.inc.php");

class SL_Twitter
{
	const TWITTER_HOST = "twitter.com";
	const TWITTER_MAX_LEN = 140;
	const TWITTER_SOURCE = "socialiteawordpressplugin";
	const TWITTER_FOLLOW_USER = "collegepages";

	private $sl_obj = null;
	private $enc_login = "";

	public function __construct(&$socialite)
	{
		$this->sl_obj = &$socialite;
		$this->enc_login = self::GetLoginEncoded();
	}


	/*
	 * Sends the published or edited post to Twitter via it's update API
	 */
	public function SendUpdate($publish_opts)
	{
		$update_uri = "/statuses/update.xml?source=".self::TWITTER_SOURCE;
		$wp_post = $this->sl_obj->GetWPPost();
		$post_link = $wp_post->permalink;
		$post_title = $wp_post->post_title;
		$twitter_status = "";
		$insert_link = false;

		// a newly published post, either immediate or is future published
		if($this->sl_obj->GetOptions("twitter_new_post_active") == 1 && ($publish_opts['publish'] == 'Publish' || $publish_opts['publish_future'] === true))
		{
			$twitter_status = $this->sl_obj->GetOptions("twitter_new_post_text");
		}
		else if($this->sl_obj->GetOptions("twitter_edit_post_active") == 1 && ($publish_opts['original_post_status'] == 'publish' || $publish_opts['prev_status'] == 'publish'))
		{
			// original_post_status - wp 2.7, prev_status- pre wp 2.7
			// if an already published post is updated
			$twitter_status = $this->sl_obj->GetOptions("twitter_edit_post_text");
		}
		else // this was some other status, ignore
			return false;

		// if the user placed a link in the twitter status text, then lets
		// check if we should shorten the URL
		if(preg_match("@\#link\#@i", $twitter_status))
		{
			if($this->sl_obj->GetOptions("twitter_short_url"))
				$post_link = $this->sl_obj->ShortenPermalink($post_link);

			$insert_link = true;
		}

		// before inserting the post title and the post link, we need to determine if this post is
		// longer than what is permitted by twitter
		// strip out the placeholders and determine the text length
		// then if a link is included, add the length of the link
		// now we can determine if we should trucate the post title or not
		$tmp = preg_replace("@\#.+?\#@i", "", $twitter_status);
		$len = strlen($tmp);
		if($insert_link)
			$len += strlen($post_link);
		$len += strlen($post_title);

		// if the over all length is longer than twitter allows, truncate the post title
		if($len > self::TWITTER_MAX_LEN)
		{
			// find the text length difference, be sure to add 2 to account for the '..'
			$diff = $len - self::TWITTER_MAX_LEN + 2;
			$post_title = substr($post_title, 0, strlen($post_title)-$diff)."..";
		}

		// setup the status to send to twitter
		$twitter_status = str_replace('#title#', $post_title, $twitter_status);
		$twitter_status = str_replace('#link#', $post_link, $twitter_status);

		// Add the twitter login to the additional headers to be posted
		$headers = array("Authorization" => "Basic {$this->enc_login}");

		// ensure to encode $status for sending via http
		$data  = "source=".urlencode(self::TWITTER_SOURCE)."&";
		$data .= "status=".urlencode($twitter_status);

		// POST the status to Twitter
		$resp = @SL_Http::PostURL(self::TWITTER_HOST, $update_uri, $data, $headers);
		return $resp["data"];
	}


	/*
	 * Follow me on Twitter. This only gets called once so as not to annoy the user
	 */
	public function AddFriend()
	{
		$opts = $this->sl_obj->GetOptions();

		if($opts["twitter_added_friend"] == 1)
			return true;

		if($opts["twitter_added_friend"] != 1 && $opts["twitter_login"] != "" && $opts["twitter_password"] != "")
		{
			$friend_uri = "/friendships/create/".self::TWITTER_FOLLOW_USER.".xml";

			// Add the twitter login to the additional headers to be posted
			$headers = array("Authorization" => "Basic {$this->enc_login}");

			// POST the status to Twitter
			$resp = @SL_Http::PostURL(self::TWITTER_HOST, $friend_uri, "", $headers);
			return $resp["data"];
		}

		return false;
	}


	/*
	 * Get the Twitter Login and encode it as required by the Twitter API
	 */
	private function GetLoginEncoded()
	{
		$user = $this->sl_obj->GetOptions("twitter_login");
		$pass = $this->sl_obj->GetOptions("twitter_password");

		if($user == "" || $pass == "")
			return false;

		return base64_encode("$user:$pass");
	}
}
?>