<?php
include "includes/innerheader.php";

// Define variables and initialize with empty values
$redirect = "";

// Processing data if code entered
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if(empty(trim($_POST['confpassword']))) {
		$redirect = "Please confirm your new password";
	}
	if(empty(trim($_POST['newpassword']))) {
		$redirect = "Please enter your new password";
	}
	if(empty(trim($_POST['oldpassword']))) {
		$redirect = "Please enter your current password";
	}
	if(empty($redirect)) {
		if(password_verify(trim($_POST['oldpassword']), $user['password'])) {
			
			// Create a new password hash
			$hash_password = password_hash($db->escape(trim($_POST['newpassword'])), PASSWORD_DEFAULT);
			
			if($db->query("UPDATE users SET password = ? WHERE id = ?", array($hash_password, $user['id']))) {
				$redirect = "Password has been changed!";
			}
			else {
				$redirect = "Something went wrong. Please try again.";
			}
		}
		else {
			$redirect = "The current password that you entered is incorrect.";
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
								<a href="acct_settings.php">Back to Account Settings</a>
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="password" name="oldpassword" id="oldpassword" value="" placeholder="Current Password" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="password" name="newpassword" id="newpassword" value="" placeholder="New Password" />
							</div>
							<div class="col-8 col-12-xsmall">
								<input type="password" name="confpassword" id="confpassword" value="" placeholder="Confirm New Password" oninput="check(this);"/>
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Update Password" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				</section>
			</div>
		</section>
	</article>
<script language='javascript' type='text/javascript'>
    function check(input) {
        if (input.value != document.getElementById('newpassword').value) {
            input.setCustomValidity('Passwords Must be Matching.');
        } else {
            // input is valid -- reset the error message
            input.setCustomValidity('');
        }
    }
</script>
<?php
	include "includes/footer.php";
?>