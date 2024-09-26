<?php

require_once("./cors_config/config.php");

require_once("./server/auth_user_class.php");


if ($_SERVER["REQUEST_METHOD"] === "GET") {

    try {

        if (isset($_GET["user_id"])) {
            $new_auth = new Auth_User();

            $res = $new_auth->delete_user((int)$_GET["user_id"]);

            echo $res;
        }
        else{
            echo json_encode([
                "status" => 404,
                "msg" => "Please Provide the user Id"
            ]);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            "status" => 404,
            "msg" => $e->getMessage()
        ]);
    }
}
