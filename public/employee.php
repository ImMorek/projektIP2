<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EmployeeDetailPage extends BasePage
{
    private $employee;
    private $rooms;

    protected function prepare(): void
    {
        parent::checkIfLoggedIn();
        parent::prepare();
        //získat data z GET
        $employeeId = filter_input(INPUT_GET, 'employeeId', FILTER_VALIDATE_INT);
        if (!$employeeId)
            throw new BadRequestException();

        //najít místnost v databázi
        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("SELECT * FROM `employee` WHERE `employee_id`= :employeeId");
        $stmt->execute(['employeeId' => $employeeId]);
        if ($stmt->rowCount() < 1)
            throw new NotFoundException();

        $this->employee = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT room_id, name AS room_name FROM `key` INNER JOIN room ON (room.room_id = key.room) WHERE key.employee = :employeeId");
        $stmt->execute(['employeeId' => $employeeId]);
        $this->rooms = $stmt->fetchAll();

        $this->title = "Detail osoby {$this->employee->name} {$this->employee->surname}";

    }

    protected function pageBody()
    {
        //prezentovat data
        return MustacheProvider::get()->render(
            'employeeDetail',
            ['employee' => $this->employee, 'rooms' => $this->rooms]
        );
    }

}

$page = new EmployeeDetailPage();
$page->render();

?>
