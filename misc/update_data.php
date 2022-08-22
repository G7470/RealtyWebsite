<?php

require_once "../includes/config.php";
require_once "../includes/functions.php";

function ApplyLateFees() {
	
	// New Year? If so, reset late fee percentages
	if(date('j', time()) == '1' and date('m', time()) == '01') {
		$db->query("UPDATE users SET latefee_perc = 0");
	}
	
	// Check for new late invoices
	$latefees = $db->query("SELECT DISTINCT u.id, u.latefee_perc, i.id, i.amount AS invoice_id, o.id AS order_id FROM users u JOIN orders o ON o.userid = u.id JOIN invoices i ON i.orderid = o.id WHERE i.status = 0 AND i.duedate > UNIX_TIMESTAMP() AND (i.duedate - 86400) < UNIX_TIMESTAMP()")->fetchAll();
	foreach($latefees as $latefee) {
		if(getConfig('latefee_yearly') > $latefee['latefee_perc']) {
			
			// Apply late fee
			$latefee_amt = $latefee['amount'] * (getConfig('latefee_monthly') / 100);
			$db->query("INSERT INTO charges (inv_id, orderid, charge_type, charge_name, amount, charge_date) VALUES (?, ?, ?, ?, ?, ?)", $latefee['invoice_id'], $latefee['order_id'], 1, "Late Fee", $latefee_amt, time());
		}
	}
	
	// Every 30 days - check if invoice is late
	$latefees = $db->query("SELECT DISTINCT u.id, u.latefee_perc, i.id, i.amount AS invoice_id, o.id AS order_id FROM users u JOIN orders o ON o.userid = u.id JOIN invoices i ON i.orderid = o.id WHERE i.status = 0 AND (i.duedate - 86400) > UNIX_TIMESTAMP() AND mod(dayofmonth(date_add('1970-01-01', INTERVAL i.duedate SECOND)), 30) = 0")->fetchAll();
	foreach($latefees as $latefee) {
		if(getConfig('latefee_yearly') > $latefee['latefee_perc']) {
			
			// Apply late fee
			$latefee_amt = $latefee['amount'] * (getConfig('latefee_monthly') / 100);
			$db->query("INSERT INTO charges (inv_id, orderid, charge_type, charge_name, amount, charge_date) VALUES (?, ?, ?, ?, ?, ?)", $latefee['invoice_id'], $latefee['order_id'], 1, "Late Fee", $latefee_amt, time());
		}
	}
}

function UpdateActiveServices() {
	
	// Inactivate services that have expired
	$db->query("UPDATE order_services SET active = 0 WHERE active_until <> 0 AND active_until < ?", time());
}

function CreateMonthlyInvoices() {
	
	// If first of month, create monthly invoice 
	if(date('j', time()) == '1') {
		$q = "SELECT o_s.order_id, SUM(s.cost) AS serv_cost FROM services s JOIN order_services o_s ON o_s.serviceid = s.id AND o_s.active = 1 WHERE s.monthly = 1";
		$orders = $db->query($q)->fetchAll();
		foreach($orders as $order) {
			$lastday = date("Y-m-t");
			$timestamp = strtotime($lastday);
			$db->query("INSERT INTO invoices (orderid, amount, duedate) VALUES (?, ?, ?)", $order['order_id'], $order['serv_cost'], $timestamp);
		}
	}
}

function UpdateData() {
	ApplyLateFees();
	UpdateActiveServices();
	CreateMonthlyInvoices();
}

?>