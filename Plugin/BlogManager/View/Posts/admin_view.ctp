<div class="modal-body">
<div class="row form-row">
	<div class="col-md-6">
		<b>Name: </b>	
		<?=$page['Page']['name']?>													
	</div>
	<div class="col-md-6">
		<b>SEO Title: </b>
		<?=$page['Page']['page_title']?>
	</div>
</div>
<div class="row form-row">
	<div class="col-md-6">
	<b>Slug URL: </b>
	<?=$page['Page']['url_key']?>
	</div>
	<div class="col-md-6">
	<b>Sub Pages: </b>
	<?php
		if($page['Page']['sub_page']==1){
			$subpage='Yes';
		}else{
			$subpage='No';
		}
	?>
	<?=$subpage;?>

	</div>
</div>
<div class="row form-row">
	<div class="col-md-12">
	<b>Meta Keyword: </b>
	<?=$page['Page']['page_metakeyword']?>
	</div>
</div>
<div class="row form-row">
	<div class="col-md-12">
	<b>Meta Description: </b>
	<?=$page['Page']['page_metadescription']?>

	</div>
</div>
</div>
<div class="modal-footer">
	<button type="button" class="btn btn-default" data-dismiss="modal" id="close">Close</button>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		

		});
</script>
