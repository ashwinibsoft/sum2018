<div class="wrapper home-boxes">
	<?php $home_blocks = $this->requestAction(array('plugin'=>'content_manager','controller'=>'pages','action'=>'home_block'));
		$i=1;
		$column_count = 3;
		foreach($home_blocks as $home_block){ ?>
			<div class="one-third <?php if($i%$column_count==0){ echo "last"; } ?>">
				<h2><?php echo $home_block['Page']['name']; ?></h2>
				<div class="content">
					<?php echo $home_block['Page']['page_shortdescription']; ?>
					<a href="<?php echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view',$home_block['Page']['id'])); ?>" class="btn">Read more</a>
				</div>
			</div><!--one third close-->
		<?php $i++; } ?>
</div><!--home boxes-->
<div class="clear"></div>
