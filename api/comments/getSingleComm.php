<?php
include '../headers.php';
include '../../config/conn.php';
include '../../controllers/commentsController.php';

$db = new connection();
$dbConn = $db->dbConnect();

$commentCtrl = new commentsController($dbConn);

$commentCtrl->comment->comm_id = isset($_GET['comm_id']) ? $_GET['comm_id'] : die();

$commentCtrl->getSingleComm();

if(!empty($commentCtrl->comment->comm_text)){
    echo json_encode(array("data" => array("comm_id" => $commentCtrl->comment->comm_id, 
        "comm_text" => $commentCtrl->comment->comm_text,
        "UserEmail" => $commentCtrl->comment->comm_user_email, "UserId" => $commentCtrl->comment->comm_user_id, 
        "comm_state" => $commentCtrl->comment->comm_state)));
}else{
    echo json_encode(array("response" => "comment no found", "status" => false));
}