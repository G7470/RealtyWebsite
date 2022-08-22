<?php
	include "includes/innerheader.php";
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	if(!isset($_GET['edit']) || !is_numeric($_GET['edit'])) {
		echo '<meta http-equiv="Refresh" content="0; url=policies.php">';
		exit;
	}
	
	$rows = $db->query("SELECT * FROM policies WHERE id = ?", $db->escape($_GET['edit']))->numRows();
	if($rows <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=policies.php">';
		exit; 
	}	
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Validate service name
		if(empty(trim($_POST['name']))) {
			$status_err = "Please enter service name.";
		}
		else {
			$pol_name = $db->escape(trim($_POST['name']));
		}
		
		if(isset($_POST['active'])) {
			$active = 1;
		}
		else {
			$active = 0;
		}
	
		$pol_text = $db->escape(addslashes($_POST['text']));
		
		if(empty($status_err)) {
			$sql = "UPDATE policies SET name = ?, category = ?, active = ?, text = ?, lastupdate = ? WHERE id = ?";
			
			if($db->query($sql, array($pol_name, $db->escape($_POST['pol_type']), $active, $pol_text, time(), $db->escape($_GET['edit'])))) {
				$status_err = "Policy updated!";
			}
		} 
	}
	
	if(isset($_GET['del'])) {
		if($_GET['del'] <> $_GET['edit']) {
			$status_err = "Error deleting policy. Try again.";
		}
		else {
			$db->query("DELETE FROM policies WHERE id = ?", $db->escape($_GET['del']));
			echo '<meta http-equiv="Refresh" content="0; url=policies.php">';
			exit;
		}
	}
	
	$rows = $db->query("SELECT * FROM policies WHERE id = ?", array($db->escape($_GET['edit'])))->numRows();
	if($rows <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=policies.php">';
		exit; 
	}
	$polarr = $db->fetchArray();
	
?>
<style>
.accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.accordion:after {
  content: '\002B';
  color: #777;
  font-weight: bold;
  float: right;
  margin-left: 5px;
}

.active:after {
  content: "\2212";
}

.panel {
  padding: 0 18px;
  background-color: white;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-out;
}
</style>
<article id="main">
	<header>
		<h2>Edit Policy</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<a class="button primary" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?edit=<?php echo $polarr['id']; ?>&del=<?php echo $polarr['id']; ?>">Delete Policy</a>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?edit=<?php echo $polarr['id']; ?>">
					<span><font color="red"><?php echo $status_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<h5>Policy Name</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="name" id="name" value="<?php echo $polarr['name']; ?>" placeholder="Policy Name" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Status</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="active" name="active" <?php echo ($polarr['active'] ? 'checked' : ''); ?>>
								<label for="active">Active</a></label>
							</div>
							<span><font color="red"><?php echo $cost_err; ?></font></span>
							<div class="col-8 col-12-xsmall">
								<h5>Policy Type</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<select name="pol_type" id="pol_type">
									<option value="1" <?php if($polarr['category'] == 1) { echo "selected"; } ?>>General</option>
									<option value="2" <?php if($polarr['category'] == 2) { echo "selected"; } ?>>Commercial</option>
									<option value="3" <?php if($polarr['category'] == 3) { echo "selected"; } ?>>Residential</option>
								</select>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Policy Text</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="text" id="text" placeholder="Policy Text"><?php echo htmlspecialchars_decode(stripslashes($polarr['text'])); ?></textarea>
							</div>
							
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Edit Policy" class="primary" /></li>
								</ul>
							</div>
						</div>
				</form>
			</section>
		</div>
	</section>
</article>
<script>
tinymce.init({
    selector: '#text'
});
</script>
<?php
	include "includes/footer.php";
?>