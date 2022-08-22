<?php

	include "includes/innerheader.php";
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	
	// Processing form data when form is submitted
	$adderror = "";
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(empty(trim($_POST['enddate']))) {
			$adderror = "Please specify an end date";
		}
		else {
			$test_arr  = explode('-', $_POST['enddate']);
			if (count($test_arr) == 3) {
				if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
					$adderror = "Invalid end date: " . $_POST['enddate'];
				}
			} else {
				$adderror = "Invalid end date (not count):" . $_POST['enddate'];
			}
		}
		if(empty(trim($_POST['startdate']))) {
			$adderror = "Please specify a start date";
		}
		else {
			$test_arr  = explode('-', $_POST['startdate']);
			if (count($test_arr) == 3) {
				if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
					$adderror = "Invalid start date: " . $_POST['startdate'];
				}
			} else {
				$adderror = "Invalid start date (not count):" . $_POST['startdate'];
			}
		}
		if(!isset($_POST['orderid']) || !is_numeric($_POST['orderid'])) {
			$adderror = "Invalid order id!";
		}
		if(empty($adderror)) {
			$getmaxsort = $db->query("SELECT MAX(sort) AS sortnum FROM promotion")->fetchArray();
			if(empty($getmaxsort['sortnum'])) {
				$getmaxsort['sortnum'] = 1;
			}
			else {
				$getmaxsort['sortnum']++;
			}
			$db->query("INSERT INTO promotion VALUES (?, ?, ?, ?)", $db->escape($_POST['orderid']), strtotime($db->escape($_POST['startdate'])), strtotime($db->escape($_POST['enddate'])), $getmaxsort['sortnum']);
			$adderror = "Promotion added!";
		}
	}
	
	
	$results = $db->query("SELECT l.*, o.*, NVL(p.sort, 9999) AS sorting FROM listing l JOIN orders o ON o.id = l.orderid LEFT JOIN promotion p ON p.orderid = l.orderid ORDER BY NVL(p.sort, 9999) ASC")->fetchAll();
	
	
?>
<article id="main">
	<header>
		<h2>View Listings</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<p>This is a list of all of the listings that are currently in JSB.</p>
				<span><font color="red"><?php echo $adderror; ?></font></span>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Property Address</th>
								<th>View/Edit Listing</th>
								<th>Promote</th>
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
									<td><a href="create_listing.php?id=' . $arr['orderid'] . '">View/Edit Listing</td>
									<td>';
									if($arr['sorting'] == 9999) {
										echo '<button onclick="viewform(' . $arr['id'] . ')" class="primary">Promote</button>';
									}
									else {
										echo 'Currently Promoted';
									}
								echo '</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</section>
			<section id="newpromotion" style="display:none;">
				<h3>Add New Promotion</h3>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?">
					<input type="hidden" value="" id="orderid" name="orderid" />
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<h5>Start Date</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="date" id="startdate" name="startdate" />
						</div>
						<div class="col-8 col-12-xsmall">
							<h5>End Date</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="date" id="enddate" name="enddate" />
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Add Promotion" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
			</section>
		</div>
	</section>
</article>
<script>
	function viewform(id) {
		getinput = document.getElementById("orderid");
		getinput.value = id;
		document.getElementById("newpromotion").style.display = "block";
	}
</script>
<?php
	include "includes/footer.php";
?>