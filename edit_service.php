<?php
	include "includes/innerheader.php";
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	if(!isset($_GET['edit']) || !is_numeric($_GET['edit'])) {
		echo '<meta http-equiv="Refresh" content="0; url=services.php">';
		exit;
	}
	
	$rows = $db->query("SELECT * FROM services WHERE id = ?", $db->escape($_GET['edit']))->numRows();
	if($rows <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=services.php">';
		exit; 
	}	
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		// Validate service name
		if(empty(trim($_POST['name']))) {
			$status_err = "Please enter service name.";
		}
		else {
			$serv_name = $db->escape(trim($_POST['name']));
		}
		$_POST['cost'] = str_replace(",", "", $_POST['cost']);
		$_POST['cost'] = $db->escape(str_replace("$", "", $_POST['cost']));
		if(!is_numeric($_POST['cost'])) {
			$cost_err = "Please enter a number for the cost.";
		}
		
		if(isset($_POST['active'])) {
			$active = 1;
		}
		else {
			$active = 0;
		}
		
		if(empty($status_err) && empty($cost_err)) {
			$sql = "UPDATE services SET serv_name = ?, cost = ?, active = ? WHERE id = ?";
			
			if($db->query($sql, array($serv_name, $cost, $active, $_GET['edit']))) {
				$status_err = "Service updated!";
			}
		} 
	}
	
	if(isset($_GET['del'])) {
		if($_GET['del'] <> $_GET['edit']) {
			$status_err = "Error deleting service. Try again.";
		}
		else {
			$db->query("DELETE FROM services WHERE id = ?", $db->escape($_GET['del']));
			echo '<meta http-equiv="Refresh" content="0; url=services.php">';
			exit;
		}
	}
	
	$rows = $db->query("SELECT * FROM services WHERE id = ?", array($db->escape($_GET['edit'])))->numRows();
	if($rows <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=services.php">';
		exit; 
	}
	$servarr = $db->fetchArray();
	
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
		<h2>Edit Service</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<a class="button primary" href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?edit=<?php echo $servarr['id']; ?>&del=<?php echo $servarr['id']; ?>">Delete Service</a>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?edit=<?php echo $servarr['id']; ?>">
						<span><font color="red"><?php echo $status_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<h5>Service Name</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="name" id="name" value="<?php echo $servarr['serv_name']; ?>" placeholder="Service Name" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Status</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="active" name="active" <?php echo ($servarr['active'] ? 'checked' : ''); ?>>
								<label for="active">Active</a></label>
							</div>
							<span><font color="red"><?php echo $cost_err; ?></font></span>
							<div class="col-8 col-12-xsmall">
								<h5>Cost</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="cost" id="cost" value="$<?php echo number_format($servarr['cost']); ?>" placeholder="Cost" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Edit Service" class="primary" /></li>
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