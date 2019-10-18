<?php 
include("config.php");//db
include("classes/DomDocParser.php");

$crawlFinished = array(); //links crawled
$crawling = array(); //need to crawl
$previouslyFoundImg = array(); //imgs already found.



function linkExists($url){
	global $con;
	//prepare the statement then bind the parameters
	$query = $con->prepare("SELECT * FROM websites WHERE url = :url");


	$query->bindParam(":url", $url);
	$query->execute();
	return $query->rowCount() != 0;
	 
}
 

function insertLink($url, $title, $description , $keywords){
	global $con;
	//prepare the statement then bind the parameters
	$query = $con->prepare("INSERT INTO websites(url , title, description, keywords) 
	Values(:url, :title, :description, :keywords)");

	$query->bindParam(":url", $url);
	$query->bindParam(":title", $title);
	$query->bindParam(":description", $description);
	$query->bindParam(":keywords", $keywords);

	 return $query->execute();
	 
}

function insertImg($url, $src, $alt , $title){
	global $con;
	//prepare the statement then bind the parameters
	$query = $con->prepare("INSERT INTO images(websiteURL,	imgURL,	alt, title) 
	Values(:websiteURL,	:imgURL,:alt, :title)");

	$query->bindParam(":websiteURL", $url);
	$query->bindParam(":imgURL", $src);
	$query->bindParam(":alt", $alt);
	$query->bindParam(":title", $title);

	 return $query->execute();
	 
}
 
//convert links to actaul links.
function createLink($src, $url){

	$scheme =  parse_url($url)["scheme"]; //http
	$host =  parse_url($url)["host"]; //website

	//if the link grabbed has //
	if(substr($src, 0,2) == "//"){
		$src = $scheme.":". $src;
		//if  linked grabbed is formated /
	}else if(substr($src, 0,1) == "/"){
		$src = $scheme."://". $host . $src;
		// if linked grabbed is formated ./
	}else if(substr($src, 0,2) == "./"){
		$src = $scheme."://". $host . dirname(parse_url($url)["path"]) . substr($src , 1) ;
		// if linked grabbed is formated with ../
	}else if(substr($src, 0,3) == "../"){
		$src = $scheme."://". $host . "/" . $src;
		//if linked grabbed doesnt have http or https.
	}else if(substr($src, 0,5) != "https" && substr($src, 0,4) != "http"){
		$src = $scheme."://". $host . "/" . $src;

	}
	return $src;
}

function getDetails($url){

	global $previouslyFoundImg;

	$parser = new DomDocumentParser($url);

	$titlesArray = $parser-> getTitlesTags();

	//removes error if nothing to return.
	if(sizeof($titlesArray)==0 || $titlesArray->item(0) == NULL) {
		return;
	}

	$title = $titlesArray->item(0)->nodeValue;
	//format title to remove new line.
	$title = str_replace("\n","", $title);

	//if there is not title for link dont crawl.
	if($title == ""){
		return;
	}

	$description = "";
	$keywords = "";

	$metasArray = $parser->getMetaTags();

	//Goes through each pages meta tag
	foreach($metasArray as $meta){
		
		//gets the descrition for the site meta tag.
		if($meta->getAttribute("name") == "description"){
		
			$description = $meta-> getAttribute("content");
		}
		//gets the keywords from the page keywords meta tag.
		if($meta->getAttribute("name") == "keywords"){
		
			$keywords= $meta-> getAttribute("content");
		}

	}

	//format description to remove new line.
	$description = str_replace("\n","", $description);

	//format keywords to remove new line.
	$keywords = str_replace("\n","", $keywords);

	//keep from inserting duplicates into db.
	if(linkExists($url)){
		echo "$url already exists<br>";
	}else if(insertLink($url, $title, $description , $keywords)){
	
		echo "Success: $url <br>";
	}else{
	
	echo "Failed to insert $url <br>";
	}


	$imgArray = $parser->getImages();
	foreach($imgArray as $image){
		$src= $image->getAttribute("src");
		$alt= $image->getAttribute("alt");
		$title= $image->getAttribute("title");

		if(!$title && !$alt){
			continue;
		}
		$src = createLink($src, $url);

		if(!in_array($src, $previouslyFoundImg)){
			$previouslyFoundImg[] = $src;

			insertImg($url, $src, $alt , $title);
		}

	}


}

function linkFollow($url) {

	global  $crawlFinished;
	global  $crawling;

	$parser = new DomDocumentParser($url);

	//retrieve all the links
	$linkList = $parser->getLinks();

	//loops through the list and grabs teh links to display.
	foreach($linkList as $link){
		$href = $link->getAttribute("href");

		//disregards link that go no where.
		if(strpos($href, "#") !== false){
			continue;
		}else if(substr($href, 0, 11) == "javascript:"){
			continue;
		}
		//calls the create link function to display the links
		$href = createLink($href, $url);

		if(!in_array($href , $crawlFinished)){
			$crawlFinished[] = $href;
			$crawling[] = $href;

			//insert href
			getDetails($href);
		}
		//else return;

		//echo $href . "<br>";
	}
	//takes off array
	array_shift($crawling);

	foreach($crawling as $website){
		linkFollow($website);
	}
	

}

//$startURL = "https://www.michaels.com/";
$startURL = "https://www.kiwico.com/diy/Arts-and-Crafts-Ideas/1";
//$startURL = "https://www.hobbylobby.com/";
//$startURL = "https://www.orlandoweekly.com/Blogs/archives/2019/07/04/new-museum-of-american-arts-and-crafts-movement-readies-for-its-grand-opening-this-winter-in-st-pete";
//$startURL = "https://rhythmsofplay.com/arts-crafts/";
//$startURL = "https://highlandsartsandcraft.org/";
//$startURL = "https://www.enasco.com/c/Art-Supplies-Crafts";
//$startURL = "https://artsandcraftshomes.com/";
//$startURL = "https://www.artistcraftsman.com/";
//$startURL = "http://www.arts-crafts.com/";
//$startURL = "https://www.education.com/activity/arts-and-crafts/";
//$startURL = "https://artfulparent.com/kids-arts-crafts-activities-500-fun-artful-things-kids/";
//$startURL = "https://www.etsy.com/";
//$startURL = "https://www.joann.com/projects/projects-videos/arts-and-crafts-projects/";
//$startURL = "https://www.happinessishomemade.net/quick-easy-kids-crafts-anyone-can-make/";
linkFollow($startURL);


?>