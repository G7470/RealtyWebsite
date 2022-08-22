<?php
require_once "./includes/config.php";
require_once "./includes/functions.php";

$sortorder = $_POST['list'];
$orderid = $db->escape($_POST['orderid']);
$i = 0;
foreach($sortorder as $list) {
	$db->query("UPDATE order_pic SET sort = ? WHERE id = ?", array($i + 1, $db->escape($sortorder[$i])));
	$i++;
}

return "Success!";

?>