<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EditEmployeePage extends BasePage
{
    private $employee;
    private $roomsWithKeys;
    private $error_msg;

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
                $newName = trim($_POST["name"]);
            }
            if(empty(trim($_POST["surname"]))) {
                $this->error_msg = "Příjmení zaměstnance nemůže být prázdný!";
            } else {
                $newSurname = trim($_POST["surname"]);
            }

            if(empty(trim($_POST["job"]) )) {
                $this->error_msg = "Pozice zaměstnance nemůže být prázdná!";
            } else {
                $newJob = trim($_POST["job"]);
            }
            if(empty(trim($_POST["wage"]) )) {
                $this->error_msg = "Plat zaměstnance nemůže být prázdný! Přece tu nepracuju zadarmo";
            } else if(!is_numeric(trim($_POST["wage"]))) {
                $this->error_msg = "Plat zaměstnance musí být číslo!";
            } else {
                $newWage = trim($_POST["wage"]);
            }
            //Místnosti, hádám že tohle bude moct odejít až použiju radio button
            if(empty(trim($_POST["room"]) )) {
                $this->error_msg = "Místnost nemůže být prázdná!";
            } else {
                $newRoom = trim($_POST["room"]);
            }

            if (empty($this->error_msg)) {
                $pdo = PDOProvider::get();
                $stmt = $pdo->prepare("UPDATE `employee` SET `name` =:newName, `surname` =:newSurname, `job` =:newJob, `wage` =:newWage, `room` =:newRoom WHERE `employee_id` =:employeeId");
                $stmt->execute(['newName' => $newName, 'newSurname' => $newSurname,'newJob' => $newJob, 'newWage' => $newWage, 'newRoom' => $newRoom, 'employeeId' => $employeeId]);

                header("location: employees.php");
            }
        }
        
        //najít místnost v databázi
        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT * FROM `employee` WHERE `employee_id`= :employeeId");
        $stmt->execute(['employeeId' => $employeeId]);
        if ($stmt->rowCount() < 1)
            throw new NotFoundException();

        $this->employee = $stmt->fetch();

        //S tímto příkazem se omlouvám, využil jsem umělé inteligence chatgpt, jiný způsob pro checkboxy mě nenapadl
        $stmt = $pdo->prepare("SELECT k.room, 
        CASE WHEN e.key_id IS NOT NULL THEN e.key_id ELSE NULL END AS hasKey 
        FROM (
        SELECT DISTINCT room 
        FROM `key`) k
        LEFT JOIN (
        SELECT room, key_id 
        FROM `key`
        WHERE employee = :employeeId) e 
        ON k.room = e.room;");
        $stmt->execute(['employeeId' => $employeeId]);
        $this->roomsWithKeys = $stmt->fetchAll();

        $this->title = "Úprava osoby {$this->employee->name} {$this->employee->surname}";

    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'editEmployee',
            ['employee' => $this->employee, 'employeeKeys' => $this->roomsWithKeys]
        );
    }

}

$page = new EditEmployeePage();
$page->render();

?>
