<?php

ob_start();

try {
	//using mysql connect to database named craftysearch with localhost.
	$con = new PDO("mysql:dbname=craftsearch;host=localhost", "root", "");
	//set error mode to errormode warning.
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);

}catch(PDOException $e){
	echo "Connection Failed: ". $e->getMessage();

}

?>