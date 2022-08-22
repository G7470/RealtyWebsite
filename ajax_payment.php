<?php
include "./includes/config.php";

$error = false;
$invid = $db->escape($_POST['invid']);
$ordid = $db->escape($_POST['orderid']);
$email_addr = $db->escape($_POST['emailaddress']);
$payid = $db->escape($_POST['payid']);
$status = $db->escape($_POST['status']);
$amount = $db->escape($_POST['amount']);
$currency_code = $db->escape($_POST['currency_code']);
$transaction_time = $db->escape(strtotime($_POST['transaction_time']));

// Set up email in case of errors 
$to = "jsb@jsbenterpriserealtyservices.com";
$subject = "JSB Website Notification - Payment Error";
$message = "<html>
			<head>
			<title>JSB Enterprise Realty Services</title>
			</head>
			<style>
				table {
				  font-family: arial, sans-serif;
				  border-collapse: collapse;
				  width: 100%;
				}

				td, th {
				  border: 1px solid #dddddd;
				  text-align: left;
				  padding: 8px;
				}

				tr:nth-child(even) {
				  background-color: #dddddd;
				}
			</style>
			<body>";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";

if(empty($invid) || empty($ordid) || empty($email_addr) 
|| empty($payid) || empty($status) || empty($amount)
|| empty($currency_code) || empty($transaction_time)) {
	
	// invalid transaction - send notification to admim
	$error = true;
	$message .= "<p> There was an invalid payment made to an invoice. Below are the details:
			<table>
				<tr><th><b>Order ID</b></th><th><b>Invoice ID </b></th><th><b>Email Address</b></th><th><b>Amount</b></th><th><b>Currency Code</b></th>
				<th><b>
				Transaction Time</b></th></tr>";
	$message .= "<tr><td>" . $ordid . "</td><td>" . $invid . "</td><td>" . $email_addr . "</td><td>" . $amount . "</td><td>" . $currency_code . "</td>";
	$message .= "<td>" . $transaction_time . "</td></tr></table>";
}
elseif($status <> "COMPLETED") {
	
	// not a completed transaction yet - send notification to admin
	$error = true;
	$message .= "<p> The payment has not been completed, so the payment has not been posted. Below are the details:
			<table>
				<tr><th><b>Order ID</b></th><th><b>Invoice ID </b></th><th><b>Email Address</b></th><th><b>Amount</b></th><th><b>Currency Code</b></th>
				<th><b>
				Transaction Time</b></th></tr>";
	$message .= "<tr><td>" . $ordid . "</td><td>" . $invid . "</td><td>" . $email_addr . "</td><td>" . $amount . "</td><td>" . $currency_code . "</td>";
	$message .= "<td>" . $transaction_time . "</td></tr></table>";
}
else {
	// Post to database
	if(!$db->query("INSERT INTO payments (inv_id, orderid, pmt_type, email_address,
		txn_id, amount, currency_code, pay_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
		, $invid, $ordid, '1', $email_addr, $payid, $amount, $currency_code, $transaction_time)) {
			
		// Error posting to database - send notification to admin
		$error = true;
		$message .= "<p> There was a problem posting this payment to the website. Below are the details:
			<table>
				<tr><th><b>Order ID</b></th><th><b>Invoice ID </b></th><th><b>Email Address</b></th><th><b>Amount</b></th><th><b>Currency Code</b></th>
				<th><b>
				Transaction Time</b></th></tr>";
		$message .= "<tr><td>" . $ordid . "</td><td>" . $invid . "</td><td>" . $email_addr . "</td><td>" . $amount . "</td><td>" . $currency_code . "</td>";
		$message .= "<td>" . $transaction_time . "</td></tr></table>";
	}
	else {
		$totaldue = 0;
		// Check if invoice has been paid
		
		// Get Invoice
		$getinv = $db->query("SELECT * FROM invoices WHERE id = ?", $invid)->fetchArray();
		
		$totaldue = $getinv['amount'];
		
		// Get Charges
		$getchargerows = $db->query("SELECT SUM(amount) AS charge_sum FROM charges WHERE inv_id = ?", $invid)->numRows();
		$getcharges = $db->fetchArray();
		
		// Get Payments
		$getpaymentrows = $db->query("SELECT SUM(amount) AS payment_sum FROM payments WHERE inv_id = ?", $invid)->numRows();
		$getpayments = $db->fetchArray();
		
		if($getchargerows > 0) {
			$totaldue += $getcharges['charge_sum'];
		}
		
		if($getpaymentrows > 0) {
			$totaldue -= $getpayments['payment_sum'];
		}
		
		if($totaldue <= 0) {
			// PAID - Update Invoice Status
			$db->query("UPDATE invoices SET status = 1 WHERE id = ?", $invid);
		}
	}
}
if($error) {
	//mail($to, $subject, $message, $headers);
	return header("not-real.php", true, 404);
}
else {
	return "Success!";
}
?>