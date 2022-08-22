<?php
	include "includes/innerheader.php";
?>
<article id="main">
		<header>
			<h2>Welcome <?php echo $user['username']; ?> to your dashboard!</h2>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<?php
					include "alerts.php";
					if(!checkAdmin($user['usertype'])) {
				?>
				<section>
					<div class="col-8 col-12-xsmall">
						<a href="order.php">Place Order</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="order_history.php">View Orders</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="view_invoices.php">View/Pay Invoice</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="acct_settings.php">Account Settings</a>
					</div>
					<!--<div class="col-8 col-12-xsmall">
						<a href="contact.php">Contact JSB</a>
					</div>-->
				</section>
				<?php
					}
				else {
				?>
				<section>
					<div class="col-8 col-12-xsmall">
						<a href="promote.php">Promotion Panel</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="order_details.php?view=1">View Newest Order</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="invoice.php?id=1">View Newest Invoice</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="view_realtor.php">View All Realtors</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="view_orders.php">View Orders</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="invoice_search.php">Find an Invoice</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="policies.php">Update Policies</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="services.php">Update Services</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="cron_config.php">Email Notification Configuration</a>
					</div>
					<div class="col-8 col-12-xsmall">
						<a href="global_config.php">Alert and General Configuration</a>
					</div>
			
					
				</section>
				<?php
				}
				?>
			</div>
		</section>
	</article>
<?php
	include "includes/footer.php";
?>