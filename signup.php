<?php

require_once("./cors_config/config.php");

require_once("./server/auth_user_class.php");


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $data = file_get_contents("php://input");

        $en_data = json_decode($data, true);

        $new_auth = new Auth_User();

        $res = $new_auth->signup_user($en_data);

        echo $res;

    } catch (Exception $e) {
        echo json_encode([
            "status" => 404,
            "msg" => $e->getMessage()
        ]);
    }
}

?>