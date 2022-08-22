<?php
require_once "./includes/config.php";
require_once "./includes/functions.php";

$orderid = $db->escape(trim($_POST['id']));
$emailaddr = $db->escape(trim($_POST['emailaddr']));

$orderrows = $db->query("SELECT u.email_addr, o.* FROM users u JOIN orders o ON o.userid = u.id WHERE o.id = ?", $orderid)->numRows();
$order = $db->fetchArray();
if($orderrows == 1 && !empty($emailaddr)) {
	// Send email
	$to = $order['email_addr'];
	$subject = "JSB Enterprise Realty Services - Someone Interested in Listing!";
	
	$message = "
		<html>
		<head>
		<title>JSB Enterprise Realty Services</title>
		</head>
		<body>
			<p>Someone is interested in hearing more about your listing! The person's email address is ";
	$message .= $emailaddr;
	$message .= "</p><p>The property they are interested in is located at: ";
	$message .= $order['prop_addr1'] . ", ";
	if(isset($order['propr_addr2'])) {
		$message .= $order['prop_addr2'] . ", ";
	}
	$message .= $order['prop_city'] . ", " . $order['prop_state'] . ", " . $order['prop_zip'];
	$message .= ".</p>
			<p>Feel free to contact JSB Enterprise Realty Services at any time.</p>
			<p>Thank you, </p>
			<br />
			<p>JSB Enterprise Realty Services</p>
		</body>
		</html>
		";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";
	return "Success!";
	
}
else {
	return header("not-real.php", true, 404);
}

?>