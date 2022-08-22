<?php
require_once "./includes/config.php";
require_once "./includes/functions.php";

$promotion = $db->escape(trim($_POST['orderid']));

// Delete Alert
$db->query("DELETE FROM promotion WHERE orderid = ?", $promotion);
return "Success!";

?>