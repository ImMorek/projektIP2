<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";
session_start();
class LoginPage extends BasePage {
    private $login;
    private $password = "";
    private $userInfo;
    private $error_msg;
    protected function prepare(): void {

        $this->login = ($_SERVER["REQUEST_METHOD"] == "POST") ? trim($_POST["login"]) : "";

        parent::prepare();

        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT login, password, admin FROM `employee` WHERE `login`= :login");
        $stmt->execute(['login' => $this->login]);
        
        $this->userInfo = $stmt->fetch();

        if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
            header("location: index.php");
            exit;
        }
        if($_SERVER["REQUEST_METHOD"] == "POST"){
            if(empty(trim($_POST["login"]))){
                $login_err = true;
                $this->error_msg = "Zadejte prosím login.";
            } else{
                $this->login = trim($_POST["login"]);
            }
            if(empty(trim($_POST["password"]))){
                $password_err = true;
                $this->error_msg = "Zadejte prosím heslo.";
            } else{
                $this->password = trim($_POST["password"]);
            }


            if(!$login_err && !$password_err && !empty($this->userInfo)){
                //Přidat hashování nějak s password_verify()
                if($this->password == $this->userInfo->password){
                    
                    session_start();
                    $_SESSION["loggedin"] = true;
                    $_SESSION["username"] = $this->userInfo->login;
                    $_SESSION["admin"] = $this->userInfo->admin;
                    header("location: index.php");
                } else{
                    $this->error_msg = "Chybný jméno nebo heslo.";
                }
            }
        }
    }

    protected function pageBody()
    {
        return MustacheProvider::get()->render(
            'login',
            ['login' => $this->login, 'error_msg' => $this->error_msg]
        );
    }
}
$page = new LoginPage();
$page->render();
?>