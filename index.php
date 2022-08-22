<?php
	include "includes/header.php";
?>

				<!-- Banner -->
					<section id="banner">
						<div class="inner">
							<h2>JSB Enterprise</h2>
							<p></p>
						</div>
						<a href="#one" class="more scrolly">Learn More</a>
					</section>

				<!-- One -->
					<section id="one" class="wrapper style1 special">
						<div class="inner">
							<header class="major">
								<h2>Welcome to JSB</h2>
								<p>Welcome to JSB Enterprise, our service provided to Realtor's Sign and Post Installation and Removal along with Commercial needs. 
									JSB Enterprise, takes quality into consideration. Our Post are all repainted before each install order even comes in. We aim to making sure the jobs are always completed right the first time. <br/>
									Realtor's and Real Estate Agents inquiring service of JSB Enterprises, open communication, and reliable service. Please follow the instruction's, under submitting an inquiry, regarding your order for Sign's and Post Installation's, Removals, and Repairs.</p>
							</header>
							<ul class="icons major">
								<li><span class="icon solid fa-home major style3"><span class="label">Lorem</span></span></li>
								<li><span class="icon solid fa-sign major style3"><span class="label">Ipsum</span></span></li>
								<li><span class="icon solid fa-tools major style3"><span class="label">Dolor</span></span></li>
							</ul>
						</div>
					</section>
					<section id="carousel" class="wrapper style4">
						<div class="inner">
							<!-- Slideshow container -->
							<div class="slideshow-container">
								<?php
									$numpromotions = $db->query("SELECT o.*, l.* FROM listing l JOIN orders o ON o.id = l.orderid JOIN promotion p ON p.orderid = l.orderid WHERE ? BETWEEN p.date_start AND p.date_end ORDER BY p.sort ASC", time())->numRows();
									$promotions = $db->fetchAll();
									foreach($promotions as $promotion) {
										// Set Address
										$addr = $promotion['prop_addr1'] . ', ';
										if(isset($promotion['propr_addr2'])) {
											$addr .= $promotion['prop_addr2'] . ', ';
										}
										$addr .= $promotion['prop_city'] . ', ' . $promotion['prop_state'] . ', ' . $promotion['prop_zip'];
										
										// Get Picture
										$picture = $db->query("SELECT filename FROM order_pic WHERE orderid = ? AND sort = 1", $promotion['orderid'])->fetchArray();
										if(!isset($picture['filename'])) {
											$filepath = 'images/';
											$picture['filename'] = 'no_image.png';
										}
										else {
											$filepath = 'images/userimages/' . $promotion['userid'] . '_' . $promotion['orderid'] . '/';
										}
								?>
									  <!-- Full-width images with number and caption text -->
									  <div class="mySlides fade">
										<div class="numbertext"></div>
										<a href="view_home.php?id=<?php echo $promotion['orderid']; ?>"><img src="<?php echo $filepath . $picture['filename']; ?>" style="width:100%"></a>
										<div class="text"><span><?php echo ($l['show_addr'] == 0) ? $addr : ''; ?></span></div>
									  </div>
								<?php
									}
									if($numpromotions > 1) {
								?>
									  <!-- Next and previous buttons -->
									  <a class="prev" onclick="plusSlides(-1)">&#10094;</a>
									  <a class="next" onclick="plusSlides(1)">&#10095;</a>
								<?php
									}
								?>
							</div>
							<br>
							<div style="text-align:center">
							<?php
								$i = 1;
								foreach($promotions as $promotion) {
							?>
									<!-- The dots/circles -->
									<span class="dot" onclick="currentSlide(<?php echo $i; ?>)"></span>
							<?php
									$i++;
								}
							?>
						</div>
					</section>
				<!-- Two -->
					<section id="two" class="wrapper alt style2">
						<section class="spotlight">
							<div class="image"><img src="images/pic01.jpg" alt="" /></div><div class="content">
								<h2>JSB Enerprise (Philosophy)</h2>
								<p>At JSB Enterprise we believe in presentation. Presenting Quality Wooden 4x4 Post to the realtor, to stick out to the buyer's eyes. Why does a Post overrule yard sign's? Nothing stand out more for a realtor's professionalism, like having a Post placed over a small yard sign. Visibility!</p>
							</div>
						</section>
						<section class="spotlight">
							<div class="image"><img src="images/pic02.jpg" alt="" /></div><div class="content">
								<h2>Installation, Repair's, and Removals<br />
								elementum magna</h2>
								<p>JSB Enterprise, providing quality Sign Post, Quick Installations, Free Learning Repair's and much more. </p>
							</div>
						</section>
						<section class="spotlight">
							<div class="image"><img src="images/pic03.jpg" alt="" /></div><div class="content">
								<h2>Customization</h2>
								<p>JSB Enterprise brings option to the Real Estate Agents. Customization to orders can be made! We offer Post Color choices (Prices may change on Paint pricing), Hook options to hang your panel's, and much more. </p>
							</div>
						</section>
						<section class="spotlight">
							<div class="image"><img src="images/pic03.jpg" alt="" /></div><div class="content">
								<h2>24/7 Live Support</h2>
								<p>Contact JSB Enterprise with our Email - jsb.enterprise.llc2018@gmail.com
							All email received are meet answered with in 24hrs. Call's or Texts messages received will also be meet with the same 24hrs (Call/Texts back and emails). </p>
						</div>
						</section>
					</section>

				<!-- Three -->
					<section id="three" class="wrapper style3 special">
						<div class="inner">
							<header class="major">
								<p>Hello, my name is Jared Boudreau. Owner of JSB Enterprise. Started in 2019, New England Real Estate Agency service provider. Our goal and aim is moving your business forward. Your Business and Listing are our first priority.</p>
							</header>
						</div>
					</section>

				<!-- CTA -->
					<section id="cta" class="wrapper style4">
						<div class="inner">
							<header>
								<h2>Want to Work with JSB Enterprise?</h2>
								<p>Register to get started!</p>
							</header>
							<ul class="actions stacked">
								<li><a href="register.php" class="button fit primary">Register</a></li>
								<li><a href="login.php" class="button fit">Login</a></li>
							</ul>
						</div>
					</section>
<script>
var slideIndex = 1;
showSlides(slideIndex);

// Next/previous controls
function plusSlides(n) {
  showSlides(slideIndex += n);
}

// Thumbnail image controls
function currentSlide(n) {
  showSlides(slideIndex = n);
}

function showSlides(n) {
  var i;
  var slides = document.getElementsByClassName("mySlides");
  var dots = document.getElementsByClassName("dot");
  if (n > slides.length) {slideIndex = 1}
  if (n < 1) {slideIndex = slides.length}
  for (i = 0; i < slides.length; i++) {
      slides[i].style.display = "none";
  }
  for (i = 0; i < dots.length; i++) {
      dots[i].className = dots[i].className.replace(" active", "");
  }
  slides[slideIndex-1].style.display = "block";
  dots[slideIndex-1].className += " active";
}

</script>
<?php
	include "includes/footer.php";
?>