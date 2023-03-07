<?php
include '../headers.php';
include '../../config/conn.php';
include '../../controllers/commentsController.php';

$db = new connection();
$dbConn = $db->dbConnect();

$commentsCtrl = new commentsController($dbConn);

$commentsCtrl->comment->comm_art_id = isset($_GET['comm_art_id']) ? $_GET['comm_art_id'] : die();

$result = $commentsCtrl->getCommByArt();

if(!empty($result)){
    $dataArr = [];
    $dataArr['data'] = array();

    $resultArr = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach($resultArr as $r){
        $commData = array("comm_id" => $r['comm_id'], "comm_text" => $r['comm_text'],
        "comm_state" => $r['comm_state'], "comm_created_at" => $r['comm_created_at'], "UserId" => $r['UserId'], 
        "UserName" => $r['UserName'], "UserEmail" => $r['UserEmail'], "UserRolId" => $r['UserRolId']);
        
        array_push($dataArr['data'], $commData);
    }

    echo json_encode($dataArr);
} else {
    echo json_encode(array("response" => "comment not found", "status" => false));
}