<div class="contact_box2">
    <div class="left_text2">
		<?php echo $this->ShortLink->show($page['Page']['page_longdescription']);?>	
    </div>
    <div class="right_img3">
		<?php echo $this->element('ContentManager.online_tax'); ?>
    </div>
</div>
<div class="clear"></div>

<?php echo $this->Html->script('jquery-ui.js'); ?>
<script type="text/javascript">
$( "#accordion" ).accordion();
$(function() {
	$( "#datepicker" ).datepicker();
	$( "#datepicker1" ).datepicker();
	$( "#datepicker2" ).datepicker();
	$( "#datepicker3" ).datepicker();
	$( "#datepicker4" ).datepicker();
	$( "#datepicker5" ).datepicker();
});
</script>
