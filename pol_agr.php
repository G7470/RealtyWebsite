<?php
include "includes/innerheader.php";
$pol_err = "";
$rows = $db->query("SELECT p.* FROM policies p, users u WHERE p.lastupdate > u.policy_agree AND u.id = ? ORDER BY p.id", $user['id'])->numRows();
if ($rows < 1) {
	echo '<meta http-equiv="Refresh" content="0; url=main.php">';
	exit;
}
// Processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){
	$policies = $db->fetchAll();
	foreach($policies as $p) {
		if(!isset($_POST['agree' . $p['id']])) {
			$pol_err .= "You must agree to the " . $p['name'] . " in order to continue.<br />";
		}
	}
	if(empty($pol_err)) {
		if($db->query("UPDATE users SET policy_agree = ? WHERE id = ?", array(time(), $user['id']))) {
			echo '<meta http-equiv="Refresh" content="0; url=main.php">';
			exit;
		}
		else {
			$pol_err = "Error updating data. Try again!";
		}
	}
}
$rows = $db->query("SELECT p.* FROM policies p, users u WHERE p.lastupdate > u.policy_agree AND u.id = ?", $user['id'])->numRows();
if ($rows < 1) {
	echo '<meta http-equiv="Refresh" content="0; url=main.php">';
	exit;
}
$policies = $db->fetchAll();

?>
<article id="main">
	<header>
		<h2>Policies</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<div class="row gtr-uniform">
					<div class="col-8 col-12-xsmall">
						<span><font color="red"><?php echo $pol_err; ?></font></span>
					</div>
					<?php
						foreach($policies as $p) {
							echo '<div class="col-8 col-12-small"><p>You must agree to the ' . $p['name'] . ' that is shown below in order to continue using this site.</p></div>';
							echo '<div class="col-8 col-12-small" style="padding-left: 10%;">' . htmlspecialchars_decode(stripslashes($p['text'])) . '</div>';
					?>
						<div class="col-8 col-12-xsmall" style="border-bottom: 1px dotted black; padding-bottom: 10px;">
								<input type="checkbox" id="<?php echo 'agree' . $p['id']; ?>" name="<?php echo 'agree' . $p['id']; ?>">
								<label for="<?php echo 'agree' . $p['id']; ?>">I Agree</a></label>
						</div>
					<?php
						}
					?>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Submit Agreements" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
			</section>
		</div>
	</section>
</article>
<?php
	include "includes/footer.php";
?>