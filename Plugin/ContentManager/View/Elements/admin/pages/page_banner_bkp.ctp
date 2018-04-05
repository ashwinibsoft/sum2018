<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
	<h4>Banner <span class="semi-bold">Image</span>
	<span style="" class="tip" data-toggle="tooltip" title="Banner Image For Header" data-placement="right"><i class="fa fa-question-circle"></i></span>
	</h4>
		<div class="tools"> 
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border">
		<div class="row">
			<div class="form-group">
					<div class="controls">
					<?php echo $this->Form->file('banner_image',array('required'=> false, 'style'=>'border:0;')); ?>
					<?=$this->Form->error('banner_image',null,array('wrap' => 'span', 'class' => 'error')); ?>
					<span style="color:red;"></span> <span class="help">(Only png, gif, jpg, jpeg images are allowed. Please upload 1000x500 dimension of image for better resolution.)</span>
					<div id="banner-image-showcase"  style="padding-top: 19px;overflow:hidden;">
					<?php 
						/* Resize Image */
						if(!empty($this->data['Page']['banner_image']) && file_exists(Configure::read('Path.Banner').$this->data['Page']['banner_image'])) {
							$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$this->data['Page']['banner_image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'),'noimg'=>Configure::read('Path.NoImage'));
							$resizedImg = $this->ImageResize->ResizeImage($imgArr);
							echo $this->Html->image($resizedImg,array('border'=>'0'));
						}
					?>
					<?php if(!empty($this->data['Page']['banner_image']) && file_exists(Configure::read('Path.Banner').$this->data['Page']['banner_image'])) {?>
					<div style="float:right;">
						
						<a data-toggle="modal" data-id="CropImageId" class="tip link-color" data-original-title="Crop image"  href="#viewDetail"> Crop this image.?
									</a>
						
						
						<div class="modal fade" id="viewDetail" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog modal-dialog1">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<br>
										<i class="icon-credit-card icon-7x"></i>
										<h4 id="myModalLabel" class="semi-bold">Crop Banner Image</h4>
										<br>
									</div>
									<!--start page content here for popup-->
									<div class="modal-body">
									
									<div class="row">
	<div class="" style="overflow:scroll;">
		<div style="margin: 0 0.3em;">
			<?php echo $this->Html->image(Configure::read('Folder.Banner').DS.$this->data['Page']['banner_image'],array('id'=>'photo')); ?>
		</div>
	</div>
</div>
	<?php $ratio  = Configure::read('image_crop_ratio');?>
	
	<div class="container demo" style="width:750px">
	  <div style="float: left; width: 50%;">
		<p class="instructions">
		  Click and drag on the image to select an area. 
		</p>
	
		
	</div>
		<div style="float: left; width: 100%;">
		<p style="font-size: 110%; font-weight: bold; padding-left: 0.1em;">
		</p>
		<?php echo $this->Form->create();?>
				<input type="hidden" value="" id="x1" name="start_width">
				<input type="hidden" id="w" value="" name="width">
				<input type="hidden" value="" id="y1" name="start_height">
				<input type="hidden" value="" id="h" name="height">
				<input type="hidden" value="" id="x2">
				<input type="hidden" value="" id="y2">
			  
		<button class="btn btn-primary btn-lg btn-large" type="submit" id="save_thumb">Submit</button>
		<button type="button" class="btn btn-primary btn-lg btn-large" onclick="closeWindow();" data-dismiss="modal">Cancel</button>
		<?php echo $this->Form->end();?>
	  </div>
	</div>
								<script type="text/javascript">
									function preview(img, selection) {
									var scaleX = 100 / selection.width; 
									var scaleY = 100 / selection.height; 
										$('#x1').val(selection.x1);
										$('#y1').val(selection.y1);
										$('#x2').val(selection.x2);
										$('#y2').val(selection.y2);
										$('#w').val(selection.width);
										$('#h').val(selection.height); 
									}
											$(function () {
										$('#photo').imgAreaSelect({handles: true,
											fadeSpeed: 200,onSelectChange: preview });
										});
									
									$(document).ready(function () { 
									$('#save_thumb').click(function() {
											
										var x1 = $('#x1').val();
										var y1 = $('#y1').val();
										var x2 = $('#x2').val();
										var y2 = $('#y2').val();
										var w = $('#w').val();
										var h = $('#h').val();
										
										if(w==0 && h==0){
											alert("You must make a selection first");
											return false;
										}else{
											return true;
										}
									});
								}); 
									
									function closeWindow()
									{
											window.close();
									}
										
									</script>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
								</div>
								<!--end page content here for popup-->
								</div>
								<!-- /.modal-content -->
							</div>
							<!-- /.modal-dialog -->
							</div>
							<!-- /.modal -->
						
						
						<!--<a data-toggle="modal" data-id="" id="banner-popup" class="tip link-color" data-original-title="Resize"  href="<?php //echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_crop_image',$page_id)); ?>">Crop this image?</a>--> | 
						<a href="<?php echo Router::url(array('plugin'=>'content_manager','controller'=>'pages','action'=>'delete_banner_image',$page_id)); ?>" class="remove_banner_ajax" target="_blank">Remove this image</a>
						<script type="text/javascript">
							$(document).ready(function(){
								$('.remove_banner_ajax').bind('click',function(){
									$.post( $(this).attr('href'), function( data ) {
										$('#banner-image-showcase').html('');
										
									});
									return false;
									});
								
								});
						</script>
					</div>
					<?php } ?>
					</div>
					<div class="checkbox check-primary checkbox-circle">
						<?php echo $this->Form->checkbox('use_default_image', array('options'=>array(),'label' => false,'div'=>false,'empty'=>'Not Selected','style'=>'width:100%','hiddenField'=>true)); ?>
						<?php echo $this->Form->label('use_default_image', 'Use Default Banner Image'); ?>
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function(){
var dataitem=null;
$('#banner-popup').bind('click',function(){
	var windowSizeArray ="width=750,height=600,scrollbars=yes";
	var url = $(this).attr("href")+'/?popup=1';
	var windowName = "popUp";//$(this).attr("name");
	//var windowSize = windowSizeArray[$(this).attr("rel")];
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
function update_banner(id,data){
$('#banner-image-showcase').html(data);
}

</script>

