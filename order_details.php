<?php
	include "includes/innerheader.php";
	if(!isset($_GET['view']) || !is_numeric($_GET['view'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit;
	}
	$rows = $db->query("SELECT * FROM orders WHERE id = ? AND userid = ?", array($db->escape($_GET['view']), $user['id']))->numRows();
	if($rows <> 1 && !checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit; 
	}
	
	if(checkAdmin($user['usertype'])) {
		$rows = $db->query("SELECT * FROM orders WHERE id = ?", array($db->escape($_GET['view'])))->numRows();
		if($rows <> 1) {
			echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
			exit; 
		}
	}
	
	$ordarr = $db->fetchArray();
	$inv_exist = $db->query("SELECT * FROM invoices WHERE orderid = ?", $ordarr['id'])->numRows();
	$services = $db->query("SELECT * FROM order_services WHERE orderid = ? AND active = 1", $ordarr['id'])->fetchAll();
	
	if($_SERVER['REQUEST_METHOD'] == "POST") {
		if(isset($_POST['comm_message'])) {
			$message = $db->escape(strip_tags($_POST['comm_message']));
			if(empty($message)) {
				$comm_err = "The message text is empty.";
			}
			else {
				if($db->query("INSERT INTO order_messages (orderid, user_from, message, timesent) VALUES (?, ?, ?, ?)", array($ordarr['id'], $user['id'], $message, time()))) {
					$db->query("UPDATE orders SET status = 1 WHERE id = ?", $ordarr['id']);
					$comm_err = "Message Sent!";
				}
				else {
					$comm_err = "Something went wrong! Please try again.";
				}
			}
		}
		if(isset($_GET['type'])) {
			if ($_FILES['file-upload']['error'] !== UPLOAD_ERR_OK) {
				$pic_err = "Upload failed with error code " . $_FILES['file-upload']['error'];
			}
			if(is_uploaded_file($_FILES['file-upload']['tmp_name'])) {
				$tmpname = $db->escape($_FILES['file-upload']['tmp_name']);
				$filename = $db->escape($_FILES['file-upload']['name']);
				if(!is_dir(dirname(__FILE__) . "/images/userimages/" . $user['id'] . "_" . $ordarr['id'])) {
					mkdir(dirname(__FILE__) . "/images/userimages/" . $user['id'] . "_" . $ordarr['id'], 0777, false);
				}
				$exists = $db->query("SELECT * FROM order_pic WHERE filename = ? AND orderid = ?", $filename, $ordarr['id'])->numRows();
				if($exists < 1) {
					move_uploaded_file($db->escape($_FILES['file-upload']['tmp_name']), dirname(__FILE__) . "/images/userimages/" . $user['id'] . "_" . $ordarr['id'] . "/" . $_FILES['file-upload']['name']);
					$db->query("INSERT INTO order_pic (orderid, filename) VALUES (?, ?)", array($ordarr['id'], $_FILES['file-upload']['name']));
				}
				else {
					$pic_err = "Picture with that file name already exists! Change the name of the file and re-upload the picutre.";
				}
			}
			else {
				$pic_err = "No picture! Try again.";
			}
		}
	}
	
	$getpictures = $db->query("SELECT * FROM order_pic WHERE orderid = ?", $ordarr['id'])->fetchAll();
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
		<h2>View Order</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<?php if(checkAdmin($user['usertype'])) {
			?>
				<a class="button primary" href="edit_order.php?id=<?php echo $ordarr['id']; ?>">Update Order</a>
			<?php
			}
					if($ordarr['status'] == 1) {
						echo '<p><font color="red">This order is waiting for your response! Please view communications below.</font></p>';
					}
					elseif($ordarr['status'] == 2) {
						echo '<p><font color="red">This order is waiting for your payment! Please pay the invoice below.</font></p>';
					}
				?>
				<div class="col-8 col-12-xsmall">
					<h5>Order Details</h5>
				</div>
				<div class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<td>Order Placed</td>
								<?php
								echo '<td>' . getDateFromUNIX($ordarr['order_placed']) . '</td>
							</tr>
							<tr>
								<td>Property Address</td>
								<td>' . $ordarr['prop_addr1'] . ', ';
								if(isset($ordarr['propr_addr2'])) {
									echo $ordarr['prop_addr2'] . ', ';
								}
								echo $ordarr['prop_city'] . ', ' . $ordarr['prop_state'] . ', ' . $ordarr['prop_zip'] . '</td>
							<tr>
								<td>Services Requested</td>
								<td>';
								$i = 1;
								foreach($services as $service) {
									if($i > 1) {
										echo ', ';
									}
									$servname = $db->query("SELECT * FROM services WHERE id = ?", $service['serviceid'])->fetchArray();
									echo $servname['serv_name'];
									$i++;
								}
							echo '</td>
							</tr>
							<tr>
								<td>Status</td>
								<td>' . translate_status($ordarr['status']) . '</td>
							
							</tr>
							<tr>
								<td>Comments</td>
								<td>' . $arr['comments'] . '</td>
							</tr>
							<tr>
								<td>Invoice</td>
								<td>';
								if($inv_exist > 0) {
									echo '<a href="view_invoices.php?id=' . $ordarr['id'] . '">View Invoices</a>';
								}
								else {
									echo 'N/A';
								}
							echo '</td>
							</tr>';
							?>
							<tr>
								<td>Listing Information</td>
								<td><a href="create_listing.php?id=<?php echo $ordarr['id']; ?>">View Listing</a></td>
							</tr>
							<?php 
								if(checkAdmin($user['usertype'])) {
									echo '<tr><td>New Invoice</td>
									<td><a href="create_invoice.php?id=' . $ordarr['id'] . '">Create Invoice</a></td></tr>';
								}
							?>
						</tbody>
					</table>
					<span><font color="red"><?php echo $pic_err; ?></font></span>
				<button class="accordion">Pictures</button>
				<div class="panel">
					<br />
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?view=<?php echo $ordarr['id']; ?>&type=picture" enctype="multipart/form-data">
						<div class="row gtr-uniform">
							<div class="col-12">
								Upload New Picture:
								<input name="file-upload" id="file-upload" type="file" accept="image/*" />
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Add Picture" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
					<br />
					<ul id="sortable" style="list-style: none;">
						<?php 
							foreach($getpictures as $picture) {
						?>
								<li class="ui-state-default" id="<?php echo $picture['id']; ?>"><span class="ui-icon ui-icon-arrowthick-2-n-s"></span><img src="images/userimages/<?php echo $ordarr['userid'] . '_' . $ordarr['id'] . '/' . $picture['filename']; ?>" width="25%" length="25%"/>
									<button onclick="deletepicture(<?php echo $picture['id']; ?>);">Delete</button>
								</li>
						<?php
							}
						?>
					</ul>
					<button class="primary" onclick="updatePictureSort(<?php echo $ordarr['id']; ?>);">Update Order</button>
				</div>
				<br />
				<span><font color="red"><?php echo $comm_err; ?></font></span>
				<div class="communication">
					<div class="col-8 col-12-xsmall"> 
						<h5>Send Message to JSB Regarding Order</h5>
					</div>
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
function updatePictureSort(ordid) {
	let rowsort = [' '];
	// get all elements
	var pictures = document.querySelectorAll('#sortable li');
	var i = 1;
	// convert NodeList into an array
	// for older browser use [].slice.call(element)
	Array.from(pictures)
		// iterate over the element
		.forEach(function(ele, i) {
			if(i == 0) {
				rowsort.pop();
				rowsort[i] = ele.getAttribute("id");
				i++;
			}
			else {
				rowsort.push(ele.getAttribute("id"));
			}
		});
		for(i = 0; i < rowsort.length; i++) {
			alert(rowsort[i]);
	  }
	  //newrowsort = JSON.stringify(rowsort);
	/* AJAX */
	$.ajax({
			url: 'ajax_sort_pictures.php',
			type: 'post',
			dataType: 'text',
			data: {list: rowsort,
					orderid: ordid},
			success: function(returnval) {
				alert(returnval);
				alert("Your pictures have been re-sorted!");
			},
			error: function() {
				alert("Your pictures have not been re-sorted! Refresh and try again.");
			}
		});
		// End AJAX
	
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

function deletepicture(picid) {
	var confirmation = confirm("Are you sure you want to delete this picture?");
	if(confirmation == true) {
		/* Call AJAX */
		$.ajax({
			url: 'ajax_delpicture.php',
			type: 'post',
			dataType: 'text',
			data: {
				picture_id: picid
			},
			success: function() {
				alert("The picture has been deleted!");
			},
			error: function() {
				alert("The picture could not be deleted!");
			}
		});
	}
}
</script>
<?php
	include "includes/footer.php";
?>