<?php

require("./cors_config/config.php");

require_once("./server/userrecipe_class.php");


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    try {

        $data = file_get_contents("php://input");

        $en_data = json_decode($data, true);

        $new_recipe = new User_Recipes();

        $res = $new_recipe->enlist_user_recipe($en_data["user_id"], $en_data);

        echo $res;

    } catch (Exception $e) {
        echo json_encode([
            "status" => 404,
            "msg" => $e->getMessage()
        ]);
    }
}
?>