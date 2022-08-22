<?php

	include "includes/innerheader.php";
	$q = "SELECT o.id, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM orders o ";
	$q .= "WHERE o.userid = ? AND o.id IN (SELECT i.orderid FROM invoices i WHERE i.id IN (SELECT p.inv_id FROM payments p WHERE i.id = p.inv_id))";
	$rows = $db->query($q, $user['id'])->numRows();
	$text = "This is a list of all of the payments that you have made to JSB.";

	// If admin, retrieve the correct data
	if(checkAdmin($user['usertype'])) {
		if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
			echo '<meta http-equiv="Refresh" content="0; url=main.php">';
			exit;
		}
		$text = "This is a list of all of the payments that the realtor has made to JSB.";
		$q = "SELECT o.id, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip FROM orders o ";
		$q .= "WHERE o.userid = ? AND o.id IN (SELECT i.orderid FROM invoices i WHERE i.id IN (SELECT p.inv_id FROM payments p WHERE i.id = p.inv_id))";
		$rows = $db->query($q, $db->escape($_GET['id']))->numRows();
	}
	$results = $db->fetchAll();
	
	
?>
<article id="main">
	<header>
		<h2>View Payment History</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
			<?php
				if ($rows > 0) {
			?>
				<p>
				<?php
					echo $text;
				?>
				</p>
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
							foreach($results as $arr) {
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
					if(!checkAdmin($user['usertype'])) {
						echo '<p>You have not made any payments yet!</p>';
					}
					else {
						echo '<p>This realtor has not made any payments yet.</p>';
					}
				}
			?>
			</section>
		</div>
	</section>
</article>
<?php
	include "includes/footer.php";
?>