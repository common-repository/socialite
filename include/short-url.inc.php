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
include_once(dirname(__FILE__)."/socialite.inc.php");

class SL_ShortURL
{
	public function __construct()
	{

	}

	/*
	 * http://tinyurl.com
	 */
	public function Tinyurl($url)
	{
		$data = "url=".urlencode($url);
		$resp = SL_Http::GetURL("tinyurl.com", "/api-create.php", $data);

		return trim($resp["data"]);
	}

	/*
	 * http://zz.gd
	 */
	public function ZZgd($url)
	{
		$data = "url=".urlencode($url);
		$resp = SL_Http::GetURL("zz.gd", "/api-create.php", $data);

		return trim($resp["data"]);
	}

	/*
	 * http://bit.ly
	 */
	public function Bitly($url, $login, $api_key)
	{
		$version = "2.0.1";
		$format = "xml";

		$data  = "version=$version&longUrl=".urlencode($url)."&";
		$data .= "login=".urlencode($login)."&apiKey=".urlencode($api_key)."&format=$format";
		$resp = SL_Http::GetURL("api.bit.ly", "/shorten", $data);

		$arr = Socialite::XML2Array($resp["data"]);
		$url = $arr[0]["elements"][2]["elements"][0]["elements"][4]["text"];

		return trim($url);
	}

	/*
	 * http://ur.ly
	 */
	public function URly($url)
	{
		$data = "href=".urlencode($url);
		$resp = SL_Http::GetURL("ur.ly", "/new.xml", $data);

		$matches = array();
		preg_match("/code\=\"(.*?)\"/i", $resp["data"], $matches);
		$code = trim($matches[1]);

		return "http://ur.ly/$code";
	}

	/*
	 * http://is.gd
	 */
	public function ISgd($url)
	{
		$data = "longurl=".urlencode($url);
		$resp = SL_Http::GetURL("is.gd", "/api.php", $data);

		return trim($resp["data"]);
	}
}
?>