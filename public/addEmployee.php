<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EditRoomPage extends BasePage
{
    private $employee = "";
    private $rooms = "";
    private $error_msg = "";
    private $newName = "";
    private $newSurname = "";
    private $newJob = "";
    private $newWage = "";
    private $newRoom = "";

    protected function prepare(): void
    {
        parent::checkIfLoggedIn();
        parent::prepare();
        //získat data z GET
        $employeeId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId){
            throw new BadRequestException();
        }

        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if(empty(trim($_POST["name"]))) {
                $this->error_msg = "Jméno zaměstnance nemůže být prázdný!";
            } else {
                $this->newName = trim($_POST["name"]);
            }
            if(empty(trim($_POST["surname"]))) {
                $this->error_msg = "Příjmení zaměstnance nemůže být prázdný!";
            } else {
                $this->newSurname = trim($_POST["surname"]);
            }
            if(empty(trim($_POST["job"]) )) {
                $this->error_msg = "Pozice zaměstnance nemůže být prázdná!";
            } else {
                $this->newJob = trim($_POST["job"]);
            }
            
            if(empty(trim($_POST["wage"]) )) {
                $this->error_msg = "Plat zaměstnance nemůže být prázdný!";
            } else if(!is_numeric(trim($_POST["wage"]))) {
                $this->error_msg = "Plat zaměstnance musí být číslo!";
            } else {
                $this->newWage = trim($_POST["wage"]);
            }
            
            if(empty(trim($_POST["room"]) )) {
                $this->error_msg = "Místnost zaměstnance nemůže být prázdná!";
            } else if(!is_numeric(trim($_POST["room"]))) {
                $this->error_msg = "Místnost zaměstnance musí být číslo!";
            } else {
                $this->newRoom = trim($_POST["room"]);
            }

            if (empty($this->error_msg)) {
                $pdo = PDOProvider::get();
                $stmt = $pdo->prepare("INSERT INTO `employee` (`employee_id`, `name`, `surname`, `job`, `wage`, `room`,  `login`, `password`, `admin`) VALUES (:employeeId, :newName, :newSurname, :newJob, :newWage, :newRoom, '', '', 0)");
                $stmt->execute(['employeeId' => $employeeId,'newName' => $this->newName, 'newSurname' => $this->newSurname, 'newJob' => $this->newJob, 'newWage' => $this->newWage, 'newRoom' => $this->newRoom]);

                header("location: employees.php");
            }
        }

        $this->title = "Nový zaměstnanec";

    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'editEmployee',
            ['employee' => $this->employee, 'rooms' => $this->rooms, 'error_msg' => $this->error_msg, 'new_name' => $this->newName, 'new_surname' => $this->newSurname, 'new_job' => $this->newJob, 'new_wage' => $this->newWage, 'new_room' => $this->newRoom]
        );
    }

}

$page = new EditRoomPage();
$page->render();

?>
