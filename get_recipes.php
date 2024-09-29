<?php
require("./cors_config/config.php");

include_once("./server/userrecipe_class.php");

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    try {
        $recipes = new User_Recipes();

        if (isset($_GET["user_id"])) {

            if (isset($_GET["page"])) {
                if (isset($_GET["search_query"])) {
                    echo $recipes->get_user_recipe((int)$_GET["user_id"], (int)$_GET["page"], strtolower($_GET["search_query"]));
                } else {
                    echo $recipes->get_user_recipe((int)$_GET["user_id"], (int)$_GET["page"]);
                }
            } else {
                if (isset($_GET["search_query"])) {
                    echo $recipes->get_user_recipe((int)$_GET["user_id"],search_query:strtolower($_GET["search_query"]));
                }
                else{
                    echo $recipes->get_user_recipe((int)$_GET["user_id"]);
                }
            }
        } else {

            if (isset($_GET["page"])) {

                if (isset($_GET["search_query"])) {
                    echo $recipes->get_all_recipe((int)$_GET["page"], strtolower($_GET["search_query"]));
                } else {
                    echo $recipes->get_all_recipe((int)$_GET["page"]);
                }
            } else {
                if (isset($_GET["search_query"])) {
                    echo $recipes->get_all_recipe(search_query:strtolower($_GET["search_query"]));
                } else {
                    echo $recipes->get_all_recipe();
                }
            }
        }
    } catch (Exception $e) {
        echo json_encode([
            "status" => 404,
            "msg" => $e->getMessage()
        ]);
    }
}
