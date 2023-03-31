<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class RoomsPage extends BasePage
{

    public function __construct()
    {
        $this->title = "Výpis místností";
    }

    protected function pageBody()
    {
        parent::checkIfLoggedIn();
        //získat data
        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT * FROM `room`");
        $stmt->execute();
        $rooms = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT MAX(room_id) AS maximum FROM `room`");
        $stmt->execute();
        $row = $stmt->fetch();
        $highest = $row->maximum + 1;
        
        session_start();
        $isAdmin = $_SESSION["admin"] == 1 ? "" : "disabled";
        if(isset($_SESSION["roomErrorMsg"])) {
            $error_msg = $_SESSION["roomErrorMsg"];
            $_SESSION["roomErrorMsg"] = "";
        }

        return MustacheProvider::get()->render('roomList',['rooms' => $rooms, 'isadmin' => $isAdmin, 'highest' => $highest, 'error_msg' => $error_msg]);
    }

}

$page = new RoomsPage();
$page->render();

?>
