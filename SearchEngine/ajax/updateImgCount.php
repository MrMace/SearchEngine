<?php
include("../config.php");


//if the link Id has been send go ahead update values.
if(isset($_POST["imgURL"])){

$query = $con->prepare("UPDATE images SET clicks = clicks + 1 WHERE imgURL=:imgURL");
$query->bindParam(":imgURL", $_POST["imgURL"]);
$query->execute();

}else{
	echo "No img url passed.";
}

?>