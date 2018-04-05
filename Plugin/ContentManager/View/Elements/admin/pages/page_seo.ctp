<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border " style="">
	  <h4 id="page_seo" style="cursor:pointer;">SEO <span class="semi-bold">Information</span></h4>
	  <div class="tools"> <a class="collapse" href="javascript:;"></a></div>
	</div>
	<div class="grid-body no-border" id="page_seoContent">	
		<div class="form-group">
		<label class="form-label">SEO Title</label><span style="" class="tip" data-toggle="tooltip" title="This field is used for SEO title of page." data-placement="right" style="vertical-align:top;"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->text('page_title',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('page_title',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
		<label class="form-label" >Slug Url</label><span style="" class="tip" data-toggle="tooltip" title="This field is used for url of page." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
			  <?=$this->Form->text('slug_url',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
				<?=$this->Form->error('slug_url',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
		<label class="form-label">Meta Keywords</label><span style="" class="tip" data-toggle="tooltip" title="This field is used for meta keyword of this page" data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
			<?=$this->Form->textarea('page_metakeyword',array('class'=>'form-control'));?>
			<?=$this->Form->error('page_metakeyword',null,array('wrap' => 'span', 'class' => 'error')); ?>
			</div>
		</div>
		<div class="form-group">
		<label class="form-label">Meta Description</label><span style="" class="tip" data-toggle="tooltip" title="This field is used for meta description of this page" data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
			   <?=$this->Form->textarea('page_metadescription',array('class'=>'form-control'));?>
				<?=$this->Form->error('page_metadescription',null,array('wrap' => 'span', 'class' => 'error-message')); ?>
			</div>
		</div>
	</div>	
</div>
