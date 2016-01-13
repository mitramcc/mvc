<?php

class View{
	
	function __construct(){
	}

	public function render($name='')
	{
		require_once("../app/views/".$name.".phtml");
	}
}

?>