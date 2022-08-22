<?php
require_once "./includes/config.php";
require_once "./includes/functions.php";

$picid = $db->escape(trim($_POST['picture_id']));

// Delete Picture
$db->query("DELETE FROM order_pic WHERE id = ?", $picid);
return "Success!";

?>