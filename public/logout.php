<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";
session_start();
class LogOutPage extends BasePage {
    protected function pageBody() {
        $_SESSION = array();
        session_destroy();
        header("location: login.php");
    }

}
$page = new LogOutPage();
$page->render();
?>