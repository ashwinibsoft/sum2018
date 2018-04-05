<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('ExistingBuyer',array('name'=>'ebuyers','id'=>'ExistingBuyerCms','action'=>'reset_expire',$id,'onsubmit'=>'//return validatefields();','type'=>'file'))?>
 <?php  echo $this->Form->hidden('id',array('value'=>$id)); ?>
<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4>Expire<span class="semi-bold">Date</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div> 
  <div class="grid-body no-border">
		<div class="form-group">
			<label class="form-label">Old Date</label>
			<div class="controls">
				<?php if(!empty($details)) {
					foreach ($details as $_details){ ?>
					
					<?php echo $_details['EbLoginDetail']['link_expire_date']; ?>
					<?php  echo $this->Form->hidden('old_date',array('value'=>$_details['EbLoginDetail']['link_expire_date'])); ?>
					
					<?php } } else {?>
					<p style="color:red;">This Existing buyer have no account login details.</p>	
						
						<?php }?>
			</div>
		 </div>
	</div>
<?php if(!empty($details)) { ?>
	<div class="grid-body no-border">
		<div class="form-group">
			<label class="form-label">New Date</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for add date." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('link_expire_date',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('link_expire_date',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		 </div>
	</div>
	
	<?php }?>
	
	
</div>
<div class="admin-bar" id="quick-access" style="left:250px;bottom: 0px;">
	<div class="admin-bar-inner">
		<div class="form-horizontal">
		</div>
		<button type="button" class="btn btn-link btn-sm btn-small" id="ajax-loader-button" style="display:none;"><i class="fa fa-spinner fa fa-2x fa-spin" id="animate-icon"  ></i>
		</button>
		<button type="button" class="btn btn-info btn-sm btn-small" onclick="setLocation('<?php echo $back_url; ?>');"> <i class="fa fa-angle-left"></i> Back</button> 
		
		<button class="btn btn-success btn-sm btn-small" type="submit"  onClick="jQuery('#PageStatus').val(1).select2();" name="save_close" value="Save & Close">Submit</button>
	</div>
</div>

<?php echo $this->Form->end();?>
<script type="text/javascript">
  $(function() {
    $("#ExistingBuyerLinkExpireDate").click(function() {
        $(this).datepicker().datepicker( "show" );
        
    });
});
</script>
