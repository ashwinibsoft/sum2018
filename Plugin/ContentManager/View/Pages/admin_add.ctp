<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('Page',array('name'=>'pages','id'=>'PageCms','action'=>'add',$page_id,'onsubmit'=>'//return validatefields();','type'=>'file'))?>
 <?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?php echo $this->Form->input('id');?>
<?php  echo $this->Form->hidden('parent_id'); ?>
<?php  echo $this->Form->hidden('form',array('value'=>'page_add')); ?>
<?php  echo $this->Form->hidden('url_back_redirect',array('value'=>$referer_url)); ?>
	<div class="row">
		<div class="col-md-8">
			<?php echo $this->element('admin/pages/page_basic',array(),array('plugin'=>'ContentManager')); ?>
			<div class="row">
				<div class="col-md-12">
				<?php echo $this->element('admin/pages/page_seo',array(),array('plugin'=>'ContentManager')); ?>
				</div>
			</div>
		</div>
		<div class="col-md-4">
		<?php if(!empty($this->request->data['Page']['id']) && ($this->request->data['Page']['system_page']!=1) ){ ?>
		
			<?php echo $this->element('admin/pages/page_publish',array(),array('plugin'=>'ContentManager')); ?>
		
		<?php }else{ ?>
		<?php  echo $this->Form->hidden('status',array('value'=>1)); ?>
		<?php } ?>
		<?php echo $this->element('admin/pages/page_banner',array(),array('plugin'=>'ContentManager')); ?>
		
		<?php  echo $this->element('admin/pages/page_photo',array(),array('plugin'=>'ContentManager')); ?>
		
		
		
		<?php // if($this->request->data['Page']['system_page']!=1){ ?>
		<?php echo $this->element('admin/pages/page_attribute',array(),array('plugin'=>'ContentManager')); ?>
		<?php // } ?>
		
		
		</div>
		<?php /*<div class="col-md-4">
		
		</div>
		<div class="col-md-4">
		
		</div>*/?>
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
		<button class="btn btn-success btn-sm btn-small" type="submit"  name="save_close" value="update" onclick="javascript: action_reset()">Update</button>
		<?php }else{ ?>
		<button class="btn btn-success btn-sm btn-small" type="submit"  onClick="action_reset();jQuery('#PageStatus').val(1).select2();" name="save_close" value="Save & Close">Publish</button>
		<button class="btn btn-success btn-sm btn-small" type="submit" onClick="action_reset();jQuery('#PageStatus').val(2).select2();" name="save" value="Save">Save as Draft</button>
		
		<?php }?>
		<?php if(!empty($this->request->data)){ ?>
		<button class="btn btn-info btn-sm btn-small preview" type="button" onclick="javascript: preview_submit()" name="preview" value="preview"><i class="fa fa-eye"></i> Preview</button><?php  } ?>
		<?php if(!empty($this->request->data['Page']['id']) && ($this->request->data['Page']['system_page']!=1) ){ ?>
		<a href="<?php echo Router::url(array('action'=>'admin_one_delete',$this->request->data['Page']['id'],'?'=>array('back'=>$referer_url))); ?>" class="btn btn-link btn-sm btn-small tip" id="delete-button" data-toggle="tooltip"  data-original-title="Delete this page?click here."><i class="fa fa-2x fa-trash-o" id="animate-icon"  ></i>
		</a>
		<?php } ?>
	</div>
</div>
<?php echo $this->Form->end();?>


<?php if(!empty($this->request->data['Page']['id'])) { ?>
<?php echo $this->element('admin/crop',array('form_action'=>Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_default_image_crop',$this->data['Page']['id'])),
									'image'=>$this->webroot.'img/banner/'.($this->request->data['Page']['banner_image']),
									'width'=>Configure::read('banner_image_width'),
									'height'=>Configure::read('banner_image_height'),
									'id'=>'viewDetail')); ?>
<?php } ?>
<script type="text/javascript">

 <?php $path = $this->Html->webroot; ?>
 var fckeditor = new Array;
		addeditor(0,'PagePageShortdescription')
		addeditor(1,'PagePageLongdescription')
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
		var response = confirm("Are you sure want to delete this page?.");
		if(response){
			return true;
		}else{
			return false;
		}
	});
	
	}); 
</script>
<?php echo $this->element('admin/forms/form_validation_js',array('form_id'=>'PageCms','form_validation_url'=>Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'ajax_validation','json','admin'=>false)))); ?>
<script type="text/javascript">
function preview_submit(){
	var page_id = '<?php echo $page_id; ?>';
	$('#PageCms').attr({'action':'/content_manager/pages/preview/'+page_id});
	$('#PageCms').attr({'target':'_blank'});
	document.pages.submit();
	
}
function action_reset(){
	$('#PageCms').attr({'action':''});
	$('#PageCms').attr({'target':''});
}
</script>
<script type="text/javascript">
$(document).ready(function(){
	$("#page_basic").click(function(){
		$("#page_basicContent").slideToggle();
	});
	$("#page_seo").click(function(){
		$("#page_seoContent").slideToggle();
	});
	$("#page_publish").click(function(){
		$("#page_publishContent").slideToggle();
	});
	$("#page_banner").click(function(){
		$("#page_bannerContent").slideToggle();
		
	});
	$("#page_attribute").click(function(){
		$("#page_attributeContent").slideToggle();
		
	});
	
});

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

$("#PageBannerImage").change(function(){
    readURL(this);
});
</script>
