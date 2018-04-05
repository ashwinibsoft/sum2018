<?php echo $this->element('admin/message');?>
<?php echo $this->Form->create('Slide',array('name'=>'slides','id'=>'Slide','action'=>'add' ,'onsubmit'=>'//return validatefields();','type'=>'file','novalidate'=>true))?>
<?php echo $this->Form->input('id');?>
<?php echo $this->Form->hidden('status');?>
<?php echo $this->Form->hidden('x1');?>
<?php echo $this->Form->hidden('x2');?>
<?php echo $this->Form->hidden('y1');?>
<?php echo $this->Form->hidden('y2');?>
<?=$this->Form->hidden('redirect', array('value' => $url)); ?>
<div class="row">
	<div class="col-md-8">
		<div class="grid simple horizontal green no-margin-grid">
			<div style="" class="grid-title no-border ">
			<h4>Basic <span class="semi-bold">Information</span></h4>
			<div class="tools"> <a href="javascript:;" class="collapse"></a></div>
		</div>
			<div class="grid-body no-border">
				<div class="form-group">
				<label class="form-label">Slide Name</label>
				<span style="color:red;">*</span>
				<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for slide name." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?=$this->Form->text('name',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
						<?=$this->Form->error('name',null,array('wrap' => 'span', 'class' => 'error')); ?>
					</div>
				</div>
				<?php if(Configure::read('Fields.Options.slide_title')){ ?>
				<div class="form-group">
				<label class="form-label">Title</label>
				<span style="color:red;">*</span>
				<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for slide title." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?=$this->Form->text('title',array('class'=> 'form-control','size'=>'45','required'=>false)); ?>
						<?=$this->Form->error('title',null,array('wrap' => 'span', 'class' => 'error')); ?>
					</div>
				</div>
				<?php } ?>
				
				<div class="form-group" style="display:none">
					<label class="form-label">Transition Effects</label>
					<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for slide's transition effects." data-placement="right"><i class="fa fa-question-circle"></i></span>
<div class="controls">
 <?php $effects=array('Box Mask'=>'Box Mask','Box Mask Mosaic'=>'Box Mask Mosaic','Slot Zoom Horizontal'=>'Slot Zoom Horizontal','Slot Slide Horizontal'=>'Slot Slide Horizontal','Slot Fade Horizontal'=>'Slot Fade Horizontal','Slot Zoom Vertical'=>'Slot Zoom Vertical','Slot Slide Vertical'=>'Slot Slide Vertical','Slot Fade Vertical'=>'Slot Fade Vertical','Curtain One'=>'Curtain One','Curtain Two'=>'Curtain Two','Curtain Three'=>'Curtain Three','Slide Left'=>'Slide Left','Slide Right'=>'Slide Right','Slide Up'=>'Slide Up','Slide Down','Slide Down','Fade');?>
 <?=$this->Form->input('transition_effect',array('type'=>'select','options'=>$effects,'empty'=>'Please Select Transition Effect','class'=>'form-control','label'=>false));?>
				</div>
		</div>
<div class="form-group" style="display:none">
	<label class="form-label">Slide Time (Duration)</label>
	<div class="controls">
		<?php $slide_time_options=range(1,100); ?>
		<?=$this->Form->input('slide_time',array('type'=>'select','options'=>$slide_time_options,'empty'=>'Please Select Slide Time (Duration) in seconds','class'=>'form-control','label'=>false));?>
			</div>
	</div>
<div class="form-group" style="display:none">
	<label class="form-label">Show Timer</label>
<div class="radio radio-success">
		<?php $show_timer=array(1=>'Yes',0=>'No');?>
		<?=$this->Form->radio('show_timer',$show_timer,array('legend'=>false,'div'=>false)); ?>
			</div>
				</div>
				
 <?php if(Configure::read('Fields.Options.slide_description')){ ?>
	<div class="form-group">
		<label class="form-label">Description</label>
		<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for slide description." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?=$this->Form->textarea('description',array('class'=>'form-control','required'=>false));?>
						<?=$this->Form->error('description',null,array('wrap' => 'span', 'class' => 'error')); ?>
					</div>
				</div>
				<?php  } ?>
				<?php  if($this->Menu->is_super_admin()){   ?>
					
				<div class="form-group">
				<label class="form-label">Theme</label>
				<span style="" class="tip" data-toggle="tooltip" title="This field 
is used for theme." data-placement="right"><i class="fa fa-question-circle"></i></span>
					<div class="controls">
						<?php 
							echo $this->Form->input('theme',array('options'=>$themes,'empty'=>'All Themes','label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%'));
						?> 
						<?=$this->Form->error('theme',null,array('wrap' => 'span', 'class' => 'error')); ?>
					</div>
				</div><?php   } ?> 
			</div> 
		</div> 
	</div>
	<div class="col-md-4">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border" >
				<h4>Slide <span class="semi-bold">Image</span><span style="color:red;font-size:9px;">*</span>
				<span style="" class="tip" data-toggle="tooltip" title="This field is used for slide image." data-placement="right"><i class="fa fa-question-circle"></i></span>
				<span style="color:red;"></span>
                </h4>
				<div class="tools"> 
					<a class="collapse" href="javascript:;"></a>
				</div>
                
			</div>
		<div class="grid-body no-border">
		<div class="row" style="padding-top: 10px;">
		<div class="form-group">
		<div class="controls">
	<?php echo $this->Form->file('image',array('id'=>'SlideImage', 'style'=>'border:0;')); ?>
	<span style="color:red;"></span> <span class="help">(Only png, gif, jpg, jpeg types are allowed.Please upload <?php echo Configure::read('slide_image_width'); ?>x <?php echo Configure::read('slide_image_height'); ?> dimension of image for better resolution.)</span>
	<?=$this->Form->error('image',null,array('wrap' => 'span', 'class' => 'error')); ?>
	<div id="banner-image-showcase"  style="padding-top: 19px" class="admin_slide">
		<?php 
	/* Resize Image */
if(isset($this->data['Slide']['image'])) {
		$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$this->data['Slide']['image'],'width'=>Configure::read('image_edit_width'),'height'=>Configure::read('image_edit_height'),'noimg'=>Configure::read('Path.Noimage'));
	$resizedImg = $this->ImageResize->ResizeImage($imgArr);
echo $this->Html->image($resizedImg,array('border'=>'0','id'=>'banner-image-preview'));	
		}
			?>
<img id="banner-image-preview" src="" alt="" style="display: none" />
<canvas id="canvas" height="5" width="5"></canvas>
 <input type="button" id="btnCrop" value="Crop" style="display: none" />
<input type="hidden" name="imgX1" id="imgX1" />
<input type="hidden" name="imgY1" id="imgY1" />
<input type="hidden" name="imgWidth" id="imgWidth" />
<input type="hidden" name="imgHeight" id="imgHeight" />
<input type="hidden" name="imgCropped" id="imgCropped" />								
	<?php if(!empty($this->data['Slide']['image'])) {?>
								
	 <div style="float:right;">
		<?php // if(){ ?>
	<a data-toggle="modal" data-id="banner_crop image" class="tip link-color" data-original-title="Crop image"  href="#viewDetail">Crop this image.?
			</a>
 <?php //} ?>
	<?php //echo $this->Html->link('Crop this image?',array('action'=>'image_crop',$this->data['Slide']['id'])); ?>
	</div>
		<?php } ?>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php echo $this->element('admin/forms/form_submit',array('referer_url'=>$referer_url)); ?>
<?php echo $this->Form->end();?>
<?php if(!empty($this->data['Slide']['id'])){?>
<?php echo $this->element('admin/crop',array('form_action'=>Router::url(array('plugin'=>'slide_manager','controller'=>'slides','action'=>'admin_default_image_crop',$this->data['Slide']['id'])),
								'image'=>$this->webroot.'img/slide/'.$this->data['Slide']['image'],
								'width'=>Configure::read('slide_image_width'),
								'height'=>Configure::read('slide_image_height'),
								'id'=>'viewDetail')); ?>
<?php }?>
<?php echo $this->element('admin/forms/form_validation_js',array('form_id'=>'Slide','form_validation_url'=>Router::url(array('plugin'=>'slide_manager','controller'=>'slides','action'=>'ajax_validation','json','admin'=>false)))); ?>
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
<script type="text/javascript" src="http://cdn.rawgit.com/tapmodo/Jcrop/master/js/jquery.Jcrop.min.js"></script>
<script type="text/javascript">
$(function () {
    $('#SlideImage').change(function () { 
        $('#banner-image-preview').hide();
        var reader = new FileReader();
        reader.onload = function (e) {
            $('#banner-image-preview').show();
            $('#banner-image-preview').attr("src", e.target.result);
           $('#banner-image-preview').Jcrop({
                onChange: SetCoordinates,
                onSelect: SetCoordinates
            });
        }
        reader.readAsDataURL($(this)[0].files[0]);
    });
 
    $('#btnCrop').click(function () {
        var x1 = $('#imgX1').val();
        var y1 = $('#imgY1').val();
        var width = $('#imgWidth').val();
        var height = $('#imgHeight').val();
        var canvas = $('#canvas')[0];
        var context = canvas.getContext('2d');
        var img = new Image();
          img.onload = function () {
            canvas.height = height;
            canvas.width = width;
            context.drawImage(img, x1, y1, width, height, 0, 0, width, height);
            $('#imgCropped').val(canvas.toDataURL()); 
            
        };
        img.src = $('#banner-image-preview').attr("src");
        reader.readAsDataURL(input.files[0]);
    });
});
function SetCoordinates(c) {
    $('#imgX1').val(c.x);
    $('#imgY1').val(c.y);
    $('#imgWidth').val(c.w);
    $('#imgHeight').val(c.h);
    //$('#btnCrop').show();
};
</script>
<!--
<script type="text/javascript">
function readURL(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
				$("#banner-image-preview").remove();
				$("#banner-image-showcase > div").remove();
				$("#banner-image-showcase > div").remove();
				var img = $('<img id="dynamic">'); 
				var file = input.files[0];
				canvasResize(file, {
				  width: '290',
				  height: '240',
				 crop: true,
				quality: 100,
				//rotate: 90,
				callback: function(data, width, height) {
				img.attr('src', data);
					  }
		   });
			
			img.attr('id', 'banner-image-preview');
			img.attr('width', '250');
            img.attr('height', '180');
            $('#banner-image-showcase').prepend(img);
           
        }

        reader.readAsDataURL(input.files[0]);
    }
}


$("#SlideImage").change(function(){
    readURL(this);
});

</script>
-->
