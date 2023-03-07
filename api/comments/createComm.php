<?php
include '../headers.php';
include '../../config/conn.php';
include '../../controllers/commentsController.php';

function createComment()
{
    $tkn = getAuthorizationHeader();

    if ($tkn == null) {
        echo json_encode(array("response" => "no token!", "status" => false));
        die();
    } else {
        $ch = curl_init();

        $url = "http://localhost:92/api/Users/GetIdFromToken";

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
            $db = new connection();
            $dbConn = $db->dbConnect();

            $commentCtrl = new commentsController($dbConn);

            $commData = json_decode(file_get_contents("php://input"));

            $commentCtrl->comment->comm_text = $commData->comm_text;
            $commentCtrl->comment->comm_art_id = $commData->art_id;
            $commentCtrl->comment->comm_user_id = (int) $parssedData['result'];

            if ($commentCtrl->createComm()) {
                echo json_encode(array("response" => "comment created", "status" => true));
            } else {
                echo json_encode(array("response" => "something went wrong!", "status" => false));
            }
        } else {
            echo json_encode(array("response" => $parssedData['errorMessages'], "status" => false));
            die();
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

createComment();
