<?php

require_once "config.php";

function translate_status($status) {
		switch($status) {
			case 0:
				$label = "Not Started";
				break;
			case 1:
				$label = "Waiting for Your Review";
				break;
			case 2:
				$label = "Waiting for Your Payment";
				break;
			case 3:
				$label = "In Progress";
				break;
			case 4:
				$label = "Completed";
				break;
		}
	return $label;
}

function translate_payment_status($status) {
	switch($status) {
		case 1:
			$label = "Electronic";
			break;
		case 2:
			$label = "Check";
			break;
		case 3:
			$label = "Cash";
			break;
	}
	return $label;
}

function getDateFromUNIX($timestamp) {
	$datetime = new DateTime();
	$datetime = DateTime::createFromFormat('U', $timestamp);
	$date_time_format = $datetime->format('m/d/Y');
	$time_zone_from="UTC";
	$time_zone_to='America/New_York';
	$display_date = new DateTime($date_time_format, new DateTimeZone($time_zone_from));
	$display_date->setTimezone(new DateTimeZone($time_zone_to));
	return $display_date->format('m/d/Y');
}

function checkAdmin($usertype) {
	switch($usertype) {
		case 1:
			$admin = false;
			break;
		case 2:
			$admin = true;
			break;
	}
	return $admin;
}

function getConfig($key) {
	global $db;
	
	$getsetting = $db->query("SELECT config_value FROM settings WHERE config_key = ?", $key)->fetchArray();
	if(isset($getsetting['config_value'])) {
		return $getsetting['config_value'];
	}
	else {
		return 'Undefined';
	}
}
function getConfigs($key) {
	global $db;
	
	$key = '%' . $key . '%';
	$getsettingsrows = $db->query("SELECT * FROM settings WHERE config_key LIKE ?", $key)->numRows();
	$getsettings = $db->fetchAll();
	if($getsettingsrows > 0) {
		return $getsettings;
	}
	else {
		return 'Undefined';
	}
}
?>