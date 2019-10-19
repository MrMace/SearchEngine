<?php
include("config.php");//db
include("classes/ResultsProvider.php"); //class call
include("classes/ImgResultsProvider.php"); //class call
	//If user don't type anything in search, resond with messege.
	$term = isset($_GET["term"]) ? $_GET["term"] : exit("I work with words. No words, no search!");

	//If term is not set make websites tab active.
	$type = isset($_GET["type"]) ? $_GET["type"] : "websites";
	//gets the page var and reverts to one if none;
	$page = isset($_GET["page"]) ? $_GET["page"] : 1;
	
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Search Craft</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.css" />
	<link rel="stylesheet" type"text/css" href="styles/style.css">
	<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
</head>
<body>

<div class="wrapper">
	<div class="header">

		<div class="headerContent">
			
			<div class="logoContain">
				<a href="index.php">
					<img src="imgs/logo.png">
				</a>
		</div>

		<div class="searchContain">
			<form action="search.php" method="GET">

				
					<div class="searchBarContain">
						<input type="hidden" name="type" value="<?php echo $type ?>">
						<input class="searchBox" type="text" name="term" value="<?php echo$term; ?>">
				<button class="searchBtn">
				<img src="imgs/icons/search.png">
				</button>
					</div>
			</form>

		</div>
		</div>

		<div class="tabNavContain">

			<ul class="tabNav">
				<li class="<?php echo $type == 'websites' ? 'active' : '' ?>">
					<a href='<?php echo "search.php?term=$term&type=websites"; ?>'>
					Websites
					</a>
				</li>
				<li class="<?php echo $type == 'images' ? 'active' : '' ?>">
					<a href='<?php echo "search.php?term=$term&type=images"; ?>'>
					Images
					</a>
				</li>
			</ul>
		</div>
	</div>
	
	<div class="resultSection">
	
		<?php 

		//different classes sleected based on the link of website or images in search.
		
			if($type == "websites"){
				$resultsProvider = new ResultsProvider($con);
				$pageSize = 20;

			}else{
					$resultsProvider = new ImgResultsProvider($con);
					$pageSize = 50;
			}
		
			$numOfResults =  $resultsProvider->getNumResults($term);

			echo"<p class='resultCount'>$numOfResults Results Found";

			echo $resultsProvider->getResults($page,$pageSize,$term);
		
		?>
	
	</div>

	<div class="pagenationContain">


		<div class="pageBtns">

			<div class="pageNumContainer">


			</div>

			<?php 
			//page system to calculate remain pages.
			$pagesShow = 10;
			$numOfPages = ceil($numOfResults/ $pageSize);
			$pagesLeft = min($pagesShow, $numOfPages);
			//round down pages
			$currentPage = $page - floor($pagesShow / 2);
			//if current page is less than one set to one.
			if($currentPage < 1){
				$currentPage = 1;
			}
			if($currentPage + $pagesLeft > $numOfPages +1){
				$currentPage = $numOfPages + 1 - $pagesLeft;
			}

			while($pagesLeft != 0 && $currentPage <= $numOfPages){

				if($currentPage == $page){
			
				echo "<div class='pageNumContainer'>
				
				<span class='pageNum'>$currentPage</span>
				</div>";
				}else {
				echo "<div class='pageNumContainer'>
					<a href='search.php?term=$term&type=$type&page=$currentPage'>
						<span class='pageNum'>$currentPage</span>
					</a>
				</div>";
				}

				$currentPage++;
				$pagesLeft--;

			}
			
			
			?>
		</div>
	</div>

</div>
<script type="text/javascript" src="js/script.js"></script>
<script src="https://unpkg.com/masonry-layout@4/dist/masonry.pkgd.min.js"></script>
<script src="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.7/dist/jquery.fancybox.min.js"></script>


</body>
</html>