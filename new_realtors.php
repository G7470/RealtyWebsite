<?php
	include "includes/innerheader.php";
	
	if(!checkAdmin($user['usertype'])) {
		echo '<meta http-equiv="Refresh" content="0; url=main.php">';
		exit; 
	}
	
	$q = "SELECT u.id, u.company FROM users u WHERE u.new = 1";	
	$numrealtors = $db->query($q)->numRows();
	$realtors = $db->fetchAll();
?>
<style>
.accordion {
  background-color: #eee;
  color: #444;
  cursor: pointer;
  width: 100%;
  border: none;
  text-align: left;
  outline: none;
  font-size: 15px;
  transition: 0.4s;
}

.active, .accordion:hover {
  background-color: #ccc;
}

.accordion:after {
  content: '\002B';
  color: #777;
  font-weight: bold;
  float: right;
  margin-left: 5px;
}

.active:after {
  content: "\2212";
}

.panel {
  padding: 0 18px;
  background-color: white;
  max-height: 0;
  overflow: hidden;
  transition: max-height 0.2s ease-out;
}
</style>
<article id="main">
	<header>
		<h2>View New Realtors</h2>
	</header>
	<section class="wrapper style5">
		<div class="inner">
			<section>
<div class="table-wrapper">
<?php
	if($numrealtors > 0) {
?>
	<table id="myTable">
		<thead>
			<tr>
				<th>Company Name</th>
				<th>View Information</th>
				<th>Viewed?</th>
			</tr>
		</thead>
		<tbody>
		<?php
			foreach($realtors as $realtor) {
				echo '<tr>
					<td>' . $realtor['company'] . '</td>';
				echo '<td><a href="realtor.php?id=' . $realtor['id'] . '">View Other Information</a></td>
					<td><input type="button" id="viewed' . $realtor['id'] . '" value="Mark as Viewed" onclick="Checked(' . $realtor['id'] . ');" /></td></tr>';
			}
		?>
	</table>
<?php
	}
	else {
		echo '<p>There are no new realtors for you to view!</p>';
	}
?>
</div>
			</section>
		</div>
	</section>
</article>
<script type="text/javascript">
function Checked(realtorID) {
	// Call AJAX
	$.ajax({
		url: 'ajax_realtor_viewed.php',
		type: 'post',
		dataType: 'text',
		data: {id: realtorID},
		success: function() {
			alert("Realtor has been marked as viewed!");
		},
		error: function() {
			alert("Error marking realtor as viewed.");
		}
	});
	// End AJAX
} 

</script>
<?php
	include "includes/footer.php";
?>