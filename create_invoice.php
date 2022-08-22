<?php
	include "includes/innerheader.php";
	$redirect = "";
	$amt_err = "";
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}

	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=view_realtor.php">';
		exit;
	}
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		if(empty(trim($_POST['duedate']))) {
			$redirect = "Please specify a payment date";
		}
		else {
			$test_arr  = explode('-', $_POST['duedate']);
			if (count($test_arr) == 3) {
				if (!checkdate($test_arr[1], $test_arr[2], $test_arr[0])) {
					$redirect = "Invalid payment date: " . $_POST['duedate'];
				}
			} else {
				$redirect = "Invalid payment date (not count):" . $_POST['duedate'];
			}
		}
		$comments = $db->escape(trim($_POST['comments']));
		$_POST['amount'] = str_replace(",", "", $_POST['amount']);
		$_POST['amount'] = str_replace("$", "", $_POST['amount']);
		if(!is_numeric($_POST['amount'])) {
			$amt_err = "Please enter a number for the list price.";
		}
		if(empty($redirect) && empty($amt_err)) {
			$sql = "INSERT INTO invoices (orderid, comments, amount, createdate, duedate) VALUES (?, ?, ?, ?, ?)";
			$db->query($sql, array($db->escape($_GET['id']), $comments, $db->escape($_POST['amount']), time(), strtotime($db->escape($_POST['duedate']))));
			$redirect = "Invoice created! Going back to order...";
			echo '<meta http-equiv="Refresh" content="2; url=order_details.php?view=' . $_GET['id'] . '">';
		}
	}
	$num = $db->query("SELECT * FROM orders WHERE id = ?", $db->escape($_GET['id']))->numRows();
	if($num <> 1) {
		echo '<meta http-equiv="Refresh" content="0; url=view_realtor.php">';
		exit;
	}
	$ordarr = $db->fetchArray();
	
	
	
?>
<style>
.accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.accordion:after {
  content: '\002B';
  color: #777;
  font-weight: bold;
  float: right;
  margin-left: 5px;
}

.active:after {
  content: "\2212";
}

.panel {
  padding: 0 18px;
  background-color: white;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-out;
}
</style>
<article id="main">
		<header>
			<h2>Create Invoice</h2>
			<p>Make a New Invoice for Order</p>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<section>
					<button class="accordion">Order Details</button>
					<div class="panel">
						<div class="table-wrapper">
							<table>
								<tbody>
									<tr>
										<td>Order Number</td>
										<?php
										echo '<td>' . $ordarr['id'] . '</td>
									</tr>
									<tr>
										<td>Property Address</td>
										<td>' . $ordarr['prop_addr1'] . ', ';
										if(isset($ordarr['propr_addr2'])) {
											echo $ordarr['prop_addr2'] . ', ';
										}
										echo $ordarr['prop_city'] . ', ' . $ordarr['prop_state'] . ', ' . $ordarr['prop_zip'] . '</td>
									<tr>
										<td>Services Requested</td>
										<td>';
										$i = 1;
										foreach($services as $service) {
											if($i > 1) {
												echo ', ';
											}
											$servname = $db->query("SELECT * FROM services WHERE id = ?", $service['serviceid'])->fetchArray();
											echo $servname['serv_name'];
											$i++;
										}
									echo '</td>
									</tr>
									<tr>
										<td>Status</td>
										<td>' . translate_status($ordarr['status']) . '</td>
									
									</tr>
									<tr>
										<td>Comments</td>
										<td>' . $arr['comments'] . '</td>
									</tr>
									<tr>
										<td>Estimate Cost</td>
										<td>';
										echo ($ordarr['estimate'] == -1) ? 'No Estimate Yet' : '$' . number_format($ordarr['estimate']);
										echo '</td>
									<tr>
										<td>Invoice</td>
										<td>';
										if($inv_exist > 0) {
											echo '<a href="view_invoices.php?id=' . $ordarr['id'] . '">View Invoices</a>';
										}
										else {
											echo 'N/A';
										}
									echo '</td>
									</tr>';
									?>
									<tr>
										<td>Listing Information</td>
										<td>N/A<!--<a href="listing.php?id=<?php /* echo $ordarr['id']; */ ?>">View Listing</a>-->
										<!--<span id="listprice"><?php
										/*if($ordarr['list_price'] == 0) {
											echo 'N/A';
										}
										else {
											echo '$' . number_format($ordarr['list_price']);
										}*/
										?>
										</span><span id="updpricetext">(double-click price to update)</span>
										<button class="primary" id="updpricebut" style="display:none;" onclick="updateprice();">Update</button>--></td></tr>
								</tbody>
							</table>
						</div>
					</div>
					<span><font color="red"><?php echo $redirect; ?></font></span>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . '?id=' . $ordarr['id']; ?>">
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<input type="date" name="duedate" id="duedate" value="" placeholder="Invoice Due Date" required />
							</div>
							<span><font color="red"><?php echo $amt_err; ?></font></span>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="amount" id="amount" value="" placeholder="Invoice Amount" required />
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea name="comments" id="comments" placeholder="Comments" rows="6"></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Create Invoice" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				</section>
			</div>
		</section>
	</article>
<script type="text/javascript">
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    this.classList.toggle("active");
    var panel = this.nextElementSibling;
    if (panel.style.maxHeight) {
      panel.style.maxHeight = null;
    } else {
      panel.style.maxHeight = panel.scrollHeight + "px";
    } 
  });
}
</script>
<?php
	include "includes/footer.php";
?>