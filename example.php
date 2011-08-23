<?php 
// This is not necessary for all implementations. This is only 
// preset because after authentication, we set the auth_key to 
// a session for this example.
session_start();
/*
// Include the Box_Rest_Client class
include('lib/Box_Rest_Client.php');

// Set your API Key. If you have a lot of pages reliant on the 
// api key, then you should just set it statically in the 
// Box_Rest_Client class.
$api_key = '8m8kl3acvplo438izp3v4se622h5m86q';


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
var_dump($folder);

// Let us create a folder
// $box_net->post('create_folder',array('parent_id' => 0, 'name' => 'Box_Rest_Client', 'share' => 1));
// */

function br($num) {
	for($i = 0; $i < $num; ++$i) {
		echo '<br>';
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>BoxNet Example</title>
		<link href='http://fonts.googleapis.com/css?family=Droid+Sans+Mono' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" type="text/css" href="style.css">
		<meta charset="utf-8">
	</head>
	<body>
		<table id="wrapper">
			<tr>
				<td class="spanner">
					<p>The Box_Rest_Client is a simple PHP based library to access and work with the Box.net ReST api. 
					By providing a standard get/post interface, Box_Rest_Client automatically supports all 
					get/post requests on the Box.net API.</p><br>
					<p>In addition to that, this library provides various "aliases" to ensure a uniform environment. 
					Aliases are simply calls to the box.net api which are abstracted. For example, you could easily 
					get a list of folders by calling the get_account_tree api method, or you could call the folder() method 
					in the Box_Rest_Client, which returns a list of files and folders as Box_Client_File/Box_Client_Folder 
					respectively. These classes then provide additional aliases which allow you to easy access the api 
					programmatically.</p><br>
					<p>This tutorial is designed to teach you the basics of working with the Box_Rest_Client. It teaches 
					you how to: 
					<ol>
						<li><a href="#step1">Authenticate a user</a></li>
						<li><a href="#step2">Get a list of files and folders</a></li>
						<li><a href="#step3">Create a folder</a></li>
						<li><a href="#step4">Upload a file</a></li>
					</ol>
					<hr>
					<h2><a name="step1">Authenticate a user</a></h2>
					<p>To authenticate a user we must first have an API KEY. These can be received by registering your 
					application by visiting <a href="http://box.net/developers">http://box.net/developers</a>. After you 
					register, you will be assigned an API KEY. All your requests will require an API key so you should set 
					it in the Box_Rest_Client class itself. </p>
					<?php br(1); ?>
					<p>Create an $api_key and pass it as the only argument to your <code>Box_Rest_Client</code> instance. 
					If you need to, just define it in your Box_Rest_Client.php file.</p>
					<?php br(2); ?>
					<p>Our <code>Box_Rest_Client_Auth</code> class currently returns the auth_token. We assign the returned token 
					to our <code>$_SESSION</code> variable. 
					<?php br(13); ?>
					<h2><a name="step2">Get a list of files and folders</a></h2>
					<p>There are two ways to perform this action. The easiest way is to 
					use the alias. The harder way is to utilize the get/post wrappers and 
					manually call your method. This exmaple will only show you the </p>
					<?php br(2); ?>
					<p>This will return an entire tree listing of every file and 
					directory under your root folder. This was is preferred as each 
					file/folder will return as an instance of Box_Client_File and 
					Box_Client_Folder respectively. This allows you to perform other 
					actions on files/folders such as move, update and delete.</p>
					<?php br(5); ?>
					<h2><a name="step3">Create a folder</a></h2>
					<p>Creating a folder is very simple. You can simply create an 
					instance of "Box_Client_Folder", then just set the following through 
					the <code>attr()</code> method. <br>
					- The name of the folder.<br>
					- The id of the parent folder (or leave blank for the root folder)<br>
					- Whether or not to share the folder (by default, this is set to false) </p>
					<br>
					<p>Your folder object will now contain all the attributes of the 
					folder you just created. The create method will also return a value 
					to indicate whether or not the error was successful. The values 
					returned match directly with possible status output params.</p>
				</td>
				<td class="spanner" id="code">
					<?php br(23); ?>
					<?php highlight_string('<?php 
// Include the Box_Rest_Client class
include(\'lib/Box_Rest_Client.php\');
											
// Set your API Key. If you have a lot of pages reliant on the 
// api key, then you should just set it statically in the 
// Box_Rest_Client class.
$api_key = \'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\';
$box_net = new Box_Rest_Client($api_key);
					


if(!array_key_exists(\'auth\',$_SESSION) || empty($_SESSION[\'auth\'])) {
	$_SESSION[\'auth\'] = $box_net->authenticate();
}
else {
	// If the auth $_SESSION key exists, then we can say that 
	// the user is logged in. So we set the auth_token in 
	// box_net.
	$box_net->auth_token = $_SESSION[\'auth\'];
} ?>
'); br(16); ?>
<?php highlight_string('<?php 
$folder = $box_net->folder(0); 
?>'); br(9); ?>

<?php highlight_string('<?php 
$my_folder = new Box_Client_Folder();

$my_folder->attr(\'name\',\'New Folder\');
$my_folder->attr(\'parent_id\', 0);

$my_folder->attr(\'share\',false);


$box_net->create($folder);
?>')?>
				</td>
			</tr>
		</table>
	</body>
</html>