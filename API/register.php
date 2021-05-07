<?php
require(__DIR__.'/lib_db.php');
require(__DIR__.'/lib_session.php');
header('Content-type: application/json');


switch($_SERVER['REQUEST_METHOD']){
	case 'GET':
		if(isset($_GET['id'])) detail($pdo);
		break;
	case 'POST': 
		create($pdo);
		break;
	case 'DELETE':
		parse_str(file_get_contents("php://input"),$_DELETE);
		delete($pdo,$_DELETE['id']);
		break;
}



function detail($pdo){
	$stmt = $pdo->prepare('SELECT ID, user_ID FROM registration WHERE event_ID=?');
	$stmt->execute([$_GET['id']]);
	die(json_encode(['users'=>$stmt->fetchAll()]));
}

function create($pdo){
	if(!isset($_SESSION['user/ID'])) die(json_encode(['status'=>-1,'message'=>'This page is for registered users only. Please <a href="auth.php">Sign in</a>.']));
	
	$query=$pdo->prepare('SELECT ID FROM registration WHERE user_ID=? AND event_ID=?');
	$query->execute([$_SESSION['user/ID'],$_POST['id']]);
	if($query->rowCount()>0){
		die(json_encode(['status'=>-1,'message'=>'You have already registered.']));
	}
	
	if(count($_POST)>0){
		$stmt = $pdo->prepare('INSERT INTO registration (event_ID, user_ID) VALUES (?,?)');
		$stmt->execute([$_POST['id'],$_SESSION['user/ID']]);
		die(json_encode(['status'=>1,'message'=>'You have been registered.']));
	}
}

function delete($pdo,$id){
	$stmt = $pdo->prepare('SELECT * FROM registration WHERE ID=?');
	$stmt->execute([$id]);
	$post=$stmt->fetch();
	if(!isset($_SESSION['user/ID']) || $post['user_ID']!=$_SESSION['user/ID']) die(json_encode(['status'=>-1,'message'=>'You don\'t have the rights for this action']));
	$stmt = $pdo->prepare('DELETE FROM posts WHERE ID=?');
	$stmt->execute([$id]);
	die(json_encode(['status'=>1,'message'=>'The post has been deleted']));
}