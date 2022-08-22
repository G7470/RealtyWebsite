<?php
	include "includes/innerheader.php";
	$promotes = $db->query("SELECT * FROM promotion p JOIN orders o ON p.orderid = o.id ORDER BY p.sort ASC")->fetchAll();
?>
<article id="main">
		<header>
			<h2>Promote Panel</h2>
			<p>Control the listings that appear on the front page</p>
		</header>
		<section class="wrapper style5">
			<div class="inner">
				<section>
					<table>
							<tr>
								<th></th>
								<th>Property Address</th>
								<th>Show Dates</th>
								<th>Actions</th>
							</tr>
							<tbody id="sortable">
						<?php
							foreach($promotes as $promote) {
								$addr = $promote['prop_addr1'] . ', ';
								if(isset($promote['propr_addr2'])) {
									$addr .= $promote['prop_addr2'] . ', ';
								}
								$addr .= $promote['prop_city'] . ', ' . $promote['prop_state'] . ', ' . $promote['prop_zip'];
								echo '
									<tr id="' . $promote['orderid'] . '">
										<td><span class="ui-icon ui-icon-arrowthick-2-n-s"></span></td>
										<td>' . $addr . '</td>
										<td>' . getDateFromUNIX($promote['date_start']) . '-' . getDateFromUNIX($promote['date_end']) . '</td>
										<td><a href="edit_promotion.php?id=' . $promote['orderid'] . '">Edit Promotion</a>
									</tr>';
							}
						?>
							</tbody>
					</table>
					<a class="button" href="view_listings.php">Add New Listing to Promotion</a><br />
					<button class="primary" id="updorder" onclick="updateorder();">Change Promote Order</button>
				</section>
			</div>
	</section>
</article>
<script type="text/javascript">
init();
var mouseMoved = false;
function touchHandler(event) {
    // Declare the default mouse event.
    var mouseEvent = "mousedown";
    // Create the event to transmit.
        var simulatedEvent = document.createEvent("MouseEvent");

        switch (event.type) {
        case "touchstart":
            mouseEvent = "mousedown";
            break;
        case "touchmove":
            /*
            * If this has been hit, then it's a move and a mouseup, not a click
            * will be transmitted.
            */
            mouseMoved = true;
            mouseEvent = "mousemove";
            break;
        case "touchend":
            /*
            * Check to see if a touchmove event has been fired. If it has
            * it means this have been a move and not a click, if not
            * transmit a mouse click event.
            */
            if (!mouseMoved) {
                mouseEvent = "click";
            } else {
                mouseEvent = "mouseup";
            }
            // Initialize the mouseMove flag back to false.
            mouseMoved = false;
            break;
        }

        var touch = event.changedTouches[0];

        /*
         * Build the simulated mouse event to fire on touch events.
         */
        simulatedEvent.initMouseEvent(mouseEvent, true, true, window, 1,
        touch.screenX, touch.screenY,
        touch.clientX, touch.clientY, false,
        false, false, false, 0, null);

        /*
         * Transmit the simulated event to the target. This, in combination
         * with the case statement above, ensures that click events are still
         * transmitted and bubbled up the chain.
         */
        touch.target.dispatchEvent(simulatedEvent);

        /*
         * Prevent default dragging of element.
         */
        event.preventDefault();
    }

function init() {
    document.getElementById('sortable').addEventListener("touchstart", touchHandler, true);
    document.getElementById('sortable').addEventListener("touchmove", touchHandler, true);
    document.getElementById('sortable').addEventListener("touchend", touchHandler, true);
    document.getElementById('sortable').addEventListener("touchcancel", touchHandler, true);
}
$(function() {
    $( "#sortable" ).sortable();
    $( "#sortable" ).disableSelection();
	});
function updateorder() {
	let rowsort = [' '];
	var table = document.getElementById("sortable");
	var rowlength = table.rows.length;
	for(i = 0; i < rowlength; i++) {
		if(i == 0) {
			var nothing = rowsort.pop();
			rowsort.push(table.rows[i].id);
		}
	}
	/* AJAX */
	$.ajax({
			url: 'ajax_sort_promotion.php',
			type: 'post',
			dataType: 'text',
			data: {list: rowsort},
			success: function() {
				alert("Promotions have been re-sorted!");
			},
			error: function() {
				alert("Promotions have not been re-sorted! Refresh and try again.");
			}
		});
		// End AJAX
	
}
</script>
<?php
	include "includes/footer.php";
?>