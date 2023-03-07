<?php
include '../headers.php';
include '../../config/conn.php';
include '../../controllers/articlesController.php';

function updtArticle()
{
    $tkn = getAuthorizationHeader();

    if ($tkn == null) {
        echo json_encode(array("response" => "no token!", "status" => false));
    } else {
        $ch = curl_init();

        $url = "http://localhost:92/api/Users/CheckRole";

        $headers = [
            "Authorization: {$tkn}"
        ];

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);

        $unparssedData = curl_exec($ch);
        curl_close($ch);

        $parssedData = json_decode($unparssedData, true);

        if ($parssedData['isSuccess']) {
            $valid_extensions = array('jpeg', 'jpg', 'png');

            $oldImg = isset($_POST['oldImg']) ? $_POST['oldImg'] : null;

            if (!empty($_FILES['imagen'])) {
                $fileName = basename($_FILES["imagen"]["name"]);
                $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

                if (in_array($fileType, $valid_extensions)) {
                    $image = $_FILES['imagen']['tmp_name'];
                    $imgContent = base64_encode(file_get_contents($image));

                    $db = new connection();
                    $dbConn = $db->dbconnect();

                    $articleCtrl = new articlesController($dbConn);

                    $articleCtrl->article->art_id = $_POST['art_id'];
                    $articleCtrl->article->art_name = $_POST['titulo'];
                    $articleCtrl->article->art_desc = $_POST['texto'];
                    $articleCtrl->article->art_img_ext = $fileType;
                    $articleCtrl->article->art_img = $imgContent;

                    if ($articleCtrl->updtArticle()) {
                        echo json_encode(array("response" => "article updated", "status" => true));
                    } else {
                        echo json_encode(array("response" => "something went wrong", "status" => false));
                    }
                }
            } else {
                if ($oldImg == null || $oldImg == "null") {
                    $db = new connection();
                    $dbConn = $db->dbconnect();

                    $articleCtrl = new articlesController($dbConn);

                    $articleCtrl->article->art_id = $_POST['art_id'];
                    $articleCtrl->article->art_name = $_POST['titulo'];
                    $articleCtrl->article->art_desc = $_POST['texto'];
                    $articleCtrl->article->art_img_ext = "";
                    $articleCtrl->article->art_img = null;

                    if ($articleCtrl->updtArticle()) {
                        echo json_encode(array("response" => "article updated", "status" => true));
                    } else {
                        echo json_encode(array("response" => "something went wrong", "status" => false));
                    }
                } else {
                    $imgData = base64_decode($oldImg);
                    $f = finfo_open();

                    $mime_type = finfo_buffer($f, $imgData, FILEINFO_MIME_TYPE);
                    $img_ext = substr($mime_type, 6, strlen($mime_type));

                    finfo_close($f);

                    $db = new connection();
                    $dbConn = $db->dbconnect();

                    $articleCtrl = new articlesController($dbConn);

                    $articleCtrl->article->art_id = $_POST['art_id'];
                    $articleCtrl->article->art_name = $_POST['titulo'];
                    $articleCtrl->article->art_desc = $_POST['texto'];
                    $articleCtrl->article->art_img_ext = $img_ext;
                    $articleCtrl->article->art_img = $oldImg;

                    if ($articleCtrl->updtArticle()) {
                        echo json_encode(array("response" => "article updated", "status" => true));
                    } else {
                        echo json_encode(array("response" => "something went wrong", "status" => false));
                    }
                }
            }
        } else {
            echo json_encode(array("response" => "no authorized", "status" => false));
        }
    }
}

function getAuthorizationHeader()
{
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) { //Nginx or fast CGI
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        // Server-side fix for bug in old Android versions (a nice side-effect of this fix means we don't care about capitalization for Authorization)
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        //print_r($requestHeaders);
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    return $headers;
}

updtArticle();
