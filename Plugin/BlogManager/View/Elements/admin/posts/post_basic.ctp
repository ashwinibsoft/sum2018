<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4>Basic <span class="semi-bold">Information</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div>
	<div class="grid-body no-border">
		<div class="form-group">
			<label class="form-label">Title</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for title of post." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('post_name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('post_name',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label ">Description</label>
			<span style="color:red;">*</span>
			<span style="" class="tip" data-toggle="tooltip" title="This field 
			is post description of this post." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?php echo $this->Form->textarea('description', array('class'=>'form-control'));
			?>
			</div>
		</div>
		<!--<div class="form-group">
			<label class="form-label">Long Description</label><span style="" class="tip" data-toggle="tooltip" title="This field is used for post long description, which is displayed in front." data-placement="right"><i class="fa fa-question-circle"></i></span>
				<div class="controls">
				<?php 	
					//echo $this->Form->textarea('post_longdescription', array('class'=>'form-control'));
				?>
				</div>
		</div>-->
	</div>
</div>
