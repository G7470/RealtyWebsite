<?php
require_once "./includes/config.php";
require_once "./includes/functions.php";

$sortorder = $_POST['list'];
$i = 0;
foreach($sortorder as $list) {
	$db->query("UPDATE promotion SET sort = ? WHERE orderid = ?", array($i + 1, $db->escape($sortorder[$i])));
	$i++;
}

return "Success!";

?>