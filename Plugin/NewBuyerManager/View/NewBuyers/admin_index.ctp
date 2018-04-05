<style type="text/css">
.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open .dropdown-toggle.btn-primary{ background:#0aa699 !important;}
</style>

<div class="row">
	<div class="btn-group1" style="float: right;">
		<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space">Export NewBuyer List 
			<span class="caret"></span> 
		</a>
		<ul class="dropdown-menu">
			<li>
				<a href="<?=Router::url(array('controller' => 'new_buyers', 'action' => 'create_pdf'));?>" target="_blank">As PDF File</a>
			</li>
			<li class="divider"></li>
			<li>
				<a href="<?=Router::url(array('controller' => 'new_buyers', 'action' => 'export'));?>" target="_blank">As Excel File</a>
			</li>
		</ul>
	</div>
	<div style="clear:both;"></div>
	<!--<div class="export-btn">
	<?php //echo $this->Html->link('Export New Buyer List',array('controller'=>'new_buyers','action'=>'export'), array('target'=>'_blank','class'=>'btn btn-primary btn-sm right'));?>
	</div>-->
	<div class="col-md-12">
		<div class="btn-group">
			<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space"> Select action 
				<span class="caret"></span> 
			</a>
			<ul class="dropdown-menu">
				<li>
					<a href="javascript:" onClick="return formsubmit('Publish');">Activate</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="javascript:" onClick="return formsubmit('Unpublish');">Disable</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="javascript:" onClick="return formsubmit('Delete');">Delete</a>
				</li>
			</ul>
		</div>
		<?php //echo $this->Html->link('Add New Buyer', array('controller' => 'new_buyers', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
		<form method="post" style="display:inline;">
		<div class="col-md-1 right-col">
			<button type="submit" class="btn btn-primary btn-sm">
					<i class="fa fa-search"></i>
				</button>
		</div>
		<div class="col-md-2 right-col">
			<?php 
					echo $this->Form->input('limit',array('options'=>array('20'=>20,'50'=>50,'100'=>100,'999'=>'All'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%','empty'=>'Results: 10','value'=>$limit));
				?>
			</div>
		<div class="col-md-2 right-col">
				<input type="text" class="form-control" name="search" value="<?=$search?>" placeholder="Search" id="search">
		</div>
	
		</form>
		<div style="clear:both;"></div>
	</div>
</div>
<?php echo $this->element('admin/message'); ?>
<?=$this->Form->create('NewBuyer', array('name' => 'new_buyer', 'action' => 'delete', 'id' => 'NewBuyerDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
<?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<?=$this->Form->hidden('redirect', array('value' => $url)); ?>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border " style="padding: 0px 15px 0px;"></div>
			<div class="grid-body no-border">
				<div style="margin-top:5px;"></div>
				<table class="table table-bordered no-more-tables">
					<thead style="background-color: #d0d0d0;">
						<tr>
							<th style="width:3%">
								<div class="checkbox">
									<?= $this->Form->input('check', array('type'=>'checkbox','value' => 1, 'onchange' => "CheckAll(this.value)",'label'=>'')); ?>
								</div>
							</th>
							<th class="h-align-center" style="width:10%">S.No.</th>
							<?php
							$class = '';
							if($this->Paginator->sortKey('NewBuyer')=='org_name'){
								if($this->Paginator->sortDir('NewBuyer')=='asc'){
									$class = 'sorting_asc';
								}else{
									$class = 'sorting_desc';
								}
							}
							?>
							<th class="tip sortable <?php echo $class; ?>  h-align-center" style="width:15%">
								<?php echo $this->Paginator->sort('org_name','Organisation',array('escape' => false,'class'=>'tip','title'=>'Click to arrange in ascending and descending order','data-toggle'=>'tooltip','data-placement'=>'top'));?>
							</th>
							<?php
							$class = '';
							if($this->Paginator->sortKey('NewBuyer')=='first_name'){
								if($this->Paginator->sortDir('NewBuyer')=='asc'){
									$class = 'sorting_asc';
								}else{
									$class = 'sorting_desc';
								}
							}
							?>
							<th class="tip sortable <?php echo $class; ?>  h-align-center" style="width:10%">
								<?php echo $this->Paginator->sort('first_name','Name',array('escape' => false,'class'=>'tip','title'=>'Click to arrange in ascending and descending order','data-toggle'=>'tooltip','data-placement'=>'top'));?>
							</th>
							<?php
							$class = '';
							if($this->Paginator->sortKey('NewBuyer')=='email_id'){
								if($this->Paginator->sortDir('NewBuyer')=='asc'){
									$class = 'sorting_asc';
								}else{
									$class = 'sorting_desc';
								}
							}
							?>
							<th class="tip sortable <?php echo $class; ?>  h-align-center" style="width:20%">
								<?php echo $this->Paginator->sort('email_id','Email',array('escape' => false,'class'=>'tip','title'=>'Click to arrange in ascending and descending order','data-toggle'=>'tooltip','data-placement'=>'top'));?>
							</th>
							<th class="h-align-center" style="width:10%">Designation</th>
							<th class="h-align-center" style="width:10%">Status</th>
							<th class="h-align-center" style="width:15%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($new_buyers)){ ?>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($new_buyers as $new_buyer) {
							$j=$i;	
						?>
						<tr id="sort_<?= $new_buyer['NewBuyer']['id'] ?>" style="">
							<td class="v-align-middle">
								<div class="checkbox">
								<?php echo $this->Form->input('NewBuyer.id.'.$i, array('type'=>'checkbox','value' => $new_buyer['NewBuyer']['id'])); ?>
								</div>
							</td>
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle">
								<span class="muted">
							<?php echo  $new_buyer['NewBuyer']['org_name'];?>
					
								</span></td>
							<td class="v-align-middle  h-align-center">
								<span class="muted">
								<?php echo  $new_buyer['NewBuyer']['first_name'];?>
								</span></td>
							<td class="v-align-middle  h-align-center">
							<span class="muted">
							<?php echo  $new_buyer['NewBuyer']['email_id'];?>
							</span></td>
							<td class="v-align-middle  h-align-center">
							<span class="muted">
							<?php echo  $new_buyer['NewBuyer']['designation'];?>
							</span></td>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($new_buyer['NewBuyer']['status'] == '1'){
											//echo"<i class='fa fa-check tip link-color' title='Publish' data-toggle='tooltip' data-original-title='Publish'></i>";
											echo "Active";
									}
									else if ($new_buyer['NewBuyer']['status'] == '2'){
											//echo "<i class='fa fa-save link-color' title='Draft' data-toggle='tooltip' data-original-title='Draft'></i>";
											echo "Blocked";
									}else{
											//echo "<i class='fa fa-times link-color' title='Unpublish' data-toggle='tooltip' data-original-title='Unpublish'></i>";
											echo "Disabled";
									}
								?>
								</span>
							</td>
							<td class="v-align-middle h-align-center">
								<a href="<?=Router::url(array('controller' => 'new_buyers', 'action' => 'add',  $new_buyer['NewBuyer']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
								<i class="fa fa-edit"></i></a>
								<a data-toggle="modal" data-id="<?=$new_buyer['NewBuyer']['id'];?>" class="tip link-color preview" data-original-title="View"  href="#viewDetail_<?=$j;?>">
								<i class="fa fa-eye"></i></a>
							</td>
						</tr>
						
						<div class="modal fade" id="viewDetail_<?=$j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
							<div class="modal-dialog">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
										<br>
										<i class="icon-credit-card icon-7x"></i>
										<h4 id="myModalLabel" class="semi-bold">
											<?=$new_buyer['NewBuyer']['org_name']?>
										</h4>
										<br>
									</div>
									<!--start page content here for popup-->
									<div class="modal-body">
										<div class="row form-row">
											<div class="col-md-6">
												<b>Contact Person</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['contact_person'];?>
											</div>		
										</div>
										<div class="row form-row">
											<div class="col-md-6">
												<b>Title</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['title'];?>
											</div>		
										</div>
										
										<div class="row form-row">
											<div class="col-md-6">
												<b>Full Name</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['first_name'].' '.$new_buyer['NewBuyer']['middle_name'].' '.$new_buyer['NewBuyer']['last_name'];?>
											</div>		
										</div>
										<div class="row form-row">
											<div class="col-md-6">
												<b>Email</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['email_id']?>
											</div>		
										</div>
										
										<div class="row form-row">
											<div class="col-md-6">
												<b>Contact Number</b>
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['contact_number']?>
											</div>
										</div>
										<div class="row form-row">
											<div class="col-md-6"><b>Designation</b></div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['designation']?>
											</div>
										</div>
										
										<hr>
										
										<div class="row form-row">
											<div class="col-md-6">
												<b>Second Contact Person</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['s_contact_person'];?>
											</div>		
										</div>
										<div class="row form-row">
											<div class="col-md-6">
												<b>Title</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['s_title'];?>
											</div>		
										</div>
										
										<div class="row form-row">
											<div class="col-md-6">
												<b>Full Name</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['s_first_name'].' '.$new_buyer['NewBuyer']['s_middle_name'].' '.$new_buyer['NewBuyer']['s_last_name'];?>
											</div>		
										</div>
										<div class="row form-row">
											<div class="col-md-6">
												<b>Email</b>	
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['s_email']?>
											</div>		
										</div>
										
										<div class="row form-row">
											<div class="col-md-6">
												<b>Contact Number</b>
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['s_contact_number']?>
											</div>
										</div>
										<div class="row form-row">
											<div class="col-md-6"><b>Designation</b></div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['s_designation']?>
											</div>
										</div>
										
										<hr>
										
										<div class="row form-row">
											<div class="col-md-6">
												<b>State/Province</b>
											</div>
											<div class="col-md-6">
												<?=$new_buyer['NewBuyer']['state']?>
											</div>
										</div>										
										<div class="row form-row">
											<div class="col-md-6">
												<b>Country</b>
											</div>
											<div class="col-md-6">
												<?=$new_buyer['Country']['country_name']?>
											</div>
										</div>
										<div class="row form-row">
										<div class="col-md-6">
											<b>Required Number of Feedback</b>
										</div>
										<div class="col-md-6">
											<?=$new_buyer['NewBuyer']['required_feedback']?>
										</div>
									</div>
															</div>
									<div class="modal-footer">
										<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
									</div>							
								</div>							
							</div>						
							</div>					
						<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$new_buyers) { ?>
				<div style='color:#FF0000;text-align:center;'>No Record Found</div>
				<?php }else{ ?>
				<!--
				<div style='text-align:center;'>
					<button class="btn btn-success btn-cons" id="load-more-button" type="button" style="display:none;">Load more...</button>
				</div>
				-->
					<div class="row">
						<div class="col-md-12">
							<div class="dataTables_paginate paging_bootstrap pagination">
								
							<?php if($this->Paginator->hasPrev()){?>
							<span class="pagin btn btn-white">
							<?=$this->Paginator->prev('<i class="fa fa-chevron-left"></i> ',array('escape' => false, 'disabledTag' => 'a'));?>
							</span>
							<?php } ?>
							
							<?=$this->Paginator->numbers(array('modulus'=>6,'type'=>'button','separator' => '','class'=>'pagin btn btn-white ','currentClass' => 'active')); ?>

							<?php if($this->Paginator->hasNext()){?>
							<span class="pagin btn btn-white">
							<?=$this->Paginator->next('<i class="fa fa-chevron-right"></i>',array('escape' => false, 'disabledTag' => 'a'));?>
							</span>
							<?php } ?>
							</div>
							
					</div>
					
				<?php }?>
			</div>
		</div>
	</div>
</div>
<?php echo $this->Form->end(); ?>

<script type="text/javascript">
    function formsubmit(action)
    {
        var flag = true;
        if (flag)
        {
            document.getElementById('action').value = action;
            if (validate())
                document.getElementById('NewBuyerDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.new_buyer.elements.length; i++) {
            if (document.new_buyer.elements[i].type == "checkbox") {
                if (document.new_buyer.elements[i].checked) {
                    ans = "1";
                    break;
                }
            }
        }
        if (ans == "0") {
            alert("Please select new_buyer(s) to " + (document.getElementById('action').value).toLowerCase()+".");
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value + ' new_buyer(s)');
            if (!answer)
                return false;
        }
        return true;
    }


    function CheckAll(chk)
    {
        var fmobj = document.getElementById('NewBuyerDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
                fmobj.elements[i].checked = document.getElementById('NewBuyerCheck').checked;
        }

    }
    
</script>
<script type="text/javascript">
	$(document).ready(function(){
        $( "tbody" ).sortable({
			//placeholder: "ui-state-highlight",
			opacity: 0.6,
            update: function(event, ui) {
                var info = $(this).sortable("serialize");
                $.ajax({
                    type: "POST",
                    url: "<?php echo Router::url(array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'ajax_sort','admin'=>false)); ?>",
                    data: info,
                    context: document.body,
                    success: function(){
                        
                       // alert("cool");
                    }
              });
            }
        });
        $( "tbody" ).disableSelection();         
    });
</script>
<script type="text/javascript">
		$(document).ready(function(){
				$('#search').focus(function(){
				$(this).data('placeholder',$(this).attr('placeholder'))
				$(this).attr('placeholder','');
			});
				$('#search').blur(function(){
					$(this).attr('placeholder',$(this).data('placeholder'));
				});
		});
</script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('th.sortable').hover(function(){
		if(!jQuery(this).hasClass('sorting_asc') && !jQuery(this).hasClass('sorting_desc')){
			jQuery(this).addClass('tmp sorting_asc');
		}
		if(jQuery(this).hasClass('sorting_asc') && !jQuery(this).hasClass('tmp sorting_asc')){
			jQuery(this).addClass('tmp sorting_desc');
		}
		if(jQuery(this).hasClass('sorting_desc') && !jQuery(this).hasClass('tmp sorting_desc')){
			jQuery(this).addClass('tmp sorting_asc');
		}
		
		
	},function(){
		if(jQuery(this).hasClass('tmp sorting_asc')){
			jQuery(this).removeClass('tmp sorting_asc');
		}
		if(jQuery(this).hasClass('tmp sorting_desc')){
			jQuery(this).removeClass('tmp sorting_desc');
		}
	});
	});
</script>
