<?php 
		$source_path =Configure::read('Path.Slide');
		echo $this->Html->image('slide/'.$slide_image['Slide']['image'],array('id'=>'photo'));
		$sizes = getimagesize($source_path.$slide_image['Slide']['image']);
		$current_large_image_width = $sizes[0];
		$current_large_image_height = $sizes[1];
?>
	<?php echo $this->Form->create();?>
		<input type="hidden" value="" id="x1" name="start_width">
		<input type="hidden" id="w" value="" name="width">
		<input type="hidden" value="" id="y1" name="start_height">
		<input type="hidden" value="" id="h" name="height">
		<input type="hidden" value="" id="x2">
		<input type="hidden" value="" id="y2">
		<br/>
		<button class="btn btn-primary btn-lg btn-large" type="submit" id="save_thumb">Submit</button>
	<?php echo $this->Form->end();?>
<script type="text/javascript">
function preview(img, selection) { 
	var scaleX = 100 / selection.width; 
	var scaleY = 100 / selection.height; 
	
	$('#thumbnail + div > img').css({ 
		width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
		height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
		marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
		marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
	});
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height);
} 
$(document).ready(function () { 
	$('#save_thumb').click(function() {
		var x1 = $('#x1').val();
		var y1 = $('#y1').val();
		var x2 = $('#x2').val();
		var y2 = $('#y2').val();
		var w = $('#w').val();
		var h = $('#h').val();
		if(w==0 || h==0){
			alert("You must make a selection first");
			return false;
		}else{
			return true;
		}
	});
}); 
$(window).load(function () { 
	$('#photo').imgAreaSelect({ 
		maxWidth: 1304, maxHeight: 477, handles: true,
		onSelectEnd:preview 
		}); 
});
</script>
