<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EmployeesPage extends BasePage
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
        $stmt = $pdo->prepare("SELECT employee.name AS employeeName, employee.*, room.name AS room, room.phone AS roomPhone FROM `employee` INNER JOIN room ON employee.room = room.room_id");
        $stmt->execute();
        $employees = $stmt->fetchAll();

        $stmt = $pdo->prepare("SELECT MAX(employee_id) AS maximum FROM `employee`");
        $stmt->execute();
        $row = $stmt->fetch();
        $highest = $row->maximum + 1;

        session_start();
        $isAdmin = $_SESSION["admin"] == 1 ? "" : "disabled";

        //prezentovat data
        return MustacheProvider::get()->render('employeeList',['employees' => $employees, 'isadmin' =>$isAdmin, 'highest' => $highest]);
    }

}

$page = new EmployeesPage();
$page->render();

?>
