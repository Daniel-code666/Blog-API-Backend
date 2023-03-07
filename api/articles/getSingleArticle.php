<?php
include '../headers.php';
include '../../config/conn.php';
include '../../controllers/articlesController.php';

$db = new connection();
$dbConn = $db->dbconnect();

$articleCtrl = new articlesController($dbConn);

$articleCtrl->article->art_id = isset($_GET['art_id']) ? $_GET['art_id'] : die();

$articleCtrl->getSingleArticle();

if(!empty($articleCtrl->article->art_name)){
    echo json_encode(array('data'=>array('art_id' => $articleCtrl->article->art_id, 
        'art_name' => $articleCtrl->article->art_name, 'art_desc' => $articleCtrl->article->art_desc, 
        'art_img' => $articleCtrl->article->art_img, 'art_img_ext' => $articleCtrl->article->art_img_ext,
        'created_at' => $articleCtrl->article->created_at)));
}else{
    echo json_encode(array('response' => "no data found", "status" => false));
}


