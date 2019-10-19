<?php 

class ImgResultsProvider{


	private $con;

	public function __construct($con) {
	
		$this->con = $con;
	}

	public function getNumResults($term) {
	//query database to get the number of matching results to display.
		$query = $this->con->prepare("SELECT COUNT(*) as total 
		FROM images 
		WHERE (title LIKE :term 
		OR alt LIKE :term)
		AND broken=0");
		
		$searchTerm =  "%". $term."%";
		$query->bindParam(":term", $searchTerm);
		$query->execute();

		$row = $query->fetch(PDO::FETCH_ASSOC);
		return $row["total"]; //display out
	
	}

	public function getResults($page, $pageSize, $term){

	//paging 
	$fromLimit = ($page - 1) * $pageSize;
	//page 1: 1-1 * 20 = 0
	//page 2: 2-1 * 20 = 20
	//page 3: 3- 1 * 20 = 40



	//query throught db get all the results display most popular first. 
		$query = $this->con->prepare("SELECT *  
		FROM images 
		WHERE (title LIKE :term 
		OR alt LIKE :term)
		AND broken=0 
		ORDER BY clicks DESC 
		LIMIT :fromLimit, :pageSize");
		
		//bind parameters for db security.
		$searchTerm =  "%". $term."%";
		$query->bindParam(":term", $searchTerm);
		$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
		$query->execute();

		$resultsHTML = "<div class='imgResults'>"; 



		$count=0;
		while($row = $query->fetch(PDO::FETCH_ASSOC)){
		$count++;
		
		$id = $row["id"];
		$imgURL = $row["imgURL"];
		$websiteURL = $row["websiteURL"];
		$title = $row["title"];
		$alt = $row["alt"];

		//displays the text in the image depeding on the parameters that it has. title, alt, link (order)
		if($title){
			$displayText = $title;
		}else if($alt){
			$displayText = $alt;
		}else{
			$displayText = $imgURL;
		}

		//structure for images.

		$resultsHTML .= "<div class='itemInGrid img$count'>
								<a href='$imgURL' data-linkId='$id' data-fancybox data-caption='$displayText' data-websiteurl='$websiteURL'>
								<script>
									$(document).ready(function(){
									
									loadImg(\"$imgURL\", \"img$count\");

									});
									</script>
								</a>
								<span class='imgDetails'>$displayText</span>
							
						</div>";

		}

		$resultsHTML .= "</div>";

		return $resultsHTML;

	}


}



?>