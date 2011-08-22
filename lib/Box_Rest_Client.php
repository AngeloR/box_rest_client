<?php 
/**
 *    ___  ____ _  _     ____ ____ ____ ___     ____ _    _ ____ _  _ ___ 
 *		|__] |  |  \/      |__/ |___ [__   |      |    |    | |___ |\ |  |  
 *		|__] |__| _/\_ ___ |  \ |___ ___]  |  ___ |___ |___ | |___ | \|  |  v0.2
 *
 *
 *
 *	The Box_Rest_Client is a PHP library for accessing the Box.net ReST api. It 
 *	provides a PHP cURL based interface that allows access to any number of 
 *  api methods that are currently in place. The code is built in a way to 
 *  ensure modularity, easy updates (everything is this one file) and aims to 
 *  be a simple easy to use solution for working with the excellent Box api. 
 *  
 *  Each of the classes in this file was licensed under the MIT Licensing 
 *  agreement located below this introductary comment block. 
 *  
 *  Dependencies:
 *  	1) cURL: This library relies on cURL to perform the http verbs. Without 
 *  			it, this library will not function. There are plenty of tutorials for 
 *  			enabling cURL on your specific system out there on the web, just a 
 *  			short google away.
 *  	2) SimpleXML: Results from the Box api currently return (sometimes 
 *  			malformed) xml. It is enabled by default unless you specifically 
 *  			disabled it during install.. in which case you probably know what 
 *  			you're doing.
 *  	3) This code has only been tested on the following versions of PHP: 
 *  			5.3.5,
 *  
 *  			If you have tested this code and believe it to be working on a 
 *  			different version, please drop me an email.
 *  
 *  
 *  Installation:
 *  	1) Copy this file wherever you want.
 *  	2) Copy the Box_Rest_Client_Auth class and paste it in its own file. This 
 *  			class is called after an auth_token is received from the API. You 
 *  			need to decide what to do with it. Box says that the auth_tokens 
 *  			never expire..so maybe you want to store them? At least in a session.
 *  
 *  
 *  
 *  Using:
 *  	I recommend that you check out the example.php file that was included 
 *  	with this download for how to use it. There will hopefully be some more 
 *  	documentation on the wiki, but at the moment I can't really concentrate 
 *  	on building the library and having detailed examples of each API method.
 *  
 *	                                                                    
 */


/**
 * The MIT License (MIT)
 * Copyright (c) 2011 Angelo Rodrigues
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software 
 * and associated documentation files (the "Software"), to deal in the Software without 
 * restriction, including without limitation the rights to use, copy, modify, merge, publish, 
 * distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom 
 * the Software is furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all copies or 
 * substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING 
 * BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND 
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */


/**
 *
 * I recommend that you pull this class into its own file and the proceed to make
 * any modifications to it you want. It will always receive the auth_token as the
 * first argument and then you are free to do whatever you want with it.
 *
 * Since we invoke the class like it has a constructor, you could potentially
 * connect to a database and create more methods (apart from store) that could
 * act as a model for the authentication token.
 * @author Angelo R
 *
 */
class Box_Rest_Client_Auth {

	public function store($auth_token) {
		return $auth_token;
	}
}

/**
 * 
 * The ReST Client is really what powers the entire class. It provides access 
 * the the basic HTTP verbs that are currently supported by Box
 * @author SupportCon
 *
 */

class Rest_Client {
	
	public static function get($url) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}
	
	public static function post($url,array $params = array()) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}
}



/**
 * This is the main API class. This is what you will be invoking when you are dealing with the 
 * API. 
 * 
 * I would suggest reading up the example.php file instead of trying to peruse through this 
 * file as it's a little much to take in at once. The example.php file provides you the basics 
 * of getting started. 
 * 
 * If you want to inspect what various api-calls will return check out inspector.php which 
 * provides a nice little interface to do just that.
 * 
 * That being said, here's a quick intro to how to use this class. 
 * 
 * - If you are utilizing it on more than one page, definitely set the api_key within the 
 * 		class. It will save you a lot of time. I am going to assume that you did just that.
 * - I am assuming that you have !NOT! configured the Box_Rest_Client_Auth->store() 
 * 		method and it is default. Therefore, it will just return the auth_token.
 * 
 * $box_rest_client = new Box_Rest_Client();
 * if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth']) {
 * 	$box_rest_client->authenticate();
 * }
 * else {
 * 	$_SESSION['auth'] = $box_rest_client->authenticate();
 * }
 * 
 * $box_rest_client->folder(0);
 * 
 * The above code will give you a nice little tree-representation of your files.
 * 
 * For more in-depth examples, either take a look at the example.php file or check out 
 * inspector/index.php
 * 
 * @todo Proper SSL support
 * 				The current SSL setup is a bit of a hack. I've just disabled SSL verification 
 * 				on cURL. Instead, the better idea would be to implement something like this 
 * 				at some point: 
 * 
 * 				http://unitstep.net/blog/2009/05/05/using-curl-in-php-to-access-https-ssltls-protected-sites/
 * 
 * @todo File Manipulation
 * @todo Folder Manipulation
 * 
 * @author Angelo R
 *
 */
class Box_Rest_Client {
	
	public $api_key;
	public $ticket;
	public $auth_token;
	
	private $api_version = '1.0';
	private $base_url = 'https://www.box.net/api';

	
	public $MOBILE = false;
	
	/**
	 * You need to create the client with the API KEY that you received when 
	 * you signed up for your apps. 
	 * 
	 * @param string $api_key
	 */
	public function __construct($api_key = '') {
		if(empty($this->api_key) && empty($api_key)) {
			throw new Box_Rest_Client_Exception('Invalid API Key. Please provide an API Key when creating an instance of the class, or by setting Box_Rest_Client->api_key');
		}
		else {
			$this->api_key = (empty($api_key))?$this->api_key:$api_key;
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
			$res = $this->get('get_ticket',array('api_key' => $this->api_key));
			if($res['status'] === 'get_ticket_ok') {
				$this->ticket = $res['ticket'];
				
				if($this->MOBILE) {
					header('location: https://m.box.net/api/1.0/auth/'.$this->ticket);
				}
				else {
					header('location: https://www.box.net/api/1.0/auth/'.$this->ticket);
				}
				
			}
			else {
				throw new Box_Rest_Client_Exception($res['status']);
			}
		}
	}
	
	/**
	 * 
	 * This folder method is provided as it tends to be what a lot of people will most 
	 * likely try to do. It returns a list of folders/files utilizing our 
	 * Box_Client_Folder and Box_Client_File classes instead of the raw tree array 
	 * that is normally returned. 
	 * 
	 * You can totally ignore this and instead rely entirely on get/post and parse the 
	 * tree yourself if it doesn't quite do what you want. 
	 * 
	 * @param int $root The root directory that you want to load the tree from.
	 * @param string $params Any additional params you want to pass, comma separated.
	 * @return Box_Client_Folder 
	 */
	public function folder($root,$params = array('params' => array('nozip'))) {
		$params['folder_id'] = $root;
		$res = $this->get('get_account_tree', $params);
	
		$folder = new Box_Client_Folder;
		if(array_key_exists('tree',$res)) {
			$folder->import($res['tree']['folder']);
		}
		return $folder;
	}
	
	/**
	 * 
	 * Since we provide a way to get information on a folder, it's only fair that we 
	 * provide the same interface for a file. This will grab the info for a file and 
	 * push it back as a Box_Client_File. Note that this method (for some reason) 
	 * gives you less information than if you got the info from the tree view. 
	 * 
	 * @param int $file_id
	 * @return Box_Client_File
	 */
	public function file($file_id) {
		$res = $this->get('get_file_info',array('file_id' => $file_id));
		
		// For some reason the Box.net api returns two different representations 
		// of a file. In a tree view, it returns the more attributes than 
		// in a standard get_file_info view. As a result, we'll just trick the 
		// implementation of import in Box_Client_File.
		$res['@attributes'] = $res['info'];
		$file = new Box_Client_File;
		$file->import($res);
		return $file;
	}
	
	/**
	 * 
	 * Creates a folder on the server with the specified attributes.
	 * @param Box_Client_Folder $folder
	 */
	public function create(Box_Client_Folder &$folder) {
		$params = array(
			'name' => $folder->attr('name'),
			'parent_id' => $folder->attr('parent_id'),
			'share' => $folder->attr('share')
		);
		$res = $this->post('create_folder',$params);
		if($res['status'] == 'create_ok') {
			foreach($res['folder'] as $key => $val) {
				$folder->attr($key,$val);
			}
		}
		return $res['status'];
	}
	
	/**
	 * Returns the url to upload a file to the specified parent folder
	 */
	public static function upload_url($folder_id = 0) {
		
	}
	
	/**
	 * 
	 * Uploads the file to the specified folder. You can set the parent_id 
	 * attribute on the file for this to work.
	 * @param Box_Client_File $file
	 */
	public function upload(Box_Client_File &$file) {
		
	}
	
	/**
	 * 
	 * Executes an api function using get with the required opts. It will attempt to 
	 * execute it regardless of whether or not it exists.
	 * 
	 * @param string $api
	 * @param array $opts
	 */
	public function get($api, array $opts = array()) {
		$opts = $this->set_opts($opts);
		$url = $this->build_url($api,$opts);
		
		$data = Rest_Client::get($url);

		return $this->parse_result($data);
	}
	
	/**
	*
	* Executes an api function using post with the required opts. It will
	* attempt to execute it regardless of whether or not it exists.
	*
	* @param string $api
	* @param array $opts
	*/
	public function post($api, array $params = array(), array $opts = array()) {
		$opts = $this->set_opts($opts);
		$url = $this->build_url($api,$opts);
		
		$data = Rest_Client::post($url,$params);
		return $this->parse_result($data);
	}
	
	/**
	 * 
	 * To minimize having to remember things, get/post will automatically 
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
		$base = $this->base_url.'/'.$this->api_version.'/rest';
		
		$base .= '?action='.$api_func;
		foreach($opts as $key=>$val) {
			if(is_array($val)) {
				foreach($val as $i => $v) {
					$base.= '&'.$key.'[]='.$v;
				}
			}
			else {
				$base .= '&'.$key.'='.$val;
			}
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
 * @author Angelo R
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
	 * This method will create a folder in the folder that is assigned as the 
	 * "parent_id". Simply calling this method with a folder name will create 
	 * this folder in the root directory and keep it private. 
	 * 
	 * @param string $name
	 * @param int $parent_id
	 * @param bool $share
	 */
	public function create($name, $parent_id = 0, $share = false) {
		$this->attr('name',$name);
		$this->attr('parent_id',intval($parent_id));
		$this->attr('share',int($share));
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
	 * at some point. Don't run import manually. It expects certain things that are 
	 * delivered through the API. Instead, if you need a tree structure of something, 
	 * simply call Box_Rest_Client->folder(folder_id); and it will automatically return 
	 * the right stuff.
	 * 
	 * Due to an inconsistency with the Box.net ReST API, this section invovles a few 
	 * more checks than normal to ensure that all the necessary values are available 
	 * when doing the import.
	 * @param array $tree
	 */
	public function import(array $tree) {
		foreach($tree['@attributes'] as $key=>$val) {
			$this->attr[$key] = $val;
		}
		
		if(array_key_exists('folders',$tree)) {
			if(array_key_exists('folder',$tree['folders'])) {
				if(array_key_exists('@attributes',$tree['folders']['folder'])) {
					// this is the case when there is a single folder within the root
					$box_folder = new Box_Client_Folder;
					$box_folder->import($tree['folders']['folder']);
					$this->folder[] = $box_folder;
				}
				else {
					// this is the case when there are multiple folders within the root
					foreach($tree['folders']['folder'] as $i => $folder) {
						$box_folder = new Box_Client_Folder;
						$box_folder->import($folder);
						$this->folder[] = $box_folder;
					}
				}
			}
		}
		
		if(array_key_exists('files',$tree)) {
			if(array_key_exists('file',$tree['files'])) {
				if(array_key_exists('@attributes',$tree['files']['file'])) {
					// this is the case when there is a single file within a directory
					$box_file = new Box_Client_File;
					$box_file->import($tree['files']['file']);
					$this->file[] = $box_file;
				}
				else {
					// this is the case when there are multiple files in a directory
					foreach($tree['files']['file'] as $i => $file) {
						$box_file = new Box_Client_File;
						$box_file->import($file);
						$this->file[] = $box_file;
					}
				}
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
 * @author Angelo R
 *
 */
class Box_Client_File {
	
	private $attr;
	private $tags;

	
	public function __construct($path_to_file = '') {
		if(!empty($path_to_file)) {
			$this->attr('localpath',$path_to_file);
		}
	}
	
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
	
	/**
	 * 
	 * Gets or sets file attributes. For a complete list of attributes please 
	 * check the info object (get_file_info)
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
	
	public function tag() {
		
	}
}

$file = new Box_Client_File('/uploads/file.tmp');
$box_rest_client->upload($file);

$folder = new Box_Client_Folder();
$folder->attr('parent_id', 945);
$folder->attr('name','Some Name');
$folder->attr('share',5);
$box_rest_client->create($folder);

/**
 * 
 * Thrown if we encounter an error with the actual client class. This is fairly 
 * useless except it gives you a little more information about the type of error 
 * being thrown.
 * @author Angelo R
 *
 */
class Box_Rest_Client_Exception extends Exception {
	
}

/* 
 * 53 6F 6C 6F 6E 
 * 67 61 6E 64 74 
 * 68 61 6E 6B 73 
 * 66 6F 72 61 6C 
 * 6C 74 68 65 66 
 * 69 73 68 
 */