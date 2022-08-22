<?php

	include "includes/innerheader.php";
	
	$rows = $db->query("SELECT * FROM orders WHERE userid = ? ORDER BY status ASC", $user['id'])->numRows();
	$results = $db->fetchAll();
	
	
?>
<article id="main">
	<header>
		<h2>View Orders</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
			<?php
				if ($rows > 0) {
			?>
				<p>This is a list of all of the orders that you have placed with JSB.</p>
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
							foreach($results as $arr) {
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
					echo '<p>You have not ordered yet! <a href="order.php">Place your order here</a></p>';
				}
			?>
			</section>
		</div>
	</section>
</article>
<?php
	include "includes/footer.php";
?>