<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('Page',array('name'=>'pages','action'=>'settings','onsubmit'=>'//return validatefields();','type'=>'file'))?>
<?php echo $this->Form->hidden('form',array('value'=>'page_settings')); ?>
<?php echo $this->Form->hidden('url_back_redirect',array('value'=>$referer_url)); ?>
	<div class="row">
		<?php if(Configure::read('Section.default_banner_image') /* || $this->Menu->is_super_admin() */){ ?>
			
		<div class="col-md-8">
			<?php echo $this->element('admin/page_setting/default_banner_image'); ?>
		</div>
		<?php } ?>
		<?php if(Configure::read('Section.default_banner_image') /* || $this->Menu->is_super_admin() */){ ?>
			<div class="col-md-4">
		<?php }else { ?>
			<div class="col-md-12">
				<div class="col-md-4">
		<?php } ?>
		
			<?php if(Configure::read('Section.default_home_page') /* || $this->Menu->is_super_admin() */){  ?>
				<?php echo $this->element('admin/page_setting/default_home_page'); ?>
				<?php if(!Configure::read('Section.default_banner_image') /* && !$this->Menu->is_super_admin() */){ ?>
					</div>
					<div class="col-md-4">
				<?php } ?>
			<?php } ?>
			<?php if(Configure::read('Section.home_block_length') /* || $this->Menu->is_super_admin() */){  ?>
				<?php echo $this->element('admin/page_setting/home_block_length'); ?>
				<?php if(!Configure::read('Section.default_banner_image') /* && !$this->Menu->is_super_admin() */){ ?>
					</div>
					<div class="col-md-4">
				<?php } ?>
			<?php } ?>
			<?php if(Configure::read('Section.special_page') /* || $this->Menu->is_super_admin() */){  ?>
				<?php //echo $this->element('admin/page_setting/default_special_page'); ?>
				<?php if(!Configure::read('Section.default_banner_image') /*&& !$this->Menu->is_super_admin() */){ ?>
					</div>
					<div class="col-md-4">
				<?php } ?>
			<?php } ?>
			
			<?php if(Configure::read('Section.default_child_pages') /* || $this->Menu->is_super_admin() */){  ?>
				<?php //echo $this->element('admin/page_setting/default_child_pages'); ?>
				<?php if(!Configure::read('Section.default_banner_image') /* && !$this->Menu->is_super_admin() */){ ?>
					</div>
					<div class="col-md-4">
				<?php } ?>
			<?php } ?>
			
			<?php if(Configure::read('Section.home_page_block') /* || $this->Menu->is_super_admin() */){  ?>
				<?php echo $this->element('admin/page_setting/default_home_blocks'); ?>
			<?php } ?>
			
			<?php if(!Configure::read('Section.default_banner_image') /* && !$this->Menu->is_super_admin() */){ ?>
				</div>
			<?php } ?>
		</div>
		<?php /*<div class="col-md-4">
			
		</div>
		<div class="col-md-4">
			
		</div>*/?>
	
	</div>
<?php echo $this->element('admin/forms/form_submit',array('referer_url'=>$referer_url)); ?>
<?php echo $this->Form->end();?>
<?php echo $this->element('admin/crop',array('form_action'=>Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_default_banner_image_crop')),
									'image'=>$this->webroot.'img/banner/'.$this->data['Page']['banner_image'],
									'width'=>Configure::read('banner_image_width'),
									'height'=>Configure::read('banner_image_height'),
									'id'=>'viewDetail')); ?>
<?php echo $this->element('admin/forms/form_validation_js',array('form_id'=>'PageSettingsForm','form_validation_url'=>Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'ajax_validation','json','admin'=>false)))); ?>

<script type="text/javascript">
$(document).ready(function(){
	$("#expanderHead").click(function(){
		$("#expanderContent").slideToggle();
		/*if ($("#expanderSign").text() == "+"){
			$("#expanderSign").html("−")
		}
		else {
			$("#expanderSign").text("+")
		}*/
	});
	$("#defaulthome").click(function(){
		$("#defaulthomeContent").slideToggle();
	});
	$("#childlistinghome").click(function(){
		$("#childlistingContent").slideToggle();
	});
	$("#defaultspecialpage").click(function(){
		$("#defaultspecialpageContent").slideToggle();
		
	});
	$("#defaulthomeblocklength").click(function(){
		$("#defaulthomeBlockLength").slideToggle();
		
	});
});
</script>
