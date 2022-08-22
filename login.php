<?php
include "includes/header.php";
	
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true){
	// Redirect user to main page
    echo '<meta http-equiv="Refresh" content="0; url=main.php">';
	exit;
}
//Define variables and initialize with empty values
$username = $password = "";
$error = "";
 
// Processing form data when form is submitted
if($_SERVER['REQUEST_METHOD'] == "POST"){
 
    // Check if username is empty
    if(empty(trim($_POST['username']))){
        $error = "Your username/password credentials are incorrect.";
    } else{
        $username = $db->escape(trim($_POST['username']));
    }
    
    // Check if password is empty
    if(empty(trim($_POST['password']))){
        $error = "Your username/password credentials are incorrect.";
    } else{
        $password = trim($_POST['password']);
    }
    
    // Validate credentials
    if(empty($error)){
		$result = $db->query("SELECT * FROM users WHERE username = ?", $db->escape($_POST['username']))->fetchArray();
		if($result) {
			if(password_verify($password, $result['password'])){
				// Password is correct, so start a new session
				session_start();
							   
				// Store data in session variables
				$_SESSION['loggedin'] = true;
				$_SESSION['id'] = $id;
				$_SESSION['username'] = $username;                            
                
				if($result['2_fact_auth'] == 1) {
					$_SESSION['vcode'] = mt_rand(100000, 999999);
					// Redirect user to verification page
					echo '<meta http-equiv="Refresh" content="0; url=ver_conf.php">';
				}
				else {
					// Redirect user to main page
					echo '<meta http-equiv="Refresh" content="0; url=main.php">';
				}
            } 
			else {
				// Display an error message if password is not valid
				$error = "Your username/password credentials are incorrect.";
            }
		}
		else {
			$error = "Your username/password credentials are incorrect.";
		}
	}
}
?>

				<!-- Main -->
					<article id="main">
						<section class="wrapper style5">
							<div class="inner">
								<h2>Login</h2>
								<p>Login to JSB Enterprise</p>
								<p><font color="red"><?php echo $error; ?></font></p>
								<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
									<div class="row gtr-uniform">
										<div class="col-4 col-8-xsmall">
											<input type="text" name="username" id="username" value="" placeholder="Username" />
										</div>
										<div class="col-4 col-8-xsmall">
											<input type="password" name="password" id="password" value="" placeholder="Password" />
										</div>
										<div class="col-4 col-8-xsmall">
											<ul class="actions">
												<li><input type="submit" value="Login" class="primary" /></li>
											</ul>
										</div>
									</div>
								</form>
							</div>
							<br />
							<div class="inner">
								<div class="row gtr-uniform">
									<div class="col-3 col-8-xsmall">
										<p>New to JSB Enterprise?</p>
									</div>
									<div class="col-3 col-8-xsmall">
										<ul class="actions">
											<li><a href="register.php" class="button fit primary">Register</a></li>
										</ul>
									</div>
								</div>
						</section>
					</article>

<?php
include "includes/footer.php";
?>