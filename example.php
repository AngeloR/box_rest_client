<?php session_start(); ob_start();

include('lib/Box_Rest_Client.php');

$api_key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxx';
$box_net = new Box_Rest_Client($api_key);


if(!array_key_exists('auth',$_SESSION) || empty($_SESSION['auth'])) {
	$_SESSION['auth'] = $box_net->authenticate();
}
else {
	$box_net->auth_token = $_SESSION['auth'];
}

$folder = $box_net->folder(0);

if(array_key_exists('action',$_POST)) {
	if($_POST['action'] == 'create_folder') {
		$folder = new Box_Client_Folder();
		$folder->attr('name', $_POST['folder_name'].'.'.time());
		$folder->attr('parent_id', $_POST['parent_id']);
		$folder->attr('share', false);
		
		echo $box_net->create($folder);
    $folder = $box_net->folder(0);

	}
	else if($_POST['action'] == 'upload_file') {
		$file = new Box_Client_File($_FILES['file']['tmp_name'], $_FILES['file']['name']);
		$file->attr('folder_id', $_POST['folder_id']);
		echo $box_net->upload($file);

	}
  else if($_POST['action'] == 'reset') {
    unset($_SESSION['auth']);
    header('location: sample.php');
  }
  else if($_POST['action'] == 'rename_folder') {
    if($_POST['folder_id'] != 0) {
      foreach($folder->folder as $f) {
        if($f->attr('id') == $_POST['folder_id']) {
          $f->attr('name',$_POST['folder_name']);
          
          // how to utilize non aliased methods
          $res = $box_net->get('rename',array('target'=>'folder', 'target_id' => $f->attr('id'), 'new_name' => $f->attr('name')));
          echo $res['status'];
        }
      }
    }
  }
}

function display_folders(Box_Client_Folder $folder, $level) {
  $name = $folder->attr('name');
  $tmp = '<option value="'.$folder->attr('id').'">'.str_repeat('-',$level).' '.((empty($name))?'Root Folder':$folder->attr('name')).'</option>';
  foreach($folder->folder as $f) {
    $tmp .= display_folders($f,$level+1);
  }
  
  return $tmp;
}

?>
<html>
	<head>
		<title>Upload Test</title>
	</head>
	<body>
    <p>For the sample, it only lists the first level of folders. If you want, you can set it so that you are loading
    an entire tree.. but only do that if your account has a low level of files/folders. If you have too many the script
    will hang.</p>
    
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
     <input type="hidden" name="action" value="reset">
      <button type="submit">Reset Test</button>
    </form>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<input type="hidden" name="action" value="create_folder">
		<label>Folder name: </label><input type="text" name="folder_name"> under
		
		<select name="parent_id">
      <?php echo display_folders($folder,0); ?>
		</select><button type="submit">Create</button>
	</form>
	<hr>
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
		<input type="hidden" name="action" value="upload_file">
		
		<label>Select File: </label><input type="file" name="file">
    upload to
    <select name="folder_id">
      <?php echo display_folders($folder,0); ?>
    </select>
		<button type="submit">Upload</button>
	</form>
  
  <hr>
    <p>Please note that you can't rename the Root Folder.</p>
    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
      <input type="hidden" name="action" value="rename_folder">
      Rename
      <select name="folder_id">
        <?php echo display_folders($folder,0); ?>
      </select>
      to
      <input type="text" name="folder_name"> <button type="submit">Rename</button>
    </form>
  
	</body>
</html>
<?php ob_flush(); ?>