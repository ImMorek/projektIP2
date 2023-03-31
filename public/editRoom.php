<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EditRoomPage extends BasePage
{
    private $room;
    private $employees;
    private $error_msg;

    protected function prepare(): void
    {
        parent::checkIfLoggedIn();
        parent::prepare();
        //získat data z GET
        $roomId = filter_input(INPUT_GET, 'roomId', FILTER_VALIDATE_INT);
        if (!$roomId){
            throw new BadRequestException();
        }

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(empty(trim($_POST["name"]))) {
                $this->error_msg = "Jméno místnosti nemůže být prázdný!";
            } else {
                $newName = trim($_POST["name"]);
            }
            if(empty(trim($_POST["no"]) )) {
                $this->error_msg = "Číslo místnosti nemůže být prázdný!";
            } else {
                $newNo = trim($_POST["no"]);
            }
            if(!is_numeric(trim($_POST["no"]))) {
                $this->error_msg = "Číslo místnosti musí být číslo!";
            } else {
                $newPhone = trim($_POST["phone"]);
            }

            if(!is_numeric(trim($_POST["phone"])) && !empty(trim($_POST["phone"]))) {
                $this->error_msg = "Telefon místnosti musí být číslo!";
            } else {
                $newPhone = trim($_POST["phone"]);
            }


            if (empty($this->error_msg)) {
                $pdo = PDOProvider::get();
                $stmt = $pdo->prepare("UPDATE `room` SET `name` =:newName, `no` =:newNo, `phone` =:newPhone WHERE `room_id` =:roomId");
                $stmt->execute(['newName' => $newName,'newNo' => $newNo, 'newPhone' => $newPhone, 'roomId' => $roomId]);

                header("location: rooms.php");
            }
        }
        
        //najít místnost v databázi
        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT * FROM `room` WHERE `room_id`= :roomId");
        $stmt->execute(['roomId' => $roomId]);
        if ($stmt->rowCount() < 1)
            throw new NotFoundException();

        $this->room = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT `surname`, `name`, `employee_id` FROM `employee` WHERE `room`= :roomId ORDER BY `surname`, `name`");
        $stmt->execute(['roomId' => $roomId]);
        $this->employees = $stmt->fetchAll();

        $this->title = "Úprava místnosti {$this->room->no}";

    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'editRoom',
            ['room' => $this->room, 'employees' => $this->employees, 'error_msg' => $this->error_msg]
        );
    }

}

$page = new EditRoomPage();
$page->render();

?>
