<?php
	include "includes/innerheader.php";
	
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=order_history.php">';
		exit;
	}

	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	$rows = $db->query("SELECT * FROM promotion p JOIN orders o ON o.id = p.orderid WHERE o.id = ?", $db->escape($_GET['id']))->numRows();
	if($rows <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	$getpromotion = $db->fetchArray();
	
	// Set Address
	$addr = $getpromotion['prop_addr1'] . ', ';
	if(isset($getpromotion['propr_addr2'])) {
		$addr .= $getpromotion['prop_addr2'] . ', ';
	}
	$addr .= $getpromotion['prop_city'] . ', ' . $getpromotion['prop_state'] . ', ' . $getpromotion['prop_zip'];
?>
<article id="main">
	<header>
		<h2>Edit Promotion</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
			<div class="inner">
			<section>
				<button onclick="deletepromotion(<?php echo $_GET['id']; ?>);" >Delete Promotion</button>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?">
					<input type="hidden" value="<?php echo $_GET['id']; ?>" id="orderid" name="orderid" />
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<h5>Order Address</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<p><?php echo $addr; ?></p>
						</div>
						<div class="col-8 col-12-xsmall">
							<h5>Start Date</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="date" id="startdate" value="<?php echo Date("Y-m-d", $getpromotion['date_start']); ?>" name="startdate"  />
						</div>
						<div class="col-8 col-12-xsmall">
							<h5>End Date</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="date" id="enddate" name="enddate" value="<?php echo Date("Y-m-d", $getpromotion['date_end']); ?>" />
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Edit Promotion" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
			</section>
		</div>
	</section>
</article>
<script>
	function deletepromotion(key) {
		var confirmation = confirm("Are you sure you want to delete this promotion?");
		if(confirmation == true) {
			/* Call AJAX */
			$.ajax({
				url: 'ajax_delpromotion.php',
				type: 'post',
				dataType: 'text',
				data: {
					orderid: key
				},
				success: function() {
					alert("The promotion has been deleted!");
					window.location.replace("promote.php");
				},
				error: function() {
					alert("The promotion could not be deleted!");
				}
			});
		}
	}
</script>