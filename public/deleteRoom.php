<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class RoomDeletePage extends BasePage
{
    private $error_msg;
    protected function prepare(): void
    {
        parent::prepare();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $roomId = $_POST["roomId"];
            echo($_POST["roomId"]);
        }

        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT * FROM `employee` WHERE `room` =:roomId");
        $stmt->execute(['roomId' => $roomId]);
        $employee_ids = $stmt->fetchAll();

        //když poslal data
        if(count($employee_ids) != 0){ 
            session_start();
            $_SESSION["roomErrorMsg"] = "Nelze smazat místnost: V místnosti jsou lidé!";
        } else {
            echo("mazal bych btw");
            $stmt = $pdo->prepare("DELETE FROM `room` WHERE room_id = :roomId");
            $stmt->execute(['roomId' => $roomId]);
        }
        header("location: rooms.php");


        //přesměrovat
    }

    protected function pageBody()
    {
        return "";
    }

}

$page = new RoomDeletePage();
$page->render();

?>