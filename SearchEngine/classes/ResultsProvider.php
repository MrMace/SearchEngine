<?php 

class ResultsProvider{


	private $con;

	public function __construct($con) {
	
		$this->con = $con;
	}

	public function getNumResults($term) {
	//query database to get the number of matching results to display.
		$query = $this->con->prepare("SELECT COUNT(*) as total FROM websites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term");
		
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
		$query = $this->con->prepare("SELECT * FROM websites WHERE title LIKE :term OR url LIKE :term OR keywords LIKE :term OR description LIKE :term ORDER BY clicks DESC LIMIT :fromLimit, :pageSize");
		
		$searchTerm =  "%". $term."%";
		$query->bindParam(":term", $searchTerm);
		$query->bindParam(":fromLimit", $fromLimit, PDO::PARAM_INT);
		$query->bindParam(":pageSize", $pageSize, PDO::PARAM_INT);
		$query->execute();

		$resultsHTML = "<div class='websiteResults'>"; 

		while($row = $query->fetch(PDO::FETCH_ASSOC)){
		
		$id = $row["id"];
		$url = $row["url"];
		$title = $row["title"];
		$description = $row["description"];

		$title = $this->trimmer($title, 50);
		$description = $this->trimmer($description, 250);

		$resultsHTML .= "<div class='resultsContain'>
							<h3 class='title'>
								<a class='result' href='$url' data-linkId='$id'>
								$title
								</a>
							</h3>
							<span class='url'>$url</span>
							<span class='description'>$description</span>
						</div>";

		}

		$resultsHTML .= "</div>";

		return $resultsHTML;

	}

	private function trimmer($string, $charLimit){
	
		$dots = strlen($string) > $charLimit ? "..." : "";

		return substr($string, 0 , $charLimit) . $dots;


		
	}

}



?>