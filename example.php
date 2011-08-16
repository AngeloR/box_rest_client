<?php session_start();

include('Box_Rest_Client.php');




// Set your API Key.
$api_key = '8m8kl3acvplo438izp3v4se622h5m86q';
// create an instance of the client
$box_net = new Box_Rest_Client($api_key);

// run the authentication method to authenicate a user against 
// box.net. the authenticate method that is being called will 
// called the Box_Rest_Client_Auth::store() method which is 
// by default just configured to store the key in a session 
// called auth
if(!array_key_exists('auth',$_SESSION)) {
	$box_net->authenticate();
}
else {
	$box_net->auth_token = $_SESSION['auth'];
}

$folder = $box_net->tree(0);
var_dump($folder);