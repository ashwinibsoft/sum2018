<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('NewBuyer',array('name'=>'new_buyers','id'=>'NewBuyerCms','action'=>'add',$nb_id,'onsubmit'=>'//return validatefields();','type'=>'file'))?>
 <?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?php echo $this->Form->input('id');?>
<?php  echo $this->Form->hidden('form',array('value'=>'new_buyer_add')); ?>
<?php  echo $this->Form->hidden('status',array('value'=>1)); ?>
<?php  echo $this->Form->hidden('url_back_redirect',array('value'=>$referer_url)); ?>
	<div class="row">
		<div class="col-md-12">
			<?php echo $this->element('admin/admin_region',array(),array('plugin'=>'NewBuyerManager')); ?>
			</div>			
		<div class="col-md-6">
			<?php echo $this->element('admin/admin_basic',array(),array('plugin'=>'NewBuyerManager')); ?>
			</div>
		<div class="col-md-6">
					<?php echo $this->element('admin/admin_second',array(),array('plugin'=>'NewBuyerManager')); ?>
			</br></br>
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
		<button class="btn btn-success btn-sm btn-small" type="submit"  onClick="jQuery('#PageStatus').val(1).select2();" name="save_close" value="Save & Close">Save</button>
		<!--<button class="btn btn-success btn-sm btn-small" type="submit" onClick="jQuery('#PageStatus').val(2).select2();;" name="save" value="Save">Save as Draft</button>-->
		
		<?php }?>
	</div>
</div>
<?php echo $this->Form->end();?>
<?php echo $this->element('admin/forms/form_validation_js',array('form_id'=>'NewBuyerCms','form_validation_url'=>Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'ajax_validation','json','admin'=>false)))); ?>
