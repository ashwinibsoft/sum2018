<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4>Basic <span class="semi-bold">Information</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div>
	<div class="grid-body no-border">
		<div class="form-group">
			<label class="form-label">Title</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for title." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?php echo $this->Form->input('title',array('options'=>array('Mr'=>'Mr','Mr Dr'=>'Mr Dr','Mr Prof'=>'Mr Prof','Mrs'=>'Mrs','Ms Dr'=>'Ms Dr','Ms Prof'=>'Ms Prof','Ms'=>'Ms'),'label' => false,'div'=>false,'empty'=>'(Select Title)','class'=>'simple-dropdown','style'=>'width:100%')); ?>
				<?=$this->Form->error('title',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">First Name</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for first name." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('first_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('first_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Middle Name</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for middle name." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('middle_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('middle_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Last Name</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for last name." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('last_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('last_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Address 1</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for address." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('address1',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('address1',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Address 2</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for address." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('address2',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('address2',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">City</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for city" data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('city', array('class'=>'form-control')); ?>
				<?=$this->Form->error('city',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">State/Province</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for state/province." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('state',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('state',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Zip/Postcode</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for zip/Postcode." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('zipcode',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('zipcode',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Country</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for country." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?//=$this->Form->text('country',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?php echo $this->Form->input('country',array('options'=> $countries,'label' => false,'div'=>false,'empty'=>'(Select Country)','class'=>'simple-dropdown','style'=>'width:100%')); ?>
				<?=$this->Form->error('country',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Email ID</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for email id." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('email_id',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('email_id',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="checkbox check-primary checkbox-circle">
			<?php echo $this->Form->checkbox('receive_info', array('options'=>array(),'label' => false,'div'=>false,'empty'=>'Not Selected','style'=>'width:100%','hiddenField'=>true)); ?>
			<?php echo $this->Form->label('receive_info', 'Receive Info Material'); ?>
			
		</div>
		<!--<div class="form-group">
			<label class="form-label">Receive Info Material</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for email id." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?//=$this->Form->text('receive_info',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?//=$this->Form->error('receive_info',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>-->
	</div>
</div>
<script type="text/javascript">

 <?php //$path = $this->Html->webroot; ?>
/* var fckeditor = new Array;
		addeditor(0,'TeamTeamLongdescription')
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
*/
</script>
