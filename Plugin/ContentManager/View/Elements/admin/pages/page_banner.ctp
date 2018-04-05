<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
	<h4 id="page_banner" style="cursor:pointer;">Banner <span class="semi-bold">Image</span>
	<span style="" class="tip" data-toggle="tooltip" title="Banner Image For Header" data-placement="right"><i class="fa fa-question-circle"></i></span>
	</h4>
		<div class="tools"> 
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border" id="page_bannerContent">
		<div class="row">
			<div class="form-group">
					<div class="controls">
					<?php echo $this->Form->file('banner_image',array('required'=> false, 'style'=>'border:0;')); ?>
					<?=$this->Form->error('banner_image',null,array('wrap' => 'span', 'class' => 'error')); ?>
					<span style="color:red;"></span> <span class="help">(Only png, gif, jpg, jpeg images are allowed. Please upload <?php printf("%spx X %spx",Configure::read('banner_image_width'),Configure::read('banner_image_height')); ?> dimension of image for better resolution.)</span>
					<div id="banner-image-showcase"  style="padding-top: 19px;overflow:hidden;">
					<?php 
						/* Resize Image */
						if(!empty($this->data['Page']['banner_image']) && file_exists(Configure::read('Path.Banner').$this->data['Page']['banner_image'])) {
							$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$this->data['Page']['banner_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'),'noimg'=>Configure::read('Path.NoImage'));
							$resizedImg = $this->ImageResize->ResizeImage($imgArr);
							echo $this->Html->image($resizedImg,array('border'=>'0','id'=>'banner-image-preview'));
						}?>
						
					<?php if(!empty($this->data['Page']['banner_image']) && file_exists(Configure::read('Path.Banner').$this->data['Page']['banner_image'])) {?>
					<div class="clearfix"></div>
					<div style="float:right;">
						<?php if((int)$this->data['Page']['is_cropped']!=1){ ?>
						<a data-toggle="modal" data-id="banner_crop image" class="tip link-color" data-original-title="Crop image"  href="#viewDetail"> Crop this image.?</a>
						| 
						<?php } ?>
						<a href="<?php echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'delete_banner_image',$page_id)); ?>" class="remove_banner_ajax" target="_blank">Remove this image</a>
						<script type="text/javascript">
							$(document).ready(function(){
								$('.remove_banner_ajax').bind('click',function(){
									var answer = confirm("Are you sure to remove this image.");
										if (answer){
										//return true;
										}
										else{
										return false;
										}

									$.post( $(this).attr('href'), function( data ) {
										$('#banner-image-showcase').html('');
										
									});
									return false;
									});
								
								});
						</script>
					</div>
					<?php } ?>
					</div>
					<?php if((int)Configure::read('Section.default_banner_image')){ ?>
					<div class="checkbox check-primary checkbox-circle">
						<?php echo $this->Form->checkbox('use_default_image', array('options'=>array(),'label' => false,'div'=>false,'empty'=>'Not Selected','style'=>'width:100%','hiddenField'=>true)); ?>
						<?php echo $this->Form->label('use_default_image', 'Use Default Banner Image'); ?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<!--
<script type="text/javascript">
						$(document).ready(function(event){
							$('.remove_banner_ajax').on('click',function(e){ 
								
								 if(confirm("Are you sure to remove this image....?")){
									return true;
								} else {
									e.preventDefault();
									return false;
										}
								
								
						//$.post( $(this).attr('href'), function( data ) {
									//$('#banner-image-showcase').html('');
									
						//});
						//return false;
					});
							
				});
	</script>
-->
