<?php
	include "includes/innerheader.php";
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=view_invoices.php">';
		exit;
	}
	$query = "SELECT i.*, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, SUM(NVL(p.amount, 0)) as payment FROM invoices i JOIN orders o ON i.orderid = o.id LEFT JOIN payments p ON p.inv_id = i.id WHERE i.id = ?";
	$rows = $db->query($query, $db->escape($_GET['id']))->numRows();
	if($rows <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=view_invoices.php">';
		exit; 
	}
	$invarr = $db->fetchArray();
	
	// Get Charges
	$getchargerows = $db->query("SELECT SUM(amount) AS charge_sum FROM charges WHERE inv_id = ?", $db->escape($_GET['id']))->numRows();
	$getcharges = $db->fetchArray();
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(isset($_GET['date']) && $_GET['date'] == 'Y') {
			if(empty(trim($_POST['date']))) {
				$duedate_err = "Please specify a due date";
			}
			else {
				$test_arr  = explode('-', $_POST['date']);
				if (count($test_arr) == 3) {
					if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
						$duedate_err = "Invalid due date: " . $_POST['date'];
					}
				} else {
					$duedate_err = "Invalid due date (not count):" . $_POST['date'];
				}
			}
			if(empty($duedate_err)) {
				$sql = "UPDATE invoices SET duedate = ? WHERE id = ?";
				
				if($db->query($sql, $db->escape(strtotime($_POST['date'])), $invarr['id'])) {
					$confirm = "Due Date Updated";
				}
				else {
					$confirm = "Something went wrong! Please try again.";
				}
			}
		}
		else if(isset($_GET['charge']) && $_GET['charge'] == 'Y') {
			if(empty(trim($_POST['charge']))) {
				$charge_err = "Please specify an amount";
			}
			if(!is_numeric($_POST['charge'])) {
				$charge_err = "Please enter a numeric amount";
			}
			if(empty(trim($_POST['date']))) {
				$chargedate_err = "Please specify a charge date";
			}
			else {
				$test_arr  = explode('-', $_POST['date']);
				if (count($test_arr) == 3) {
					if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
						$chargedate_err = "Invalid charge date: " . $_POST['date'];
					}
				} else {
					$chargedate_err = "Invalid charge date (not count):" . $_POST['date'];
				}
			}
			if(empty(trim($_POST['chargename']))) {
				$charge_name_err = "Please specify a charge description";
			}
			
			if(empty($charge_err) && empty($chargedate_err) && empty($charge_name_err)) {
				$sql = "INSERT INTO charges (inv_id, orderid, charge_type, charge_name, amount, charge_date) VALUES (?, ?, ?, ?, ?, ?)";

				if($db->query($sql, $invarr['id'], $invarr['orderid'], 2, $db->escape($_POST['chargename']), $db->escape($_POST['charge']), $db->escape(strtotime($_POST['date'])))) {
					$confirm = "Charge Added";
				}
				else {
					$confirm = "Something went wrong! Please try again.";
				}
			}
		}
		else {
			if(empty(trim($_POST['paid']))) {
				$payamt_err = "Please specify an amount";
			}
			if(!is_numeric($_POST['paid'])) {
				$payamt_err = "Please enter a numeric amount";
			}
			if(empty(trim($_POST['date']))) {
				$paydate_err = "Please specify a payment date";
			}
			else {
				$test_arr  = explode('-', $_POST['date']);
				if (count($test_arr) == 3) {
					if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
						$paydate_err = "Invalid payment date: " . $_POST['date'];
					}
				} else {
					$paydate_err = "Invalid payment date (not count):" . $_POST['date'];
				}
			}
			// Validate payment type
			if($_POST['payment_type'] <> 1 && $_POST['payment_type'] <> 2 && $_POST['payment_type'] <> 3) {
				$paytype_err = "Please select payment type.";
			}
			if($_POST['payment_type'] == 2) {
				if(empty(trim($_POST['checknum']))) {
						$chknum_err = "Please enter the check number.";
				}
				if(empty(trim($_POST['routenum']))) {
						$rtnum_err = "Please enter the routing number.";
				}
			}
			if(empty($payamt_err) && empty($paydate_err) && empty($paytype_err) && empty($chknum_err) && empty($rtnum_err)) {
				$sql = "INSERT INTO payments (inv_id, orderid, pmt_type, chk_num, rt_num, amount, pay_date) VALUES (?, ?, ?, ?, ?, ?, ?)";

				if($db->query($sql, $invarr['id'], $invarr['orderid'], $db->escape($_POST['payment_type']), $db->escape($_POST['checknum']), $db->escape($_POST['routenum']), $db->escape($_POST['paid']), $db->escape(strtotime($_POST['date'])))) {
					$confirm = "Payment Added";
				}
				else {
					$confirm = "Something went wrong! Please try again.";
				}
			}
		}
		$query = "SELECT i.*, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, SUM(NVL(p.amount, 0)) as payment FROM invoices i JOIN orders o ON i.orderid = o.id LEFT JOIN payments p ON p.inv_id = i.id WHERE i.id = ?";
		$rows = $db->query($query, $db->escape($_GET['id']))->numRows();
		if($rows <> 1) {
			echo '<meta http-equiv="Refresh" content="0; url=view_invoices.php">';
			exit; 
		}
		$invarr = $db->fetchArray();
		
		// Get Charges
		$getchargerows = $db->query("SELECT SUM(amount) AS charge_sum FROM charges WHERE inv_id = ?", $db->escape($_GET['id']))->numRows();
		$getcharges = $db->fetchArray();
	}
?>
<article id="main">
	<header>
		<h2>Edit Invoice</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<div class="col-8 col-12-xsmall">
					<h5>Invoice Details</h5>
				</div>
				<p><font color="red"><?php echo $confirm; ?></font></p>
				<div class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<td>Order Number</td>
								<td><?php echo $invarr['orderid']; ?></td>
							</tr>
							<tr>
								<td>Property Address</td>
								<td><?php echo $invarr['prop_addr1'] . ', ';
							if(isset($invarr['propr_addr2'])) {
									echo $invarr['prop_addr2'] . ', ';
								}
								echo $invarr['prop_city'] . ', ' . $invarr['prop_state'] . ', ' . $invarr['prop_zip']; ?></td>
							<tr>
								<td>Invoice Comments</td>
								<td><?php echo $invarr['comments']; ?></td>
							</tr>
							<tr>
								<td>Invoice Amount</td>
								<td>$<?php echo number_format($invarr['amount']); ?></td>
							</tr>
							<tr>
								<td>Paid Amount</td>
								<td>$<?php echo number_format($invarr['payment']); ?></td>
							</tr>
							<tr>
								<td>Extra Charges</td>
								<td>$<?php 
										if($getchargerows > 0) {
											echo number_format($getcharges['charge_sum'], 2); 
										}
										else {
											echo number_format(0, 2);
										}
									?>
								</td>
							</tr>
							<tr>
								<td>Total Due</td>
								<td id="invdue">$<?php if($getchargerows > 0) {
									if($invarr['amount'] + $getcharges['charge_sum'] - $invarr['payment'] < 0) {
										$amount = 0;
									}
									else {
										$amount = $invarr['amount'] + $getcharges['charge_sum'] - $invarr['payment'];
									}	
								}
								else {
									$amount = $invarr['amount'] - $invarr['payment'];
								}
								echo number_format($amount, 2); ?>
								</td>
							</tr>
							<tr>
								<td>Due Date</td>
								<td><?php echo getDateFromUNIX($invarr['duedate']); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="col-8 col-12-xsmall">
					<h5>Update Due Date</h5>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $_GET['id']; ?>&date=Y">
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $duedate_err; ?></font></span>
							<input type="date" name="date" id="date" placeholder="Payment Date (MM/DD/YYYY)" style="color: #4E4852;" />
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Update Due Date" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
				<div class="col-8 col-12-xsmall">
					<h5>Add Payment</h5>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $_GET['id']; ?>">
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $payamt_err; ?></font></span>
							<input type="text" name="paid" id="paid" placeholder="Payment Amount" />
						</div>
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $paydate_err; ?></font></span>
							<input type="date" name="date" id="date" placeholder="Payment Date (MM/DD/YYYY)" style="color: #4E4852;" />
						</div>
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $paytype_err; ?></font></span>
							<select name="payment_type" id="payment_type">
								<option value="">- Payment Type -</option>
								<option value="1">Electronic</option>
								<option value="2">Check</option>
								<option value="3">Cash</option>
							</select>
						</div>
						<div id="divcheck" style="display:none;" class="col-8 col-12-xsmall">
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $chknum_err; ?></font></span>
								<input type="text" id="checknum" name="checknum" placeholder="Check Number" />
							</div>
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $rtnum_err; ?></font></span>
								<input type="text" id="routenum" name="routenum" placeholder="Routing Number" />
							</div>
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Add Payment" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
				<div class="col-8 col-12-xsmall">
					<h5>Add Charge</h5>
				</div>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $_GET['id']; ?>&charge=Y">
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $charge_err; ?></font></span>
							<input type="text" name="charge" id="charge" placeholder="Charge Amount" />
						</div>
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $chargedate_err; ?></font></span>
							<input type="date" name="date" id="date" placeholder="Charge Date (MM/DD/YYYY)" style="color: #4E4852;" />
						</div>
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $charge_name_err; ?></font></span>
							<input type="text" name="chargename" id="chargename" placeholder="Charge Name" />
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Add Charge" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
			</section>
		</div>
	</section>
</article>
<script>
	document.getElementById("payment_type").addEventListener("change", Policies);
	
	function Policies() {
		var dropdown = document.getElementById("payment_type");
		var type = dropdown.options[dropdown.selectedIndex].value;
		var check = document.getElementById("divcheck");
		if(type == "2") {
			check.style.display = "block";
		}
		else {
			check.style.display = "none";
		}
	}
	
</script>
<?php
	include "includes/footer.php";
?>