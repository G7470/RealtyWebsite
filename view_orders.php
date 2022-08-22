<?php

	include "includes/innerheader.php";
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	
	$rows = $db->query("SELECT u.company, o.* FROM orders o JOIN users u ON o.userid = u.id ORDER BY o.id DESC")->numRows();
	$results = $db->fetchAll();
	
	
?>
<article id="main">
	<header>
		<h2>View Orders</h2>
		<p></p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
			<?php
				if ($rows > 0) {
			?>
				<p>This is a list of all of the orders that have been placed with JSB.</p>
				<input type="text" id="myInput" onkeyup="Search()" placeholder="Search for Company Name..">
				<div class="table-wrapper">
					<table>
						<thead>
							<tr>
								<th>Company Name</th>
								<th>Property Address</th>
								<th>Status</th>
								<th>View Details</th>
							</tr>
						</thead>
						<tbody>
						<?php
							foreach($results as $arr) {
								echo '
							<tr>
								<td>' . $arr['company'] . '</td>
								<td>' . $arr['prop_addr1'] . ', ';
								if(isset($arr['propr_addr2'])) {
									echo $arr['prop_addr2'] . ', ';
								}
								echo $arr['prop_city'] . ', ' . $arr['prop_state'] . ', ' . $arr['prop_zip'] . '</td>
								<td>' . translate_status($arr['status']) . '</td>
								<td><a href="order_details.php?view=' . $arr['id'] . '">Details</a></td>
							</tr>';
							}
							?>
						</tbody>
					</table>
				</div>
			<?php
				}
				else {
					echo '<p>You have not ordered yet! <a href="order.php">Place your order here</a></p>';
				}
			?>
			</section>
		</div>
	</section>
</article>
<script>
function Search() {
  // Declare variables
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("myInput");
  filter = input.value.toUpperCase();
  table = document.getElementById("myTable");
  tr = table.getElementsByTagName("tr");

  // Loop through all table rows, and hide those who don't match the search query
  for (i = 0; i < tr.length; i++) {
    td = tr[i].getElementsByTagName("td")[0];
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
</script>
<?php
	include "includes/footer.php";
?>