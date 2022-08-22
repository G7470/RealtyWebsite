<?php
	include "includes/innerheader.php";
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	
	$q = "SELECT u.id, u.company, count(o.id) as count FROM users u LEFT JOIN orders o ON u.id = o.userid WHERE u.id = ? GROUP BY u.id, u.company";
	$q2 = "SELECT MAX(om.timesent) as sent FROM order_messages om WHERE om.user_from = ?";
	$q3 = "SELECT o.userid, SUM(inv.amount) - SUM(NVL(p.amount, 0)) as payamt FROM invoices inv JOIN orders o ON inv.orderid = o.id LEFT JOIN payments p ON p.inv_id = inv.id ORDER BY SUM(inv.amount) - SUM(NVL(p.amount, 0)) DESC";
	$q4 = "SELECT u.* FROM users u WHERE u.id NOT IN (SELECT o.userid FROM invoices inv JOIN orders o ON inv.orderid = o.id LEFT JOIN payments p ON p.inv_id = inv.id)";
	
	
	$realtors = $db->query($q3)->fetchAll();
	$othrealtors = $db->query($q4)->fetchAll();
?>
<article id="main">
	<header>
		<h2>View Realtors</h2>
		<p>Search for a realtor</p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
			<input type="text" id="myInput" onkeyup="Search()" placeholder="Search for Company Name..">
<div class="table-wrapper">

	<table id="myTable">
		<thead>
			<tr>
				<th>Company Name</th>
				<th>Number of Orders</th>
				<th>Total Due</th>
				<th>Last Response Date</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($realtors as $realtor) {
				$arr = $db->query($q, $realtor['userid'])->fetchArray();
				echo '<tr>
					<td>' . $arr['company'] . '</td>
					<td>' . $arr['count'] . '</td>';
				echo '<td>$' . number_format($realtor['payamt']) . '</td>';
				$arr = $db->query($q2, $realtor['userid'])->fetchArray();
				if(isset($arr['sent'])) {
					echo '<td>' . getDateFromUNIX($arr['sent']) . '</td>';
				}
				else {
					echo '<td>None</td>';
				}
				echo '<td><a href="realtor.php?id=' . $realtor['userid'] . '">Details</a></td>
					</tr>';
			}
			foreach($othrealtors as $realtor) {
				$arr = $db->query($q, $realtor['id'])->fetchArray();
				echo '<tr>
					<td>' . $arr['company'] . '</td>
					<td>' . $arr['count'] . '</td>';
				echo '<td>$0</td>';
				$arr = $db->query($q2, $realtor['id'])->fetchArray();
				if(isset($arr['sent'])) {
					echo '<td>' . getDateFromUNIX($arr['sent']) . '</td>';
				}
				else {
					echo '<td>None</td>';
				}
				echo '<td><a href="realtor.php?id=' . $realtor['id'] . '">Details</a></td>
					</tr>';
			}
			
		?>
	</table>
</div>
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