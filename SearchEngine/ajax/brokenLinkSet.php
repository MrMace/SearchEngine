<?php
include("../config.php");

//if a link is broken send to db so it does not call the image again.
if(isset($_Post["src"])){

$query = $con->prepare("UPDATE images SET broken =  1 WHERE imgURL=:src");
$query->bindParam(":src", $_POST["src"]);
$query->execute();


}else{
	echo "No src passed.";
}


?>