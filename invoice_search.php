<?php
	include "includes/innerheader.php";
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	
	$q = "SELECT inv.id, inv.orderid, u.company, inv.status, inv.duedate FROM invoices inv JOIN orders o ON o.id = inv.orderid JOIN users u ON o.userid = u.id";
	$q .= " ORDER BY inv.status ASC, inv.duedate ASC";
	$invoices = $db->query($q)->fetchAll();
?>
<article id="main">
	<header>
		<h2>View Invoices</h2>
		<p>Search for an invoice</p>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
				<input type="text" id="myInput" onkeyup="Search()" placeholder="Search for Company Name..">
<div class="table-wrapper">

	<table id="myTable">
		<thead>
			<tr>
				<th>Order ID</th>
				<th>Company Name</th>
				<th>Due Date</th>
				<th>Status</th>
				<th>Details</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($invoices as $inv) {
				echo '<tr>
					<td>' . $inv['orderid'] . '</td>
					<td>' . $inv['company'] . '</td>
					<td>' . getDateFromUNIX($inv['duedate']) . '</td>
					<td>';
					if($inv['status'] == 0) {
						echo 'Not Paid';
					}
					else {
						echo 'Paid';
					}
					echo '</td><td><a href="invoice.php?id=' . $inv['id'] . '">Details</a></td>
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
    td = tr[i].getElementsByTagName("td")[1];
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