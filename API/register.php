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
		delete($pdo,$_DELETE['event_ID'],$_DELETE['user_ID']);
		break;
}



function detail($pdo){
	$stmt = $pdo->prepare('SELECT users.firstname, users.lastname, users.email, users.ID FROM users JOIN registration ON registration.user_ID = users.ID WHERE registration.event_ID = ?');
	$stmt->execute([$_GET['id']]);
	die(json_encode(['users'=>$stmt->fetchAll()]));
}

function create($pdo){
	if(!isset($_SESSION['user/ID'])) die(json_encode(['status'=>-2,'message'=>'This action is for registered users only... Please signin']));
	
	$query=$pdo->prepare('SELECT user_ID FROM registration WHERE user_ID=? AND event_ID=?');
	$query->execute([$_SESSION['user/ID'],$_POST['id']]);
	if($query->rowCount()>0){
		die(json_encode(['status'=>-1,'message'=>'You have already registered.']));
	}

	$query=$pdo->prepare('SELECT posts.capacity, COUNT(registration.user_ID) AS booked FROM posts JOIN registration ON posts.ID = registration.event_ID WHERE ID = ?');
	$query->execute([$_POST['id']]);
	$avaliblity=$query->fetch();
	if ($avaliblity['booked']>=$avaliblity['capacity']){
		die(json_encode(['status'=>-1,'message'=>'The event is fully booked.']));
	}
	
	if(count($_POST)>0){
		$stmt = $pdo->prepare('INSERT INTO registration (event_ID, user_ID) VALUES (?,?)');
		$stmt->execute([$_POST['id'],$_SESSION['user/ID']]);
		die(json_encode(['status'=>1,'message'=>'You have been registered.']));
	}
}

function delete($pdo,$event_ID,$user_ID){
	$stmt = $pdo->prepare('SELECT * FROM registration WHERE user_ID=? AND event_ID=?');
	$stmt->execute([$user_ID,$event_ID]);
	$post=$stmt->fetch();
	/*
	if(!isset($_SESSION['user/ID']) || $post['user_ID']!=$_SESSION['user/ID']) die(json_encode(['status'=>-1,'message'=>'You don\'t have the rights for this action']));
	*/
	$stmt = $pdo->prepare('DELETE FROM registration WHERE user_ID=? AND event_ID=?');
	$stmt->execute([$user_ID,$event_ID]);
	die(json_encode(['status'=>1,'message'=>'The registration has been deleted']));
}