<!--content-->
<div class="contact_box">
	<div class="right_img">
    	<?php echo $this->Html->image($post['Post']['post_image'],array('alt'=>'')); ?>
    </div>
    <?php print_r($post); ?>
    <div class="left_text">
    	<?php echo $this->ShortLink->show($post['Post']['post_longdescription']);?>
    </div>
</div>
<div class="clear"></div>
