<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
	<h4>Feature <span class="semi-bold">Image</span>
	<span style="" class="tip" data-toggle="tooltip" title="Post Image For Header" data-placement="right"><i class="fa fa-question-circle"></i></span>
	</h4>
		<div class="tools"> 
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border">
		<div class="row">
			<div class="form-group">
					<div class="controls">
					<?php echo $this->Form->file('post_image',array('required'=> false, 'style'=>'border:0;')); ?>
					<?=$this->Form->error('post_image',null,array('wrap' => 'span', 'class' => 'error')); ?>
					<span style="color:red;"></span> <span class="help">(Only png, gif, jpg, jpeg images are allowed. Please upload <?php printf("%spx X %spx",Configure::read('post_image_width'),Configure::read('post_image_height')); ?> dimension of image for better resolution.)</span>
					<div id="post-image-showcase"  style="padding-top: 19px;overflow:hidden;">
					<?php 
						/* Resize Image */
						if(!empty($this->data['Post']['post_image']) && file_exists(Configure::read('Path.Post').$this->data['Post']['post_image'])) {
							$imgArr = array('source_path'=>Configure::read('Path.Post'),'img_name'=>$this->data['Post']['post_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'),'noimg'=>Configure::read('Path.NoImage'));
							$resizedImg = $this->ImageResize->ResizeImage($imgArr);
							echo $this->Html->image($resizedImg,array('border'=>'0','id'=>'banner-image-preview'));
						}
					?>
					<?php if(!empty($this->data['Post']['post_image']) && file_exists(Configure::read('Path.Post').$this->data['Post']['post_image'])) {?>
					<div class="clearfix"></div>
					<div style="float:right;">
						<?php if((int)$this->data['Post']['is_cropped']!=1){ ?>
						<a data-toggle="modal" data-id="post_crop image" class="tip link-color" data-original-title="Crop image"  href="#viewDetail"> Crop this image.?</a>
						| 
						<?php } ?>
						<a href="<?php echo Router::url(array('plugin'=>'blog_manager','controller'=>'posts','action'=>'delete_post_image',$post_id)); ?>" class="remove_post_ajax" target="_blank">Remove this image</a>
						<script type="text/javascript">
							$(document).ready(function(){
								$('.remove_post_ajax').bind('click',function(){
									var answer = confirm("Are you sure?");
										if (answer){
										//return true;
										}
										else{
										return false;
										}

									$.post( $(this).attr('href'), function( data ) {
										$('#post-image-showcase').html('');
										
									});
									return false;
									});
								
								});
						</script>
					</div>
					<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>



