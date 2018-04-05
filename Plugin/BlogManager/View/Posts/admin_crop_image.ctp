<div class="row">
	<div class="" style="overflow:scroll;">
		<div style="margin: 0 0.3em; " class="frame">
			<!--<img src="<?php echo Router::url('/img/banner/')?><?php echo $page_detail['Page']['banner_image'];?>" id="photo" height="auto">-->
			<?php echo $this->Html->image(Configure::read('Folder.Banner').DS.$page_detail['Page']['banner_image'],array('id'=>'photo')); ?>
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
	<p style="font-size: 110%; font-weight: bold; padding-left: 0.1em;"> </p>	  
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


	
