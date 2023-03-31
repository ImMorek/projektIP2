<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EditRoomPage extends BasePage
{
    private $room = "";
    private $employees = "";
    private $error_msg = "";
    private $newName = "";
    private $newNo = "";
    private $newPhone = "";

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
                $this->newName = trim($_POST["name"]);
            }
            if(empty(trim($_POST["no"]) )) {
                $this->error_msg = "Číslo místnosti nemůže být prázdný!";
            } else {
                $this->newNo = trim($_POST["no"]);
            }
            if(!is_numeric(trim($_POST["no"]))) {
                $this->error_msg = "Číslo místnosti musí být číslo!";
            } else {
                $this->newPhone = trim($_POST["phone"]);
            }

            if(!is_numeric(trim($_POST["phone"])) && !empty(trim($_POST["phone"]))) {
                $this->error_msg = "Telefon místnosti musí být číslo!";
            } else {
                $this->newPhone = trim($_POST["phone"]);
            }


            if (empty($this->error_msg)) {
                $pdo = PDOProvider::get();
                $stmt = $pdo->prepare("INSERT INTO `room` (`room_id`, `no`, `name`, `phone`) VALUES (:roomId, :newNo, :newName, :newPhone)");
                $stmt->execute(['roomId' => $roomId,'newNo' => $this->newNo, 'newName' => $this->newName, 'newPhone' => $this->newPhone]);

                header("location: rooms.php");
            }
        }

        $this->title = "Nová místnost";

    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'editRoom',
            ['room' => $this->room, 'employees' => $this->employees, 'error_msg' => $this->error_msg, 'new_name' => $this->newName, 'new_no' => $this->newNo, 'new_phone' => $this->newPhone]
        );
    }

}

$page = new EditRoomPage();
$page->render();

?>
