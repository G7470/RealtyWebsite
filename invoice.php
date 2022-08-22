<?php
	include "includes/innerheader.php";
	
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
		echo '<meta http-equiv="Refresh" content="0; url=view_invoices.php">';
		exit;
	}
	$query = "SELECT i.*, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, SUM(NVL(p.amount, 0)) as payment FROM invoices i JOIN orders o ON i.orderid = o.id LEFT JOIN payments p ON p.inv_id = i.id WHERE o.userid = ? AND ";
	$rows = $db->query($query . "i.id = ?", array($user['id'], $db->escape($_GET['id'])))->numRows();
	if($rows <> 1 && !checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=view_invoices.php">';
		exit; 
	}
	// If admin, retrieve the correct data
	if(checkAdmin($user['usertype'])) {
		$query = "SELECT i.*, o.prop_addr1, o.prop_addr2, o.prop_city, o.prop_state, o.prop_zip, SUM(NVL(p.amount, 0)) as payment FROM invoices i JOIN orders o ON i.orderid = o.id LEFT JOIN payments p ON p.inv_id = i.id WHERE i.id = ?";
		$rows = $db->query($query, $db->escape($_GET['id']))->numRows();
		if($rows <> 1) {
			echo '<meta http-equiv="Refresh" content="0; url=view_invoices.php">';
			exit; 
		}
	}
	$invarr = $db->fetchArray();
	// Get Charges
	$getchargerows = $db->query("SELECT SUM(amount) AS charge_sum FROM charges WHERE inv_id = ?", $db->escape($_GET['id']))->numRows();
	$getcharges = $db->fetchArray();
	// Get Balance
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
		<h2>View Invoice</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
			<?php if(checkAdmin($user['usertype'])) {
			?>
				<a class="button primary" href="edit_invoice.php?id=<?php echo $invarr['id']; ?>">Update Invoice</a>
			<?php
			}
				if($invarr['status'] == 0) {
					echo '<p><font color="red">This invoice is due! View Make a Payment Below</font></p>';
				}
				?>
				<div class="col-8 col-12-xsmall">
					<h5>Invoice Details</h5>
				</div>
				<div class="table-wrapper">
					<table>
						<tbody>
							<tr>
								<td>View Printable Version</td>
								<td><a href="invoice_pdf.php?id=<?php echo $invarr['id']; ?>" target="_blank">Printable Version</a></td>
							</tr>
							<tr>
								<td>Order Number</td>
								<td id="ordid"><?php echo $invarr['orderid']; ?></td>
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
								<td>$<?php echo number_format($invarr['amount'], 2); ?></td>
							</tr>
							<tr>
								<td>Paid Amount</td>
								<td>$<?php echo number_format($invarr['payment'], 2); ?></td>
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
				<h4>Pay Invoice: </h4>
				<div id="smart-button-container">
    <div style="text-align: center"><input type="text" style="visibility: hidden; name="descriptionInput" id="description" maxlength="127" value="Invoice Payment"></div>
      <p id="descriptionError" style="visibility: hidden; color:red; text-align: center;">Please enter a description</p>
    <div  class="col-8 col-12-xsmall" style="text-align: center"><h5>Pay Amount: </h5><input name="amountInput" type="number" id="amount" value="<?php echo number_format($amount, 2); ?>" step=".01"><span> USD</span></div>
      <p id="priceLabelError" style="visibility: hidden; color:red; text-align: center;">Please enter a price</p>
    <!--<div id="invoiceidDiv" style="text-align: center; display: none;"><label for="invoiceid"> </label><input name="invoiceid" maxlength="127" type="text" id="invoiceid" value="<?php echo $invarr['id']; ?>" ></div>
      <p id="invoiceidError" style="visibility: hidden; color:red; text-align: center;">Please enter an Invoice ID</p>-->
    <div style="text-align: center; margin-top: 0.625rem;" id="paypal-button-container"></div>
  </div>
				<div class="col-8 col-12-xsmall">
				</div>
			</section>
			<section>
			<?php
				$q = "SELECT pmt_type AS amt_type, amount, pay_date AS data_date, CASE WHEN pmt_type = 1 THEN CONCAT('Email Address: ', email_address) WHEN pmt_type = 2 THEN CONCAT('Check Number: ', chk_num, ' - Routing: ', rt_num) ELSE ' ' END AS extra_info, 'Payment' AS data_type FROM payments WHERE inv_id = 1
					UNION
					SELECT ' ' AS amt_type, amount, charge_date AS data_date, charge_name AS extra_info, 'Charge' AS data_type FROM charges WHERE inv_id = 1
					ORDER BY data_date DESC;";
				$rows = $db->query($q, $_GET['id'])->numRows();
				$results = $db->fetchAll();
				if ($rows > 0) {
			?>
				<h2>Transaction History</h2>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Transaction Type</th>
								<th>Payment Type
								<th>Amount</th>
								<th>Details</th>
								<th>Transaction Date</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($results as $arr) {
								echo '
							<tr>
								<td>' . $arr['data_type'] . '</td>
								<td>' . translate_payment_status($arr['amt_type']) . '</td>
								<td>$' . number_format($arr['amount'], 2) . '</td>
								<td>' . $arr['extra_info'] . '</td>
								<td>'. getDateFromUNIX($arr['data_date']) . '</td>
							</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
				}
			?>
			</section>
		</div>
	</section>
		</div>
	</section>
</article>
<script src="https://www.paypal.com/sdk/js?client-id=Abvm81rYhCtjSxi4sM4r8TATfB3HsY8IF0IFyeoiZQwAgdj_7yuqEZmvEf22FR4teY_xXVYtf2ukI9e3&currency=USD" data-sdk-integration-source="button-factory"></script>
<script>
  function initPayPalButton() {
    var amount = document.querySelector('#smart-button-container #amount');
    var descriptionError = document.querySelector('#smart-button-container #descriptionError');
    var priceError = document.querySelector('#smart-button-container #priceLabelError');
    //var invoiceid = document.querySelector('#smart-button-container #invoiceid');
    //var invoiceidError = document.querySelector('#smart-button-container #invoiceidError');
    //var invoiceidDiv = document.querySelector('#smart-button-container #invoiceidDiv');

    var elArr = [description, amount];

    /*if (invoiceidDiv.firstChild.innerHTML.length > 1) {
      invoiceidDiv.style.display = "block";
    }*/

    var purchase_units = [];
    purchase_units[0] = {};
    purchase_units[0].amount = {};

    function validate(event) {
      return event.value.length > 0;
    }

    paypal.Buttons({
      style: {
        color: 'gold',
        shape: 'rect',
        label: 'paypal',
        layout: 'vertical',
        
      },

      onInit: function (data, actions) {
        actions.enable();

        /*if(invoiceidDiv.style.display === "block") {
          elArr.push(invoiceid);
        }*/

        elArr.forEach(function (item) {
          item.addEventListener('keyup', function (event) {
            var result = elArr.every(validate);
            if (result) {
              actions.enable();
            } else {
              actions.disable();
            }
          });
        });
      },

      onClick: function () {
        /*if (description.value.length < 1) {
          descriptionError.style.visibility = "visible";
        } else {
          descriptionError.style.visibility = "hidden";
        }*/

        if (amount.value.length < 1) {
          priceError.style.visibility = "visible";
        } else {
          priceError.style.visibility = "hidden";
        }

        /*if (invoiceid.value.length < 1 && invoiceidDiv.style.display === "block") {
          invoiceidError.style.visibility = "visible";
        } else {
          invoiceidError.style.visibility = "hidden";
        }*/

        purchase_units[0].description = description.value;
        purchase_units[0].amount.value = amount.value;

        /*if(invoiceid.value !== '') {
          purchase_units[0].invoice_id = invoiceid.value;
        }*/
      },

      createOrder: function (data, actions) {
        return actions.order.create({
          purchase_units: purchase_units,
        });
      },
	  onApprove: function(data, actions) {
      // This function captures the funds from the transaction.
		return actions.order.capture().then(function(details) {
			// This function shows a transaction success message to your buyer.
			alert('Transaction completed by ' + details.payer.name.given_name);
			// Call AJAX
			$.ajax({
				url: 'ajax_payment.php',
				type: 'post',
				dataType: 'text',
				data: {
					orderid: <?php echo $invarr['orderid']; ?> 
					, invid: <?php echo $invarr['id']; ?>
					, emailaddress: details.payer.email_address
					, payerid: details.payer.payer_id
					, payid: details.purchase_units[0].payments.captures[0].id
					, status: details.purchase_units[0].payments.captures[0].status
					, amount: details.purchase_units[0].payments.captures[0].amount.value
					, currency_code: details.purchase_units[0].payments.captures[0].amount.currency_code
					, transaction_time: details.purchase_units[0].payments.captures[0].update_time
				},
				success: function() {
					alert("Your payment has been posted. Refresh this page to reflect your payment.");
				},
				error: function() {
					alert("The payment has not been posted due to an error. The administrator has been notified and will contact you shortly about correcting this issue.");
				}
			});
			// End AJAX
        });
      }
	}).render('#paypal-button-container');
	};
initPayPalButton();
  //This function displays Smart Payment Buttons on your web page.
</script>
<script type="text/javascript">
/*$(function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	});
$('#listprice').bind('dblclick', function() {
        $(this).attr('contentEditable', true);
		$('#updpricebut').css({"display":"block"});
		$('#updpricetext').css({"display":"none"});
    });
document.querySelector("#file-upload").onchange = function(){
  document.querySelector("#file-name").textContent = this.files[0].name;
}
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
function updateprice() {
	var price = document.getElementById("listprice").innerHTML;
	alert("New value: " + price);
	$(this).attr('contentEditable', false);
	$('#updpricebut').css({"display":"none"});
	$('#updpricetext').css({"display":"block"});
	// Call AJAX
	// End Call AJAX
}*/
</script>
<?php
	include "includes/footer.php";
?>