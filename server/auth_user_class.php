<?php

abstract class Abstract_User
{
    abstract public function login_user(array $userdata): string;
    abstract public function signup_user(array $userdata): string;
    abstract public function delete_user(int $userid): string;
}

class Auth_User extends Abstract_User
{

    private $host_user = "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";

    private $user_name = "2F7Xc2PWaCveCeC.root";

    private $password = "SUgdYUO9Iz9T5eoh";

    private $database_name = "recipebook";

    private $port = 4000;

    private $database_conn = null;

    private $databse_conn_status = null;

    private $user_password = null;

    private $ssl_ca = "/xampp/htdocs/recipe_book_backend/db/isrgrootx1.pem";

    public function __construct()
    {
        try {

            $db = new PDO("mysql:host=$this->host_user;port=$this->port;dbname=$this->database_name", $this->user_name, $this->password,[
                PDO::MYSQL_ATTR_SSL_CA => $this->ssl_ca,
            ]);

            $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->database_conn = $db;
            
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

    public function login_user(array $userdata): string
    {
        try {

            $name = $userdata["name"];
            $email = $userdata["email"];
            $password = $userdata["password"];

            $h_pass = $this->database_conn->prepare("SELECT username,email,user_password FROM users WHERE email=:email");

            $h_pass->bindParam(":email", $email);

            $h_pass->execute();

            $res = $h_pass->fetchAll(PDO::FETCH_ASSOC);

            if ($res !== []) {

                if ($res[0]["username"] === $name) {
                    $verify = password_verify($password, $res[0]["user_password"]);

                    if ($verify) {
                        return json_encode([
                            "database_connectivity_status" => $this->databse_conn_status,
                            "status" => 200,
                            "msg" => "Successfully Logged In"
                        ]);
                    } else {
                        return json_encode([
                            "database_connectivity_status" => $this->databse_conn_status,
                            "status" => "Unable to signed up",
                            "msg" => "Password does not match"
                        ]);
                    }
                } else {
                    return json_encode([
                        "database_connectivity_status" => $this->databse_conn_status,
                        "status" => "Unable to signed up",
                        "msg" => "Username does not match"
                    ]);
                }
            } else {
                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "status" => "Unable to signed up",
                    "msg" => "Email Id not found"
                ]);
            }
        } catch (Exception $e) {
            $this->database_conn->rollBack();

            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => "Unable to signed up",
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function signup_user(array $userdata): string
    {
        try {
            $email = $userdata["email"];


            $u_email = $this->database_conn->prepare("SELECT email FROM users WHERE email=:email");
            $u_email->bindParam(":email", $email);

            $u_email->execute();

            $res = $u_email->fetchAll(PDO::FETCH_ASSOC);

            if ($res === []) {

                $name = $userdata["name"];
                $this->user_password = password_hash($userdata["password"], PASSWORD_DEFAULT);

                //Begin Transaction
                $this->database_conn->beginTransaction();


                // signup data to the sql
                $enl_query = $this->database_conn->prepare("INSERT INTO users (username, email, user_password) VALUES (:username, :email,:userpassword)");


                $enl_query->bindParam(":username", $name);
                $enl_query->bindParam(":email", $email);
                $enl_query->bindParam(":userpassword", $this->user_password);


                $enl_query->execute();

                // getting the user id 
                $u_id = $this->database_conn->prepare("SELECT user_id FROM users WHERE email=:email");

                $u_id->bindParam(":email", $email);

                $u_id->execute();

                $res = $u_id->fetchAll(PDO::FETCH_ASSOC);

                // Commit the Changes
                $this->database_conn->commit();

                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "status" => 200,
                    "msg" => "Successfully Signed In",
                    "userID" => $res[0]["user_id"],
                ]);
            } else {
                return json_encode([
                    "database_connectivity_status" => $this->databse_conn_status,
                    "status" => 500,
                    "msg" => "Email Already Exists Unable to Sign Up",
                ]);
            }
        } catch (Exception $e) {

            $this->database_conn->rollBack();

            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => "Unable to signed up",
                "msg" => $e->getMessage()
            ]);
        }
    }

    public function delete_user(int $userid): string
    {
        try{
            $this->database_conn->beginTransaction();

            $d_user = $this->database_conn->prepare("DELETE FROM users WHERE user_id=:userid");

            $d_user->bindParam(":userid",$userid,PDO::PARAM_INT);

            $d_user->execute();

            $this->database_conn->commit();

            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => 200,
                "msg" => "User Deleted Successfully"
            ]);

        }
        catch (Exception $e){
            $this->database_conn->rollBack();

            return json_encode([
                "database_connectivity_status" => $this->databse_conn_status,
                "status" => "Unable to Delete Account",
                "msg" => $e->getMessage()
            ]);
        }
    }
}
