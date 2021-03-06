<?php
require(__DIR__.'/lib_session.php');
header('Content-type: application/json');
if(isset($_GET['action']) && $_GET['action']=='signout'){
	
	if(isset($_SESSION['user/ID'])){
		session_destroy();
		die(json_encode(['status'=>1,'message'=>'You have been signed out']));
	}else{
		die(json_encode(['status'=>1, 'logged'=> 0,'message'=>'The user is not singed in']));
	}
	
}

if(isset($_GET['action']) && $_GET['action']=='checkadmin'){
	
	if($_SESSION['user/is_admin']==1){
		die(json_encode(['status'=>1, 'admin'=> 1,'message'=>'The user is an admin']));
	}else{
		die(json_encode(['status'=>1, 'admin'=> 0,'message'=>'The user is not an admin']));
	}
}

if(isset($_GET['action']) && $_GET['action']=='checklogged'){
	
	if(isset($_SESSION['user/ID'])){
		die(json_encode(['status'=>1, 'logged'=> 1,'message'=>'The user is singed in']));
	}else{
		die(json_encode(['status'=>1, 'logged'=> 0,'message'=>'The user is not singed in']));
	}
}

if(isset($_SESSION['user/ID'])) die(json_encode(['status'=>-1,'message'=>'The user is already logged in.']));

if(count($_POST)>0){
	switch($_POST['action']){
		case 'signin':
			signin($_POST['email'],$_POST['password']);
			break;
		case 'signup':
			signup($_POST['email'],$_POST['password'],$_POST['firstname'],$_POST['lastname']);
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

function signup($email,$password,$firstname,$lastname){
	require(__DIR__.'/lib_db.php');
	// Check if they already have an account
	$query=$pdo->prepare('SELECT ID FROM users WHERE email=?');
	$query->execute([$email]);
	if($query->rowCount()>0){
		die(json_encode(['status'=>-1,'message'=>'The user already exists. Please, sign in.']));
	}
	//Add the user to the database
	$query=$pdo->prepare('INSERT INTO users(email,password,firstname,lastname) VALUES(?,?,?,?)');
	$query->execute([$email,password_hash($password, PASSWORD_DEFAULT),$firstname,$lastname]);
	die(json_encode(['status'=>1,'message'=>'Your account has been created. Please, sign in.']));
	//Show a message
}
