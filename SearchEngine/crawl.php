<?php 

include("classes/DomDocParser.php");

$crawlFinished = array(); //links crawled
$crawling = array(); //need to crawl
 
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
	echo "URL: $url, Title: $title<br>";

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
		else return;

		//echo $href . "<br>";
	}
	//takes off array
	array_shift($crawling);

	foreach($crawling as $website){
		linkFollow($website);
	}
	

}

$startURL = "https://www.michaels.com/";
linkFollow($startURL);


?>