<?php
/**
 * @package	Socialite
 * @author	Ryan Gilfether <ryan@gilfether.com>
 * @license	http://www.gnu.org/copyleft/gpl.html GPL V3
 * @version	0.1 Beta
 * @link	http://www.gilfether.com/socialite
 * @date	March 7, 2009
 */


include_once(dirname(__FILE__)."/http-curl.inc.php");

/*
 * MySpace does not have an API (including their REST api) that interacts with the
 * MySpace profile blog. So we have to do this the old school way, using CURL
 */

class SL_MySpace
{
	const MS_TITLE_MAX_LEN = 95; // set by myspace

	private $sl_obj = null;
	private $http = null;


	public function __construct(&$socialite)
	{
		$this->sl_obj = &$socialite;
		$this->http = new SL_HttpCURL();
	}


	/*
	 * Sends the published or edited post to Facebook
	 *
	 * NOTE: $wp_post->post_content_formatted is created inside of Socialite::SetWPPost()
	 * and is not part of the original wp_post object created by Wordpress
	 */
	public function PublishPost($publish_opts)
	{
		$wp_post = $this->sl_obj->GetWPPost();
		$body = "";
		$subject = "";

		// a newly published post, either immediate or is future published
		if($this->sl_obj->GetOptions("myspace_new_post_active") == 1 && ($publish_opts['publish'] == 'Publish' || $publish_opts['publish_future'] === true))
		{
			// apply title formatting to published post
			$subject = $this->sl_obj->GetOptions("myspace_new_post_title");
			$subject = self::ApplyTemplate($subject, $wp_post);

			// apply content body formatting to published post
			$body = $this->sl_obj->GetOptions("myspace_new_post_body");
			$body = self::ApplyTemplate($body, $wp_post);
		}
		else if($this->sl_obj->GetOptions("myspace_edit_post_active") == 1 && ($publish_opts['original_post_status'] == 'publish' || $publish_opts['prev_status'] == 'publish'))
		{
			// editing an existing published post
			// original_post_status - wp 2.7, prev_status- pre wp 2.7

			// apply title formatting to published post
			$subject = $this->sl_obj->GetOptions("myspace_edit_post_title");
			$subject = self::ApplyTemplate($subject, $wp_post);

			// apply content body formatting to published post
			$body = $this->sl_obj->GetOptions("myspace_edit_post_body");
			$body = self::ApplyTemplate($body, $wp_post);
		}
		else // this was some other status, ignore
			return false;

		$body .= "\n\n<p style=\"font-size: 8px\">Published with <a href=\"http://www.gilfether.com/socialite\" target=\"_blank\" style=\"font-size: 8px\">Socialite. A Wordpress Plugin.</a></p>\n";

		// log into myspace
		$html = $this->Login();

		// we need to post preview because there are hidden inputs we need to grab
		$html = $this->PostPreview($subject, $body);

		$form_url = "http://blogs.myspace.com/index.cfm?fuseaction=blog.create&editor=False";
		$form_post_url = "http://blogs.myspace.com/index.cfm?fuseaction=blog.create&editor=False";

		// find the form named aspnetForm
		// <form name="aspnetForm" method="post" action="/index.cfm?fuseaction=blog.create&amp;editor=False" onsubmit="javascript:return WebForm_OnSubmit();" id="aspnetForm">
		$regex = "<form name=\"aspnetForm\".+?action=\".+?fuseaction=blog.create.+?\".+?>(.+?)<\/form>";
		$match = array();
		preg_match_all("/$regex/si", $html, $match, PREG_SET_ORDER);

		// save the form portion of html
		$form_html = $match[0][1];

		// now grab all the hidden fields that have values
		$match = array();
		$regex = "<input\s+type=\"hidden\"\s+name=\"(.+?)\".+?value=\"(.*?)\".*?\/>";
		preg_match_all("/$regex/si", $form_html, $match, PREG_SET_ORDER);

		// save the hidden fields
		$hidden_fields = array();
		foreach($match as $k=>$v)
			$hidden_fields[$v[1]] = $v[2];

		$form_fields = array (
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdDate" => date("M j, Y g:i A"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdSubject" => $subject,
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdBody" => $body,
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdViewingPrivacy" => $this->sl_obj->GetOptions("myspace_privacy"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdProhibitComments" => $this->sl_obj->GetOptions("myspace_allow_comments") == 1 ? "False" : "True",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdCategoryId" => $this->sl_obj->GetOptions("myspace_category"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdMoodSetID" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdMoodId" => "0",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdMoodOther" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdEnclosure" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$btnSubmit2" => "Post"
		);

		// if comments are not allowed, we need to set an empty value to the var below
		if($this->sl_obj->GetOptions("myspace_allow_comments") != 1)
			$form_fields["ctl00\$ctl00\$cpMain\$PostEditorV2\$chkCommentsClosed"] = "";

		// now merge the hidden input fields with values with the rest of the fields
		$form_fields = array_merge($hidden_fields, $form_fields);

 		return @$this->http->PostURL($form_post_url, $form_fields);
	}


	/*
	 * Before posting the Wordpress post, MySpace makes the user preview the post, we need to post
	 * to this preview page, and then capture all the hidden input variables needed to continue
	 * on with posting to the MySpace blog
	 */
	private function PostPreview($subject, $body)
	{
		$form_url = "http://blogs.myspace.com/index.cfm?fuseaction=blog.create&editor=False";
		$form_post_url = "http://blogs.myspace.com/index.cfm?fuseaction=blog.create&editor=False";

		$form_fields = array (
			"ddMonth" => date("n"),
			"ddDay" => date("j"),
			"ddYear" => date("Y"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$ddlHour" => date("g"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$ddlMinute" => date("i"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$ddlAmPm" => date("A"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$txbTitle" => $subject,
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$ddlCategories" => $this->sl_obj->GetOptions("myspace_category"),
			"body" => $body,
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$ddlAmazon" => "Music",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$ddlMoods" => "0",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$txtMoodOther" => "",
			//"ctl00\$ctl00\$cpMain\$PostEditorV2\$chkCommentsClosed" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$rbnViewingPrivacy" => $this->sl_obj->GetOptions("myspace_privacy"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$tbxPodcast" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$btnPreview" => "Preview & Post",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdDate" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdSubject" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdBody" => $body,
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdViewingPrivacy" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdProhibitComments" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdCategoryId" => $this->sl_obj->GetOptions("myspace_category"),
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdMoodSetID" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdMoodId" => 0,
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdMoodOther" => "",
			"ctl00\$ctl00\$cpMain\$PostEditorV2\$hdEnclosure" => ""
		);

		// if comments are not allowed, we need to set an empty value to the var below
		if($this->sl_obj->GetOptions("myspace_allow_comments") != 1)
			$form_fields["ctl00\$ctl00\$cpMain\$PostEditorV2\$chkCommentsClosed"] = "";

		// get the html from the form so we can parse out values we need
		// we must do a POST instead of GET to get this form, else myspace gives us an error
		$html = @$this->http->PostURL($form_url);

		// find the form named aspnetForm
		// <form name="aspnetForm" method="post" action="/index.cfm?fuseaction=blog.create&amp;editor=False" onsubmit="javascript:return WebForm_OnSubmit();" id="aspnetForm">
		$regex = "<form name=\"aspnetForm\".+?action=\".+?fuseaction=blog.create.+?\".+?>(.+?)<\/form>";
		$match = array();
		preg_match_all("/$regex/si", $html, $match, PREG_SET_ORDER);

		// save the form portion of html
		$form_html = $match[0][1];

		// now grab all the hidden fields that have values
		$match = array();
		$regex = "<input\s+type=\"hidden\"\s+name=\"(.+?)\".+?value=\"(.*?)\".*?\/>";
		preg_match_all("/$regex/si", $form_html, $match, PREG_SET_ORDER);

		// save the hidden fields
		$hidden_fields = array();
		foreach($match as $k=>$v)
			$hidden_fields[$v[1]] = $v[2];

		// now merge the hidden input fields with values with the rest of the fields
		$form_fields = array_merge($hidden_fields, $form_fields);

		return @$this->http->PostURL($form_post_url, $form_fields);
	}


	/*
	 * Login to MySpace, this allows all the necessary cookies to be set
	 */
	private function LogIn()
	{
		$form_url = "http://www.myspace.com";
		$form_post_url = "https://secure.myspace.com/index.cfm?fuseaction=login.process";
		$form_fields = array(
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$Email_Textbox" => $this->sl_obj->GetOptions("myspace_login"),
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$Password_Textbox" => $this->sl_obj->GetOptions("myspace_password"),
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$Remember_Checkbox" => "on",// Remember Me checkbox
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$SingleSignOnHash" => "",	// hidden input, no value
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$SingleSignOnRequestUri" => "",// hidden input, no value
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$nexturl" => "",			// hidden input, no value
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$apikey" => "",				// hidden input, no value
			"ctl00\$ctl00\$cpMain\$cpMain\$LoginBox\$ContainerPage" => ""		// hidden input, no value
		);


		// first, get the page with the login form
		$html = @$this->http->GetURL($form_url);

		// find the form named aspnetForm
		// <form action="https://secure.myspace.com/index.cfm?fuseaction=login.process" method="post" id="LoginForm" name="aspnetForm">
		$regex = "<form action=\".+?fuseaction=login.process\".+?name=\"aspnetForm\".+?>(.+?)<\/form>";
		$match = array();
		preg_match_all("/$regex/si", $html, $match, PREG_SET_ORDER);

		// save the form portion of html
		$form_html = $match[0][1];

		// now grab all the hidden fields that have values
		$match = array();
		$regex = "<input\s+type=\"hidden\"\s+name=\"(.+?)\".+?value=\"(.*?)\".*?\/>";
		preg_match_all("/$regex/si", $form_html, $match, PREG_SET_ORDER);

		// save them
		$hidden_fields = array();
		foreach($match as $k=>$v)
			$hidden_fields[$v[1]] = $v[2];

		// now merge the hidden input fields with values with the rest of the fields
		$form_fields = array_merge($hidden_fields, $form_fields);

		// do the login
		return @$this->http->PostURL($form_post_url, $form_fields);
	}


	/*
	 * Applies the appropriate field data to the given template
	 * Template parameters accepted are:
	 * #title# - The wordpress post title
	 * #link# - The wordpress permalink
	 * #excerpt# - The wordpress post excerpt
	 * #content# - The wordpress post content, with formatting
	 */
	function ApplyTemplate($template, $wp_post)
	{
		$link = $wp_post->permalink;
		if($this->sl_obj->GetOptions("myspace_short_url"))
			$link = $this->sl_obj->ShortenPermalink($link);

		$template = str_replace('#title#', $wp_post->post_title, $template);
		$template = str_replace('#link#', $link, $template);
		$template = str_replace('#excerpt#', $wp_post->post_excerpt, $template);
		$template = str_replace('#content#', $wp_post->post_content_formatted, $template);

		return $template;
	}
}
?>