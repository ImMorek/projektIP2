<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";
session_start();
class ChangePasswordPage extends BasePage {
    private $userInfo;
    private $login;
    private $error_msg;
    protected function pageBody() {
        parent::checkIfLoggedIn();
        $this->login = $_SESSION["username"];

        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT password FROM `employee` WHERE `login`= :login");
        $stmt->execute(['login' => $this->login]);
        
        $this->userInfo = $stmt->fetch();

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(empty(trim($_POST["oldPass"]))) {
                $this->error_msg = "Vyplňte staré heslo";
            }
            else {
                $oldPass = trim($_POST["oldPass"]);
            }
            if(empty(trim($_POST["newPass"]))) {
                $this->error_msg = "Vyplňte nové heslo";
            }
            else {
                $newPass = trim($_POST["newPass"]);
            }
            if(empty(trim($_POST["newPassRe"]))) {
                $this->error_msg = "Zopakujte nové heslo!";
            }
            else {
                $newPassRe = trim($_POST["newPassRe"]);
            }


            
            
            if($newPass == $oldPass) {
                $this->error_msg = "Nové heslo nemůže být stejné, jako staré";
            }
            if($newPass != $newPassRe) {
                $this->error_msg = "Nové heslo a zopakované heslo se neshodují!";
            }
            if($oldPass != $this->userInfo->password) {
                $this->error_msg = "Špatně zadané heslo!";
            }
            if(empty($this->error_msg) && $_SESSION["loggedin"]) {
                $_SESSION["passwordChanged"] = true;
                echo("password change incoming");
                if($_SESSION["passwordChanged"]) {
                    echo("password je true");
                }
                $stmt = $pdo->prepare("UPDATE `employee` SET `password` =:newPassword WHERE `login` = :login");
                $stmt->execute(['newPassword' => $newPass,'login' => $this->login]);
            }

        }

        if($_SESSION["passwordChanged"]) {
            $_SESSION["passwordChanged"] = false;
            return MustacheProvider::get()->render('passwordChanged', [] );
        }
        return MustacheProvider::get()->render('changePassword', ['login' => $this->login, 'error_msg' => $this->error_msg] );
    }

}
$page = new ChangePasswordPage();
$page->render();
?>