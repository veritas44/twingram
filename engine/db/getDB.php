<?php

$db = array();
$site = array();

if(isset($_POST['setting_submit'])){ //If settings changed
	changeSetting("site-name", $_POST['site-name']);
	changeSetting("site-image", $_POST['site-image']);
}


function changeSetting($setting, $value){
	$query = db_query("UPDATE `settings` SET `value` = '$value' WHERE `settings`.`setting` = '$setting'");
}

function dashboard(){
	$values = [];
	$query = db_query('SELECT * FROM `posts` WHERE id=(SELECT max(id) FROM `posts`)');
	$values['posts'] = 0;
	while ($row = mysqli_fetch_assoc($query)) { 
			$values['posts'] = $row['id'];
		}
	$query = db_query('SELECT * FROM `users` WHERE id=(SELECT max(id) FROM `users`)');
	$values['users'] = 0;
	while ($row = mysqli_fetch_assoc($query)) { 
			$values['users'] = $row['id'];
		}
	$query = db_query('SELECT * FROM `comments` WHERE id=(SELECT max(id) FROM `comments`)');
	$values['comments'] = 0;
	while ($row = mysqli_fetch_assoc($query)) {
			 
			$values['comments'] = $row['id'];
		}
		return $values;
	}


function loginAdmin($username, $password){
	$query = db_query('SELECT * FROM `admin` WHERE username = "' . $username . '"');
	while ($row = mysqli_fetch_assoc($query)) { 
		if(password_verify($password, $row['passwordhash'])){
			return $row;
		}
	}
	return 0;	
}

function loginUser($username, $password){
	$query = db_query('SELECT * FROM `users` WHERE username = "' . $username . '"');
	while ($row = mysqli_fetch_assoc($query)) { 
		if(password_verify($password, $row['passhash'])){
			return $row;
		}
	}
	return 0;	
}

function registerUser($email, $password, $repassword, $username){
	$return = "<ul>";
	if($email == ""){
		$return = $return . "<li>Email is incorrect</li>";
	}
	if($username == ""){
		$return = $return . "<li>Username is incorrect</li>";
	}
	if($password == ""){
		$return = $return . "<li>Password is incorrect</li>";
	}else if($password == $repassword){
		$hash = password_hash($password, PASSWORD_BCRYPT);
		$query = db_query("INSERT INTO `users` (`id`, `username`, `passhash`, `displayname`, `image`, `bigimage`, `date`) VALUES (NULL, '$email', '$hash', '$username', 'profile.png', 'profile.png', CURRENT_TIMESTAMP)") or die(mysqli_error($connection));
		$return = $return . "<li>Registered succesfully</li>";
	}else{
		$return = $return . "<li>Passwords don't match.</li>";
	}
	$return = $return . "</ul>";
	return $return;
}

	function getUserFromID($id){
		$query = db_query('SELECT * FROM `users` WHERE id = ' . $id);
    while ($row = mysqli_fetch_assoc($query)) { 
			return $row;
		}	
	}

	function getComments($id){
		$comments = array();
		$query = db_query('SELECT * FROM `comments` WHERE postid = ' . $id);
    while ($row = mysqli_fetch_assoc($query)) { 
			array_push($comments, $row);
		}	
		return $comments;
	}

	// New post

	if(isset($_POST['postText']) && $_POST['postText'] != "" && $_POST['postTitle'] != ""){
		$postText = prepareStringforPost($_POST['postText']);
		$query = db_query('INSERT INTO `posts` (`id`, `userid`, `title`, `text`, `date`) VALUES (NULL, "' . $_SESSION['id'] .  '", "' . $_POST['postTitle'] . '", "' . $postText . '", CURRENT_TIMESTAMP)');
		header("location: /");
	}


	// POSTS
	$posts = array();
	$query = db_query('SELECT * FROM `posts`');
    while ($row = mysqli_fetch_assoc($query)) { 
		array_push($posts, $row);
	}
	$db['posts'] = array_reverse($posts);
	 unset($posts);
	 
	// SITE SETTINGS  
		$query = db_query('SELECT * FROM `settings`');
    while ($row = mysqli_fetch_assoc($query)) { 
		$site[$row['setting']] = $row['value'];
	}

	// SITE PAGES
	$pages = array();
	$query = db_query('SELECT * FROM `pages`');
	while ($row = mysqli_fetch_assoc($query)) { 
		$pages[$row['pageName']] = json_decode($row['pages']);
	}

	$users = array();
	$query = db_query('SELECT * FROM `users`');
    while ($row = mysqli_fetch_assoc($query)) { 
		array_push($users, $row);
	}
	$db['users'] = $users;
	 unset($users);