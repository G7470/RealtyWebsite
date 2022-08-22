<?php
include "includes/innerheader.php";

$redirect = $ext_text = "";

// Processing data if code entered
if($_SERVER['REQUEST_METHOD'] == "POST"){
	if($_POST['code'] == $_SESSION['vcode']) {
		$result = $db->query("SELECT * FROM user_email_tmp WHERE userid = ? AND code = ?", $user['id'], $db->escape($_POST['code']))->fetchArray();
		if($result) {
			switch($result['type']) {
				case 1:
					if($db->query("UPDATE users SET email = ? WHERE id = ?", array($result['new_email'], $user['id']))) {
						unset($_SESSION['vcode']);
						$_SESSION['prevpage'] = "";
						$redirect = "Email updated! Directing you to main page...";
						echo '<meta http-equiv="Refresh" content="1; url=login.php">';
					}
					else {
						// something went wrong
					}
					break;
				case 2:
					unset($_SESSION['vcode']);
					$_SESSION['prevpage'] = "";
					echo '<meta http-equiv="Refresh" content="0; url=login.php">';
					break;
			}
		}
	}
	else {
		$redirect = "Code invalid.";
	}
}
else {
	if(isset($_SESSION['vcode'])) {
		// Send email with verification code
		$to = $user['email'];
		$subject = "JSB Enterprise Realty Services Verification Code";

		$message = "
		<html>
		<head>
		<title>JSB Enterprise Realty Services</title>
		</head>
		<body>
		<p>Your verification code for your JSB Enterprise Realty Services Account is: " . $_SESSION['vcode'] . ".</p>
		<p>If you have not requested this code, please contact JSB Enterprise Realty Services.</p>
		<p>Thank you,<br />
		JSB Enterprise Realty Services</p>
		</body>
		</html>
		";

		// Always set content-type when sending HTML email
		$headers = "MIME-Version: 1.0" . "\r\n";
		$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

		// More headers
		$headers .= 'From: <jsb@jsbenterpriserealtyservices.com>' . "\r\n";

		if(mail($to,$subject,$message,$headers)) {
			echo "Email Sent!";
		}
		else {
			if(!mail($to,$subject, "Hi")) {
				echo "Email Failed!";
			}
		}
	}
}
if(isset($_SESSION['prevpage']) && $_SESSION['prevpage'] == "Account Settings") {
	$ext_text = "update your account information";
}
else {
	$ext_text = "login to your account";
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
							<span>An email has been sent to your current email address on file with a code. Please confirm the code in the box below to <?php echo $ext_text; ?>:</span>
							<div class="col-8 col-12-xsmall">
								<input type="text" name="code" id="code" value="" placeholder="Verification Code" />
							</div>
							<div class="col-8 col-12-xsmall">
								<ul class="actions">
									<li><input type="submit" value="Verify" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
					<div class="row gtr-uniform">
						<span>Did not receive the code?</span>
					</div>
					<a href="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="button primary">Resend Verification Code</a>
				</section>
			</div>
		</section>
	</article>