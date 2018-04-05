<?php $i = 1;if(!empty($pages)){ ?>
<table class="table no-more-tables table-striped">
	<thead style="background-color: #d0d0d0;">
		<tr>
			<th style="width:9%">SNo.</th>
			<th style="width:22%">Page Name</th>
			<th style="width:10%">Action</th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($pages as $page) { ?> 
		<tr>
			<td class="v-align-middle"><?=$i++; ?></td>
			<td class="v-align-middle"><span class="muted"><?php echo $page['Page']['name']; ?></span></td>
			<td class="v-align-middle">
			<a href="<?=Router::url(array('plugin'=>'content_manager','controller' => 'pages', 'action' => 'add', $page['Page']['parent_id'], $page['Page']['id'],'admin'=>true));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
			<i class="fa fa-edit"></i></a>
			</td>
		</tr>
	<?php } ?>
	</tbody>
</table>
<?php } else{ ?>
<div style='color:#FF0000;text-align:center;'>No Content Found</div>
<?php } ?>                

<?php if($i > 5) {?> 
<div class=view-all-right>
<?php echo $this->Html->link('View all', array('plugin'=>'content_manager','controller'=>'pages', 'action' => 'admin_index','admin'=>true), array('escape' => false));?>  
</div>
<?php }?>
