<?php

abstract class Abstract_User{
    abstract public function login_user(array $userdata) : string;
    abstract public function signup_user(array $userdata) : string;
}

class Auth_User extends Abstract_User{

    private $host_user = "localhost";

    private $user_name = "root";

    private $password = null;

    private $database_name = "recipebook";

    private $database_conn = null;

    private $databse_conn_status = null;

    private $user_password = null;

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

    public function login_user(array $userdata): string
    {
        return "";
    }

    public function signup_user(array $userdata): string
    {
        $name = $userdata["name"];
        $email = $userdata["email"];
        $this->user_password = $userdata["password"];
        return "";
    }
}

?>