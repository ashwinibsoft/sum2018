<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
	<h4 id="page_attribute" style="cursor:pointer;">Page <span class="semi-bold">Attribute</span></h4>
		<div class="tools"> 
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border" id="page_attributeContent">
		<div class="row">
			
			<div class="form-group">
				<label class="form-label">Template
				<span style="" class="tip" data-toggle="tooltip" title="This field is used to set layout of page." data-placement="right"><i class="fa fa-question-circle"></i></span>
				</label>
					<div class="controls">
					<?php 
						echo $this->Form->input('page_template',array('options'=>Configure :: Read('Template'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%'));
					?> 
				</div>
			</div>
			<?php if(empty($this->request->data) || $this->request->data['Page']['system_page']!=1 || $this->Menu->is_super_admin()){ ?>
			<?php if((int)Configure::read('Section.gallery') && $this->_is_active_plugins('GalleryManager')){ ?>
			<div class="form-group">
				<label class="form-label">Gallery
				<span style="" class="tip" data-toggle="tooltip" title="This field is used to set gallery for page." data-placement="right"><i class="fa fa-question-circle"></i></span>
				</label>
					<div class="controls">
						<?php echo $this->Form->input('gallery',array('options'=>$galleries,'label' => false,'div'=>false,'empty'=>'(Select Gallery)','class'=>'simple-dropdown','style'=>'width:100%')); ?>
					<?php if($this->Menu->modify_permission('GalleryManager')){ ?>
					<p>
						<!--<div id="show" style="float:left; width:100%;"></div>
						<br />
						<a class="btn btn-primary newwindow" href="<?php echo Router::url(array('plugin'=>'gallery_manager','controller'=>'galleries','action'=>'add')); ?>" target="_blank">Add new gallery</a>
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
										alert(dataitem);
									}
								}
								return false;
								});
						});
						function update_gallery(id,data){
							$('#PageGallery').append('<option value="'+id+'">'+ data.name +'</option>').val(id);
							$("#PageGallery").select2();
						}
					</script>
					<?php } ?>
				</div>
			</div>-->
			<?php } ?>
			
			<div class="form-group">
				<label class="form-label">Parent 
				<span style="" class="tip" data-toggle="tooltip" title="This field is used for parent." data-placement="right"><i class="fa fa-question-circle"></i></span>
				</label>
				<div class="controls">
					<?php echo $this->Page->page_select_mutlilevel('parent_id',array('options'=>array(),'label' => false,'div'=>false,'empty'=>'Select Parent','class'=>'simple-dropdown','style'=>'width:100%'),$page_list,empty($this->request->data['Page']['parent_id'])?'':(int)$this->request->data['Page']['parent_id']);?> 					  
				</div>
			</div>
			
			<!--<div class="form-group">
				<label class="form-label">Page Order
				<span style="" class="tip" data-toggle="tooltip" title="This field is to set page order for the page" data-placement="right"><i class="fa fa-question-circle"></i></span>
				</label>
					<div class="controls">
					<?php //echo $this->Form->text('page_order',array('label' => false,'div'=>false));?> 
				</div>
			</div>-->
			<?php } ?>
		
		</div>
	</div>
</div>
