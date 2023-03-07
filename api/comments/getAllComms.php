<?php
include '../headers.php';
include '../../config/conn.php';
include '../../controllers/commentsController.php';

$db = new connection();
$dbConn = $db->dbConnect();

$commentsCtrl = new commentsController($dbConn);

$result = $commentsCtrl->getAllComms();

if (!empty($result)) {
    $dataArr = [];
    $dataArr['data'] = array();

    $resultArr = $result->fetchAll(PDO::FETCH_ASSOC);

    foreach($resultArr as $r){
        $commData = array("comm_id" => $r['comm_id'], "comm_text" => $r['comm_text'],
        "comm_state" => $r['comm_state'], "comm_created_at" => $r['comm_created_at'], "UserId" => $r['UserId'], 
        "UserName" => $r['UserName'], "UserEmail" => $r['UserEmail'], "UserRolId" => $r['UserRolId'],
        "art_id" => $r['art_id'], "art_name" => $r['art_name'], "art_desc" => $r['art_desc'], 
        "art_img_ext" => $r['art_img_ext'], "art_img" => $r['art_img']);

        array_push($dataArr['data'], $commData);
    }

    echo json_encode($dataArr);
}else{
    echo json_encode(array("response" => "there is no data to show", "status" => false));
}
