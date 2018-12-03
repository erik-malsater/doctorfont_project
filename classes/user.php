<?php
session_start();
class User 
{

    private $pdo;

    public function __construct($pdo){
        $this->pdo = $pdo;
    }

    public function register($username, $password, $email){

        /*Checking if username, password is to short or if email is missing
        or if any of them is just made upp of empty space.*/

        if(strlen($username) < 4 || ctype_space($username)){
            $_SESSION['invalid_username'] = true;
        } else{
            $_SESSION['invalid_username'] = false;
        }
        
        if(strlen($password) < 4 || ctype_space($password)){
            $_SESSION['invalid_password'] = true;
        } else{
            $_SESSION['invalid_password'] = false;
        }
        
        if(strlen($email) < 1 || ctype_space($email)){
            $_SESSION['invalid_email'] = true;
        } else{
            $_SESSION['invalid_email'] = false;
        }

        //Checking if username or email is taken.

        $statement = $this->pdo->prepare("SELECT username, mail FROM users WHERE username = :username OR mail = :email");
        $statement->execute(
            [
                ":username" => $username,
                ":email" => $email
            ]
        );

        $fetched_data = $statement->fetch();
        
        if($fetched_data['username'] == $username){
            $_SESSION['taken_username'] = true;
        } else{
            $_SESSION['taken_username'] = false;
        }
        
        if($fetched_data['mail'] == $email){
            $_SESSION['taken_email'] = true;
        } else{
            $_SESSION['taken_email'] = false;
        }

        if ($_SESSION['invalid_username'] || $_SESSION['invalid_password']
         || $_SESSION['invalid_email'] || $_SESSION['taken_username']
          || $_SESSION['taken_email']) {
            header('Location: ../views/register_view.php');
            exit();
        }
/*
        if($_SESSION['taken_username'] || $_SESSION['taken_email']){
            header('Location: ../views/register_view.php');
        }
*/
        //Registering user data in database.

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $statement = $this->pdo->prepare("INSERT INTO users (username, password, mail) VALUES (:username, :password, :email)");

        $statement->execute(
            [
                ":username" => $username,
                ":password" => $hashed_password,
                ":email" => $email
            ]
        );

        header('Location: ../views/login_view.php');
        

    }


    public function login($username, $password){

        //Fetch all information from logged in user
        $statement = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $statement->execute(
            [
                ":username" => $username,
            ]
        );

        $fetched_user = $statement->fetch();

        $is_password_correct = password_verify($password, $fetched_user["password"]);


        if($is_password_correct){
            $_SESSION['username'] = $fetched_user['username'];
            $_SESSION['user_id'] = $fetched_user['id'];
            $_SESSION['is_admin'] = $fetched_user['is_admin'];
            $_SESSION['is_logged_in'] = true;
            header('Location: ../index.php');
            exit();
        }else{
            header('Location: ../views/login_view.php?login_failed=true');
            exit();
        }
    }

    
}
