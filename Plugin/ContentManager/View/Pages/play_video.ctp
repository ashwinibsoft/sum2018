<section class="welcome">
	<div class="wrapper">
		<div class="heading">
			<h1><span><?php echo "Video"; ?></span></h1>
			<div class="title-line"></div>
		</div>
		<div class="div_center">

		<?php   

		$ext = explode('.',$video);
		//$first_value = reset($ext); // First Element's Value

		?>
		<video width="600px" controls>
		  <source src="<?php echo '/Video/'.$video ?>" type="video/mp4">
		  Your browser does not support HTML5 video.
		</video>
		

</div>
<div class="clear"></div>
</section>
