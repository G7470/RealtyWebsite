<?php
include "includes/innerheader.php";

if(!checkAdmin($user['usertype'])) {
	echo '<meta http-equiv="Refresh" content="0; url=main.php">';
	exit; 
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
	// Validate service name
	if(empty(trim($_POST['name']))) {
		$status_err = "Please enter service name.";
	}
	else {
		$serv_name = $db->escape(trim($_POST['name']));
	}
	$_POST['cost'] = str_replace(",", "", $_POST['cost']);
	$_POST['cost'] = $db->escape(str_replace("$", "", $_POST['cost']));
	if(!is_numeric($_POST['cost'])) {
		$cost_err = "Please enter a number for the cost.";
	}
	
	if(isset($_POST['active'])) {
		$active = 1;
	}
	else {
		$active = 0;
	}
	
	if(empty($status_err) && empty($cost_err)) {
		$db->query("INSERT INTO services (serv_name, cost, active) VALUES (?, ?, ?)", array($serv_name, $_POST['cost'], $active));
		$name_err = "Service added!";
	} 
}
$results = $db->query("SELECT * FROM services")->fetchAll();
	
	
?>
<article id="main">
	<header>
		<h2>Edit Services</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<p>This is a list of all of the services that you have.</p>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Service Name</th>
								<th>Status</th>
								<th>Cost</th>
								<th>Edit</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($results as $arr) {
								echo '
							<tr>
								<td>' . $arr['serv_name'] . '</td><td>'
								 . ($arr['active'] ? 'Active' : 'Inactive');
								echo '</td>
								<td>$' . number_format($arr['cost']) . '</td>
								<td><a href="edit_service.php?edit=' . $arr['id'] . '">Edit</a></td>
							</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</section>
			<section>
			<h3>Add New Service</h3>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $name_err; ?></font></span>
							<input type="text" name="name" id="name" placeholder="Service Name" />
						</div>
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $cost; ?></font></span>
							<input type="text" name="cost" id="cost" placeholder="Service Cost (Number Only)" />
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="checkbox" id="active" name="active">
							<label for="active">Active</a></label>
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Add Service" class="primary" /></li>
							</ul>
						</div>
					</div>
				</form>
			</section>
		</div>
	</section>
</article>
<?php
	include "includes/footer.php";
?>