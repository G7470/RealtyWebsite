<?php
	include "includes/innerheader.php";
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		$_GET['id'] = 0;
	}
	if(checkAdmin($user['usertype'])) {
		$userid = 0;
	}
	else {
		$userid = $user['id'];
	}
	$query = "SELECT i.*, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM invoices i JOIN orders o ON i.orderid = o.id WHERE (o.userid = ? OR 0 = ?) AND (o.id = ? OR 0 = ?) AND ";
	$notpaidrows = $db->query($query . "i.status = 0 ORDER BY i.duedate ASC", array($userid, $userid, $_GET['id'], $_GET['id']))->numRows();
	$notpaidresults = $db->fetchAll();
	$paidrows = $db->query($query . "i.status = 1 ORDER BY i.duedate ASC", array($userid, $userid, $_GET['id'], $_GET['id']))->numRows();
	$paidresults = $db->fetchAll();
?>
<article id="main">
		<header>
			<h2>Invoices</h2>
			<p>Current invoices are listed below</p>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<section>
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
							echo '<p>You do not have any invoices yet! <a href="order_history.php">Check order history</a> to see if you have any pending orders!</a></p>';
						}
					?>
				</section>
			</div>
	</section>
</article>

<?php
	include "includes/footer.php";
?>