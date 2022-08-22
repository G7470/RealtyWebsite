<?php
include "includes/header.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
	echo '<meta http-equiv="Refresh" content="0; url=register.php">';
	exit;
}

$policy = $db->query("SELECT * FROM policies WHERE id = ?", $db->escape($_GET['id']))->fetchArray();

?>
<article id="main">
	<header>
		<h2><?php echo $policy['name']; ?></h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
			<?php
				echo '<div class="col-8 col-12-small" style="padding-left: 10%;">' . htmlspecialchars_decode(stripslashes($policy['text'])) . '</div>';
			?>
			</section>
		</div>
	</section>
</article>
<?php
	include "includes/footer.php";
?>