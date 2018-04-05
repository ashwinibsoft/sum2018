<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('BlogCategorie',array('name'=>'blog_categories','id'=>'BlogCategorie','action'=>'add' ,'onsubmit'=>'//return validatefields();','type'=>'file','novalidate'=>true))?>
<?php echo $this->Form->input('id');?>
<?php  echo $this->Form->hidden('form',array('value'=>'cat_add')); ?>
<?php  echo $this->Form->hidden('url_back_redirect',array('value'=>$referer_url)); ?>
<div class="row">
	<div class="col-md-8">
		<div class="grid simple horizontal green no-margin-grid">
			<div style="" class="grid-title no-border ">
			<h4>Basic <span class="semi-bold">Information</span></h4>
			<div class="tools"> <a href="javascript:;" class="collapse"></a></div>
		</div>
			<div class="grid-body no-border">
				<div class="form-group">
				<label class="form-label">Category Name</label>
				<span style="color:red;">*</span>
				<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for Category name." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?=$this->Form->text('cat_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
						<?=$this->Form->error('cat_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
					</div>
				</div>
				
				<div class="form-group">
				<label class="form-label">Parent Category</label>
				<span style="" class="tip" data-toggle="tooltip" title="This field is used for event parent category." data-placement="right"><i class="fa fa-question-circle"></i></span>
				
				<div class="controls">
					<?php echo $this->BlogCategorie->category_select_mutlilevel('cat_parent',array('options'=>array(),'label' => false,'div'=>false,'empty'=>'Select Parent','class'=>'simple-dropdown','style'=>'width:100%'),$cat_list,empty($this->request->data['BlogCategorie']['cat_parent'])?'':(int)$this->request->data['BlogCategorie']['cat_parent']);?> 				  
				</div>
			</div>
				<div class="form-group">
				<label class="form-label">Category Description</label>
				<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for category description." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?=$this->Form->textarea('cat_description',array('class'=>'form-control','required'=>false));?>
						<?=$this->Form->error('cat_description',null,array('wrap' => 'span', 'class' => 'error')); ?>
						
					</div>
				</div>
			</div> 
		</div> 
	</div>
	<div class="col-md-4">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border" >
				<h4>Category <span class="semi-bold">Image</span>
				<span style="" class="tip" data-toggle="tooltip" title="This field is used for category image." data-placement="right"><i class="fa fa-question-circle"></i></span>
				
                </h4>
				<div class="tools"> 
					<a class="collapse" href="javascript:;"></a>
				</div>
                
			</div>
		<div class="grid-body no-border">
			<div class="row" style="padding-top: 10px;">
				 	<div class="form-group">
						<div class="controls">
				<?php echo $this->Form->file('cat_image',array('required'=> false, 'style'=>'border:0;')); ?>
					<span style="color:red;"></span> <span class="help">(Only png, gif, jpg, jpeg images are allowed. Please upload <?php printf("%spx X %spx",Configure::read('cat_image_width'),Configure::read('cat_image_height')); ?> dimension of image for better resolution.)</span>
					
					<div id="banner-image-showcase"  style="padding-top: 19px;overflow:hidden;">
								<?php 
								/* Resize Image */
								if(isset($this->data['BlogCategorie']['cat_image'])) {
									$imgArr = array('source_path'=>Configure::read('Path.PostCategory'),'img_name'=>$this->data['BlogCategorie']['cat_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'),'noimg'=>Configure::read('Path.Noimage'));
									$resizedImg = $this->ImageResize->ResizeImage($imgArr);
									echo $this->Html->image($resizedImg,array('border'=>'0','id'=>'banner-image-preview'));
									
								}
								?>
								<?php if(!empty($this->data['BlogCategorie']['cat_image'])) {?>
								
								<div style="float:right;">
									
									<a data-toggle="modal" data-id="banner_crop image" class="tip link-color" data-original-title="Crop image"  href="#viewDetail">Crop this image.?
									</a>
									
								</div>
								<?php } ?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $this->element('admin/forms/form_submit',array('referer_url'=>$referer_url)); ?>
<?php echo $this->Form->end();?>
<?php if(!empty($this->data['BlogCategorie']['id'])){?>
<?php echo $this->element('admin/crop',array('form_action'=>Router::url(array('plugin'=>'blog_manager','controller'=>'blog_categories','action'=>'admin_default_image_crop',$this->data['BlogCategorie']['id'])),
								'image'=>$this->webroot.'img/postcategory/'.$this->data['BlogCategorie']['cat_image'],
								'width'=>Configure::read('cat_image_width'),
								'height'=>Configure::read('cat_image_height'),
								'id'=>'viewDetail')); ?>
<?php }?>
<?php echo $this->element('admin/forms/form_validation_js',array('form_id'=>'BlogCategorie','form_validation_url'=>Router::url(array('plugin'=>'blog_manager','controller'=>'blog_categories','action'=>'ajax_validation','json','admin'=>false)))); ?>
<script type="text/javascript">
function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
				$("#banner-image-preview").remove();
				$("#banner-image-showcase > div").remove();
				$("#banner-image-showcase > div").remove();
				var img = $('<img id="dynamic">'); 
				var file = input.files[0];
				canvasResize(file, {
				  width: '<?php echo Configure::read('image_edit_width')?>',
				  height: '<?php echo Configure::read('image_edit_height');?>',
				 crop: true,
				quality: 100,
				//rotate: 90,
				callback: function(data, width, height) {
				img.attr('src', data);
					  }
		   });
			
			img.attr('id', 'banner-image-preview');
			img.attr('width', '<?php echo Configure::read('image_edit_width')?>');
            img.attr('height', '<?php echo Configure::read('image_edit_height');?>');
            $('#banner-image-showcase').prepend(img);
           
        }

        reader.readAsDataURL(input.files[0]);
    }
}


$("#BlogCategorieCatImage").change(function(){
    readURL(this);
});
</script>
