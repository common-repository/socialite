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
include_once(dirname(__FILE__)."/short-url.inc.php");
include_once(dirname(__FILE__)."/facebook.inc.php");
include_once(dirname(__FILE__)."/twitter.inc.php");


/*
 * Handles basic operations on Socialite Data
 */
class Socialite
{
	const WP_SOCIALITE_OPTIONS = "socialite_options";

	protected $options = "";
	protected $wp_post = "";
	protected $wp_short_permalink = "";

	public function __construct()
	{
		// initialize and store the options
		$this->GetOptions();
	}


	/*
	 * Get a specific Socialite option if $key is set or return
	 * all the Socialite Options
	 */
	public function GetOptions($key = "")
	{
		// get the Socialite Specific options
		$this->options = get_option(self::WP_SOCIALITE_OPTIONS);

		// if no value is returned, there was a problem
		if($this->options == "")
			return false;

		// now unserialze the options to get the array of values
		$this->options = unserialize($this->options);

		// if for some reason, we did not get an array, there was a problem
		if(!is_array($this->options))
			return false;

		// if a specific option was requested, return just that option
		if($key != "")
			return $this->options[$key];

		$this->options = self::ArrayMapMD("stripslashes", $this->options);

		// else return the entire array of options
		return $this->options;
	}


	/*
	 * Save the Socialite Management options to the wp_options table
	 */
	public function SaveOptions($opts)
	{
		$opts = self::ArrayMapMD("stripslashes", $opts);

		// options are stored serialized into the wp_options table
		$val = serialize($opts);

		if(get_option(self::WP_SOCIALITE_OPTIONS))
			update_option(self::WP_SOCIALITE_OPTIONS, $val);
		else
			add_option(self::WP_SOCIALITE_OPTIONS, $val, '', 'no');

		// grab the options we just saved
		$this->GetOptions();
	}


	/*
	 * Resets a value of an option
	 * If no value is given, then the option is deleted, effectively resetting it
	 */
	public function ResetOption($key, $val = "")
	{
		$opts = $this->GetOptions();

		if($val == "")
			unset($opts[$key]);
		else
			$opts[$key] = $val;

		$this->SaveOptions($opts);
	}


	/*
	 * Get the Wordpress Post from it's post_id
	 */
	public function GetWPPost($post_id = "")
	{
		if(is_numeric($post_id))
			$this->SetWPPost(get_post($post_id));

		return $this->wp_post;
	}


	/*
	 * 03/12/09 - We need this function because when GetWPPost() is called from within the
	 * wordpress add_action('future_to_post'), GetWPPost() does not receive the $post_id
	 * passed to it from add_action('future_to_post')
	 *
	 * 03/12/09 - It now handles setting post_content formatting and post_excerpt
	 */
	public function SetWPPost($wp_post)
	{
		$this->wp_post = $wp_post;

		// add the wordpress formatting to the post
		$this->wp_post->post_content_formatted = self::AddContentFormatting($this->wp_post->post_content);

		// if a post excerpt was not given manually by the author, then create one
		if($this->wp_post->post_excerpt == "")
			$this->wp_post->post_excerpt = self::CreateExcerpt($this->wp_post->post_content);

		// add the permalink to the post
		$this->wp_post->permalink = get_permalink($this->wp_post->ID);
	}


	/*
	 * Shorten the Wordpress Post Permalink, once it is shorten, we save
	 * the shortened URL so we don't have to keep doing it.
	 */
	public function ShortenPermalink($link)
	{
		// if the permalink hasn't been shortened yet, lets do it, then save it
		if($this->wp_short_permalink == "")
		{
			$short_url = $this->GetOptions("short_url_service");

			if(strtolower($short_url) == "bit.ly")
				$this->wp_short_permalink = SL_ShortURL::Bitly($link, $this->options["bitly_login"], $this->options["bitly_api_key"]);
			else if(strtolower($short_url) == "tinyurl.com")
				$this->wp_short_permalink = SL_ShortURL::Tinyurl($link);
			else if(strtolower($short_url) == "ur.ly")
				$this->wp_short_permalink = SL_ShortURL::URly($link);
			else if(strtolower($short_url) == "is.gd")
				$this->wp_short_permalink = SL_ShortURL::ISgd($link);
			else // if(strtolower($shorten_method) == "zz.gd") // DEFAULT
				$this->wp_short_permalink = SL_ShortURL::ZZgd($link);
		}

		return $this->wp_short_permalink;
	}


	/*
	 * Runs array_map on multi-dimensional arrays
	 */
	public function ArrayMapMD($function, $arr)
	{
		if(!is_array($arr))
			return false;
			
		$new_array = array();
		
		foreach($arr as $key=>$val)
		{
			$new_array[$key] = is_array($val) ? array_map($function, $val) : $function($val);
		}
		
		return $new_array;
	}

	/*
	 * Convert an xml string into an array
	 * Taken from: http://www.bytemycode.com/snippets/snippet/445/
	 */
	public function XML2Array($xml)
	{
        $xmlary = array();
               
        $reels = '/<(\w+)\s*([^\/>]*)\s*(?:\/>|>(.*)<\/\s*\\1\s*>)/s';
        $reattrs = '/(\w+)=(?:"|\')([^"\']*)(:?"|\')/';

        preg_match_all($reels, $xml, $elements);

        foreach ($elements[1] as $ie => $xx) {
                $xmlary[$ie]["name"] = $elements[1][$ie];
               
                if ($attributes = trim($elements[2][$ie])) {
                        preg_match_all($reattrs, $attributes, $att);
                        foreach ($att[1] as $ia => $xx)
                                $xmlary[$ie]["attributes"][$att[1][$ia]] = $att[2][$ia];
                }

                $cdend = strpos($elements[3][$ie], "<");
                if ($cdend > 0) {
                        $xmlary[$ie]["text"] = substr($elements[3][$ie], 0, $cdend - 1);
                }

                if (preg_match($reels, $elements[3][$ie]))
                        $xmlary[$ie]["elements"] = self::XML2Array($elements[3][$ie]);
                else if ($elements[3][$ie]) {
                        $xmlary[$ie]["text"] = $elements[3][$ie];
                }
        }

        return $xmlary;
	}


	/*
	 * Excerpt code take from wp-includes/formatting.php
	 */
	private function CreateExcerpt($text)
	{
		$text = strip_shortcodes( $text );

		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);
		$text = strip_tags($text);
		$excerpt_length = apply_filters('excerpt_length', 55);
		$words = explode(' ', $text, $excerpt_length + 1);
		if (count($words) > $excerpt_length) {
			array_pop($words);
			array_push($words, '[...]');
			$text = implode(' ', $words);
		}

		return $text;
	}


	/*
	 * Adds the Wordpress supplied formatting to the post, as returned by
	 * the Wordpress function the_content()
	 */
	private function AddContentFormatting($text)
	{
		$text = apply_filters('the_content', $text);
		$text = str_replace(']]>', ']]&gt;', $text);

		return $text;
	}
}
?>