<?php
require(__DIR__.'/lib_db.php');
require(__DIR__.'/lib_session.php');
header('Content-type: application/json');


switch($_SERVER['REQUEST_METHOD']){
	case 'GET':
		if(isset($_GET['id'])) detail($pdo);
		else index($pdo);
		break;
	case 'POST': 
		create($pdo);
		break;
	case 'PUT':
		parse_str(file_get_contents("php://input"),$_PUT);
		edit($pdo,$_PUT);
		break;
	case 'DELETE':
		parse_str(file_get_contents("php://input"),$_DELETE);
		delete($pdo,$_DELETE['id']);
		break;
}


function index($pdo){
	$stmt=$pdo->prepare('SELECT ID,title,date FROM events');
	$stmt->execute([]);
	if(!isset($_SESSION['user/is_admin'])){
		$_SESSION['user/is_admin'] = 0;
	}
	die(json_encode(['events'=>$stmt->fetchAll(),'logged'=>isset($_SESSION['user/ID']),'admin'=>$_SESSION['user/is_admin']]));
}

function detail($pdo){
	$stmt = $pdo->prepare('SELECT events.capacity, events.ID, events.title, events.content, events.user_ID, events.date, COUNT(registration.user_ID) AS booked FROM events JOIN registration ON events.ID = registration.event_ID WHERE ID = ?');
	$stmt->execute([$_GET['id']]);
	$post=$stmt->fetch();
	if(isset($_SESSION['user/ID']) && ($post['user_ID']==$_SESSION['user/ID'] || $_SESSION['user/is_admin']==1)) $post['manage']=1;
	else $post['manage']=0;
	if(isset($_SESSION['user/ID'])){
		$post['logged']=1;
	}else{
		$post['logged']=0;
	}
	die(json_encode($post));
}

function create($pdo){
	if(!isset($_SESSION['user/ID'])) die(json_encode(['status'=>-1,'message'=>'This page is for registered users only. Please <a href="auth.php">Sign in</a>.']));
	if(count($_POST)>0){
		$stmt = $pdo->prepare('INSERT INTO events (title, content, date, capacity, user_ID) VALUES (?,?,?,?,?)');
		$stmt->execute([$_POST['title'],$_POST['content'],str_replace('T',' ',$_POST['date']),$_POST['capacity'],$_SESSION['user/ID']]);
		die(json_encode(['status'=>1,'message'=>'The message has been saved.']));
	}
}

function edit($pdo,$_PUT){
	if(count($_PUT)>0){
		$stmt = $pdo->prepare('UPDATE events SET title = ?, content = ?, date = ?, user_ID =? WHERE events.ID =?');
		$stmt->execute([$_PUT['title'],$_PUT['content'],str_replace('T',' ',$_PUT['date']),$_PUT['user_ID'],$_PUT['ID']]);
		die(json_encode(['status'=>1,'message'=>'Your data have been saved']));
	}
	die(json_encode(['status'=>-1,'message'=>'Your data were not saved']));
}

function delete($pdo,$id){
	$stmt = $pdo->prepare('SELECT * FROM events WHERE ID=?');
	$stmt->execute([$id]);
	$post=$stmt->fetch();
	if(!isset($_SESSION['user/ID']) && ($post['user_ID']!=$_SESSION['user/ID'] || $_SESSION['user/is_admin']!=1)) die(json_encode(['status'=>-1,'message'=>'You don\'t have the rights for this action']));
	$stmt = $pdo->prepare('DELETE FROM events WHERE ID=?');
	$stmt->execute([$id]);
	die(json_encode(['status'=>1,'message'=>'The post has been deleted']));
}