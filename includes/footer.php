<!-- Footer -->
<footer id="footer">
	<ul class="icons">
		<li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
		<li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
		<li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
		<li><a href="#" class="icon brands fa-dribbble"><span class="label">Dribbble</span></a></li>
		<li><a href="#" class="icon solid fa-envelope"><span class="label">Email</span></a></li>
	</ul>
	<ul class="copyright">
		<li>&copy;<?php echo date("Y"); ?> G7470 Programming</li>
	</ul>
</footer>

</div>
<!-- Scripts -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/jquery.scrollex.min.js"></script>
<script src="assets/js/jquery.scrolly.min.js"></script>
<script src="assets/js/browser.min.js"></script>
<script src="assets/js/breakpoints.min.js"></script>
<script src="assets/js/util.js?v=<?php echo time();?>"></script>
<script src="assets/js/main.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script>
	document.getElementById("showopt").addEventListener("change", Views);
	
	function Views() {
		var dropdown = document.getElementById("showopt");
		var type = dropdown.options[dropdown.selectedIndex].value;
		var user = document.getElementsByClassName("user");
		var employee = document.getElementsByClassName("employee");
		
		if(type == "1") {
			for(var i = 0; i < employee.length; i++) {
				employee[i].style.display = "none";
			}
			for(var i = 0; i < user.length; i++) {
				user[i].style.display = "block";
			}
		}
		else if(type == "2") {
			for(var i = 0; i < user.length; i++) {
				user[i].style.display = "none";
			}
			for(var i = 0; i < employee.length; i++) {
				employee[i].style.display = "block";
			}
		}
		else {
		}
	}
</script>
</body>
</html>