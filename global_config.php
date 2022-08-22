<?php

include "includes/innerheader.php";
		
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	$globalalerts = getConfigs('globalalert');
	$internalalerts = getConfigs('internalalert');
	$latefeemonthly = getConfig('latefee_monthly');
	$latefeeyearly = getConfig('latefee_yearly');
	$adminemail = getConfig('admin_email');
	$companyname = getConfig('admin_company_name');
	$companyaddr1 = getConfig('admin_company_addr1');
	$companyaddr2 = getConfig('admin_company_addr2');
	$companycity = getConfig('admin_company_city');
	$companystate = getConfig('admin_company_state');
	$companyzip = getConfig('admin_company_zip');
	
	if($_SERVER['REQUEST_METHOD'] == "POST"){
		switch($_GET['val']) {
			case 'latefees':
				$fee_err = "";
				if(empty(trim($_POST['latefeemonthly']))) {
					$_POST['latefeemonthly'] = 0;
				}
				if(empty(trim($_POST['latefeeyearly']))) {
					$_POST['latefeeyearly'] = 0;
				}
				if($db->query("UPDATE settings SET config_value = ? WHERE config_key = 'latefee_monthly'", $db->escape($_POST['latefeemonthly']))) {
					if($db->query("UPDATE settings SET config_value = ? WHERE config_key = 'latefee_yearly'", $db->escape($_POST['latefeeyearly']))) {
						$fee_err = "Late fees have been updated!";
						$latefeemonthly = getConfig('latefee_monthly');
						$latefeeyearly = getConfig('latefee_yearly');
					}
					else {
						$fee_err = "There was a problem updating the late fees! Try again.";
					}
				}
				else {
					$fee_err = "There was a problem updating the late fees! Try again.";
				}
				break;
			case 'addalert':
				$status_err = "";
				if($_POST['alert_type'] <> 1 && $_POST['alert_type'] <> 2) {
					$alerttype_err = "Invalid alert type!";
				}
				else {
					switch($_POST['alert_type']) {
						case 1:
							$searchtext = 'globalalert';
							break;
						case 2:
							$searchtext = 'internalalert';
							break;
					}
				}
				$_POST['alert_text'] = $db->escape(trim($_POST['alert_text']));
				$config_key = $db->query("SELECT MAX(config_key) AS config_key FROM settings WHERE config_key LIKE ?", $searchtext . '%')->fetchArray()['config_key'];
				if(!empty($config_key)) {
					$config_key = str_replace($searchtext, '', $config_key);
					$config_key++;
				}
				else {
					$config_key = 1;
				}
				if($db->query("INSERT INTO settings VALUES (?, ?)", array($searchtext . $config_key, $_POST['alert_text']))) {
					$status_err = "Alert has been added!";
					$globalalerts = getConfigs('globalalert');
					$internalalerts = getConfigs('internalalert');
				}
				else {
					$status_err = "There was an error adding the new alert! Try again.";
				}
				break;
			case 'other':
				$gen_config_err = "";
				if(empty(trim($_POST['adminemail']))) {
					$gen_config_err = "Please enter the admin email address. ";
				}
				if(empty(trim($_POST['companyname']))) {
					$gen_config_err.= "Please enter the admin company name. ";
				}
				// Validate company address
				if(empty(trim($_POST['companyaddr1'])) || empty(trim($_POST['companycity'])) || empty(trim($_POST['companystate'])) || empty(trim($_POST['companyzip']))) {
					$gen_config_err .= "Please enter company address. ";
				}
				elseif(!is_numeric($_POST['companyzip'])) {
					$gen_config_err .= "Please enter valid zip code. ";
				}
				else {
					$compaddr1 = $db->escape(trim($_POST['companyaddr1']));
					$compaddr2 = $db->escape(trim($_POST['companyaddr2']));
					$compcity = $db->escape(trim($_POST['companycity']));
					$compstate = $db->escape(trim($_POST['companystate']));
					$compzip = $db->escape(trim($_POST['companyzip']));
				}
				if(empty($gen_config_err))  {
					$q = "UPDATE settings SET config_value = ? WHERE config_key = ?";
					$db->query($q, $db->escape(trim($_POST['adminemail'])), 'admin_email');
					$db->query($q, $db->escape(trim($_POST['companyname'])), 'admin_company_name');
					$db->query($q, $compaddr1, 'admin_company_addr1');
					$db->query($q, $compaddr2, 'admin_company_addr2');
					$db->query($q, $compcity, 'admin_company_city');
					$db->query($q, $compstate, 'admin_company_state');
					$db->query($q, $compzip, 'admin_company_zip');
					$gen_config_err = "Configuration Updated!";
					$adminemail = getConfig('admin_email');
					$companyname = getConfig('admin_company_name');
					$companyaddr1 = getConfig('admin_company_addr1');
					$companyaddr2 = getConfig('admin_company_addr2');
					$companycity = getConfig('admin_company_city');
					$companystate = getConfig('admin_company_state');
					$companyzip = getConfig('admin_company_zip');
				}
				break;
		}
	}			
	
?>
<article id="main">
	<header>
		<h2>Global Settings</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<!-- Active Alerts -->
			<section>
			<?php
				if($globalalerts <> 'Undefined' || $internalalerts <> 'Undefined') {
			?>
				<h3>Active Alerts</h3>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Alert Level</th>
								<th>Text</th>
								<th>Remove</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($globalalerts as $alert) {
								$alerttext = str_replace("\'", "'", $alert['config_value']);
								$alerttext = stripslashes($alert['config_value']);
								echo '
								<tr>
									<td>Entire Site</td>
									<td>' . $alerttext . '</td>
									<td><button onclick="deleteAlert(\'' . $alert['config_key'] . '\');">Delete</button></td>
								</tr>';
							}
							foreach($internalalerts as $alert) {
								echo '
							<tr>
								<td>Entire Site</td>
								<td>' . $alert['config_value'] . '</td>
								<td><button onclick="deleteAlert(\'' . $alert['config_key'] . '\');">Delete</button></td>
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
			<!-- Add new alert -->
			<section>
				<h3>Add New Alert</h3>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?val=addalert">
						<span><font color="red"><?php echo $status_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<span><font color="red"><?php echo $alerttype_err; ?></font></span>
								<select name="alert_type" id="alert_type">
									<option value="">- Alert Type -</option>
									<option value="1">Site-Wide</option>
									<option value="2">Internal</option>
								</select>
							</div>
							<div class="col-8 col-12-xsmall">
								<textarea id="alert_text" name="alert_text"></textarea>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Add Alert" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
			</section>
			<!-- Update Late Fees -->
			<section>
				<h3>Update Late Fees</h3>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?val=latefees">
						<span><font color="red"><?php echo $fee_err; ?></font></span>
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<h5>Monthly Late Fee Percentage (Number Only)</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="number" name="latefeemonthly" id="latefeemonthly" value="<?php echo $latefeemonthly; ?>" placeholder="Monthly Late Fee" />
							</div>
							<div class="col-8 col-12-xsmall">
								<h5>Maximum Late Fee Percentage Per Year (Number Only)</h5>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="number" name="latefeeyearly" id="latefeeyearly" value="<?php echo $latefeeyearly; ?>" placeholder="Yearly Late Fee" />
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Update Late Fees" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
			</section>
			<section>
				<h3>Other General Config</h3>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>?val=other">
					<span><font color="red"><?php echo $gen_config_err; ?></font></span>
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<h5>Admin Email Address</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="text" name="adminemail" id="adminemail" value="<?php echo $adminemail; ?>" placeholder="Admin Email" />
						</div>
						<div class="col-8 col-12-xsmall">
							<h5>Admin Company Name (shows on invoices)</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="text" name="companyname" id="companyname" value="<?php echo $companyname; ?>" placeholder="Admin Company Name" />
						</div>
						<div class="col-8 col-12-xsmall">
							<h5>Admin Company Address (shows on invoices)</h5>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="text" name="companyaddr1" id="companyaddr1" value="<?php echo $companyaddr1; ?>" placeholder="Admin Company Address 1" />
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="text" name="companyaddr2" id="companyaddr2" value="<?php echo $companyaddr2; ?>" placeholder="Admin Company Address 2" />
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="text" name="companycity" id="companycity" value="<?php echo $companycity; ?>" placeholder="Admin Company City" />
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="text" name="companystate" id="companystate" maxlength="2" value="<?php echo $companystate; ?>" placeholder="Admin Company State (Initials Only)" />
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="number" name="companyzip" id="companyzip" maxlength="5" value="<?php echo $companyzip; ?>" placeholder="Admin Company Zip Code (First 5 Digits Only)" />
						</div>
						
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Update Other Config" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
			</section>
		</div>
	</section>
</article>
<script>
	tinymce.init({
		selector: '#alert_text'
	});
	function deleteAlert(key) {
		var confirmation = confirm("Are you sure you want to delete this alert?");
		if(confirmation == true) {
			/* Call AJAX */
			$.ajax({
				url: 'ajax_delalert.php',
				type: 'post',
				dataType: 'text',
				data: {
					config_key: key
				},
				success: function() {
					alert("The alert has been deleted! Refresh to show current alert list.");
				},
				error: function() {
					alert("The alert could not be deleted!");
				}
			});
		}
	}
</script>
<?php
	include "includes/footer.php";
?>