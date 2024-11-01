<?php
/**
 * @package	Socialite
 * @author	Ryan Gilfether <ryan@gilfether.com>
 * @license	http://www.gnu.org/copyleft/gpl.html GPL V3
 * @version	0.1 Beta
 * @link	http://www.gilfether.com/socialite
 * @date	March 7, 2009
 */

class SL_HttpCURL
{
	const CURL_COOKIE = "socialite-cookies.txt";
	const CURL_USER_AGENT = "Socialite (http://www.gilfether.com/socialite)";
	//const CURL_USER_AGENT = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";

	protected $curl_handle = 0;
	protected $curl_referer = "";
	protected $curl_http_info = "";

	public function __construct()
	{
		$this->curl_handle = curl_init();
		curl_setopt($this->curl_handle, CURLOPT_HEADER, 0);
		curl_setopt($this->curl_handle, CURLOPT_RETURNTRANSFER, true); // allows us to save page contents into a variable
		curl_setopt($this->curl_handle, CURLOPT_USERAGENT, self::CURL_USER_AGENT);
		curl_setopt($this->curl_handle, CURLOPT_FOLLOWLOCATION, true); // allows curl to follow redirects
		curl_setopt($this->curl_handle, CURLOPT_REFERER, $this->curl_referer);

		if(is_dir("/tmp"))
			$cl = "/tmp/".self::CURL_COOKIE;
		else if($_ENV["TMPDIR"] != "")
			$cl = $_ENV["TMPDIR"]."/".self::CURL_COOKIE;
		else
			$cl = dirname(__FILE__)."/".self::CURL_COOKIE;

		$this->SetCookieLocation($cl);
	}


	public function __destruct()
	{
		$this->EndSession();
	}


	/**
	 * GET a URL
	 *
	 * @param string $url - The url to post to
	 * @param mixed $params - Either a string with the parameters in url format, or an array
	 *		where the key is the parameter name
	 * @return string The contents of the page posted to
	 */
	public function GetURL($url, $params = "")
	{
		if(is_array($params))
		{
			$get_vars = "";
			foreach($params as $k=>$v)
				$get_vars .= urlencode($k)."=".urlencode($v)."&";
			
			// strip off the trailing &
			$get_vars = substr($get_vars,0,-1);
			$url .= "?$get_vars";
		}
		else
			$url .= "?$params";

		$this->SetURL($url);
		
		curl_setopt($this->curl_handle, CURLOPT_HTTPGET, true);
		$html = curl_exec($this->curl_handle);
		$this->curl_http_info = curl_getinfo($this->curl_handle, CURLINFO_HTTP_CODE);
		
		return $html;
	}


	/**
	 * POST to a URL using the HTTP POST method
	 *
	 * @param string $url - The url to post to
	 * @param mixed $params - Either a string with the parameters in url format, or an array
	 *		where the key is the parameter name
	 * @return string The contents of the page requested
	 */
	public function PostURL($url, $params = "")
	{
		$this->SetURL($url);

		if(is_array($params))
		{
			$post_str = "";
			foreach($params as $k=>$v)
				$post_str .= urlencode($k)."=".urlencode($v)."&";
			
			// strip off the trailing &
			$post_str = substr($post_str,0,-1);
		}
		else
			$post_str = $params;

		curl_setopt($this->curl_handle, CURLOPT_POST, true);
		curl_setopt($this->curl_handle, CURLOPT_POSTFIELDS, $post_str);
		
		$html = curl_exec($this->curl_handle);
		$this->curl_http_info = curl_getinfo($this->curl_handle);

		return $html;
	}


	/**
	 * Closes and Ends the Curl Session
	 */
	public function EndSession()
	{
		if(is_resource($this->curl_handle))
			curl_close($this->curl_handle);
	}


	public function SetCookieLocation($cookie_loc)
	{
		curl_setopt($this->curl_handle, CURLOPT_COOKIEFILE, $cookie_loc);
		curl_setopt($this->curl_handle, CURLOPT_COOKIEJAR, $cookie_loc);
	}


	/*
	 * Sometimes we need to manually set the Referer
	 */
	public function SetReferer($ref)
	{
		$this->curl_referer = $url;
	}

	/*******************
	 * PRIVATE FUNCTIONS
	 *******************/

	/**
	 * Set the URL we are going to open
	 */
	private function SetURL($url)
	{
		curl_setopt($this->curl_handle, CURLOPT_REFERER, $this->curl_referer);
		curl_setopt($this->curl_handle, CURLOPT_URL, $url);

		// set the referer as this URL for the next time we open a new url
		$this->SetReferer($url);
	}
}
?>