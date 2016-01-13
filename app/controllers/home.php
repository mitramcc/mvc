<?php
class Home extends Controller{

    public function __construct(){
        parent::__construct();
        Session::init();
    }

    public function index()
    {
        $this->view("index");
    }

    public function cookies()
    {
        $cookies_eu = Session::getCookie("cookies_eu");
        if (empty($cookies_eu)) {
            $cookies_eu = Session::setCookie("cookies_eu", "visible", 31556926);
        }
    }

    public function contactForm()
    {
        if(!isset($_POST) || empty($_POST["msg"]))
            return false;
        $this->load("email");
        if ($this->email->sendContact($_POST)) {
            echo 1;
        }
    }
}
?>