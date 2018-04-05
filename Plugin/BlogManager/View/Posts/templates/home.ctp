<div class="container-box">
	<div class="left">    
		<h2><?=$page['Page']['name'];?></h2>
		<?php echo $this->ShortLink->show($page['Page']['page_shortdescription']);?>
		<p class="read-more">
		<a href="<?php echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view',$page['Page']['id'])); ?>">Read More <span></span></a>
		</p>
	</div>
	<div class="right">
		<!--Why choose us block-->
		<?php $home_blocks = $this->requestAction(array('plugin'=>'content_manager','controller'=>'pages','action'=>'home_block')); 
		
		foreach($home_blocks as $home_block){ ?>
			<?php if($home_block['Page']['id']==18){ ?>
			<h2><?php echo $home_block['Page']['name']; ?></h2>
				<?php echo $home_block['Page']['page_shortdescription']; ?>
			<?php } ?>				
			<?php
			}
		?>
	</div>
	<div class="clear"></div>
</div>
<!--Home block-->
<div class="three-boxes">
<?php  
foreach($home_blocks as $home_block){ ?>
		<?php if(($home_block['Page']['id']==19) || ($home_block['Page']['id']==20)){ ?>
		<div class="inner-box space">
			<div class="one-third">
				<div class="text-center">
					<h3><?php echo $home_block['Page']['name']; ?></h3>
				</div>
				<?php echo $home_block['Page']['page_shortdescription']; ?>
				<p class="read-more">
					<a href="<?php echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'view',$home_block['Page']['id'])); ?>">Read More <span></span></a>
				</p>
				<?php echo $this->Html->image('box-shadow.png',array('alt'=>'Box-Shadow')); ?>
			</div>
		</div>
		<?php } ?>				
<?php
}
?>
	<?php echo $this->element('ContentManager.homegallery'); ?>
	<div class="clear"></div>
</div>


