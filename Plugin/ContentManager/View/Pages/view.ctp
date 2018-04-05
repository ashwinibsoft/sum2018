<?php if($page['Page']['id']!=21){?>
<h2><?=$page['Page']['name'];?></h2>
<?php } ?>
<?php echo $this->ShortLink->show($page['Page']['page_longdescription']);?>

<?php if($galleries){ ?>
	<div class="fancy-row">
	<?php foreach($galleries as $gallery){ ?>		
	 
	<a class="fancybox" href="img/gallery/<?php echo $gallery['GalleryImage']['image']; ?>" data-fancybox-group="gallery">
		<div class="fancy-box01">
			<?php
			$imgArr = array('source_path'=>Configure::read('Path.Gallery'),'img_name'=>$gallery['GalleryImage']['image'],'width'=>Configure::read('image_front_list_width'),'height'=>Configure::read('image_front_list_height'),'noimg'=>Configure::read('Path.NoImage'));
				$resizedImg = $this->ImageResize->ResizeImage($imgArr);
				echo $this->Html->image($resizedImg,array('border'=>'0','class'=>'="thumb-nail01'));					
			
			 ?>
			
			<div class="fancy-hover"><img src="img/search-icon.png" alt="search-icon" /></div>
		</div>
	</a>
                    	

                        
<?php	}?>
<div class="clear"></div>
</div>
<?php }?>
	


