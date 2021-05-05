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
	$stmt=$pdo->prepare('SELECT ID,email FROM users');
	$stmt->execute([]);
	die(json_encode($stmt->fetchAll()));
}

function detail($pdo){
	$stmt = $pdo->prepare('SELECT ID, email, firstname, lastname, is_admin FROM users WHERE ID=?');
	$stmt->execute([$_GET['id']]);
	$user=$stmt->fetch();
	if(isset($_SESSION['user/ID']) && ($user['ID']==$_SESSION['user/ID'] || $_SESSION['user/is_admin']==1)) $user['manage']=1;
	else $user['manage']=0;
	die(json_encode($user));
}

function edit($pdo,$_PUT){
	if(count($_PUT)>0){
		$stmt = $pdo->prepare('UPDATE users SET email = ?, firstname = ?, lastname = ?, is_admin = ?,  WHERE users.ID =?');
		$stmt->execute([$_PUT['email'],$_PUT['firstname'],$_PUT['lastname'],$_PUT['is_admin'],$_PUT['ID']]);
		die(json_encode(['status'=>1,'message'=>'Your data have been saved']));
	}
	die(json_encode(['status'=>-1,'message'=>'Your data were not saved']));
}

function delete($pdo,$id){
	$stmt = $pdo->prepare('SELECT * FROM users WHERE ID=?');
	$stmt->execute([$id]);
	$user=$stmt->fetch();
	if(!isset($_SESSION['user/ID']) || $user['user_ID']!=$_SESSION['user/ID']) die(json_encode(['status'=>-1,'message'=>'You don\'t have the rights for this action']));
	$stmt = $pdo->prepare('DELETE FROM users WHERE ID=?');
	$stmt->execute([$id]);
	die(json_encode(['status'=>1,'message'=>'The user has been deleted']));
}