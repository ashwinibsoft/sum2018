<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
		<h4 id="page_publish" style="cursor:pointer;">Status <span class="semi-bold"></span> 
		<span style="" class="tip" data-toggle="tooltip" title="It is used to set page status" data-placement="right"><i class="fa fa-question-circle"></i></span>
		</h4>
		<div class="tools">
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border" id="page_publishContent">
		<div class="row">
			<?php 
					echo $this->Form->input('status',array('options'=>array('1'=>'Publish','0'=>'Unpublish','2'=>'Draft'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%'));
				?> 
		</div>
	</div>
</div>
