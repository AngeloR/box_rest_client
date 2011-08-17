<?php 
// This is not necessary for all implementations. This is only 
// preset because after authentication, we set the auth_key to 
// a session for this example.
session_start();

// Include the Box_Rest_Client class
include('lib/Box_Rest_Client.php');

// Set your API Key. If you have a lot of pages reliant on the 
// api key, then you should just set it statically in the 
// Box_Rest_Client class.
$api_key = 'xxxxxxxxxxxx';


// create an instance of the client and pass in the api_key
$box_net = new Box_Rest_Client($api_key);

// run the authentication method to authenicate a user against 
// box.net. The authenticate method that is being called will 
// called the Box_Rest_Client_Auth::store() method which is 
// by default just configured to return the auth_token
if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth'])) {
	$_SESSION['auth'] = $box_net->authenticate();
}
else {
	// If the auth $_SESSION key exists, then we can say that 
	// the user is logged in. So we set the auth_token in 
	// box_net.
	$box_net->auth_token = $_SESSION['auth'];
}

// We load up the folder 0 as a tree
$folder = $box_net->folder(0);

// just so you can see the output
var_dump($folder->folder[0],$folder->file,$folder->folder[0]->file[0]);

// Let us create a folder
$box_net->post('create_folder',array('parent_id' => 0, 'name' => 'Box_Rest_Client', 'share' => 1));