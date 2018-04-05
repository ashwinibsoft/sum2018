<div class="grid simple horizontal green no-margin-grid">
	<div class="grid-title no-border">
	<h4>Featured image 
	<span style="" class="tip" data-toggle="tooltip" title="for image upload." data-placement="right"><i class="fa fa-question-circle"></i></span>
	</h4>
		<div class="tools"> 
			<a class="collapse" href="javascript:;"></a>
		</div>
	</div>
	<div class="grid-body no-border">
		<div class="controls">
		<?php echo $this->Form->file('featureimage',array('required'=> false, 'style'=>'border:0;')); ?>
		<?=$this->Form->error('featureimage',null,array('wrap' => 'span', 'class' => 'error')); ?>
		<span style="color:red;"></span> <span class="help">(Only png, gif, jpg, jpeg images are allowed.)</span>
		<div id="banner-image-showcase1"  style="padding-top: 19px;overflow:hidden;">
		<?php 
			/* Resize Image */
			if(!empty($this->data['Page']['featureimage']) && file_exists(Configure::read('Path.Blog').$this->data['Page']['featureimage'])) {
				$imgArr = array('source_path'=>Configure::read('Path.Blog'),'img_name'=>$this->data['Page']['featureimage'],'width'=>320,'height'=>240,'noimg'=>Configure::read('Path.NoImage'));
				$resizedImg = $this->ImageResize->ResizeImage($imgArr);
				echo $this->Html->image($resizedImg,array('border'=>'0','id'=>'feature-image-preview'));
			}
		?>
		
		</div>
	</div>
</div> 
</div>
<script type="text/javascript">
function readURL1(input) {

    if (input.files && input.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
				$("#feature-image-preview").remove();
				$("#banner-image-showcase1 > div").remove();
				$("#banner-image-showcase1 > div").remove();
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
			
			img.attr('id', 'feature-image-preview');
			img.attr('width', '240');
            img.attr('height', '160');
            $('#banner-image-showcase1').prepend(img);
           
        }

        reader.readAsDataURL(input.files[0]);
    }
}


$("#PageFeatureimage").change(function(){
    readURL1(this);
});

</script>
