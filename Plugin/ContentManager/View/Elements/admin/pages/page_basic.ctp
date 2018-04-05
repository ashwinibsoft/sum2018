<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4 id="page_basic" style="cursor:pointer;">Basic <span class="semi-bold">Information</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div>
	<div class="grid-body no-border" id="page_basicContent">
		<div class="form-group">
			<label class="form-label">Page Name</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for name of page." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label ">Page Short Description</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field 
			is page short description of this page." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?php echo $this->Form->textarea('page_shortdescription', array('class'=>'form-control'));
			?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label">Page Long Description</label><span style="" class="tip" data-toggle="tooltip" title="This field is used for page long description, which is displayed in front." data-placement="right"><i class="fa fa-question-circle"></i></span>
				<div class="controls">
				<?php 	
					echo $this->Form->textarea('page_longdescription', array('class'=>'form-control'));
				?>
				</div>
		</div>
	</div>
</div>
