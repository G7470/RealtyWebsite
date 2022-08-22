<?php
	include "includes/innerheader.php";
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit;
	}
	
	$theme_err = $descr_err = $prop_type_err = $res_prop_err = $comm_prop_err = $purch_type_err = "";
	
	$rows = $db->query("SELECT l.*, o.list_price FROM orders o LEFT JOIN listing l ON o.id = l.orderid WHERE o.id = ? AND o.userid = ?", array($db->escape($_GET['id']), $user['id']))->numRows();
	if($rows <> 1 && !checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit; 
	}
	$listingarr = $db->fetchArray();
	// If admin, retrieve the correct data
	if(checkAdmin($user['usertype'])) {
		$query = "SELECT l.*, o.list_price FROM orders o LEFT JOIN listing l JOIN ON o.id = l.orderid WHERE o.id = ?";
		$rows = $db->query($query, $db->escape($_GET['id']))->numRows();
		if($rows <> 1) {
			echo '<meta http-equiv="Refresh" content="0; url=main.php">';
			exit; 
		}
	}
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(!isset($_POST['theme_default'])){
			$theme_err = "You must select a theme.";
		}
		if(empty(trim($_POST['description']))) {
			$descr_err = "You must enter in a property description.";
		}
		if($_POST['prop_type'] <> 1 && $_POST['prop_type'] <> 2) {
			$prop_type_err = "Please select property type.";
		}
		else {
			$proptype = $db->escape($_POST['prop_type']);
		}
		// Validate policy approval
		if(empty($prop_type_err)) {
			switch($proptype) {
				case 2:
					if($_POST['res_prop_type'] <> 1 && $_POST['res_prop_type'] <> 2 && $_POST['res_prop_type'] <> 3 
						&& $_POST['res_prop_type'] <> 4 && $_POST['res_prop_type'] <> 5 && $_POST['res_prop_type'] <> 6) {
						$res_prop_err = "You must select a residential property type.";
					}
					break;
				case 1:
					if($_POST['comm_prop_type'] <> 1 && $_POST['comm_prop_type'] <> 2 && $_POST['comm_prop_type'] <> 3 
						&& $_POST['comm_prop_type'] <> 4) {
						$comm_prop_err = "You must select a commercial property type.";
					}
					break;
			}
		}
		if($_POST['purch_type'] <> 1 && $_POST['purch_type'] <> 2) {
			$purch_type_err = "Please select purchase type.";
		}
		$_POST['list_price'] = $db->escape(trim($_POST['list_price']));
		if(empty($theme_err) && empty($descr_err) && empty($prop_type_err) && empty($res_prop_err)
		&& empty($comm_prop_err) && empty($purch_type_err))
		{
			// Create/update listing
			$rows = $db->query("SELECT l.*, o.list_price FROM listing l JOIN orders o ON o.id = l.orderid WHERE l.orderid = ?", $_GET['id'])->numRows();
			if($rows <> 1) {
				// Create new listing
				$db->query("INSERT INTO listing VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", $db->escape($_GET['id']), 1, $db->escape($_POST['description'])
					, $db->escape($_POST['prop_addr']), $db->escape($_POST['bedrooms']), $db->escape($_POST['bathrooms']), $db->escape($_POST['sqfeet']), $db->escape($_POST['lotsize']), $db->escape($_POST['prop_type'])
					, $db->escape($_POST['res_prop_type']), $db->escape($_POST['comm_prop_type']), $db->escape($_POST['purch_type']), time());
				
				// Upload picture
				if(is_uploaded_file($_FILES['fileupload']['tmp_name'])) {
					if(!is_dir("./images/userimages/" . $user['id'] . "_" . $ordID)) {
						mkdir("./images/userimages/" . $user['id'] . "_" . $ordID, "0777", true);
					}
					move_uploaded_file($_FILES['fileupload']['tmp_name'], "/images/userimages/" . $user['id'] . "_" . $ordID);
				
					$db->query("INSERT INTO order_pic (orderid, filename) VALUES (?, ?)", array($ordID, $_FILES['fileupload']['tmp_name']));
				}
			}
			else {
				// Update listing
				$q = "UPDATE listing SET theme = ?, description = ?, show_addr = ?, bedrooms = ?, bathrooms = ?, sq_feet = ?, lot_size = ?, prop_type = ?
						, res_prop_type = ?, comm_prop_type = ?, purch_type = ?, lastupdated = ? WHERE orderid = ?";
				$db->query($q, $db->escape($_POST['theme_default']), $db->escape($_POST['description']), $db->escape($_POST['prop_addr']), $db->escape($_POST['bedrooms']), $db->escape($_POST['bathrooms']), $db->escape($_POST['sqfeet'])
					, $db->escape($_POST['lotsize']), $db->escape($_POST['prop_type']), $db->escape($_POST['res_prop_type']), $db->escape($_POST['comm_prop_type']), $db->escape($_POST['purch_type']), time(), $_GET['id']);
			}
			$db->query("UPDATE orders SET list_price = ? WHERE id = ?", array($_POST['list_price'], $db->escape($_GET['id'])));
			
			$rows = $db->query("SELECT l.*, o.list_price FROM listing l JOIN orders o ON o.id = l.orderid WHERE l.orderid = ?", $db->escape($_GET['id']))->numRows();
			if($rows == 1) {
				$listingarr = $db->fetchArray();
			}
			
		}
		else {
			// Fill in array with posted values
			$listingarr['show_addr'] = $_POST['prop_addr'];
			$listingarr['bedrooms'] = $_POST['bedrooms'];
			$listingarr['bathrooms'] = $_POST['bathrooms'];
			$listingarr['sq_feet'] = $_POST['sqfeet'];
			$listingarr['lot_size'] = $_POST['lotsize'];
			$listingarr['description'] = $_POST['description'];
			$listingarr['prop_type'] = $_POST['prop_type'];
			$listingarr['res_prop_type'] = $_POST['res_prop_type'];
			$listingarr['comm_prop_type'] = $_POST['comm_prop_type'];
			$listingarr['purch_type'] = $_POST['purch_type'];	
		}
    }
?>
<article id="main">
		<header>
			<h2>Update Listing</h2>
			<p>Build your Listing</p>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<section>
					<span><font color="red"><?php echo $redirect; ?></font></span>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $_GET['id']; ?>">
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<h5>Select Theme</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $theme_err; ?></font></span>
								<input type="checkbox" id="theme_default" name="theme_default" checked>
								<label for="theme_default">Default Theme</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Show Property Address</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="prop_addr" name="prop_addr" <?php echo ($listingarr['show_addr'] == 2 ? '' : 'checked'); ?>>
								<label for="prop_addr">Show Property Address</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>List Price</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="number" name="list_price" id="list_price" value="<?php echo $listingarr['list_price']; ?>" placeholder="List Price" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Number of Bedrooms</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="bedrooms" id="bedrooms" value="<?php echo $listingarr['bedrooms']; ?>" placeholder="Number of Bedrooms" />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Number of Bathrooms</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="bathrooms" id="bathrooms" value="<?php echo $listingarr['bathrooms']; ?>" placeholder="Number of Bathrooms" />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Square Feet</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="sqfeet" id="sqfeet" value="<?php echo $listingarr['sq_feet']; ?>" placeholder="Square Feet" />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Lot Size (in acres)</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="lotsize" id="lotsize" value="<?php echo $listingarr['lot_size']; ?>" placeholder="Lot Size (in acres)" />
							</div>
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $descr_err; ?></font></span>
								<h5>Description</h5>
							</div>
							<div class="col-12">
								<textarea name="description" id="description" placeholder="Description" rows="8" required><?php echo $listingarr['description']; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $prop_type_err; ?></font></span>
								<h5>Property Type</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<select name="prop_type" id="prop_type">
									<option value="">- Property Type -</option>
									<option value="1" <?php if($listingarr['prop_type'] == 1) { echo "selected"; } ?>>Commercial</option>
									<option value="2" <?php if($listingarr['prop_type'] == 2) { echo "selected"; } ?>>Residential</option>
								</select>
							</div>
							<div class="col-8 col-12-xsmall" id="divres" style="display:none;">
								<div class="col-8 col-12-xsmall">
									<h5>Residential Property Type</h5>
								</div>
								<div class="col-8 col-12-xsmall">
									<span><font color="red"><?php echo $res_prop_err; ?></font></span>
									<select name="res_prop_type" id="res_prop_type">
										<option value="">- Residential Property Type -</option>
										<option value="1" <?php if($listingarr['res_prop_type'] == 1) { echo "selected"; } ?>>Single-Family Home</option>
										<option value="2" <?php if($listingarr['res_prop_type'] == 2) { echo "selected"; } ?>>Condominium</option>
										<option value="3" <?php if($listingarr['res_prop_type'] == 3) { echo "selected"; } ?>>Townhouse</option>
										<option value="4" <?php if($listingarr['res_prop_type'] == 4) { echo "selected"; } ?>>Co-op</option>
										<option value="5" <?php if($listingarr['res_prop_type'] == 5) { echo "selected"; } ?>>Multi-Family Home</option>
										<option value="6" <?php if($listingarr['res_prop_type'] == 6) { echo "selected"; } ?>>Land</option>
									</select>
								</div>
							</div>
							<div class="col-8 col-12-xsmall" id="divcomm" style="display:none;">
								<div class="col-8 col-12-xsmall">
									<h5>Commercial Property Type</h5>
								</div>
								<div class="col-8 col-12-xsmall">
									<span><font color="red"><?php echo $comm_prop_err; ?></font></span>
									<select name="comm_prop_type" id="comm_prop_type">
										<option value="">- Commercial Property Type -</option>
										<option value="1" <?php if($listingarr['comm_prop_type'] == 1) { echo "selected"; } ?>>Office Space</option>
										<option value="2" <?php if($listingarr['comm_prop_type'] == 2) { echo "selected"; } ?>>Industrial Use</option>
										<option value="3" <?php if($listingarr['comm_prop_type'] == 3) { echo "selected"; } ?>>Multi-Family Rental</option>
										<option value="4" <?php if($listingarr['comm_prop_type'] == 4) { echo "selected"; } ?>>Retail</option>
									</select>
								</div>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Purchase Type</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $purch_type_err; ?></font></span>
								<select name="purch_type" id="purch_type">
									<option value="">- Purchase Type -</option>
									<option value="1" <?php if($listingarr['purch_type'] == 1) { echo "selected"; } ?>>For Sale</option>
									<option value="2" <?php if($listingarr['purch_type'] == 2) { echo "selected"; } ?>>For Rent</option>
								</select>
							</div>
							<div class="col-12">
								Upload New Picture:
								<input id="fileupload" type="file" name="fileupload" accept="image/*">
								<label id="filename"></label>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="<?php echo ($listingarr['theme'] == 1 ? "Update" : "Create"); ?> Listing" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				</section>
			</div>
		</section>
</article>
<script>
	document.getElementById("prop_type").addEventListener("change", PropertyType);
	
	function PropertyType() {
		var dropdown = document.getElementById("prop_type");
		var type = dropdown.options[dropdown.selectedIndex].value;
		var comm = document.getElementById("divcomm");
		var res = document.getElementById("divres");
		if(type == "1") {
			comm.style.display = "block";
			res.style.display = "none";
		}
		else if(type == "2") {
			comm.style.display = "none";
			res.style.display = "block";
		}
		else {
			comm.style.display = "none";
			res.style.display = "none";
		}
	}
</script>