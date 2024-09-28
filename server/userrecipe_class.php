<?php

abstract class Abstract_UserRecipes
{
    abstract public function get_all_recipe(int $page,string $search_query): string;
    abstract public function get_user_recipe(int $user_id, int $page,string $search_query): string;
    abstract public function enlist_user_recipe(int $userid, array $recipe_data): string;
}

class User_Recipes extends Abstract_UserRecipes
{

    private $host_user = "localhost";

    private $user_name = "root";

    private $password = null;

    private $database_name = "recipebook";

    private $database_conn = null;

    private $databse_conn_status = null;

    public function __construct()
    {
        try {

            $db = new PDO("mysql:host=$this->host_user;dbname=$this->database_name", $this->user_name, $this->password);

            $this->database_conn = $db;

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if ($db) {
                $this->databse_conn_status = [
                    "status" => 200,
                    "msg" => "Connection Successfully Established"
                ];
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "Connection Not Established",
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function get_all_recipe(int $page = 0,string $search_query=""): string
    {
        try {

            if ($page <= 0) {
                $res = $this->database_conn->prepare("SELECT * FROM recipes WHERE title LIKE :squery");

                $s_pattern = "%".$search_query."%";

                $res->bindParam(":squery",$s_pattern,PDO::PARAM_STR);

                $res->execute();

                $result = $res->fetchAll(PDO::FETCH_ASSOC);

                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "recipes" => $result
                ]);
            } else {

                $limit = 10;

                $offset = $limit * $page - $limit;

                $res = $this->database_conn->prepare("SELECT * FROM recipes WHERE title LIKE :squery LIMIT :datalimit OFFSET :dataoffset");

                $s_pattern = "%".$search_query."%";

                $res->bindParam(":squery",$s_pattern,PDO::PARAM_STR);
                $res->bindParam(":datalimit",$limit,PDO::PARAM_INT);
                $res->bindParam(":dataoffset",$offset,PDO::PARAM_INT);

                $res->execute();

                $result = $res->fetchAll(PDO::FETCH_ASSOC);

                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "recipes" => $result
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => "Cannot fetch data from server",
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function get_user_recipe(int $user_id, int $page = 0,string $search_query=""): string
    {

        try {
            if ($page <= 0) {
                $res_recipes = $this->database_conn->prepare("SELECT * FROM recipes WHERE user_id=:userid AND title LIKE :squery");

                $s_pattern = "%".$search_query."%";

                $res_recipes->bindParam(":squery",$s_pattern,PDO::PARAM_STR);
                $res_recipes->bindParam(":userid",$user_id,PDO::PARAM_INT);

                $res_recipes->execute();

                $result_user_recipes = $res_recipes->fetchAll(PDO::FETCH_ASSOC);

                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "recipes" => $result_user_recipes
                ]);
            } else {
                $limit = 10;

                $offset = $limit * $page - $limit;

                $res_recipes = $this->database_conn->prepare("SELECT * FROM recipes WHERE user_id=:userid AND title LIKE :squery LIMIT :datalimit OFFSET :dataoffset");

                $s_pattern = "%".$search_query."%";

                $res_recipes->bindParam(":squery",$s_pattern,PDO::PARAM_STR);
                $res_recipes->bindParam(":userid",$user_id,PDO::PARAM_INT);
                $res_recipes->bindParam(":datalimit",$limit,PDO::PARAM_INT);
                $res_recipes->bindParam(":dataoffset",$offset,PDO::PARAM_INT);


                $res_recipes->execute();

                $result_user_recipes = $res_recipes->fetchAll(PDO::FETCH_ASSOC);

                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "recipes" => $result_user_recipes
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => "Cannot fetch data from server",
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function enlist_user_recipe(int $userid, array $recipe_data): string
    {
        try {

            $title = $recipe_data['title'];
            $description = $recipe_data['description'];
            $ingredients = json_encode($recipe_data['ingredients']);
            $image_url = $recipe_data['image_url'];
            $category = $recipe_data['category'];
            $doe = $recipe_data["date_of_enlist"];


            //Begin Transaction
            $this->database_conn->beginTransaction();

            // add data to the sql
            $enl_query = $this->database_conn->prepare("INSERT INTO recipes (title, description, ingredients, image_url, category, user_id,date_of_enlist) VALUES (:title, :description,:ingredients, :image_url, :category,:userid,:doe)");

            $enl_query->bindParam(":title",$title);
            $enl_query->bindParam(":description",$description);
            $enl_query->bindParam(":ingredients",$ingredients);
            $enl_query->bindParam(":image_url",$image_url);
            $enl_query->bindParam(":category",$category);
            $enl_query->bindParam(":userid",$userid,PDO::PARAM_INT);
            $enl_query->bindParam(":doe",$doe);


            $enl_query->execute();

            // getting the recipe id 
            $r_id = $this->database_conn->prepare("SELECT recipe_id FROM recipes WHERE user_id=:userid AND description = :description");

            $r_id->bindParam(":userid",$userid,PDO::PARAM_INT);
            $r_id->bindParam(":description",$description);

            $r_id->execute();

            $res = $r_id->fetchAll(PDO::FETCH_ASSOC);


            // Commit the Changes
            $this->database_conn->commit();

            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => 200,
                "msg" => "Data added Successfully",
                "recipe ID" => $res[0]['recipe_id']
            ]);
        } catch (Exception $e) {


            // Rollback 
            $this->database_conn->rollBack();

            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => "Cannot add data to the database",
                "msg" => $e->getMessage()
            ]);
        }
    }
}
?>