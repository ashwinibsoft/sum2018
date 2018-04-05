<div class="banner">
    <div class="flexslider">
        <ul class="slides">
		<?php foreach($slider as $slide) {?>			
		<li >
			
			<?php 
				if(isset($slide['Slide']['image'])) {
					$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$slide['Slide']['image'],'width'=>Configure::read('slide_image_width'),'height'=>Configure::read('slide_image_height'),'noimg'=>Configure::read('Path.NoImage'));
					$resizedImg = $this->ImageResize->ResizeImage($imgArr);
					echo $this->Html->image($resizedImg);
					
				}
			?>
			<div class="banner-txt">
				<div class="wrapper">
					<div class="txt">
						<h3>Complete your tax return</h3>
						<h2>Online Today</h2>
						<a href="#" class="btn">Online Tax Return</a>
					</div>
				</div>
			</div><!--banner-txt close-->
		 </li>
	   <?php } ?>  
	</ul>
    </div><!--flexslider close-->
    
</div><!--banner close -->
