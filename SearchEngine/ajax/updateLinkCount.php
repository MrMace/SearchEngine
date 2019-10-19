<?php
include("../config.php");


//if the link Id has been send go ahead update values.
if(isset($_POST["linkId"])){

$query = $con->prepare("UPDATE websites SET clicks = clicks + 1 WHERE id=:id");
$query->bindParam(":id", $_POST["linkId"]);
$query->execute();

}else{
	echo "No link passed.";
}

?>