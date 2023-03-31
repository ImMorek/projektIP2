<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class EmployeeDeletePage extends BasePage
{
    protected function prepare(): void
    {
        parent::prepare();
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $employeeId = $_POST["employeeId"];
        }

        $pdo = PDOProvider::get();
        $stmt = $pdo->prepare("DELETE FROM `key` WHERE `employee` = :employeeId");
        $stmt->execute(['employeeId' => $employeeId]);
        $stmt = $pdo->prepare("DELETE FROM `employee` WHERE `employee_id` = :employeeId");
        $stmt->execute(['employeeId' => $employeeId]);
        header("location: employees.php");


        //přesměrovat
    }

    protected function pageBody()
    {
        return "";
    }

}

$page = new EmployeeDeletePage();
$page->render();

?>