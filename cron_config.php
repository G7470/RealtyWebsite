<?php
	include "includes/innerheader.php";
	
	$vals = array('seven_before', 'after_due_date', 'order_placed', 'comm_notif', 'inv_create', 'inv_update', 'inv_paid', 'ord_update', 'acc_update');
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		$seven_before = $db->escape(addslashes(trim($_POST['seven_before'])));
		$seven_before_active = isset($_POST['seven_before_active']) ? '1' : '0';
		$after_due_date = $db->escape(addslashes(trim($_POST['after_due_date'])));
		$after_due_date_active = isset($_POST['after_due_date_active']) ? '1' : '0';
		$order_placed = $db->escape(addslashes(trim($_POST['order_placed'])));
		$order_placed_active = isset($_POST['order_placed_active']) ? '1' : '0';
		$comm_notif = $db->escape(addslashes(trim($_POST['comm_notif'])));
		$comm_notif_active = isset($_POST['comm_notif_active']) ? '1' : '0';
		$inv_create = $db->escape(addslashes(trim($_POST['inv_create'])));
		$inv_create_active = isset($_POST['inv_create_active']) ? '1' : '0';
		$inv_update = $db->escape(addslashes(trim($_POST['inv_update'])));
		$inv_update_active = isset($_POST['inv_update_active']) ? '1' : '0';
		$inv_paid = $db->escape(addslashes(trim($_POST['inv_paid'])));
		$inv_paid_active = isset($_POST['inv_paid_active']) ? '1' : '0';
		$ord_update = $db->escape(addslashes(trim($_POST['ord_update'])));
		$ord_update_active = isset($_POST['ord_update_active']) ? '1' : '0';
		$acc_update = $db->escape(addslashes(trim($_POST['acc_update'])));
		$acc_update_active = isset($_POST['acc_update_active']) ? '1' : '0';

		for($i = 0; $i < count($vals); $i++) {
			$db->query("UPDATE emails_config SET active = ?, email_text = ? WHERE email_val = ?", ${$vals[$i] . '_active'}, ${$vals[$i]}, $vals[$i]);
		}
		$status_err = "Email text updated";
	}
	$i = 0;
	$IDs = $db->query("SELECT * FROM emails_config")->fetchAll();
	foreach($IDs AS $emailid) {
		${$vals[$i]} = stripslashes($emailid['email_text']);
		${$vals[$i] . 'active'} = $emailid['active'];
		$i++;
	}
?>
<article id="main">
	<header>
		<h2>Email Text Settings</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
						<span><font color="red"><?php echo $status_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<h5>7 Days Before Invoice Due Notification</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="seven_before_active" name="seven_before_active" <?php echo ($seven_before_active == 1) ? 'checked' : ''; ?>>
								<label for="seven_before_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea id="seven_before" name="seven_before"><?php echo $seven_before; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Invoice Past Due (1 day after due date)</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="after_due_date_active" name="after_due_date_active" <?php echo ($after_due_date_active == 1) ? 'checked' : ''; ?>>
								<label for="after_due_date_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="after_due_date" id="after_due_date"><?php echo $after_due_date; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Order Placed Notification</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="order_placed_active" name="order_placed_active" <?php echo ($order_placed_active == 1) ? 'checked' : ''; ?>>
								<label for="order_placed_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="order_placed" id="order_placed"><?php echo $order_placed; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Communication Notification</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="comm_notif_active" name="comm_notif_active" <?php echo ($comm_notif_active == 1) ? 'checked' : ''; ?>>
								<label for="comm_notif_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="comm_notif" id="comm_notif"><?php echo $comm_notif; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Invoice Created</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="inv_create_active" name="inv_create_active" <?php echo ($inv_create_active == 1) ? 'checked' : ''; ?>>
								<label for="inv_create_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="inv_create" id="inv_create"><?php echo $inv_create; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Invoice Updated</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="inv_update_active" name="inv_update_active" <?php echo ($inv_update_active == 1) ? 'checked' : ''; ?>>
								<label for="inv_update_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="inv_update" id="inv_update"><?php echo $inv_update; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Invoice Paid in Full</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="inv_paid_active" name="inv_paid_active" <?php echo ($inv_paid_active == 1) ? 'checked' : ''; ?>>
								<label for="inv_paid_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="inv_paid" id="inv_paid"><?php echo $inv_paid; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Order Updated</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="ord_update_active" name="ord_update_active" <?php echo ($ord_update_active == 1) ? 'checked' : ''; ?>>
								<label for="ord_update_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="ord_update" id="ord_update"><?php echo $ord_update; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Account Updated</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="acc_update_active" name="acc_update_active" <?php echo ($acc_update_active == 1) ? 'checked' : ''; ?>>
								<label for="acc_update_active">Active</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="acc_update" id="acc_update"><?php echo $acc_update; ?></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Update Email Text" class="primary" /></li>
								</ul>
							</div>
						</div>
			</section>
		</div>
	</section>
</article>
<script>
tinymce.init({
    selector: '#after_due_date'
});
tinymce.init({
    selector: '#seven_before'
});
tinymce.init({
    selector: '#order_placed'
});
tinymce.init({
    selector: '#comm_notif'
});
tinymce.init({
    selector: '#inv_create'
});
tinymce.init({
    selector: '#inv_update'
});
tinymce.init({
    selector: '#inv_paid'
});
tinymce.init({
    selector: '#ord_update'
});
tinymce.init({
    selector: '#acc_update'
});
</script>
<?php
	include "includes/footer.php";
?>