<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('Post',array('name'=>'posts','action'=>'settings','onsubmit'=>'//return validatefields();','type'=>'file'))?>
<?php echo $this->Form->hidden('form',array('value'=>'event_settings')); ?>
<?php echo $this->Form->hidden('url_back_redirect',array('value'=>$referer_url)); ?>
	<div class="row">
		<div class="col-md-4">
			<div class="grid simple horizontal green no-margin-grid">
				<div class="grid-title no-border">
					<h4>Blog <span class="semi-bold">Template</span></h4>
					<div class="tools">
						<a href="javascript:;" class="collapse"></a>
					</div>
				</div>
				<div class="grid-body no-border">
					<div class="row">
						<?php echo $this->Form->input('blog_template',array('label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%','options'=>Configure::read('Blog.templates'))); ?>
					</div>
				</div>
			</div>
		</div>
		<?php /*<div class="col-md-4">
			
		</div>
		<div class="col-md-4">
			
		</div>*/?>
	
	</div>
<?php echo $this->element('admin/forms/form_submit',array('referer_url'=>$referer_url)); ?>
<?php echo $this->Form->end();?>
