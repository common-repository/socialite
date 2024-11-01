<?php
/**
 * @package	Socialite
 * @author	Ryan Gilfether <ryan@gilfether.com>
 * @license	http://www.gnu.org/copyleft/gpl.html GPL V3
 * @version	0.1 Beta
 * @link	http://www.gilfether.com/socialite
 * @date	March 7, 2009
 */

class SL_Http
{
	const USER_AGENT = "Socialite (http://www.gilfether.com/socialite)";
	//const USER_AGENT = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)";

	public function __construct()
	{

	}


	/**
	 * Posts data to the requested Host using HTTP POST method
	 *
	 * @param string $host The host to submit the lead to
	 * @param string $path The path to the script to process the lead on the host
	 * @param string $data The query string of data to be submitted
	 * @return mixed The response string on success, else false on error
	 */
	function PostURL($host, $path, $data = "", $headers = "", $port = 80)
	{
		$buf = "";
		$head = "";

		if(!preg_match("@^/@", $path))
			$path = "/$path";

		if(is_array($headers))
		{
			foreach($headers as $k=>$v)
				$head .= "$k: $v\r\n";
		}

		$opts = array (
        	'http' => array (
				'method' => 'POST',
				'header'=>	"Content-Type: application/x-www-form-urlencoded\r\n".
							"Content-Length: ".strlen($data)."\r\n".
							"User-Agent: ".self::USER_AGENT."\r\n".
							"$head",
				'content' => $data
			)
        );

		$context = stream_context_create($opts);

		$resp["data"] = file_get_contents("http://{$host}:{$port}{$path}", false, $context);
		$resp["header"] = $http_response_header;

		return $resp;
	}
	
	
	/**
	 * Simple function to grab a url using the HTTP GET method and return the results
	 *
	 * @param string $host The host to submit the lead to
	 * @param string $path The path to the script to process the lead on the host
	 * @param string $data The query string of data to be submitted
	 * @return mixed The response string on success, else false on error
	 */
	public function GetURL($host, $path, $data = "", $headers = "", $port = 80)
	{
		$buf = "";
		$head = "";

		if(!preg_match("@^/@", $path))
			$path = "/$path";

		if(is_array($headers))
		{
			foreach($headers as $k=>$v)
				$head .= "$k: $v\r\n";
		}

		$opts = array (
        	'http' => array (
				'method' => 'GET',
				'header'=>	"User-Agent: ".self::USER_AGENT."\r\n".
							"$head"
			)
        );

		$context = stream_context_create($opts);

		if($data != "")
			$path .= "?$data";

		$resp["data"] =  file_get_contents("http://{$host}:{$port}{$path}", false, $context);
		$resp["header"] = $http_response_header;

		return $resp;
	}
}
?>