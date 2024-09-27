<?php

require_once("./cors_config/config.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    try {

        echo json_encode([
            "status" => 200,
            "msg" => "Welcome to the API"
        ]);

    } catch (Exception $e) {
        echo json_encode([
            "status" => 404,
            "msg" => $e->getMessage()
        ]);
    }
}
?>