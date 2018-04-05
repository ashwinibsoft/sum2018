<div class="row">
	<?php echo $this->Html->image(Configure::read('Folder.Banner').DS.$page_detail,array('id'=>'photo')); ?>
	<p class="instructions">Click and drag on the image to select an area.</p>
</div>
<div class="row">
<?php echo $this->Form->create();?>
<input type="hidden" id="w" value="-" name="width">
<input type="hidden" id="w" value="banner_image" name="key">
<input type="hidden" value="-" id="x1" name="start_width">
<input type="hidden" value="-" id="y1" name="start_height">
<input type="hidden" value="-" id="h" name="height">
<input type="hidden" value="-" id="x2">
<input type="hidden" value="-" id="y2">
<button class="btn btn-primary btn-lg btn-large" type="submit">Submit</button>
<?php echo $this->Form->end();?>
</div>
<script type="text/javascript">
function preview(img, selection) {
	if (!selection.width || !selection.height)
	return;
	
	$('#x1').val(selection.x1);
	$('#y1').val(selection.y1);
	$('#x2').val(selection.x2);
	$('#y2').val(selection.y2);
	$('#w').val(selection.width);
	$('#h').val(selection.height); 
}
$(function () {
	$('#photo').imgAreaSelect({  handles: true,fadeSpeed: 200,onSelectChange: preview });
	$('.page-sidebar').addClass('mini');
	$('.page-content').addClass('condensed-layout');
	$('.footer-widget, .header-seperation').hide();
	$('.scrollup').addClass('to-edge');	
	calculateHeight();
});
</script>
