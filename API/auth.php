<?php
require(__DIR__.'/lib_session.php');
header('Content-type: application/json');
if(isset($_GET['action']) && $_GET['action']=='signout' && isset($_SESSION['user/ID'])){
	session_destroy();
	die(json_encode(['status'=>1,'message'=>'You have been signed out']));
}
if(isset($_SESSION['user/ID'])) die(json_encode(['status'=>-1,'message'=>'The user is already logged.']));

if(count($_POST)>0){
	switch($_POST['action']){
		case 'signin':
			signin($_POST['email'],$_POST['password']);
			break;
		case 'signup':
			signup($_POST['email'],$_POST['password']);
			break;
	}
}
die(json_encode(['status'=>-1,'message'=>'This route is invalid']));


function signin($email,$password){
	require(__DIR__.'/lib_db.php');
	// Check if the user is in the database
	$query=$pdo->prepare('SELECT ID,password,is_admin FROM users WHERE email=?');
	$query->execute([$email]);
	if($query->rowCount()==0){
		die(json_encode(['status'=>-1,'message'=>'The user does not exist. Please, sign up.']));
	}
	//Check whether the password is correct
	$user=$query->fetch();
	if(password_verify($password,$user['password'])){
		$_SESSION['user/ID']=$user['ID'];
		$_SESSION['user/is_admin']=$user['is_admin'];
		die(json_encode(['status'=>1,'message'=>'Welcome to our website']));
	}else{
		die(json_encode(['status'=>-1,'message'=>'The email or password are incorrect']));
	}
}

function signup($email,$password){
	require(__DIR__.'/lib_db.php');
	// Check if they already have an account
	$query=$pdo->prepare('SELECT ID FROM users WHERE email=?');
	$query->execute([$email]);
	if($query->rowCount()>0){
		echo 'The user already exists. Please, sign in.';
		return;
	}
	//Add the user to the database
	$query=$pdo->prepare('INSERT INTO users(email,password) VALUES(?,?)');
	$query->execute([$email,password_hash($password, PASSWORD_DEFAULT)]);
	echo 'Your account has been created. Please, sign in.';
	//Show a message
}
