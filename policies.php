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
		$pol_name = $db->escape(trim($_POST['name']));
	}
	
	if(isset($_POST['active'])) {
		$active = 1;
	}
	else {
		$active = 0;
	}
	if($_POST['pol_type'] == 0) {
		$pol_err = "Please select a policy type.";
	}
	
	$pol_text = $db->escape($_POST['text']);
	
	if(empty($status_err) && empty($pol_err)) {
		$db->query("INSERT INTO policies (name, category, active, text, lastupdate) VALUES (?, ?, ?, ?, ?)", array($pol_name, $db->escape($_POST['pol_type']), $active, $pol_text, time()));
		$name_err = "Policy added!";
	} 
}
$results = $db->query("SELECT * FROM policies")->fetchAll();
	
	
?>
<article id="main">
	<header>
		<h2>Edit Policies</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<p>This is a list of all of the policies that you have.</p>
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Policy Name</th>
								<th>Status</th>
								<th>Category</th>
								<th>Last Updated</th>
								<th>Edit</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($results as $arr) {
								echo '
							<tr>
								<td>' . $arr['name'] . '</td><td>'
								 . ($arr['active'] ? 'Active' : 'Inactive');
								echo '</td>
								<td>';
								switch($arr['category']) {
									case 1:
										echo 'General';
										break;
									case 2:
										echo 'Commercial';
										break;
									case 3:
										echo 'Residential';
										break;
								}
								echo '</td>
								<td>' . getDateFromUNIX($arr['lastupdate']) . '</td>
								<td><a href="edit_policy.php?edit=' . $arr['id'] . '">Edit</a></td>
							</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			</section>
			<section>
			<h3>Add New Policy</h3>
				<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
					<div class="row gtr-uniform">
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $name_err; ?></font></span>
							<input type="text" name="name" id="name" placeholder="Policy Name" />
						</div>
						<div class="col-8 col-12-xsmall">
							<span><font color="red"><?php echo $pol_err; ?></font></span>
							<select name="pol_type" id="pol_type">
								<option value="0">- Policy Type -</option>
								<option value="1">General</option>
								<option value="2">Commercial</option>
								<option value="3">Residential</option>
							</select>
						</div>
						<div class="col-8 col-12-xsmall">
							<input type="checkbox" id="active" name="active">
							<label for="active">Active</a></label>
						</div>
						<div class="col-8 col-12-xsmall">
							<textarea name="text" id="text" placeholder="Policy Text"></textarea>
						</div>
						<div class="col-8 col-12-xsmall">
							<ul class="actions">
								<li><input type="submit" value="Add Policy" class="primary" /></li>
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
    selector: '#text'
});
</script>
<?php
	include "includes/footer.php";
?>