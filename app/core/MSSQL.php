<?php

class MSSQL extends PDO{

	function __construct(){
      putenv('TDSVER=80');
        if(MSSQL_DRIVER=="sqlsrv"){
            parent::__construct(MSSQL_DRIVER.":Server=".MSSQL_HOST.";Database=".MSSQL_NAME, MSSQL_USER, MSSQL_PASS);
        }else{
            parent::__construct(MSSQL_DRIVER.":host=".MSSQL_HOST.";dbname=".MSSQL_NAME.";charset=UTF-8", MSSQL_USER, MSSQL_PASS);
        }
        //$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
}

?>
