<div class="row">
	<?php 
	/* Resize Image */
	if(!empty($page_detail['Page']['banner_image'])) {
		$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$page_detail['Page']['banner_image'],'width'=>Configure::read('banner_image_width'),'height'=>Configure::read('banner_image_height'),'noimg'=>Configure::read('Path.NoImage'));
		$resizedImg = $this->ImageResize->ResizeImage($imgArr);
		echo $this->Html->image($resizedImg,array('border'=>'0'));
	}
	?>
</div>
<br />
<button type="button" class="btn btn-default" id="close" onclick="closeWindow();" data-dismiss="modal">Close</button>

<?php
$imgArr = array('source_path'=>Configure::read('Path.Banner'),'img_name'=>$page_detail['Page']['banner_image'],'width'=>Configure::read('default_banner_thumb_width'),'height'=>Configure::read('default_banner_thumb_height'),'noimg'=>Configure::read('Path.NoImage'));
$resizedImg = $this->ImageResize->ResizeImage($imgArr);
$image = $this->Html->image($resizedImg,array('border'=>'0'));
?>
<script type="text/javascript">
var added = false;
function addProduct(closeAfter) {
    if(window.opener!=null && !added) {
                window.opener.update_banner(<?php echo (int)$page_id; ?>,'<?php echo $image; ?>');
                added = true;
    }

    if(closeAfter)
    {
        closeWindow();
    }
}

function closeWindow()
{
    if (window.opener) {
        window.opener.focus();
    }
    window.close();
}

addProduct(false);
//setTimeout(closeWindow, 10000);
$(function () {
	$('.page-sidebar').addClass('mini');
	$('.page-content').addClass('condensed-layout');
	$('.footer-widget, .header-seperation').hide();
	$('.scrollup').addClass('to-edge');	
	calculateHeight();
});
</script>
        

