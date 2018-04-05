<?php $i =1;if(!empty($slides)){ ?>
<table class="table no-more-tables table-striped">
	<thead style="background-color: #d0d0d0;">
		<tr>
			<th style="width:9%">SNo.</th>
			<th style="width:22%">Image</th>
			<th style="width:10%">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($slides as $slide) {?> 
		<tr>
			<td class="v-align-middle"><?=$i++; ?></td>
			<td class="v-align-middle"><span class="muted">
			 <?php 
			/* Resize Image */ 
			if(isset($slide['Slide']['image'])) {
				$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$slide['Slide']['image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
				$resizedImg = $this->ImageResize->ResizeImage($imgArr);
				
				echo $this->Html->image($resizedImg);
			}
			?>
			</span></td>
			<td class="v-align-middle">
			<a href="<?=Router::url(array('plugin'=>'slide_manager','controller' => 'slides', 'action' => 'add', $slide['Slide']['id'],'admin'=>true));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
			<i class="fa fa-edit"></i></a>
			</td>
		</tr>
		<?php }?>
	</tbody>
</table>
<?php } else { ?>
<div style='color:#FF0000;text-align:center;'>No Slide Found</div>
<?php } ?>          

<?php if($i > 5) {?>
<div class=view-all-right>
<?php echo $this->Html->link('View all', array('plugin'=>'slide_manager','controller'=>'slides', 'action' => 'index','admin'=>true), array('escape' => false));?>      
</div>
<?php } ?> 
