<?php
	include "includes/innerheader.php";
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit;
	}
	
	$rows = $db->query("SELECT * FROM orders WHERE id = ? AND userid = ?", array($db->escape($_GET['id']), $user['id']))->numRows();
	if($rows <> 1 && !checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit; 
	}
	
	if(checkAdmin($user['usertype'])) {
		$rows = $db->query("SELECT * FROM orders WHERE id = ?", array($db->escape($_GET['id'])))->numRows();
		if($rows <> 1) {
			echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
			exit; 
		}
	}
	
	$results = $db->query("SELECT * FROM services")->fetchAll();	
	
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
	
	$_POST['list_price'] = str_replace(",", "", $_POST['list_price']);
	$_POST['list_price'] = $db->escape(str_replace("$", "", $_POST['list_price']));
	if(!is_numeric($_POST['list_price'])) {
		$listprice_err = "Please enter a number for the list price.";
	}
	
	if($_POST['status'] <> 0 && $_POST['status'] <> 1 && $_POST['status'] <> 2 && $_POST['status'] <> 3 && $_POST['status'] <> 4) {
		$status_err = "Invalid status";
	}
	
	if(empty($compaddr_err) && empty($listprice_err) && empty($status_err)) {
		$sql = "UPDATE orders SET prop_addr1 = ?, prop_addr2 = ?, prop_city = ?, prop_state = ?, prop_zip = ?, status = ?, list_price = ?, comments = ?";
		$sql .= " WHERE id = ?";
		
		if($db->query($sql, array($compaddr1, $compaddr2, $compcity, $compstate, $compzip, $db->escape($_POST['status']), $_POST['list_price'], $comments, $db->escape($_GET['id'])))) {
			foreach($results as $arr) {
				if($_POST['service' . $arr['id']]) {
					// Update service list
					$service = $db->query("SELECT 'X' FROM order_services WHERE orderid = ? AND serviceid = ?", $db->escape($_GET['id']), $arr['id'])->numRows();
					if($service == 0) {
						$db->query("INSERT INTO order_services (orderid, serviceid) VALUES (?, ?)", array($db->escape($_GET['id']), $arr['id']));
					}
				}
				else {
					// Update service list
					$service = $db->query("SELECT 'X' FROM order_services WHERE orderid = ? AND serviceid = ?", $db->escape($_GET['id']), $arr['id'])->numRows();
					if($service == 1) {
						$db->query("DELETE FROM order_services WHERE orderid = ? AND serviceid = ?", array($db->escape($_GET['id']), $arr['id']));
					}
				}
			}
			if(is_uploaded_file($_FILES['fileupload']['tmp_name'])) {
				if(!is_dir("./images/userimages/" . $user['id'] . "_" . $_GET['id'])) {
					mkdir("./images/userimages/" . $user['id'] . "_" . $_GET['id'], "0777", true);
				}
				move_uploaded_file($_FILES['fileupload']['tmp_name'], "/images/userimages/" . $user['id'] . "_" . $ordID);
				$db->query("INSERT INTO order_pic (orderid, filename) VALUES (?, ?)", array($db->escape($_GET['id']), $_FILES['fileupload']['tmp_name']));
			}
			// Notify admin - probably via email
		}
	} 
}
	
	$rows = $db->query("SELECT * FROM orders WHERE id = ? AND userid = ?", array($db->escape($_GET['id']), $user['id']))->numRows();
	if($rows <> 1 && !checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit; 
	}
	
	if(checkAdmin($user['usertype'])) {
		$rows = $db->query("SELECT * FROM orders WHERE id = ?", array($db->escape($_GET['id'])))->numRows();
		if($rows <> 1) {
			echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
			exit; 
		}
	}
	$ordarr = $db->fetchArray();
	
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
		<h2>Edit Order</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $ordarr['id']; ?>">
						<span><font color="red"><?php echo $status_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<h5>Status</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<select name="ordstat" id="ordstat">
									<option value="0" <?php if($ordarr['status'] == 0) { echo "selected"; } ?>>Not Started</option>
									<option value="1" <?php if($ordarr['status'] == 1) { echo "selected"; } ?>>Waiting for Your Review</option>
									<option value="2" <?php if($ordarr['status'] == 2) { echo "selected"; } ?>>Waiting for Your Payment</option>
									<option value="3" <?php if($ordarr['status'] == 3) { echo "selected"; } ?>>In Progress</option>
									<option value="4" <?php if($ordarr['status'] == 4) { echo "selected"; } ?>>Completed</option>
								</select>
							</div>
							<span><font color="red"><?php echo $compaddr_err; ?></font></span>
							<div class="col-8 col-12-xsmall">
								<h5>Property Address</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_addr1" id="prop_addr1" value="<?php echo $ordarr['prop_addr1']; ?>" placeholder="Property Street Address" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_addr2" id="prop_addr2" value="<?php echo $ordarr['prop_addr2']; ?>" placeholder="Property Apt/Suite" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_city" id="prop_city" value="<?php echo $ordarr['prop_city']; ?>" placeholder="Property City" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_state" id="prop_state" value="<?php echo $ordarr['prop_state']; ?>" placeholder="Property State" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="prop_zip" id="prop_zip" value="<?php echo $ordarr['prop_zip']; ?>" placeholder="Property Zip Code" required />
							</div>
							<span><font color="red"><?php echo $listprice_err; ?></font></span>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="list_price" id="list_price" value="<?php echo '$' . number_format($ordarr['list_price']); ?>" placeholder="List Price" />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Services</h5>
							</div>
							<?php
								foreach($results as $arr) {
									echo '
										<div class="col-8 col-12-xsmall">
											<input type="checkbox" id="service' . $arr['id'] . '" name="service' . $arr['id'] . '"';
											$service = $db->query("SELECT 'X' FROM order_services WHERE orderid =  ? AND serviceid = ?", array($ordarr['id'], $arr['id']))->numRows();
											if($service >= 1) {
												echo "checked";
											}
											echo '/><label for="service' . $arr['id'] . '">' . $arr['serv_name'] . ' - $' . number_format($arr['cost'], 2);
										if($arr['monthly'] == 1) {
											echo '/month';
										}
										echo '</label>
											<input type="checkbox" id="service' . $arr['id'] . 'active" name="service' . $arr['id'] . 'active"';
											$service = $db->query("SELECT 'X' FROM order_services WHERE orderid =  ? AND serviceid = ? AND active = 1", array($ordarr['id'], $arr['id']))->numRows();
											if($service >= 1) {
												echo "checked";
											}
											echo '/><label for="service' . $arr['id'] . 'active">Active</label>
									</div>';
								}
							?>
							<div class="col-8 col-12-xsmall">
								<h5>Comments</h5>
							</div>
							<div class="col-12">
								<p><?php echo $ordarr['comments']; ?></p>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Update Order Information" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				<button class="accordion">Pictures</button>
				<div class="panel">
					<br />
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?id=<?php echo $ordarr['id']; ?>">
						<div class="row gtr-uniform">
							<div class="col-12">
								Upload New Picture:
								<input id="file-upload" type="file" accept="image/*">
								<label id="file-name"></label>
							</div>
						</div>
						<br />
						<ul id="sortable" style="list-style: none;">
							<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><img src="https://specials-images.forbesimg.com/imageserve/1026205392/960x0.jpg?fit=scale" width="25%" length="25%"/></li>
							<li class="ui-state-default"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span>Test</li>
						</ul>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Update Pictures" class="primary" /></li>
							</ul>
						</div>
					</form>
				</div>
				<br />
				<span><font color="red"><?php echo $comm_err; ?></font></span>
				<div class="communication">
					<h6>Send Message to JSB Regarding Order</h6>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?view=<?php echo $ordarr['id']; ?>">
						<div class="row gtr-uniform">
							<div class="col-12">
								<textarea name="comm_message" id="comm_message" placeholder="Write Message Here..." rows="6"></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Send Message" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				<?php
					$comms = $db->query("SELECT * FROM order_messages WHERE orderid = ? ORDER BY timesent DESC", $ordarr['id'])->fetchAll();
					foreach($comms as $comm) {
						$username = $db->query("SELECT username FROM users WHERE id = ?", $comm['user_from'])->fetchArray();
						$datetime = new DateTime();
						$datetime = DateTime::createFromFormat('U', $comm['timesent']);
						$date_time_format = $datetime->format('m/d/Y H:i:s');
						$time_zone_from="UTC";
						$time_zone_to='America/New_York';
						$display_date = new DateTime($date_time_format, new DateTimeZone($time_zone_from));
						$display_date->setTimezone(new DateTimeZone($time_zone_to));
						echo '<span>From: ' . $username['username'] . ' at ' . $display_date->format('m/d/Y h:i:sA T') . '</span>
							<blockquote>' . $comm['message'] . '</blockquote>';
					}
				?>
				</div>
			</section>
		</div>
	</section>
</article>
<script type="text/javascript">
init();
var mouseMoved = false;
function touchHandler(event) {
    // Declare the default mouse event.
    var mouseEvent = "mousedown";
    // Create the event to transmit.
        var simulatedEvent = document.createEvent("MouseEvent");

        switch (event.type) {
        case "touchstart":
            mouseEvent = "mousedown";
            break;
        case "touchmove":
            /*
            * If this has been hit, then it's a move and a mouseup, not a click
            * will be transmitted.
            */
            mouseMoved = true;
            mouseEvent = "mousemove";
            break;
        case "touchend":
            /*
            * Check to see if a touchmove event has been fired. If it has
            * it means this have been a move and not a click, if not
            * transmit a mouse click event.
            */
            if (!mouseMoved) {
                mouseEvent = "click";
            } else {
                mouseEvent = "mouseup";
            }
            // Initialize the mouseMove flag back to false.
            mouseMoved = false;
            break;
        }

        var touch = event.changedTouches[0];

        /*
         * Build the simulated mouse event to fire on touch events.
         */
        simulatedEvent.initMouseEvent(mouseEvent, true, true, window, 1,
        touch.screenX, touch.screenY,
        touch.clientX, touch.clientY, false,
        false, false, false, 0, null);

        /*
         * Transmit the simulated event to the target. This, in combination
         * with the case statement above, ensures that click events are still
         * transmitted and bubbled up the chain.
         */
        touch.target.dispatchEvent(simulatedEvent);

        /*
         * Prevent default dragging of element.
         */
        event.preventDefault();
    }

function init() {
    document.getElementById('sortable').addEventListener("touchstart", touchHandler, true);
    document.getElementById('sortable').addEventListener("touchmove", touchHandler, true);
    document.getElementById('sortable').addEventListener("touchend", touchHandler, true);
    document.getElementById('sortable').addEventListener("touchcancel", touchHandler, true);
}
$(function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	});
document.querySelector("#file-upload").onchange = function(){
  document.querySelector("#file-name").textContent = this.files[0].name;
}
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>
<?php
	include "includes/footer.php";
?>