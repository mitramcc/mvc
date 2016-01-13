<?php

class App{

	protected $controller = "home";
	protected $method = "index";
	protected $params = [];

	public function __construct(){

		$url = $this->parseUrl();

        if($url[0]=="admin"){
            array_shift($url);
            $file = "../app/controllers/admin/".$url[0].".php";
        }else{
		    $file = "../app/controllers/".$url[0].".php";
        }
        
		if(file_exists($file)){
			$this->controller = $url[0];
			unset($url[0]);
		    require_once($file);
        }else{
            #error_log("No Controller for ".$url[0]." in file: ".$file);
            header("Location: home");
            return false;
        }


		$this->controller = new $this->controller;

		if(isset($url[1])){
			if(method_exists($this->controller, $url[1])){
				$this->method = $url[1];
				unset($url[1]);
			}
		}

		$this->params = $url ? array_values($url) : [] ;
		call_user_func_array([$this->controller, $this->method], $this->params);
	}

	public function parseUrl()
	{
		if(isset($_GET['url'])){
			return explode("/", filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
		}
	}
}

?>