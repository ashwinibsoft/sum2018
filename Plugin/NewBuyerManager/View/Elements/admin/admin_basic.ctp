<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4>Contact Person <span class="semi-bold">Information</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div>
	<div class="grid-body no-border">
		<div class="form-group">
			<label class="form-label">Contact Person</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for contact person." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('contact_person',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('contact_person',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Title</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for title." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?//=$this->Form->text('title',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?php echo $this->Form->input('title',array('options'=>array('Mr'=>'Mr','Mr Dr'=>'Mr Dr','Mr Prof'=>'Mr Prof','Mrs'=>'Mrs','Ms Dr'=>'Ms Dr','Ms Prof'=>'Ms Prof','Ms'=>'Ms'),'label' => false,'div'=>false,'empty'=>'(Select Title)','class'=>'simple-dropdown','style'=>'width:100%')); ?>
				<?=$this->Form->error('title',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">First Name</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for first name of buyer." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('first_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('first_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Middle Name</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for middle name of buyer." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('middle_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('middle_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Last Name</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for last name of buyer." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('last_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('last_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Contact Number</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for last contact number of buyer." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('contact_number',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('contact_number',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Email Address</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for email address of buyer." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('email_id',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('email_id',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Position in Organisation</label>			
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for Position in Organisation." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('designation',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('designation',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		
	
	</div>
</div>
