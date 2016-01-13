<?php

class Controller{

	function __construct(){
        $this->load("pub", true);
        $this->slides = $this->pub->getSlides();
	}

	public function model($name, $admin=false)
	{
        if($admin){
		  $path = "../app/models/admin/".$name.".php";
        }else{
          $path = "../app/models/".$name.".php";
        }

		if(file_exists($path)){
			require_once($path);
			$modelName = $name."_Model";
			$this->model = new $modelName();

		}else{
			error_log("NO MODEL 4 ".$name);
		}
	}

    public function load($name, $admin=false)
    {
        if($admin){
          $path = "../app/models/admin/".$name.".php";
        }else{
          $path = "../app/models/".$name.".php";
        }

        if(file_exists($path)){
            require_once($path);
            $modelName = $name."_Model";
            $this->{$name} = new $modelName();

        }else{
            error_log("NO MODEL 4 ".$name);
        }

    }

	public function view($view, $data=[])
	{
		$path = "../app/views/".$view.".phtml";
		if(file_exists($path)){
			require_once($path);

		}else{
			error_log("NO VIEW 4 ".$view);
		}
	}

    public function getCurrentLocation($admin=false)
    {
        return BASE_URL.($admin=="admin" ? 'admin/' : '').strtolower(get_class($this))."/";
    }

    public function getBaseLocation()
    {
        return BASE_URL;
    }

    public function getPublicLocation()
    {
        return PUBLIC_URL;
    }

    /**
    * Set current page
    * @page: page from url
    * @max: max nr of pages
    */
    public function setPage($page, $max){

        $page = (int) $page;
        $page = $page<0 ? 1 : $page;
        $page = $page>$max ? $max : $page;
        return $page;
    }

    /**
    * Checks if index exists and prints it
    * @index: index
    * @array: array
    */
    public function printIndex($index, $array)
    {
        return isset($array[$index]) ? $array[$index] : '' ;
    }

    public function selectIndex($iterationIndex, $myIndex)
    {
        if(isset($myIndex) && $iterationIndex==$myIndex)
           return "selected";
        return "";
    }

    /**
    * Checks if index exists and prints it
    * @index: index
    * @array: array
    */
    public function printDate($date)
    {
        if(!strtotime($date))
            return "- - -";
        return date("Y-m-d", strtotime($date)) ;
    }

    public function redirect($location)
    {
        header("Location: ".BASE_URL.$location);
    }

    public function printSessionMessages()
    {
        if(!Session::is_set("messages") || empty(Session::get("messages")))
            return false;

        foreach (Session::get("messages") as $index => $message) {
            $this->message = $message;
            $this->view("page/warnings");
            Session::removeMessage($index);
        }

    }

}

?>