<?php
	include "includes/innerheader.php";
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=view_realtor.php">';
		exit;
	}
	// Get company orders
	$order_rows = $db->query("SELECT * FROM orders WHERE userid = ? ORDER BY status ASC", $db->escape($_GET['id']))->numRows();
	$orders = $db->fetchAll();
	
	// Get company invoices
	$query = "SELECT i.*, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM invoices i JOIN orders o ON i.orderid = o.id WHERE o.userid = ? AND ";
	$notpaidrows = $db->query($query . "i.status = 0 ORDER BY i.duedate ASC", $db->escape($_GET['id']))->numRows();
	$notpaidresults = $db->fetchAll();
	$paidrows = $db->query($query . "i.status = 1 ORDER BY i.duedate ASC", $db->escape($_GET['id']))->numRows();
	$paidresults = $db->fetchAll();
	
	// Get company payments
	$q = "SELECT o.id, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM orders o ";
	$q .= "WHERE o.userid = ? AND o.id IN (SELECT i.orderid FROM invoices i WHERE i.id IN (SELECT p.inv_id FROM payments p WHERE i.id = p.inv_id))";
	$payrows = $db->query($q, $db->escape($_GET['id']))->numRows();
	$payresults = $db->fetchAll();
	
	// Get company licenses
	$licenserows = $db->query("SELECT u_l.* FROM user_licenses u_l WHERE u_l.userid = ?", $db->escape($_GET['id']))->numRows();
	$licenses = $db->fetchAll();
	
	// Get company info
	$compinfo = $db->query("SELECT u.* FROM users u WHERE u.id = ?", $db->escape($_GET['id']))->fetchArray();
?>
<article id="main">
	<header>
		<h2>View Realtor</h2>
		<p>View Deatils for <?php echo $compinfo['company']; ?></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section id="top">
				<div class="col-8 col-12-xsmall">
					<a href="#orders">View Orders</a>
				</div>
				<div class="col-8 col-12-xsmall">
					<a href="#invoices">View Invoices</a>
				</div>
				<div class="col-8 col-12-xsmall">
					<a href="#payments">View Payments</a>
				</div>
				<div>
					<a href="#info">View Realtor Info</a>
				</div>
				<p></p>
			</section>
			<!-- orders -->
			<section>
				<h3 id="orders">Orders</h3>
			<?php
				if ($order_rows > 0) {
			?>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Property Address</th>
								<th>Status</th>
								<th>View Details</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($orders as $arr) {
								echo '
							<tr>
								<td>' . $arr['prop_addr1'] . ', ';
								if(isset($arr['propr_addr2'])) {
									echo $arr['prop_addr2'] . ', ';
								}
								echo $arr['prop_city'] . ', ' . $arr['prop_state'] . ', ' . $arr['prop_zip'] . '</td>
								<td>' . translate_status($arr['status']) . '</td>
								<td><a href="order_details.php?view=' . $arr['id'] . '">Details</a></td>
							</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
				}
				else {
					echo '<p>They have not ordered yet!</p>';
				}
			?>
			</section>
			<!-- invoices -->
			<section>
			<h3 id="invoices">Invoices</h3>
			<?php
				if($notpaidrows > 0 || $paidrows > 0) {
					echo '
					<table>
							<tr>
								<th>Property Address</th>
								<th>Invoice Due</th>
								<th>Status</th>
								<th>Details</th>
							</tr>
							<tbody id="sortable">';
					foreach($notpaidresults as $notpaid) {
							echo '<tr>
								<td>'.  $notpaid['prop_addr1'] . ', ';
							if(isset($notpaid['propr_addr2'])) {
									echo $notpaid['prop_addr2'] . ', ';
								}
								echo $notpaid['prop_city'] . ', ' . $notpaid['prop_state'] . ', ' . $notpaid['prop_zip'] . '</td>
								<td>' . getDateFromUNIX($notpaid['duedate']) . '</td>
								<td>Not Paid</td>
								<td><a href="invoice.php?id=' . $notpaid['id'] . '">Details</a>
							</tr>';
					}
					foreach($paidresults as $paid) {
							echo '<tr>
								<td>'.  $paidresults['prop_addr1'] . ', ';
							if(isset($paidresults['propr_addr2'])) {
									echo $paidresults['prop_addr2'] . ', ';
								}
								echo $paidresults['prop_city'] . ', ' . $paidresults['prop_state'] . ', ' . $paidresults['prop_zip'] . '</td>
								<td>' . getDateFromUNIX($paidresults['duedate']) . '</td>
								<td>Paid</td>
								<td><a href="invoice.php?id=' . $paidresults['id'] . '">Details</a>
							</tr>';
					}
					?>
							</tbody>
					</table>
					<?php
						}
						else {
							echo '<p>They do not have any invoices yet!</p>';
						}
					?>
			</section>
			<!-- payments -->
			<section>
			<h3 id="payments">Payments</h3>
			<?php
				if ($payrows > 0) {
			?>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Property Address</th>
								<th>Amount</th>
								<th>Pay Date</th>
								<th>View Invoice</th>
							</tr>
						</thead>
						<tbody>
							<tr>
						<?php
							foreach($payresults as $arr) {
								echo '
								<td>' . $arr['prop_addr1'] . ', ';
								if(isset($arr['propr_addr2'])) {
									echo $arr['prop_addr2'] . ', ';
								}
								echo $arr['prop_city'] . ', ' . $arr['prop_state'] . ', ' . $arr['prop_zip'] . '</td>';
								$results2 = $db->query("SELECT p.inv_id, p.amount, p.pay_date, p.pmt_type FROM payments p JOIN invoices i ON p.inv_id = i.id WHERE i.orderid = ? ORDER BY pay_date DESC", $arr['id'])->fetchAll();
								foreach($results2 as $arr2) {
									echo '<td>$' . number_format($arr2['amount'], 2) . '</td>
									<td>' . getDateFromUNIX($arr2['pay_date']) . '</td>
									<td><a href="invoice.php?id=' . $arr2['inv_id'] . '">View</a></td>
									</tr><tr><td></td>';
								}
							echo '<td></td><td></td><td></td></tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
				}
				else {
					echo '<p>This realtor has not made any payments yet.</p>';
				}
			?>
			</section>
			<!-- licenses -->
			<section>
			<h3 id="licenses">Licenses</h3>
			<?php
				if ($licenserows > 0) {
			?>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>License State</th>
								<th>License Number</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($licenses as $license) {
								echo '<tr><td>' . $license['license_state'] . '</td>
								<td>' . $license['license_number'] . '</td>
								</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
				}
				else {
					echo '<p>This realtor does not have any licenses!</p>';
				}
			?>
			</section>
			<!-- info -->
			<section>
			<h3 id="info">Info</h3>
				<div class="row gtr-uniform">
					<div class="col-8 col-12-xsmall">
						<h5>Email:</h5><p> <?php echo $compinfo['email_addr']; ?></p>
					</div>
					<div class="col-8 col-12-xsmall">
						<h5>Phone:</h5><p> <?php echo $compinfo['phone_num']; ?></p>
					</div>
					<div class="col-8 col-12-xsmall">
						<h5>Company Name:</h5><p> <?php echo $compinfo['company']; ?></p>
					</div>
					<div class="col-8 col-12-xsmall">
						<h5>Company Address:</h5><p> <?php echo $compinfo['company_addr1'] . '<br />'
							. $compinfo['company_addr2'] . '<br />'
							. $compinfo['company_city'] . ', ' . $compinfo['company_state'] . ' '
							. $compinfo['company_zip']; ?>
						</p>
					</div>
					<div class="col-8 col-12-xsmall">
						<h5>Contact Preference:</h5><p> 
						<?php
							switch($compinfo['contact_pref']) {
								case 1:
									echo "Email";
									break;
								case 2:
									echo "Phone";
									break;
							}
						?>
						</p>
					</div>
					<div class="col-8 col-12-xsmall">
						<h5>Company Type:</h5><p> 
						<?php
							switch($compinfo['company_type']) {
								case 1:
									echo "Commercial";
									break;
								case 2:
									echo "Residential";
									break;
								case 3:
									echo "Commercial/Residential";
									break;
							}
						?>
						</p>
					</div>
				</div>
			</section>
		</div>
	</section>
</article>
<?php
	include "includes/footer.php";
?>