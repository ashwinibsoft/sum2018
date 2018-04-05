<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
	<h4>Post <span class="semi-bold">Attribute</span></h4>
		<div class="tools"> 
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border">
		<div class="row">
		<?php
		if(Configure::read('Settings.post_gallery')){
		?>		
			<div class="form-group">
				<label class="form-label">Gallery</label>
				<span style="" class="tip" data-toggle="tooltip" title="This field is used to set gallery for post." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?php echo $this->Form->input('post_gallery',array('options'=>$galleries,'label' => false,'div'=>false,'empty'=>'(Select Gallery)','class'=>'simple-dropdown','style'=>'width:100%')); ?>
						<br />
						<?php if(!empty($this->data['Post']['post_gallery'])){ ?>
						<a class="managegallery" href="<?php echo Router::url(array('plugin'=>'gallery_manager','controller'=>'galleries','action'=>'popup_manage_image',$this->data['Post']['post_gallery'])); ?>" target="_blank">Manage this gallery</a>
						<?php 
					}
						?>
					<?php if($this->Menu->modify_permission('GalleryManager')){ ?>
					<p>
						<div id="show" style="float:left; width:100%;"></div>
						<br />
						<a class="btn btn-primary newwindow" href="<?php echo Router::url(array('plugin'=>'gallery_manager','controller'=>'galleries','action'=>'add')); ?>" target="_blank">Add new gallery</a>
						<div id="show" style="float:left; width:100%;"></div>
						<br />
							
								
						
					</p>
					<script type="text/javascript">
						$(document).ready(function(){
							var dataitem=null;
							$('.newwindow').bind('click',function(){
								var windowSizeArray ="width=750,height=600,scrollbars=yes";
								var url = $(this).attr("href")+'/?popup=1';
								var windowName = "popUp";
			 					var data = window.open(url, windowName, windowSizeArray);
								data.onunload = function(){
									dataitem = window['popUp'];
									if(dataitem!=null && dataitem!=undefined){
										
									}
								}
								return false;
								});
							var dataitem=null;
							$('.managegallery').bind('click',function(){
								var windowSizeArray ="width=770,height=620,scrollbars=yes";
								var url = $(this).attr("href");
								var windowName = "popUp";
			 					var data = window.open(url, windowName, windowSizeArray);
								data.onunload = function(){
									dataitem = window['popUp'];
									if(dataitem!=null && dataitem!=undefined){
										
									}
								}
								return false;
								});	
						});
						
						function update_gallery(id,data){
							$('#PostPostGallery').append('<option value="'+id+'">'+ data.name +'</option>').val(id);
							$("#PostPostGallery").select2();
						}
					</script>
					<?php } ?>
				</div>
			</div>
				<?php
				}
				?>
			
			<?php
			if(Configure::read('Settings.post_category'))
				{
			?>
		
			<div class="form-group">
			<label class="form-label">Category</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for Blog Category." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<br>
			<div class="checkbox check-success scroller" data-height="150px" style="overflow: 
			hidden;">
				
				<?=$this->Form->input('blog_categorie_id',array('options'=>$cat_list, 'class'=>'big','type' => 'select','label'=>false,'multiple' => 'checkbox','div'=>false,'required'=>false,'style'=>'width:100%')); ?>
				
				</div>
		</div>
			<?php } ?>
			<div class="form-group">
			<label class="form-label">Tags</label>
			<span style="" class="tip" data-toggle="tooltip" title="This field is used for Post tag." data-placement="right"><i class="fa fa-question-circle"></i></span>
			<div class="controls">
				<?=$this->Form->input('post_tags',array('type'=>'text','class'=> 'span12 tagsinput','label'=>false,'data-role'=>"tagsinput",'required'=>false,'placeholder'=>'fill tag name then for next tag hit enter')); ?>
				
			</div>
		</div>
		
		</div>
	</div>
</div>
