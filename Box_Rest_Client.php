<?php 
/**
 * 
 * The current SSL setup is a bit of a hack. I've just disabled SSL verification 
 * on cURL. Instead, the better idea would be to implement something like this 
 * at some point: 
 * 
 * http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
 * 
 * For now though, this should be more than enough to get things started. The class 
 * requires a rather in-depth knowledge of the box.net API and relies solely on the 
 * ReST implementation. Instead of nicely packaging all the API's in their own little 
 * methods, you call them manually via exec('resource',array('option'=>'option value')); 
 * 
 * This returns a multi-dimensional array of the data received and it is up to you to
 * verify things. The only thing that this class DOES on it's own is the authentication 
 * mechanism since that is one of those special cases. 
 * 
 * In order to save the auth_token to a particular user you MUST provide an implementation for 
 * Box_Rest_Client_Auth->store(). This is a class that is present later on in this file. It is 
 * necessary that you do this to ensure that something is done with the auth-token that is 
 * returned. Otherwise, it will only last until the page is refreshed. Then YOU will need 
 * to provide some kind of mechanism to ensure that the auth-token is actually set.
 * 
 * @author SupportCon
 *
 */
class Box_Rest_Client {
	
	public $api_key;
	public $ticket;
	public $auth_token;
	
	private $rest_url = 'https://www.box.net/api/1.0/rest';
	
	public $MOBILE = false;
	
	/**
	 * You need to create the client with the API KEY that you received when 
	 * you signed up for your apps. 
	 * 
	 * @param string $api_key
	 */
	public function __construct($api_key) {
		if(empty($api_key)) {
			throw new Box_Rest_Client_Exception('Invalid API KEY');
		}
		else {
			$this->api_key = $api_key;
		}
	}
	
	/**
	 * 
	 * Because the authentication method is an odd one, I've provided a wrapper for 
	 * it that should deal with either a mobile or standard web application. You 
	 * will need to set the callback url from your application on the developer 
	 * website and that is called automatically. 
	 * 
	 * When this method notices the "auth_token" query string, it will automatically 
	 * call the Box_Rest_Client_Auth->store() method. You can do whatever you want 
	 * with it in that method. I suggest you read the bit of documentation that will 
	 * be present directly above the class.
	 */
	public function authenticate() {
		if(array_key_exists('auth_token',$_GET)) {
			$this->auth_token = $_GET['auth_token'];
			
			$box_rest_client_auth = new Box_Rest_Client_Auth();
			return $box_rest_client_auth->store($this->auth_token);
		}
		else {
			$res = $this->exec('get_ticket',array('api_key' => $this->api_key));
			if($res['status'] === 'get_ticket_ok') {
				$this->ticket = $res['ticket'];
				
				if($this->MOBILE) {
					header('location: https://m.box.net/api/1.0/auth/'.$this->ticket);
				}
				else {
					header('location: https://www.box.net/api/1.0/auth/'.$this->ticket);
				}
				
			}
		}
	}
	
	/**
	 * 
	 * This tree method is provided as it tends to be what a lot of people will most 
	 * likely try to do. It returns a list of folders/files utilizing our 
	 * Box_Client_Folder and Box_Client_File classes instead of the raw tree array 
	 * that is normally returned. 
	 * 
	 * You can totally ignore this and instead rely entirely on exec and parse the 
	 * tree yourself if it doesn't quite do what you want. 
	 * 
	 * @param int $root The root directory that you want to load the tree from.
	 * @param string $params Any additional params you want to pass, comma separated.
	 * @return Box_Client_Folder 
	 */
	public function tree($root,$params = 'nozip') {
		$res = $this->exec('get_account_tree',array('folder_id'=>$root, 'params[]' => $params));
		
		$folder = new Box_Client_Folder;
		$folder->import($res['tree']['folder']);
		
		return $folder;
	}
	
	/**
	 * 
	 * Executes an api function using the required opts. It will attempt to 
	 * execute it regardless of whether or not it exists.
	 * 
	 * @param string $api
	 * @param array $opts
	 */
	public function exec($api, array $opts = array()) {
		$opts = $this->set_opts($opts);
		$url = $this->build_url($api,$opts);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);

		return $this->parse_result($data);
	}
	
	/**
	 * 
	 * To minimize having to remember things, exec will automatically 
	 * call this method to set some default values as long as the default 
	 * values don't already exist.
	 * 
	 * @param array $opts
	 */
	private function set_opts(array $opts) {
		if(!array_key_exists('api_key',$opts)) {
			$opts['api_key'] = $this->api_key;
		}
		
		if(!array_key_exists('auth_token',$opts)) {
			if(isset($this->auth_token) && !empty($this->auth_token)) {
				$opts['auth_token'] = $this->auth_token;
			}
		}
		
		return $opts;
	}
	
	/**
	 * 
	 * Build the final api url that we will be curling. This will allow us to 
	 * get the results needed. 
	 * 
	 * @param string $api_func
	 * @param array $opts
	 */
	private function build_url($api_func, array $opts) {
		$base = $this->rest_url;
		
		$base .= '?action='.$api_func;
		foreach($opts as $key=>$val) {
			$base .= '&'.$key.'='.$val;
		}
		
		return $base;
	}
	
	/**
	 * 
	 * Converts the XML we received into an array for easier messing with. 
	 * Obviously this is a cheap hack and a few things are probably lost along 
	 * the way (types for example), but to get things up and running quickly, 
	 * this works quite well. 
	 * 
	 * @param string $res
	 */
	private function parse_result($res) {
		$xml = simplexml_load_string($res);
		$json = json_encode($xml);
		$array = json_decode($json,TRUE);
		
		return $array;
	}
}

/**
 * 
 * Instead of returning a giant array of things for you to deal with, I've pushed 
 * the array into two classes. The results are either a folder or a file, and each 
 * has its own class. 
 * 
 * The Box_Client_Folder class will contain an array of files, but will also have 
 * its own attributes. In addition. I've provided a series of CRUD operations that 
 * can be performed on a folder.
 * @author SupportCon
 *
 */
class Box_Client_Folder {
	
	private $attr;
	
	public $file;
	public $folder;
	
	public function __construct() {
		$this->attr = array();
		$this->file = array();
		$this->folder = array();
	}
	
	/**
	 * 
	 * Acts as a getter and setter for various attributes. You should know the name 
	 * of the attribute that you are trying to access.
	 * @param string $key
	 * @param mixed $value
	 */
	public function attr($key,$value = '') {
		if(array_key_exists($key,$this->attr)) {
			if(empty($value)) {
				return $this->attr[$key];
			}
			else { 
				$this->attr[$key] = $value;
			}
		}
	}
	
	/**
	 * 
	 * Imports the tree structure and allows us to provide some extended functionality 
	 * at some point. 
	 * @param array $tree
	 */
	public function import(array $tree) {
		foreach($tree['@attributes'] as $key=>$val) {
			$this->attr[$key] = $val;
		}
		
		if(array_key_exists('folders',$tree)) {
			foreach($tree['folders'] as $i => $folder) {
				$this->folder[$i] = new Box_Client_Folder();
				$this->folder[$i]->import($folder); 
			}
		}
		
		if(array_key_exists('files',$tree)) {
			foreach($tree['files'] as $i => $file) {
				$this->file[$i] = new Box_Client_File();
				$this->file[$i]->import($file);
			}
		}
	}
}

/**
 * 
 * Instead of returning a giant array of things for you to deal with, I've pushed 
 * the array into two classes. The results are either a folder or a file, and each 
 * has its own class. 
 * 
 * The Box_Client_File class will contain the attributes and tags that belong 
 * to a single file. In addition, I've provided a series of CRUD operations that can 
 * be performed on a file.
 * @author SupportCon
 *
 */
class Box_Client_File {
	
	private $attr;
	private $tags;
	
	/**
	 * 
	 * Imports the file attributes and tags. At some point we can add further 
	 * methods to make this a little more useful (a json method perhaps?)
	 * @param array $file
	 */
	public function import(array $file) {
		foreach($file['@attributes'] as $key=>$val) {
			$this->attr[$key] = $val;
		}
		
		foreach($file['tags'] as $i => $tag) {
			$tags[$i] = $tag;
		}
	}
}

/**
 * 
 * I recommend that you pull this class into its own file and the proceed to make 
 * any modifications to it you want. It will always receive the auth_token as the 
 * first argument and then you are free to do whatever you want with it. 
 * 
 * Since we invoke the class like ti has a constructor, you could potentially 
 * connect to a database and create more methods (apart from store) that could 
 * act as a model for the authentication token. 
 * @author SupportCon
 *
 */
class Box_Rest_Client_Auth {
	
	public function store($auth_token) {
		$_SESSION['auth'] = $auth_token;
	}
}

/**
 * 
 * Thrown if we encounter an error with the actual client class. This is fairly 
 * useless except it gives you a little more information about the type of error 
 * being thrown.
 * @author SupportCon
 *
 */
class Box_Rest_Client_Exception extends Exception {
	
}

/**
 * 
 * Thrown if we encounter an error with the API. This is fairly useless except 
 * it gives you a little more information about the type of error being thrown.
 * @author SupportCon
 *
 */
class Box_Rest_API_Exception extends Exception {
	
} 