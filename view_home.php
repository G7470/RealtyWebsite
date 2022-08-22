<?php

require_once "includes/header.php";

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=index.php">';
		exit;
}

$rows = $db->query("SELECT l.*, o.* FROM listing l JOIN orders o ON o.id = l.orderid WHERE orderid = ?", $db->escape($_GET['id']))->numRows();
if($rows == 1) {
	$listingarr = $db->fetchArray();
}

$getpictures = $db->query("SELECT * FROM order_pic WHERE orderid = ?", $db->escape($_GET['id']))->fetchAll();

if($listingarr['show_addr'] == 0) {
	$addr = $listingarr['prop_addr1'] . ', ';
	if(isset($listingarr['propr_addr2'])) {
		$addr .= $listingarr['prop_addr2'] . ', ';
	}
	$addr .= $listingarr['prop_city'] . ', ' . $listingarr['prop_state'] . ', ' . $listingarr['prop_zip'];
}
else {
	$addr = "The Current Listing";
}

?>		
			
<!-- Pictures -->
<section id="carousel" class="wrapper style4">
	<div class="inner">
		<h2>Welcome to <?php echo $addr; ?></h2>
		<h4><?php echo ($listingarr['purch_type'] == 1) ? 'For Sale' : 'For Rent'; ?></h4>
		<!-- Slideshow container -->
		<div class="slideshow-container">
			  <!-- Full-width images with number and caption text -->
		<?php 
			$i = 0;
			foreach($getpictures as $picture) {
				$i++;
		?>
				<div class="mySlides fade">
					<div class="numbertext"></div>
					<img src="images/userimages/<?php echo $user['id'] . '_' . $listingarr['orderid'] . '/' . $picture['filename'] ?>" style="width:80%">
				</div>
		<?php
			}
			if($i > 1) {
		?>
				<!-- Next and previous buttons -->
				<a class="prev" onclick="plusSlides(-1)">&#10094;</a>
				<a class="next" onclick="plusSlides(1)">&#10095;</a>
		<?php
			}
		?>
		</div>
		<br>
		<!-- The dots/circles -->
		<div style="text-align:center">
		<?php
			$i = 0;
			foreach($getpictures as $picture) {
				$i++;
		?>
				<span class="dot" onclick="currentSlide(<?php echo $i; ?>)"></span>
		<?php
			}
		?>
		</div>
		<input type="text" id="emailaddr" name="emailaddr" placeholder="Email Address" />
		<button class="primary" style="margin-top: .5em;" onclick="contact(<?php echo $db->escape($_GET['id']); ?>)">I'm Interested</button>
	</div>
</section>
<!-- Description -->
<section id="description" class="wrapper style1 special">
	<div class="inner">
		<header class="major">
			<p><?php echo $listingarr['description']; ?></p>
		</header>
	</div>
</section>
<!-- Details -->
<section id="details" class="wrapper style5">
	<div class="inner">
		<div class="row gtr-uniform"> 
			<div class="col-8 col-12-xsmall">
				<h5>List Price</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p><?php echo number_format($listingarr['list_price'], 2); ?></p>
			</div>
			<div class="col-8 col-12-xsmall">
				<h5>Number of Bedrooms</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p><?php echo number_format($listingarr['bedrooms']); ?></p>
			</div>
			<div class="col-8 col-12-xsmall">
				<h5>Number of Bathrooms</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p><?php echo number_format($listingarr['bathrooms'], 1); ?></p>
			</div>
			<div class="col-8 col-12-xsmall">
				<h5>Square Feet</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p><?php echo number_format($listingarr['sq_feet']); ?></p>
			</div>
			<div class="col-8 col-12-xsmall">
				<h5>Lot Size (in acres)</h5>
			</div>				
			<div class="col-8 col-12-xsmall">
				<p><?php echo number_format($listingarr['lot_size'], 2); ?></p>
			</div>				
			<div class="col-8 col-12-xsmall">
				<h5>Property Type</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p><?php echo ($listingarr['prop_type'] == 1) ? 'Commercial' : 'Residential'; ?></p>
			</div>
			<?php if($listingarr['prop_type'] == 2) {
			?>
			<div class="col-8 col-12-xsmall">
				<h5> Residential Property Type</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p>
				<?php 
					switch($listingarr['res_prop_type']) {
						case 1:
							echo 'Single-Family Home';
							break;
						case 2:
							echo 'Condominium';
							break;
						case 3:
							echo 'Townhouse';
							break;
						case 4:
							echo 'Co-op';
							break;
						case 5:
							echo 'Multi-Family Home';
							break;
						case 6:
							echo 'Land';
							break;
					}
				?>
				</p>
			</div>
			<?php 
			}
			if($listingarr['prop_type'] == 1) {
			?>
			<div class="col-8 col-12-xsmall">
				<h5> Commercial Property Type</h5>
			</div>
			<div class="col-8 col-12-xsmall">
				<p>
				<?php 
					switch($listingarr['comm_prop_type']) {
						case 1:
							echo 'Office Space';
							break;
						case 2:
							echo 'Industrial Use';
							break;
						case 3:
							echo 'Multi-Family Rental';
							break;
						case 4:
							echo 'Retail';
							break;
					}
				?>
				</p>
			</div>
			<?php
			}
			?>
		</div>
	</div>
</section>
<script>
	function contact(orderid) {
		// Call AJAX
		var email = document.getElementById("emailaddr").value;
		$.ajax({
			url: 'ajax_contact.php',
			type: 'post',
			dataType: 'text',
			data: {id: orderid,
					emailddr: email 
				},
			success: function() {
				alert("Realtor has been contacted.");
			},
			error: function() {
				alert("Error contacting realtor! Check that you have your email address filled out and try again.");
			}
		});
		// End AJAX
	}
</script>
<?php
	include "includes/footer.php";
?>