<?php
require_once "./includes/config.php";
/* Realtor Alerts */

// Check Invoice Alerts
$inv_past_due = $db->query("SELECT i.* FROM invoices i JOIN orders o ON i.orderid = o.id WHERE i.status = 0 AND i.duedate < ? AND o.userid = ?", array(time(), $user['id']))->numRows();
if($inv_past_due > 0) {
	echo '<p><font color="red">An invoice is past due. <a href="view_invoices.php">View Invoices</a> for more information and to make a payment.</font></p>';
}
else {
	$inv_due = $db->query("SELECT i.* FROM invoices i JOIN orders o ON i.orderid = o.id WHERE i.status = 0 AND o.userid = ?", $user['id'])->numRows();
	if($inv_due > 0) {
		echo '<p><font color="red">You have an invoice due. <a href="view_invoices.php">View Invoices</a> for more information and to make a payment.</font></p>';
	}
}

// Check Communication Alerts
if(!checkAdmin($user['usertype'])) {
	$sql = "SELECT o.* FROM orders o JOIN order_messages om ON om.orderid = o.id WHERE o.status = 1 AND om.timesent = (SELECT MAX(om_tm.timesent) FROM order_messages om_tm ";
		$sql .= "WHERE om_tm.orderid = om.orderid) AND om.user_from <> ?";
	$comm = $db->query($sql, $user['id'])->numRows();
	if($comm > 0) {
		echo '<p><font color="red">You have orders waiting for response. <a href="order_history.php">View Orders</a> to go to your order and respond.</font></p>';
	}
}

/* Admin Alerts */

// New Order Alerts
if(checkAdmin($user['usertype'])) {
	$sql = "SELECT o.* FROM orders o WHERE o.status = 0";
	$comm_set = $db->query($sql)->fetchAll();
	foreach($comm_set as $comm) {
		echo '<p><font color="red">You have an order waiting to be started. <a href="order_details.php?view=' . $comm['id'] . '">View Order</a> to begin.</font></p>';
	}
}

// New Realtor Alerts
if(checkAdmin($user['usertype'])) {
	$sql = "SELECT u.* FROM users u WHERE u.new = 1";
	$numrows = $db->query($sql)->numRows();
	if($numrows > 0) {
		echo '<p><font color="red"><a href="new_realtors.php">You have new realtors since you have last logged in!</a></font></p>';
	}
}

// Check Communication Alerts 
if(checkAdmin($user['usertype'])) {
	$sql = "SELECT o.* FROM orders o JOIN order_messages om ON om.orderid = o.id WHERE o.status = 1 AND om.timesent = (SELECT MAX(om_tm.timesent) FROM order_messages om_tm ";
	$sql .= "WHERE om_tm.orderid = om.orderid) AND om.user_from NOT IN (SELECT u.id FROM users u WHERE u.usertype = 2)";
	$comm_set = $db->query($sql)->fetchAll();
	foreach($comm_set as $comm) {
		echo '<p><font color="red">You have an order waiting for response. <a href="order_details.php?view=' . $comm['id'] . '">View Order</a> to begin.</font></p>';
	}
}
?>