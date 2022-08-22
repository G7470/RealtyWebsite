<?php
<?php
require_once "../includes/config.php";
require_once "../includes/functions.php";

function SendEmails() {

// Send 7 Days Before Due Email

/*$getID = $db->query("SELECT * FROM emails_config WHERE email_val = 'seven_before'")->fetchArray();
$config = $db->query("SELECT * FROM email_text WHERE id = ?", $getID['id'])->fetchArray();
$sql = "SELECT DISTINCT u.id, u.email_addr, u.company FROM users u JOIN orders o ON o.userid = u.id JOIN invoices i ON i.orderid = o.id WHERE i.status = 0 AND (duedate + (86400 * 7)) >= UNIX_TIMESTAMP() AND (duedate + (86400 * 6)) < UNIX_TIMESTAMP()";
$rows = $db->query($sql)->fetchAll();
foreach($rows as $sevdays) {
	// Send email
	$to = $sevdays['email_addr'];
	$subject = "JSB Enterprise Realty Services Invoice Due";
	
	$message = "
		<html>
		<head>
		<title>JSB Enterprise Realty Services</title>
		</head>
		<body>";
	$message .= stripslashes($config['email_text']);
	$message .= "
		</body>
		</html>
		";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";

	if(!mail($to,$subject,$message,$headers)) {
		// Send failure email
		$to = "jsb@jsbenterpriserealtyservices.com";
		$subject = "JSB Website Notification - Failed Email - Invoice Due";
		
		$message = "
			<html>
			<head>
			<title>JSB Enterprise Realty Services</title>
			</head>
			<body>
			<p>An email has failed to be sent to " . $sevdays['company'] . " at the following email address: " . $sevdays['email_addr'] . ". </p>
			<p>Please contact them via another means to notify them of their invoice being due.
			</body>
			</html>
			";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";
		
		mail($to, $subject, $message, $headers);
	}
}
// Send Invoice Past Due Email

$getID = $db->query("SELECT * FROM emails_config WHERE email_val = 'after_due_date'")->fetchArray();
$config = $db->query("SELECT * FROM email_text WHERE id = ?", $getID['id'])->fetchArray();
$sql = "SELECT DISTINCT u.id, u.email_addr, u.company FROM users u JOIN orders o ON o.userid = u.id JOIN invoices i ON i.orderid = o.id WHERE i.status = 0 AND duedate > UNIX_TIMESTAMP() AND (duedate - 86400) < UNIX_TIMESTAMP()";
$rows = $db->query($sql)->fetchAll();
foreach($rows as $sevdays) {
	// Send email
	$to = $sevdays['email_addr'];
	$subject = "JSB Enterprise Realty Services Invoice Past Due";
	
	$message = "
		<html>
		<head>
		<title>JSB Enterprise Realty Services</title>
		</head>
		<body>";
	$message .= stripslashes($config['email_text']);
	$message .= "
		</body>
		</html>
		";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";

	if(!mail($to,$subject,$message,$headers)) {
		// Send failure email
		$to = "jsb@jsbenterpriserealtyservices.com";
		$subject = "JSB Website Notification - Failed Email - Invoice Past Due";
		
		$message = "
			<html>
			<head>
			<title>JSB Enterprise Realty Services</title>
			</head>
			<body>
			<p>An email has failed to be sent to " . $sevdays['company'] . " at the following email address: " . $sevdays['email_addr'] . ". </p>
			<p>Please contact them via another means to notify them of their invoice being due.
			</body>
			</html>
			";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";
		
		//mail($to, $subject, $message, $headers);
	}
}

// Send Invoice Past Due Email (every 14 days)

$getID = $db->query("SELECT * FROM emails_config WHERE email_val = 'after_due_date'")->fetchArray();
$config = $db->query("SELECT * FROM email_text WHERE id = ?", $getID['id'])->fetchArray();
$sql = "SELECT DISTINCT u.id, u.email_addr, date_add('1970-01-01', INTERVAL i.duedate SECOND) FROM users u JOIN orders o ON o.userid = u.id JOIN invoices i ON i.orderid = o.id WHERE i.status = 0";
$sql .= " AND duedate > UNIX_TIMESTAMP() AND mod(dayofmonth(date_add('1970-01-01', INTERVAL i.duedate SECOND)), 14) = 0;";
$rows = $db->query($sql)->fetchAll();
foreach($rows as $sevdays) {
	// Send email
	$to = $sevdays['email_addr'];
	$subject = "JSB Enterprise Realty Services Invoice Past Due";
	
	$message = "
		<html>
		<head>
		<title>JSB Enterprise Realty Services</title>
		</head>
		<body>";
	$message .= stripslashes($config['email_text']);
	$message .= "
		</body>
		</html>
		";

	// Always set content-type when sending HTML email
	$headers = "MIME-Version: 1.0" . "\r\n";
	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

	// More headers
	$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";

	if(!mail($to,$subject,$message,$headers)) {
		// Send failure email
		$to = "jsb@jsbenterpriserealtyservices.com";
		$subject = "JSB Website Notification - Failed Email - Invoice Past Due";
		
		$message = "
			<html>
			<head>
			<title>JSB Enterprise Realty Services</title>
			</head>
			<body>
			<p>An email has failed to be sent to " . $sevdays['company'] . " at the following email address: " . $sevdays['email_addr'] . ". </p>
			<p>Please contact them via another means to notify them of their invoice being due.
			</body>
			</html>
			";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";
		
		mail($to, $subject, $message, $headers);
	}
}
*/
// Send Daily Report

$to = "jsb@jsbenterpriserealtyservices.com";
$subject = "JSB Website Notification - Daily Report";
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
			<body>
			<p>Below is a daily summary of what occurred today and what needs your attention. </p>";

// Always set content-type when sending HTML email
$headers = "MIME-Version: 1.0" . "\r\n";
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

// More headers
$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";

// Unpaid Invoices
$sql = "SELECT u.email_addr, u.phone_num, u.company, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, (i.amount - SUM(NVL(p.amount, 0))) as due_amount,";
$sql .= " i.duedate FROM invoices i JOIN orders o ON i.orderid = o.id JOIN users u ON u.id = o.userid LEFT JOIN payments p ON p.inv_id = i.id WHERE i.status = 0";
$sql .= " GROUP BY u.email_addr, u.phone_num, u.company, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, DATE_FORMAT(date_add('1970-01-01', INTERVAL i.duedate SECOND), '%c/%d/%y') ORDER BY duedate ASC";
$count = $db->query($sql)->numRows();
if($count > 0) {
	$rows = $db->fetchAll();
	// Create table
	$message .= "<table><tr><th colspan='6'><span style='font-size: 20px;'><b>Invoices Due</b></span></th></tr>
				<tr><th><b>Company Name</b></th><th><b>Email Address</b></th><th><b>Phone Number</b></th><th><b>Property Address</b></th>
				<th><b>Invoice Due Date</b></th><th><b>Invoice Amount Due</b></th></tr>";
	foreach($rows as $row) {
		$message .= "<tr>
						<td>" . $row['company'] . "</td>
						<td>" . $row['email_addr'] . "</td>
						<td>" . $row['phone_num'] . "</td>
						<td>" . $row['prop_addr1'] . " " . $row['prop_addr2'] . " " . $row['prop_city'] . ", " . $row['prop_state'] . " " . $row['prop_zip'] . "</td>
						<td>" . getDateFromUNIX($row['duedate']) . "</td>
						<td>$" . number_format($row['due_amount']) . "</td>
					</tr>";
	}
	$message .= "</table><br />";
}

// Payments Made Today
$sql = "SELECT DISTINCT u.email_addr, u.phone_num, u.company, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, p_t.amount, (i.amount - SUM(p.amount)) as due_amount, ";
$sql .= "i.duedate FROM invoices i JOIN orders o ON i.orderid = o.id JOIN users u ON u.id = o.userid JOIN payments p ON p.inv_id = i.id JOIN payments p_t ON p_t.inv_id = i.id WHERE ";
$sql .= "DATE_FORMAT(date_add('1970-01-01', INTERVAL p_t.pay_date SECOND), '%c/%d/%y') = DATE_FORMAT((CURDATE() + 1), '%c/%d/%y') ";
$sql .= "GROUP BY u.email_addr, u.phone_num, u.company, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, p_t.amount, DATE_FORMAT(date_add('1970-01-01', INTERVAL i.duedate SECOND), '%c/%d/%y') ORDER BY duedate ASC";
$count = $db->query($sql)->numRows();
if($count > 0) {
	$rows = $db->fetchAll();
	// Create table
	$message .= "<table><tr><th colspan='7'><span style='font-size: 20px;'><b>Payments Made Today</b></span></th></tr>
				<tr><th><b>Company Name</b></th><th><b>Email Address</b></th><th><b>Phone Number</b></th><th><b>Property Address</b></th>
				<th><b>Payment Amount</b></th><th><b>Total Yet Due</b></th><th><b>Invoice Due Date</b></th></tr>";
	foreach($rows as $row) {
		$message .= "<tr>
						<td>" . $row['company'] . "</td>
						<td>" . $row['email_addr'] . "</td>
						<td>" . $row['phone_num'] . "</td>
						<td>" . $row['prop_addr1'] . " " . $row['prop_addr2'] . " " . $row['prop_city'] . ", " . $row['prop_state'] . " " . $row['prop_zip'] . "</td>
						<td>$" . number_format($row['amount']) . "</td>
						<td>$" . number_format($row['due_amount']) . "</td>
						<td>" . getDateFromUNIX($row['duedate']) . "</td>
					</tr>";
	}
	$message .= "</table><br />";
}

// New Orders
$sql = "SELECT u.company, u.email_addr, u.phone_num, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM users u JOIN orders o ON o.userid = u.id WHERE o.status = 0";
$count = $db->query($sql)->numRows();
if($count > 0) {
	$rows = $db->fetchAll();
	// Create table
	$message .= "<table><tr><th colspan='4'><span style='font-size: 20px;'><b>New Orders</b></span></th></tr>
				<tr><th><b>Company Name</b></th><th><b>Email Address</b></th><th><b>Phone Number</b></th><th><b>Property Address</b></th></tr>";
	foreach($rows as $row) {
		$message .= "<tr>
						<td>" . $row['company'] . "</td>
						<td>" . $row['email_addr'] . "</td>
						<td>" . $row['phone_num'] . "</td>
						<td>" . $row['prop_addr1'] . " " . $row['prop_addr2'] . " " . $row['prop_city'] . ", " . $row['prop_state'] . " " . $row['prop_zip'] . "</td>
					</tr>";
	}
	$message .= "</table><br />";
}

// Orders Waiting for Response
$sql = "SELECT u_o.company, u_o.email_addr, u_o.phone_num, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM users u_o JOIN orders o ON o.userid = u_o.id JOIN order_messages om ON om.orderid = o.id ";
$sql .= "WHERE o.status = 1 AND om.timesent = (SELECT MAX(om_tm.timesent) FROM order_messages om_tm ";
$sql .= "WHERE om_tm.orderid = om.orderid) AND om.user_from NOT IN (SELECT u.id FROM users u WHERE u.usertype = 2)";
$count = $db->query($sql)->numRows();
if($count > 0) {
	$rows = $db->fetchAll();
	// Create table
	$message .= "<table><tr><th colspan='4'><span style='font-size: 20px;'><b>Orders Waiting for Your Response</b></span></th></tr>
				<tr><th><b>Company Name</b></th><th><b>Email Address</b></th><th><b>Phone Number</b></th><th><b>Property Address</b></th></tr>";
	foreach($rows as $row) {
		$message .= "<tr>
						<td>" . $row['company'] . "</td>
						<td>" . $row['email_addr'] . "</td>
						<td>" . $row['phone_num'] . "</td>
						<td>" . $row['prop_addr1'] . " " . $row['prop_addr2'] . " " . $row['prop_city'] . ", " . $row['prop_state'] . " " . $row['prop_zip'] . "</td>
					</tr>";
	}
	$message .= "</table><br />";
}


$message .= "</body>
			</html>
			";

echo $message;
//mail($to, $subject, $message, $headers);

}

?>