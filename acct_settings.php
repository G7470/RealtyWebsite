<?php
include "includes/innerheader.php";
	
$_SESSION['prevpage'] = "Account Settings";
	
// Define variables and initialize with default values
$email = $user['email_addr'];
$phone = $user['phone_num'];
$compname = $user['company'];
$compaddr1 = $user['company_addr1'];
$compaddr2 = $user['company_addr2'];
$compcity = $user['company_city'];
$compstate = $user['company_state'];
$compzip = $user['company_zip'];
$pref = $user['contact_pref'];
$comptype = $user['company_type'];
$policytime = $user['policy_agree'];

$email_err = $phone_err = $compname_err = $compaddr_err = $cap_err = $pref_err = $comptype_err = $policy_err = "";
 
// Processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){
	
	// Validate email address
	if(empty(trim($_POST['email']))) {
		$email_err = "Please enter email address.";
	}
	else {
		if(!(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL))) {
			$email_err = "Invalid email address.";
		}
		else {
			$email = $db->escape(trim($_POST['email']));
		}
	}
	// Validate phone number
	if(empty(trim($_POST['phone']))) {
		$phone_err = "Please enter phone number.";
	}
	else {
		$phone = $db->escape(str_replace("-", "", trim($_POST['phone'])));
	}
	
	// Validate company name
	if(empty(trim($_POST['comp_name']))) {
		$compname_err = "Please enter company name.";
	}
	else {
		$compname = $db->escape(trim($_POST['comp_name']));
	}
	
	// Validate company address
	if(empty(trim($_POST['comp_addr1'])) || empty(trim($_POST['comp_city'])) || empty(trim($_POST['comp_state'])) || empty(trim($_POST['comp_zip']))) {
		$compaddr_err = "Please enter company address.";
	}
	elseif(!is_numeric($_POST['comp_zip'])) {
		$compaddr_err = "Please enter valid zip code.";
	}
	else {
		$compaddr1 = $db->escape(trim($_POST['comp_addr1']));
		$compaddr2 = $db->escape(trim($_POST['comp_addr2']));
		$compcity = $db->escape(trim($_POST['comp_city']));
		$compstate = $db->escape(trim($_POST['comp_state']));
		$compzip = $db->escape(trim($_POST['comp_zip']));
	}
	
	// Validate captcha
	if($_POST['captcha'] <> $_SESSION['captcha_text']) {
		$cap_err = "Captcha is incorrect.";
	}
	
	// Validate contact preference
	if($_POST['contact_pref'] <> 1 && $_POST['contact_pref'] <> 2) {
		$pref_err = "Please select contact preference.";
	}
	else {
		$pref = $db->escape($_POST['contact_pref']);
	}
	
	// Validate company type
	if($_POST['comp_type'] <> 1 && $_POST['comp_type'] <> 2 && $_POST['comp_type'] <> 3) {
		$comptype_err = "Please select company type.";
	}
	else {
		$comptype = $db->escape($_POST['comp_type']);
	}
	
	// Validate policy approval
	if(empty($comptype_err)) {
		if($comptype <> $user['company_type']) {
			switch($comptype) {
				case 1:
					if(!(isset($_POST['comm_policy']))) {
						$policy_err = "You must agree to the commercial policy.";
					}
					break;
				case 2:
					if(!(isset($_POST['res_policy']))) {
						$policy_err = "You must agree to the residential policy.";
					}
					break;
				default:
					if(!(isset($_POST['comm_policy']))) {
						$policy_err = "You must agree to the commercial policy.";
					}
					if(!(isset($_POST['res_policy']))) {
						$policy_err = "You must agree to the residential policy.";
					}
					if(!(isset($_POST['comm_policy'])) && !(isset($_POST['res_policy']))) {
						$policy_err = "You must agree to the residential and commercial policies.";
					}
					break;
			}
			if(empty($policy_err)) {
				$policytime = time();
			}
		}
	}

    // Check input errors before inserting in database
   if(empty($email_err)
		&& empty($phone_err) && empty($compname_err) && empty($compaddr_err) && empty($cap_err)
		&& empty($pref_err) && empty($comptype_err) && empty($policy_err))
	{
        // Attempt to execute SQL
		$sql = "UPDATE users SET phone_num = ?, company = ?, company_addr1 = ?, company_addr2 = ?, company_city = ?, company_state = ?, company_zip = ?
			, company_type = ?, contact_pref = ?, 2_fact_auth = ?, policy_agree = ? WHERE id = ?";
		
        if($db->query($sql, array($phone, $compname, $compaddr1, $compaddr2, $compcity
				, $compstate, $compzip, $comptype, $pref, $db->escape($_POST['2factor']), $policytime))) {
			if($email <> $user['email_addr']) {
				$_SESSION['vcode'] = mt_rand(100000, 999999);
				if($db->query("INSERT INTO user_email_tmp VALUES (?, ?, ?, ?)", array($user['id'], $user['email'], $email, $_SESSION['vcode']))) {
					echo '<meta http-equiv="Refresh" content="0; url=ver_conf.php">';
				}
				else {
					echo "Something went wrong updating your email address. Please try again later.";
				}
			}
			else {
				$redirect = "Account information updated! <a href='main.php'>Go back to main page</a>";
			}
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}	
?>
<article id="main">
		<header>
			<h2>Account Settings</h2>
			<p>Update your account information</p>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<section>
					<span><font color="red"><?php echo $redirect; ?></font></span>
					<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
						<div class="row gtr-uniform">
							<div class="col-8 col-12-xsmall">
								<a href="changepassword.php">Change Password</a>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="checkbox" id="2factor" name="2factor">
								<label for="2factor">2 Factor Authentication</label>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email Address" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" placeholder="Phone Number" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="comp_name" id="comp_name" value="<?php echo $compname; ?>" placeholder="Company Name" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="comp_addr1" id="comp_addr1" value="<?php echo $compaddr1; ?>" placeholder="Company Street Address" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="comp_addr2" id="comp_addr2" value="<?php echo $compaddr2; ?>" placeholder="Company Apt/Suite" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="comp_city" id="comp_city" value="<?php echo $compcity; ?>" placeholder="Company City" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="comp_state" id="comp_state" value="<?php echo $compstate; ?>" placeholder="Company State" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="comp_zip" id="comp_zip" value="<?php echo $compzip; ?>" placeholder="Company Zip Code" />
							</div>
							<div class="col-8 col-12-xsmall">
								<img src="captcha.php" alt="CAPTCHA" class="captcha-image"><i class="fas fa-redo refresh-captcha"></i><br />
								<input type="text" id="captcha" name="captcha" placeholder="Captcha" pattern="[A-Z]{6}">
							</div>
							<div class="col-8 col-12-xsmall">
								<select name="contact_pref" id="contact_pref">
									<option value="">- Contact Preference -</option>
									<option value="1" <?php if($pref == 1) { echo "selected"; } ?>>Email</option>
									<option value="2" <?php if($pref == 2) { echo "selected"; } ?>>Phone</option>
								</select>
							</div>
							<div class="col-8 col-12-xsmall">
								<select name="comp_type" id="comp_type">
									<option value="">- Company Type -</option>
									<option value="1" <?php if($pref == 1) { echo "selected"; } ?>>Commercial</option>
									<option value="2" <?php if($pref == 2) { echo "selected"; } ?>>Residential</option>
									<option value="3" <?php if($pref == 3) { echo "selected"; } ?>>Commercial/Residential</option>
								</select>
							</div>
							<div class="col-8 col-12-xsmall" id="divcomm" style="<?php echo ($comptype == 1 || $comptype == 3) ? "display:block;" : "display:none"; ?>">
								<input type="checkbox" id="comm_policy" name="comm_policy" <?php if($user['company_type'] == 1 || $user['company_type'] == 3) { echo "checked"; } ?>>
								<label for="comm_policy">I agree to the <a href="#">Commercial Policy</a></label>
							</div>
							<div class="col-8 col-12-xsmall" id="divres" style=<?php echo ($comptype == 2 || $comptype == 3) ? "display:block;" : "display:none"; ?>>
								<input type="checkbox" id="res_policy" name="res_policy" <?php if($user['company_type'] == 2 || $user['company_type'] == 3) { echo "checked"; } ?>>
								<label for="res_policy">I agree to the <a href="#">Residential Policy</a></label>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Update" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				</section>
			</div>
		</section>
	</article>
<script>
	document.getElementById("comp_type").addEventListener("change", Policies);
	
	var refreshButton = document.querySelector(".refresh-captcha");
	refreshButton.onclick = function() {
		document.querySelector(".captcha-image").src = 'captcha.php?' + Date.now();
	}
	function Policies() {
		var dropdown = document.getElementById("comp_type");
		var type = dropdown.options[dropdown.selectedIndex].value;
		var comm = document.getElementById("divcomm");
		var res = document.getElementById("divres");
		if(type == "1") {
			comm.style.display = "block";
			res.style.display = "none";
		}
		else if(type == "2") {
			comm.style.display = "none";
			res.style.display = "block";
		}
		else if(type == "3") {
			comm.style.display = "block";
			res.style.display = "block";
		}
		else {
			comm.style.display = "none";
			res.style.display = "none";
		}
	}
	
</script>
<?php
	include "includes/footer.php";
?>