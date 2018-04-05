<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4>Location <span class="semi-bold">Information</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div>
	<div class="grid-body no-border">
		<div class="form-group">
			<label class="form-label">Organisation Name</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for organisation name." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('org_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('org_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		
		<div class="form-group">
			<label class="form-label">State</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for state new buyer belongs to." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('state',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('state',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		
		<div class="form-group">
			<label class="form-label">Country</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for country new buyer belongs to." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?php echo $this->Form->input('country',array('options'=> $countries,'label' => false,'div'=>false,'empty'=>'(Select Country)','class'=>'simple-dropdown','style'=>'width:100%')); ?>
				<?//=$this->Form->text('country',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('country',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		
	</div>
</div>
