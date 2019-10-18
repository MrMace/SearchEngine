<?php

	//If user don't type anything in search, resond with messege.
	$term = isset($_GET["term"]) ? $_GET["term"] : exit("I work with words. No words, no search!");

	//If term is not set make websites tab active.
	$type = isset($_GET["type"]) ? $_GET["type"] : "websites";
	
	
?>
<!DOCTYPE html>
<html>
<head>
	<title>Search Craft</title>
	<link rel="stylesheet" type"text/css" href="styles/style.css">
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
						<input class="searchBox" type="text" name="term">
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
	

</div>

</body>
</html>