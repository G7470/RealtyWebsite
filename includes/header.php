<?php
	// config
	require_once "config.php";
	
	// Initialize the session
	session_start();
?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>JSB Enterprise</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css?v=<?php echo time();?>" />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<script src="assets/js/jquery.min.js" ></script>
		<script src="assets/js/jquery.scrollex.min.js" ></script>
		<script src="assets/js/jquery.scrolly.min.js" ></script>
		<script src="assets/js/browser.min.js" ></script>
		<script src="assets/js/breakpoints.min.js" ></script>
	</head>
	<body class="is-preload">

		<!-- Page Wrapper -->
			<div id="page-wrapper">

				<!-- Header -->
					<header id="header">
						<h1><a href="index.php">JSB Enterprise</a></h1>
						<nav id="nav">
							<ul>
								<li class="special">
									<a href="#menu" class="menuToggle"><span>Menu</span></a>
									<div id="menu">
										<ul>
											<li><a href="index.php">Home</a></li>
											<li><a href="login.php">Login</a></li>
											<li><a href="register.php">Register</a></li>
											<li><a href="contact.php">Contact Us</a></li>
										</ul>
									</div>
								</li>
							</ul>
						</nav>
					</header>