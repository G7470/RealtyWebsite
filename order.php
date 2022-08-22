<?php
include "includes/innerheader.php";
	
$results = $db->query("SELECT * FROM services WHERE active = 1")->fetchAll();	

if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Validate property address
	if(empty(trim($_POST['prop_addr1'])) || empty(trim($_POST['prop_city'])) || empty(trim($_POST['prop_state'])) || empty(trim($_POST['prop_zip']))) {
		$compaddr_err = "Please enter property address.";
	}
	elseif(!is_numeric($_POST['prop_zip'])) {
		$compaddr_err = "Please enter valid zip code.";
	}
	else {
		$compaddr1 = $db->escape(trim($_POST['prop_addr1']));
		$compaddr2 = $db->escape(trim($_POST['prop_addr2']));
		$compcity = $db->escape(trim($_POST['prop_city']));
		$compstate = $db->escape(trim($_POST['prop_state']));
		$compzip = $db->escape(trim($_POST['prop_zip']));
	}
	$comments = $db->escape(trim($_POST['comments']));
	
	if(empty($_POST['list_price'])) {
		$_POST['list_price'] = -1;
	}
	
	if(empty($compaddr_err)) {
		$sql = "INSERT INTO orders (userid, prop_addr1, prop_addr2, prop_city, prop_state, prop_zip, list_price, comments, order_placed) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
		if($db->query($sql, $user['id'], $compaddr1, $compaddr2, $compcity, $compstate, $compzip, $db->escape($_POST['list_price']), $comments, time())) {
			$ordID = $db->lastInsertID();
			foreach($results as $arr) {
				if($_POST['service' . $arr['id']]) {
					// Add service
					$db->query("INSERT INTO order_services (orderid, serviceid) VALUES (?, ?)", array($ordID, $arr['id']));
				}
			}
			if(is_uploaded_file($_FILES['fileupload']['tmp_name'])) {
				if(!is_dir("./images/userimages/" . $user['id'] . "_" . $ordID)) {
					mkdir("./images/userimages/" . $user['id'] . "_" . $ordID, "0777", true);
				}
				$exists = $db->query("SELECT * FROM order_pic WHERE filename = ? AND orderid = ?", $filename, $ordarr['id'])->numRows();
				if($exists < 1) {
					move_uploaded_file($_FILES['fileupload']['tmp_name'], "/images/userimages/" . $user['id'] . "_" . $ordID);
					$db->query("INSERT INTO order_pic (orderid, filename) VALUES (?, ?)", array($ordID, $_FILES['fileupload']['tmp_name']));
				}
				else {
					$redirect = "There was an issue uploading your picture. Go into your order details to upload the picture again. ";
				}
			}
			$redirect .= "You have placed the order! Create your listing for this property by going <a href='create_listing.php?id=" . $ordID . "'>here</a>";
			// Notify admin - probably via email
		}
	} 
}
	
?>
<article id="main">
		<header>
			<h2>Place Order</h2>
			<p>Place a new order to JSB!</p>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<section>
					<span><font color="red"><?php echo $redirect; ?></font></span>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
						<span><font color="red"><?php echo $compaddr_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_addr1" id="prop_addr1" value="" placeholder="Property Street Address" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_addr2" id="prop_addr2" value="" placeholder="Property Apt/Suite" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_city" id="prop_city" value="" placeholder="Property City" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_state" id="prop_state" value="" placeholder="Property State" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_zip" id="prop_zip" value="" placeholder="Property Zip Code" required />
							</div>
							<?php
								foreach($results as $arr) {
									echo '
										<div class="col-8 col-12-xsmall">
											<input type="checkbox" id="service' . $arr['id'] . '" name="service' . $arr['id'] . '">
											<label for="service' . $arr['id'] . '">' . $arr['serv_name'] . ' - $' . number_format($arr['cost'], 2);
										if($arr['monthly'] == 1) {
											echo '/month';
										}
										echo '</label>
										</div>';
								}
							?>
							<div class="col-8 col-12-xsmall">
								<input type="number" name="list_price" id="list_price" value="" placeholder="List Price" />
							</div>
							<div class="col-12">
								Upload New Picture:
								<input id="fileupload" type="file" name="fileupload" accept="image/*">
								<label id="filename"></label>
							</div>
							<div class="col-12">
								<textarea name="comments" id="comments" placeholder="Comments" rows="6"></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Place Order" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				</section>
			</div>
		</section>
	</article>
<script>
document.querySelector("#fileupload").onchange = function(){
  document.querySelector("#filename").textContent = this.files[0].name;
}
</script>
<?php
	include "includes/footer.php";
?>