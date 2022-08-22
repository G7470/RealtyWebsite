<?php
include "./includes/config.php";

$ordid = $db->escape($_POST['id']);
$updprice = $_POST['updprice'];

$updprice = str_replace(",", "", $updprice);
$updprice = $db->escape(str_replace("$", "", $updprice));
$row = $db->query("SELECT * FROM orders WHERE id = ?", $ordid)->numRows();

if($row > 0 && is_numeric($updprice)) {
	$db->query("UPDATE orders SET list_price = ? WHERE id = ?", array($updprice, $ordid));
	return "Success!";
}
else {
	return header("not-real.php", true, 404);
}

?>