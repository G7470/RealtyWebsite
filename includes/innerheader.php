<?php

require_once "includes/config.php";
require_once "includes/functions.php";
// Initialize the session
session_start();
if(!isset($_SESSION['prev_loc'])) {
	$_SESSION['prev_loc'] = array();
	$_SESSION['nav_loc'] = array();
}
if(!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] <> true) {
	// Redirect user to home page
    header("location: index.php");
	exit;
}
if(isset($_SESSION['vcode']) && $_SERVER['REQUEST_URI'] <> "/~jsb/ver_conf.php") {
	//Redirect user to confirmation page
	header("location: ver_conf.php");
	exit;
}

$user = $db->query("SELECT * FROM users WHERE username = ?", $_SESSION['username'])->fetchArray();

// Check for New Policy Agreements
$rows = $db->query("SELECT p.* FROM policies p, users u WHERE p.lastupdate > u.policy_agree AND u.id = ? AND u.usertype <> 2 ORDER BY p.id", $user['id'])->numRows();
if ($rows > 0 && $_SERVER['REQUEST_URI'] <> '/~jsb/pol_agr.php') {
	header("location: pol_agr.php");
	exit;
}

/** Back Button **/
$j = 0;
$found = false;
for($i = 0; $i < count($_SESSION['prev_loc']); $i++) {
	if($_SERVER['REQUEST_URI'] == $_SESSION['prev_loc'][$i]) {
		$found = true;
		$_SESSION['back_button'] = $_SESSION['nav_loc'][$i];
	}
}
if(!$found) {
	array_push($_SESSION['prev_loc'], $_SERVER['REQUEST_URI']);
	array_push($_SESSION['nav_loc'], $_SESSION['previous_location']);
	$_SESSION['back_button'] = $_SESSION['previous_location'];
}

?>
<!DOCTYPE HTML>
<html>
	<head>
		<title>JSB Enterprise</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
		<link rel="stylesheet" href="assets/css/main.css?v="<?php echo time(); ?> />
		<noscript><link rel="stylesheet" href="assets/css/noscript.css" /></noscript>
		<!-- Scripts -->
<script src="assets/js/jquery.min.js" ></script>
<script src="assets/js/jquery.scrollex.min.js" ></script>
<script src="assets/js/jquery.scrolly.min.js" ></script>
<script src="assets/js/browser.min.js" ></script>
<script src="assets/js/breakpoints.min.js" ></script>
<script src="assets/js/tinymce/tinymce.min.js"></script>
<script src="assets/js/util.js?v="<?php echo time(); ?>></script>
<script src="assets/js/main.js" ></script>
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
		
	</head>
	<body class="is-preload">

		<!-- Page Wrapper -->
			<div id="page-wrapper">

				<!-- Header -->
					<header id="header">
						<h1><a href="main.php">JSB Enterprise</a></h1>
						<h2>
							<?php
								if(!empty($_SESSION['back_button']) && $_SERVER['REQUEST_URI'] <> '/~jsb/main.php') {
									echo '<a class="button small" href="' . substr($_SESSION['back_button'], 6) . '">Back</a>';
								}
							?>
						</h2>
						<nav id="nav">
							<ul>
								<li class="special">
									<a href="#menu" class="menuToggle"><span>Menu</span></a>
									<div id="menu">
									<!-- Check here if user is completely logged in (not needing verification code) -->
									<?php 
										if(!isset($_SESSION['vcode']) || isset($_SESSION['prevpage'])) {
									?>
										<ul>
											<li><!--<select name="showopt" id="showopt">
													<option value="">- Pick View (User Default) -</option>
													<option value="1">User</option>
													<option value="2">Employee</option>
												</select>-->
													<li class="user employee"><a href="main.php">Home</a></li>
											<?php
												switch($user['usertype']) {
													case 1:
													?>
														
														<li class="user"><a href="order.php">Place New Order</a></li>
														<li class="user"><a href="order_history.php">View Your Orders</a></li>
														<li class="user"><a href="view_invoices.php">View/Pay Invoice</a></li>
														<li class="user"><a href="usercontact.php">Contact JSB</a></li>
													<?php
														break;
													case 2:
													?>
														<li class="employee"><a href="promote.php">Promotion Panel</a></li>
														<li class="employee"><a href="invoice_search.php">View Invoices</a></li>
														<li class="employee"><a href="view_realtor.php">View Realtor</a></li>
														<li class="employee"><a href="view_orders.php">View Orders</a></li>
														<li class="employee"><a href="policies.php">Update Policies</a></li>
														<li class="employee"><a href="services.php">Update Services</a></li>
														<li class="employee"><a href="cron_config.php">Email Notification Configuration</a></li>
														<li class="employee"><a href="global_config.php">Alert and General Configuration</a></li>
													<?php
														break;
												}
												?>
												<li class="user employee"><a href="acct_settings.php">Account Settings</a></li>
												<li class="user employee"><a href="logout.php"><b><font color="red">Logout</font></b></a></li>
										</ul>
										<?php
											}
										?>
									</div>
								</li>
							</ul>
						</nav>
					</header>
<?php
	$_SESSION['previous_location'] = $_SERVER['REQUEST_URI'];
?>