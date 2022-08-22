<?php
require_once "./includes/config.php";
require_once "./includes/functions.php";

$config_key = $db->escape(trim($_POST['config_key']));
$exists = getConfig($config_key);
if($exists <> 'Undefined') {
	// Delete Alert
	$db->query("DELETE FROM settings WHERE config_key = ?", $config_key);
	return "Success!";
}
else {
	return header("not-real.php", true, 404);
}

?>