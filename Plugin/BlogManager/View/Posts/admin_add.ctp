<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('Post',array('name'=>'posts','id'=>'PostCms','action'=>'add',$post_id,'onsubmit'=>'//return validatefields();','type'=>'file'))?>
 <?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?php echo $this->Form->input('id');?>
<?php  echo $this->Form->hidden('form',array('value'=>'post_add')); ?>
<?php  echo $this->Form->hidden('url_back_redirect',array('value'=>$referer_url)); ?>
	<div class="row">
		<div class="col-md-8">
			<?php echo $this->element('admin/posts/post_basic',array(),array('plugin'=>'BlogManager')); ?>
			<?php
			if(Configure::read('Settings.post_seo')){
			?>	
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->element('admin/posts/post_seo',array(),array('plugin'=>'BlogManager')); ?>
				</div>
			</div>
			<?php } ?>
		</div>
		<div class="col-md-4">
		<?php if(!empty($this->request->data['Post']['id'])){ ?>
		
			<?php echo $this->element('admin/posts/post_publish',array(),array('plugin'=>'BlogManager')); ?>
		
		<?php }else{ ?>
		<?php  echo $this->Form->hidden('status',array('value'=>1)); ?>
		<?php } ?>
		<?php
			if(Configure::read('Settings.post_image')){
		?>	
		<?php echo $this->element('admin/posts/post_image',array(),array('plugin'=>'BlogManager')); ?>
		<?php } ?>
		<?php
			if(Configure::read('Settings.post_attribute')){
		?>	
		<?php echo $this->element('admin/posts/post_attribute',array(),array('plugin'=>'BlogManager')); ?>
		<?php } ?>
		</div>
	</div>
</div>
<div class="admin-bar" id="quick-access" style="left:250px;bottom: 0px;">
	<div class="admin-bar-inner">
		<div class="form-horizontal">
		</div>
		<button type="button" class="btn btn-link btn-sm btn-small" id="ajax-loader-button" style="display:none;"><i class="fa fa-spinner fa fa-2x fa-spin" id="animate-icon"  ></i>
		</button>
		<button type="button" class="btn btn-info btn-sm btn-small" onclick="setLocation('<?php echo $referer_url; ?>');"> <i class="fa fa-angle-left"></i> Back</button>
		<?php if(!empty($this->request->data)){ ?>
		<button class="btn btn-success btn-sm btn-small" type="submit"  name="save_close" value="update">Update</button>
		<?php }else{ ?>
		<button class="btn btn-success btn-sm btn-small" type="submit"  onClick="jQuery('#PostStatus').val(1).select2();" name="save_close" value="Save & Close">Publish</button>
		<button class="btn btn-success btn-sm btn-small" type="submit" onClick="jQuery('#PostStatus').val(2).select2();;" name="save" value="Save">Save as Draft</button>
		<?php }?>
		<?php if(!empty($this->request->data['Post']['id'])){ ?>
		<a href="<?php echo Router::url(array('action'=>'admin_one_delete',$this->request->data['Post']['id'],'?'=>array('back'=>$referer_url))); ?>" class="btn btn-link btn-sm btn-small tip" id="delete-button" data-toggle="tooltip"  data-original-title="Delete this post?click here."><i class="fa fa-2x fa-trash-o" id="animate-icon"  ></i>
		</a>
		<?php } ?>
	</div>
</div>
<?php echo $this->Form->end();?>


<?php if(!empty($this->request->data['Post']['id'])) { ?>
<?php echo $this->element('admin/crop',array('form_action'=>Router::url(array('plugin'=>'blog_manager','controller'=>'posts','action'=>'admin_default_image_crop',$this->data['Post']['id'])),
									'image'=>$this->webroot.'img/post/'.($this->request->data['Post']['post_image']),
									'width'=>Configure::read('post_image_width'),
									'height'=>Configure::read('post_image_height'),
									'id'=>'viewDetail')); ?>
<?php } ?>
<script type="text/javascript">

 <?php $path = $this->Html->webroot; ?>
 var fckeditor = new Array;
		addeditor(0,'PostDescription')
function removeeditor(id){
	 fckeditor[id].destroy();
}
 
function addeditor(id,name){
	 fckeditor[id] = CKEDITOR.replace(name,{
							language : 'eng',
							uiColor : '#e6e6e6',
							toolbar : 'MyToolbar',
							filebrowserBrowseUrl : '<?=$path?>plugins/ckfinder/ckfinder.html',
							filebrowserImageBrowseUrl : '<?=$path?>plugins/ckfinder/ckfinder.html',
							filebrowserUploadUrl : '<?=$path?>plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files',
							filebrowserImageUploadUrl : '<?=$path?>plugins/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files'
					});
}
 
$(document).ready(function(){
	$('#delete-button').bind('click',function(){
		var response = confirm("Are you sure want to delete this post?.");
		if(response){
			return true;
		}else{
			return false;
		}
	});
	
	});

function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
				$("#banner-image-preview").remove();
				$("#post-image-showcase > div").remove();
				$("#post-image-showcase > div").remove();
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
            $('#post-image-showcase').prepend(img);
           
        }

        reader.readAsDataURL(input.files[0]);
    }
}

$("#PostPostImage").change(function(){
    readURL(this);
}); 
</script>
<?php echo $this->element('admin/forms/form_validation_js',array('form_id'=>'PostCms','form_validation_url'=>Router::url(array('plugin'=>'blog_manager','controller'=>'posts','action'=>'ajax_validation','json','admin'=>false)))); ?>
