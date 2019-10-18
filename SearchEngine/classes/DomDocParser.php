<?php
class DomDocumentParser {

//contains the html of website visited.
	private $doc;

//constructor takes url
	public function __construct($url) {
	
	//created options to request the webpage. THe option is what we are going to retrive the website with, I use GET, browsers require a specification for a header which is the name of my bot searchCraft.
		$options = array('http'=> array('method'=>"GET",'header'=> "User-Agent: searchCraftBot/0.1\n")
		);
		//used when making request.
		$context = stream_context_create($options);

		//built in php class allows you to perform actions on webpages.
		$this->doc = new DomDocument();
		//loads the contents of reuqestd page. @ blocks error msg for html5.
		@$this->doc->loadHTML(file_get_contents($url, false, $context));
	}

	public function getLinks() {
		return $this->doc->getElementsByTagName('a');
	}

	public function getTitlesTags() {
		return $this->doc->getElementsByTagName('title');
	}

	public function getMetaTags() {
		return $this->doc->getElementsByTagName('meta');
	}

	public function getImages() {
		return $this->doc->getElementsByTagName('img');
	}

	
}

?>