<?php
include "./includes/config.php";

$userid = $db->escape($_POST['id']);
$db->query("UPDATE users SET new = 0 WHERE id = ?", $userid);
return "Success!";

?>