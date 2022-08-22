<?php
include "includes/header.php";

// Define variables and initialize with empty values
$username = $password = $confirm_password = $email = $phone = $compname = "";
$compaddr1 = $compaddr2 = $compcity = $compstate = $compzip = $pref = $comptype = "";
$redirect = $license_state_err = "";

$username_err = $password_err = $confirm_password_err = $email_err = $phone_err = "";
$compname_err = $compaddr_err = $cap_err = $pref_err = $comptype_err = $policy_err = "";
 
// Processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){
 
    // Validate username
    if(empty(trim($_POST['username']))){
        $username_err = "Please enter a username.";
    } 
	else {
		$result = $db->query("SELECT id FROM users WHERE username = ?", $db->escape($_POST['username']));
		if($result) {
			$rows = $db->numRows();
			if($rows > 0) {
				$username_err = "This username is already taken.";
			}
			else {
				$username = $db->escape(trim($_POST['username']));
			}
        } 
		else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
    
    // Validate password
    if(empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";     
    } 
	elseif(strlen(trim($_POST['password'])) < 6){
        $password_err = "Password must have at least 6 characters.";
    } 
	else {
        $password = $db->escape(trim($_POST['password']));
    }
    
    // Validate confirm password
    if(empty(trim($_POST['confpassword']))) {
        $confirm_password_err = "Please confirm password.";     
    } 
	else {
        $confirm_password = $db->escape(trim($_POST['confpassword']));
        if(empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }
	
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
	
	// Validate State License
	for($i = 1; $i < 51; $i++) {
		if(isset($_POST['license_state' . $i])) {
			if($_POST['license_state' . $i] == "") {
				$license_state_err = "You must select the state that you are licensed.";
			}
			else {
				$license_state[$i] = $db->escape($_POST['license_state' . $i]);
			}
		}
		else {
			$i = 51;
		}
	}
	for($i = 1; $i < 51; $i++) {
		if(isset($_POST['license_number' . $i])) {
			$license_number[$i] = $db->escape(trim($_POST['license_number' . $i]));
		}
		else {
			$i = 51;
		}
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
		switch($comptype) {
			case 1:
				$comm_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 2")->fetchAll();
				$i = 1;
				foreach($comm_policies as $policy) {
					if(!(isset($_POST['comm_policy' . $i]))) {
						$policy_err = "You must agree to the commercial policy.";
					}
					$i++;
				}
				break;
			case 2:
				$res_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 3")->fetchAll();
				$i = 1;
				foreach($res_policies as $policy) {
					if(!(isset($_POST['res_policy' . $i]))) {
						$policy_err = "You must agree to the residential policy.";
					}
					$i++;
				}
				break;
			default:
				$comm_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 2")->fetchAll();
				$i = 1;
				foreach($comm_policies as $policy) {
					if(!(isset($_POST['comm_policy' . $i]))) {
						$policy_err = "You must agree to the commercial policy.";
					}
					$i++;
				}
				$res_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 3")->fetchAll();
				$i = 1;
				foreach($res_policies as $policy) {
					if(!(isset($_POST['res_policy' . $i]))) {
						$policy_err = "You must agree to the residential policy.";
					}
					$i++;
				}
				break;
		}
	}

    // Check input errors before inserting in database
   if(empty($username_err) && empty($password_err) && empty($confirm_password_err) && empty($email_err)
		&& empty($phone_err) && empty($compname_err) && empty($compaddr_err) && empty($cap_err)
		&& empty($pref_err) && empty($comptype_err) && empty($policy_err) && empty($license_state_err))
	{
		// Creates a password hash
        $hash_password = password_hash($password, PASSWORD_DEFAULT);
		
        // Attempt to execute SQL
		$sql = "INSERT INTO users (username, password, email_addr, phone_num, company, company_addr1, company_addr2
				, company_city, company_state, company_zip, company_type, contact_pref, referral_name, policy_agree) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		
        if($db->query($sql, array($username, $hash_password, $email, $phone, $compname, $compaddr1, $compaddr2, $compcity
				, $compstate, $compzip, $comptype, $pref, $referral_name, time()))) {
			$userid = $db->lastInsertID();
			// Enter License Information
			for($i = 1; $i <= count($license_state); $i++) {
				$sql = "INSERT INTO user_licenses (userid, license_state, license_number) VALUES (?, ?, ?)";
				$db->query($sql, array($userid, $license_state[$i], $license_number[$i]));
			}
            // Redirect to login page
			$redirect = "Thank you for registering with JSB Enterprise! Directing you to login page...";
            echo '<meta http-equiv="Refresh" content="2; url=login.php">';
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
?>
<style>
option {
	color: #000 !important;
}
</style>

				<!-- Main -->
					<article id="main">
						<section class="wrapper style5">
							<div class="inner">
								<h2>Register</h2>
								<p>Register to JSB Enterprise</p>
								<span><font color="red"><?php echo $redirect; ?></font></span>
								<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
									<div class="row gtr-uniform">
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $username_err; ?></font></span>
											<input type="text" name="username" id="username" value="<?php echo $username; ?>" placeholder="Username" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<table id="LicenseTable">
												<thead>
													<tr>
														<th>License State</th>
														<th>License Number</th>
													</tr>
												</thead>
												<tbody>
													<tr>
														<td><span><font color="red"><?php echo $license_state_err; ?></font></span>
														<select name="license_state1" id="license_state1">
															<option value="">- Select State -</option>
															<option value="AL">Alabama</option>
															<option value="AK">Alaska</option>
															<option value="AZ">Arizona</option>
															<option value="AR">Arkansas</option>
															<option value="CA">California</option>
															<option value="CO">Colorado</option>
															<option value="CT">Connecticut</option>
															<option value="DE">Delaware</option>
															<option value="DC">District Of Columbia</option>
															<option value="FL">Florida</option>
															<option value="GA">Georgia</option>
															<option value="HI">Hawaii</option>
															<option value="ID">Idaho</option>
															<option value="IL">Illinois</option>
															<option value="IN">Indiana</option>
															<option value="IA">Iowa</option>
															<option value="KS">Kansas</option>
															<option value="KY">Kentucky</option>
															<option value="LA">Louisiana</option>
															<option value="ME">Maine</option>
															<option value="MD">Maryland</option>
															<option value="MA">Massachusetts</option>
															<option value="MI">Michigan</option>
															<option value="MN">Minnesota</option>
															<option value="MS">Mississippi</option>
															<option value="MO">Missouri</option>
															<option value="MT">Montana</option>
															<option value="NE">Nebraska</option>
															<option value="NV">Nevada</option>
															<option value="NH">New Hampshire</option>
															<option value="NJ">New Jersey</option>
															<option value="NM">New Mexico</option>
															<option value="NY">New York</option>
															<option value="NC">North Carolina</option>
															<option value="ND">North Dakota</option>
															<option value="OH">Ohio</option>
															<option value="OK">Oklahoma</option>
															<option value="OR">Oregon</option>
															<option value="PA">Pennsylvania</option>
															<option value="RI">Rhode Island</option>
															<option value="SC">South Carolina</option>
															<option value="SD">South Dakota</option>
															<option value="TN">Tennessee</option>
															<option value="TX">Texas</option>
															<option value="UT">Utah</option>
															<option value="VT">Vermont</option>
															<option value="VA">Virginia</option>
															<option value="WA">Washington</option>
															<option value="WV">West Virginia</option>
															<option value="WI">Wisconsin</option>
															<option value="WY">Wyoming</option>
														</select></td>
														<td><span><font color="red"><?php echo $license_number_err; ?></font></span>
															<input type="text" name="license_number1" id="license_number1" value="<?php echo $license_number[1]; ?>" placeholder="License Number" required />
														</td>
														<td>
															<input type="button" class="add-row" value="Add New Row">
														</td>
													</tr>
												</tbody>
											</table>
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $password_err; ?></font></span>
											<input type="password" name="password" id="password" value="<?php echo $password; ?>" placeholder="Password" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $confirm_password_err; ?></font></span>
											<input type="password" name="confpassword" id="confpassword" value="<?php echo $confirm_password; ?>" placeholder="Confirm Password" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $email_err; ?></font></span>
											<input type="email" name="email" id="email" value="<?php echo $email; ?>" placeholder="Email Address" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $phone_err; ?></font></span>
											<input type="text" name="phone" id="phone" value="<?php echo $phone; ?>" placeholder="Phone Number (Ex. 567-890-1234)" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $compname_err; ?></font></span>
											<input type="text" name="comp_name" id="comp_name" value="<?php echo $compname; ?>" placeholder="Company Name" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $compaddr_err; ?></font></span>
											<input type="text" name="comp_addr1" id="comp_addr1" value="<?php echo $compaddr1; ?>" placeholder="Company Street Address" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<input type="text" name="comp_addr2" id="comp_addr2" value="<?php echo $compaddr2; ?>" placeholder="Company Apt/Suite" />
										</div>
										<div class="col-8 col-12-xsmall">
											<input type="text" name="comp_city" id="comp_city" value="<?php echo $compcity; ?>" placeholder="Company City" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<input type="text" name="comp_state" id="comp_state" value="<?php echo $compstate; ?>" placeholder="Company State" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<input type="text" name="comp_zip" id="comp_zip" value="<?php echo $compzip; ?>" placeholder="Company Zip Code" required />
										</div>
										<div class="col-8 col-12-xsmall">
											<img src="captcha.php" alt="CAPTCHA" class="captcha-image"><i class="fas fa-redo refresh-captcha"></i><br />
											<span><font color="red"><?php echo $cap_err; ?></font></span>
											<input type="text" id="captcha" name="captcha" placeholder="Captcha Text" pattern="[A-Z]{6}">
										</div>
										<?php
										$gen_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 1")->fetchAll();
										$i = 1;
										foreach($gen_policies as $policy) {
										?>
											<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $policy_err; ?></font></span>
											<input type="checkbox" id="gen_policy<?php echo $i; ?>" name="gen_policy<?php echo $i; ?>">
											<label for="gen_policy<?php echo $i; ?>">I agree to the <a target="_blank" href="view_policy.php?id=<?php echo $policy['id']; ?>"><?php echo $policy['name']; ?></a></label>
										</div>
										<?php
											$i++;
										}
										?>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $pref_err; ?></font></span>
											<select name="contact_pref" id="contact_pref">
												<option value="">- Contact Preference -</option>
												<option value="1">Email</option>
												<option value="2">Phone</option>
											</select>
										</div>
										<div class="col-8 col-12-xsmall">
											<span><font color="red"><?php echo $comptype_err; ?></font></span>
											<select name="comp_type" id="comp_type">
												<option value="">- Company Type -</option>
												<option value="1">Commercial</option>
												<option value="2">Residential</option>
												<option value="3">Commercial/Residential</option>
											</select>
										</div>
										<?php
										$comm_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 2")->fetchAll();
										$i = 1;
										foreach($comm_policies as $policy) {
										?>
										<div class="col-8 col-12-xsmall" id="divcomm" style="display:none;">
												<span><font color="red"><?php echo $policy_err; ?></font></span>
												<input type="checkbox" id="comm_policy<?php echo $i; ?>" name="comm_policy<?php echo $i; ?>">
												<label for="comm_policy<?php echo $i; ?>">I agree to the <a target="_blank" href="view_policy.php?id=<?php echo $policy['id']; ?>"><?php echo $policy['name']; ?></a></label>
										</div>
										<?php
											$i++;
										}
										$res_policies = $db->query("SELECT * FROM policies WHERE active = 1 AND category = 3")->fetchAll();
										$i = 1;
										foreach($res_policies as $policy) {
											
										?>
										<div class="col-8 col-12-xsmall" id="divres" style="display:none;">
												<span><font color="red"><?php echo $policy_err; ?></font></span>
												<input type="checkbox" id="res_policy<?php echo $i; ?>" name="res_policy<?php echo $i; ?>">
												<label for="res_policy<?php echo $i; ?>">I agree to the <a target="_blank" href="view_policy.php?id=<?php echo $policy['id']; ?>"><?php echo $policy['name']; ?></a></label>
										</div>
										<?php
											$i++;
										}
										?>
										<div class="col-8 col-12-xsmall">
											<input type="text" name="referral" id="referral" value="" placeholder="Referred By" />
										</div>
										<div class="col-8 col-12-xsmall">
											<ul class="actions">
												<li><input type="submit" value="Register" class="primary" /></li>
											</ul>
										</div>
									</div>
								</form>
							</div>
						</section>
					</article>

<script>
$(document).ready(function(){
	$("#LicenseTable").on('click','.add-row',function(){
		var table = document.getElementById("LicenseTable");
		var numrows = $('#LicenseTable tr').length;
		var appenddata = '<tr><td><span><font color="red"><?php echo $license_state_err; ?></font></span><select name="license_state' + $('#LicenseTable tr').length + '"';
		appenddata = appenddata + ' id="license_state' + $('#LicenseTable tr').length + '"><option value="">- Realtor State Licensed -</option>';
		appenddata = appenddata + '<option value="AL">Alabama</option>';
		appenddata = appenddata + '<option value="AK">Alaska</option>';
		appenddata = appenddata + '<option value="AZ">Arizona</option>';
		appenddata = appenddata + '<option value="AR">Arkansas</option>';
		appenddata = appenddata + '<option value="CA">California</option>';
		appenddata = appenddata + '<option value="CO">Colorado</option>';
		appenddata = appenddata + '<option value="CT">Connecticut</option>';
		appenddata = appenddata + '<option value="DE">Delaware</option>';
		appenddata = appenddata + '<option value="DC">District Of Columbia</option>';
		appenddata = appenddata + '<option value="FL">Florida</option>';
		appenddata = appenddata + '<option value="GA">Georgia</option>';
		appenddata = appenddata + '<option value="HI">Hawaii</option>';
		appenddata = appenddata + '<option value="ID">Idaho</option>';
		appenddata = appenddata + '<option value="IL">Illinois</option>';
		appenddata = appenddata + '<option value="IN">Indiana</option>';
		appenddata = appenddata + '<option value="IA">Iowa</option>';
		appenddata = appenddata + '<option value="KS">Kansas</option>';
		appenddata = appenddata + '<option value="KY">Kentucky</option>';
		appenddata = appenddata + '<option value="LA">Louisiana</option>';
		appenddata = appenddata + '<option value="ME">Maine</option>';
		appenddata = appenddata + '<option value="MD">Maryland</option>';
		appenddata = appenddata + '<option value="MA">Massachusetts</option>';
		appenddata = appenddata + '<option value="MI">Michigan</option>';
		appenddata = appenddata + '<option value="MN">Minnesota</option>';
		appenddata = appenddata + '<option value="MS">Mississippi</option>';
		appenddata = appenddata + '<option value="MO">Missouri</option>';
		appenddata = appenddata + '<option value="MT">Montana</option>';
		appenddata = appenddata + '<option value="NE">Nebraska</option>';
		appenddata = appenddata + '<option value="NV">Nevada</option>';
		appenddata = appenddata + '<option value="NH">New Hampshire</option>';
		appenddata = appenddata + '<option value="NJ">New Jersey</option>';
		appenddata = appenddata + '<option value="NM">New Mexico</option>';
		appenddata = appenddata + '<option value="NY">New York</option>';
		appenddata = appenddata + '<option value="NC">North Carolina</option>';
		appenddata = appenddata + '<option value="ND">North Dakota</option>';
		appenddata = appenddata + '<option value="OH">Ohio</option>';
		appenddata = appenddata + '<option value="OK">Oklahoma</option>';
		appenddata = appenddata + '<option value="OR">Oregon</option>';
		appenddata = appenddata + '<option value="PA">Pennsylvania</option>';
		appenddata = appenddata + '<option value="RI">Rhode Island</option>';
		appenddata = appenddata + '<option value="SC">South Carolina</option>';
		appenddata = appenddata + '<option value="SD">South Dakota</option>';
		appenddata = appenddata + '<option value="TN">Tennessee</option>';
		appenddata = appenddata + '<option value="TX">Texas</option>';
		appenddata = appenddata + '<option value="UT">Utah</option>';
		appenddata = appenddata + '<option value="VT">Vermont</option>';
		appenddata = appenddata + '<option value="VA">Virginia</option>';
		appenddata = appenddata + '<option value="WA">Washington</option>';
		appenddata = appenddata + '<option value="WV">West Virginia</option>';
		appenddata = appenddata + '<option value="WI">Wisconsin</option>';
		appenddata = appenddata + '<option value="WY">Wyoming</option>';
		appenddata = appenddata + '</select></td>';
		appenddata = appenddata + '<td><span><font color="red"><?php echo $license_number_err; ?></font></span>';
		appenddata = appenddata + '<input type="text" name="license_number' + $('#LicenseTable tr').length + '" id="license_number' + $('#LicenseTable tr').length + '" value="<?php echo $license_number[' + numrows + ']; ?>" placeholder="Realtor License Number" required />';
		appenddata = appenddata + '</td>';
		appenddata = appenddata + '<td>';
		appenddata = appenddata + '<input type="button" class="add-row" value="Add New Row">';
		appenddata = appenddata + '<input type="button" class="delete-row" value="Delete Row">';
		appenddata = appenddata + '</td>';
		appenddata = appenddata + '</tr>';
		$("#LicenseTable").append(appenddata);
	});
    $("#LicenseTable").on('click','.delete-row',function(){
        $(this).parent().parent().remove();
    });
});
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