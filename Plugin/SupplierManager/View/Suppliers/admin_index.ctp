<style type="text/css">
.btn-primary:hover, .btn-primary:focus, .btn-primary:active, .btn-primary.active, .open .dropdown-toggle.btn-primary{ background:#0aa699 !important;}
</style>
<div class="row">
	
	<div style="clear:both;"></div>
	<!--<div class="export-btn">
	<?php //echo $this->Html->link('Export Suppliers List',array('controller'=>'suppliers','action'=>'export'), array('target'=>'_blank','class'=>'btn btn-primary btn-sm right'));?>
	</div>-->
	<div class="col-md-12">
		
		<div class="btn-group" style="float: left;margin-right:10px;">
			<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space"> Select action 
				<span class="caret"></span> 
			</a>
			<ul class="dropdown-menu">
				<li>
					<a href="javascript:" onClick="return formsubmit('Activate');">Activate</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="javascript:" onClick="return formsubmit('Disable');">Disable</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="javascript:" onClick="return formsubmit('Delete');">Delete</a>
				</li>
			</ul>
		</div>
		<div class="btn-group1" style="float: left;">
			<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space">Export Supplier List 
				<span class="caret"></span> 
			</a>
			<ul class="dropdown-menu">
				<li>
					<a href="<?=Router::url(array('controller' => 'suppliers', 'action' => 'create_pdf'));?>" target="_blank">As PDF File</a>
				</li>
				<li class="divider"></li>
				<li>
					<a href="<?=Router::url(array('controller' => 'suppliers', 'action' => 'export'));?>" target="_blank">As Excel File</a>
				</li>
			</ul>
		</div>
		<?php //echo $this->Html->link('New', array('controller' => 'suppliers', 'action' => 'add'), array('escape' => false, 'class' => 'btn btn-primary btn-cons')); ?>
		<form method="post" style="display:inline;">
		<div class="col-md-1 right-col">
			<button type="submit" class="btn btn-primary btn-sm">
					<i class="fa fa-search"></i>
				</button>
		</div>
		<div class="col-md-2 right-col">
			<?php 
				echo $this->Form->input('limit',array('options'=>array('20'=>20,'50'=>50,'100'=>100,'999'=>'All'),'label' => false,'div'=>false,'class'=>'simple-dropdown','style'=>'width:100%','empty'=>'Results:10','value'=>$limit));
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
<?=$this->Form->create('Supplier', array('name' => 'supplier', 'action' => 'delete', 'id' => 'SupplierDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
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
							<th class="h-align-center" style="width:10%">Name</th>
							<th class="h-align-center" style="width:10%">Email ID</th>
							<th class="h-align-center" style="width:10%">Country</th>
							<th class="h-align-center" style="width:10%">Receive Info Material</th>
							<th class="h-align-center" style="width:10%">Status</th>
							<th class="h-align-center" style="width:10%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($suppliers)){ ?>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($suppliers as $supplier) {
							$j=$i;	
							//print_r($supplier); die;
						?>
						<tr id="sort_<?= $supplier['Supplier']['id'] ?>" style="">
							<td class="v-align-middle">
								<div class="checkbox">
								<?php echo $this->Form->input('Supplier.id.'.$i, array('type'=>'checkbox','value' => $supplier['Supplier']['id'])); ?>
								</div>
							</td>
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle h-align-center"><?=$supplier['Supplier']['title']." ".$supplier['Supplier']['first_name']." ".$supplier['Supplier']['middle_name']." ".$supplier['Supplier']['last_name']; ?></td>
							<td class="v-align-middle h-align-center"><?=$supplier['Supplier']['email_id']; ?></td>
							<td class="v-align-middle h-align-center"><?=$supplier['Country']['country_name']; ?></td>
							
							<td class="v-align-middle h-align-center">
								<?php
									if ($supplier['Supplier']['receive_info'] == '1'){
											echo "Yes";
									} else {
										
										echo "No";
									}
								
							?>	
								
							</td>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?php
									if ($supplier['Supplier']['status'] == '1'){
											//echo"<i class='fa fa-check tip link-color' title='Publish' data-toggle='tooltip' data-original-title='Publish'></i>";
											echo "Active";
									}
									else if ($supplier['Supplier']['status'] == '2'){
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
								<a href="<?=Router::url(array('controller' => 'suppliers', 'action' => 'add',  $supplier['Supplier']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
								<i class="fa fa-edit"></i></a>
								<a data-toggle="modal" data-id="<?=$supplier['Supplier']['id'];?>" class="tip link-color preview" data-original-title="View"  href="#viewDetail_<?=$j;?>">
								<i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$suppliers) { ?>
				<div style='color:#FF0000;text-align:center;'>No Record Found</div>
				<?php }else{ ?>
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
<?php $i = $this->Paginator->counter('{:start}'); ?>
<?php foreach ($suppliers as $supplier) { ?>
<?php  $j = $i++; ?>	
<!-- Modal -->
<div class="modal fade" id="viewDetail_<?=$j;?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
	<div class="modal-content">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
			<br>
			<i class="icon-credit-card icon-7x"></i>
			<h4 id="myModalLabel" class="semi-bold"><?=$supplier['Supplier']['title']." ".$supplier['Supplier']['first_name']." ".$supplier['Supplier']['middle_name']." ".$supplier['Supplier']['last_name']?></h4>
			<br>
		</div>
		<!--start page content here for popup-->
		<div class="modal-body">
			<div class="row form-row">
				<div class="col-md-2">
					<b>Email</b>	
				</div>
				<div class="col-md-10">
					<?=$supplier['Supplier']['email_id']?>
				</div>		
			</div>
			<div class="row form-row">
				<div class="col-md-2">
					<b>Address</b>
				</div>
				<div class="col-md-10">
					<?=$supplier['Supplier']['address1']?>
				</div>
			</div>
			<div class="row form-row">
				<div class="col-md-2">
				</div>
				<div class="col-md-10">
					<?=$supplier['Supplier']['address2']?>
				</div>
			</div>
			<div class="row form-row">
				<div class="col-md-2">
					<b>City</b>
				</div>
				<div class="col-md-3">
					<?=$supplier['Supplier']['city']?>
				</div>
			</div>
			<div class="row form-row">
				<div class="col-md-2">
					<b>State/Province</b>
				</div>
				<div class="col-md-3">
					<?=$supplier['Supplier']['state']?>
				</div>
			</div>
			<div class="row form-row">
				<div class="col-md-2">
					<b>Zip Code</b>
				</div>
				<div class="col-md-3">
					<?=$supplier['Supplier']['zipcode']?>
				</div>
			</div>
			<div class="row form-row">
				<div class="col-md-2">
					<b>Country</b>
				</div>
				<div class="col-md-3">
					<?=$supplier['Country']['country_name']?>
				</div>
			</div>
			<?php /*
            <div class="row form-row">
				<div class="col-md-2">
					<b>Category</b>
				</div>
				<div class="col-md-3">
				<?php $category = Configure::read('Category');
				echo isset($category[$supplier['SupplierBuyer']['category']])?$category[$supplier['SupplierBuyer']['category']]:'Not Avilable'; ?>
				</div>
			</div>
			*/?>
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

<?php } ?>

<script type="text/javascript">
    function formsubmit(action)
    {
        var flag = true;
        if (flag)
        {
            document.getElementById('action').value = action;
            if (validate())
                document.getElementById('SupplierDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.supplier.elements.length; i++) {
            if (document.supplier.elements[i].type == "checkbox") {
                if (document.supplier.elements[i].checked) {
                    ans = "1";
                    break;
                }
            }
        }
        if (ans == "0") {
            alert("Please select supplier(s) to " + (document.getElementById('action').value).toLowerCase()+".");
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value + ' supplier(s)');
            if (!answer)
                return false;
        }
        return true;
    }


    function CheckAll(chk)
    {
        var fmobj = document.getElementById('SupplierDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
                fmobj.elements[i].checked = document.getElementById('SupplierCheck').checked;
        }

    }
    
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
