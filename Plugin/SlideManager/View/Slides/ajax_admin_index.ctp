	<?php
		$i = $this->Paginator->counter('{:start}');
		foreach ($slides as $slide) {
		$j=$i;	
	?> 
	<tr  id="sort_<?= $slide['Slide']['id'] ?>" style="cursor:move;">
		<td class="v-align-middle" style="width:1%">
		<div class="checkbox">
			<?php echo $this->Form->input('Slide.id.'.$i, array('type'=>'checkbox','value' => $slide['Slide']['id'])); ?>
		</div>
		</td>
		<td class="v-align-middle" width="6%;"><?=$i++;?></td>
		<td class="v-align-middle"  width="10%;"><span class="muted"> 
		 <?php 
		/* Resize Image */ 
		if(isset($slide['Slide']['image'])) {
			$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$slide['Slide']['image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
			$resizedImg = $this->ImageResize->ResizeImage($imgArr);
			
			echo $this->Html->image($resizedImg);
		}
		?>
		</span></td>
		<td class="v-align-middle"  width="30%;"><span class="muted"><?php echo $slide['Slide']['name']; ?></span></td>
		<td><span class="muted"  width="5%;">
		<?php
			if ($slide['Slide']['status'] == '1')
				echo"<i class='fa fa-check'></i>";
			else
				echo "<i class='fa fa-times'></i>";
		?>
		</span></td>
								
		<td class="v-align-middle"  width="45%;">

		<a href="<?=Router::url(array('controller' => 'slides', 'action' => 'add', $slide['Slide']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
		<i class="fa fa-edit"></i></a>
			
		<a data-toggle="modal" data-id="<?=$slide['Slide']['id'];?>" class="tip link-color" data-original-title="View"  href="#viewDetail_<?=$j;?>">
		<i class="fa fa-eye"></i></a>
		
		
		<!-- Modal -->
		<div class="modal fade" id="viewDetail_<?=$j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
					<br>
					<i class="icon-credit-card icon-7x"></i>
					<h4 id="myModalLabel" class="semi-bold"><?=$slide['Slide']['name']?></h4>
					<br>
				</div>
				<!--start page content here for popup-->
				<div class="modal-body">
					<div class="row form-row">
						<div class="col-md-6">
							<b>Slide Name</b>	
							<?=$slide['Slide']['name']?>													
						</div>
						<div class="col-md-6">
							<b>Slide Title</b>
							<?=$slide['Slide']['title']?>
						  
						</div>
					</div>
				
				<div class="row form-row">
					<div class="col-md-12">
						<b>Description</b>
						<?=$slide['Slide']['description']?>
						
					</div>
				</div>
				<div class="row form-row">
					<div class="col-md-12">
						<b>Slide Image</b>
						<?php 
						/* Resize Image */ 
						if(isset($slide['Slide']['image'])) {
							$imgArr = array('source_path'=>Configure::read('Path.Slide'),'img_name'=>$slide['Slide']['image'],'width'=>Configure::read('image_list_width'),'height'=>Configure::read('image_list_height'),'noimg'=>Configure::read('Path.NoImage'));
							$resizedImg = $this->ImageResize->ResizeImage($imgArr);
							
							echo $this->Html->image($resizedImg);
						}
						?>
					</div>
				</div>	
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
		</td>
	</tr>
	<?php }?>
