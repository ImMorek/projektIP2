<?php
require_once __DIR__ . "/../bootstrap/bootstrap.php";

class IndexPage extends BasePage
{
    public function __construct()
    {
        $this->title = "Prohlížeč databáze firmy";
    }

    protected function pageBody()
    {
        parent::checkIfLoggedIn();
        return MustacheProvider::get()->render('mainPages',[]);
    }


}

$page = new IndexPage();
$page->render();

?>