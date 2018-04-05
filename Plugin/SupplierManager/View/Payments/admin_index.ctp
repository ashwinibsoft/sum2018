<div class="row">
	<div class="col-md-12">
		<div class="btn-group">
			<a href="#" data-toggle="dropdown" class="btn btn-primary dropdown-toggle btn-demo-space"> Select action 
				<span class="caret"></span> 
			</a>
			<ul class="dropdown-menu">
				<li>
					<a href="javascript:" onClick="return formsubmit('Delete');">Delete</a>
				</li>
			</ul>
		</div>
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
<?=$this->Form->create('Payment', array('name' => 'payment', 'action' => 'delete', 'id' => 'PaymentDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
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
							<th class="h-align-center" style="width:10%">Transaction ID</th>
							<th class="h-align-center" style="width:10%">Amount</th>
							<th class="h-align-center" style="width:10%">Item</th>
							<th class="h-align-center" style="width:10%">Quantity</th>
							<th class="h-align-center" style="width:10%">Supplier</th>
							<th class="h-align-center" style="width:10%">Payment Date</th>
							<th class="h-align-center" style="width:10%">Status</th>
							<th class="h-align-center" style="width:10%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($payments)){ ?>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($payments as $payment) {
							$j=$i;	
							//print_r($payment); die;
						?>
						<tr id="sort_<?= $payment['Payment']['id'] ?>" style="">
							<td class="v-align-middle">
								<div class="checkbox">
								<?php echo $this->Form->input('Payment.id.'.$i, array('type'=>'checkbox','value' => $payment['Payment']['id'])); ?>
								</div>
							</td>
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle h-align-center"><?=$payment['Payment']['txn_id']; ?></td>
							<td class="v-align-middle h-align-center"><?='€'.$payment['Payment']['amount']; ?></td>
							<td class="v-align-middle h-align-center"><?=$payment['Payment']['item_name']; ?></td>
							<td class="v-align-middle h-align-center"><?=$payment['Payment']['quantity']; ?></td>
							<td class="v-align-middle h-align-center"><?=$payment['Supplier']['title']." ".$payment['Supplier']['first_name']." ".$payment['Supplier']['middle_name']." ".$payment['Supplier']['last_name']; ?></td>
							<td class="v-align-middle h-align-center">
							<?php echo date("d, F Y h:i:s A",strtotime($payment['Payment']['created_date'])); ?>
							</td>
							<td class="v-align-middle h-align-center">
								<span class="muted">
								<?=$payment['Payment']['payment_status']; ?>
								</span>
							</td>
							<td class="v-align-middle h-align-center">
				
								<a href="<?=Router::url(array('controller' => 'payments', 'action' => 'view',  $payment['Payment']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="View">
								<i class="fa fa-eye"></i></a>
							</td>
						</tr>
						<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$payments) { ?>
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

<script type="text/javascript">
    function formsubmit(action)
    {
        var flag = true;
        if (flag)
        {
            document.getElementById('action').value = action;
            if (validate())
                document.getElementById('PaymentDeleteForm').submit();
        }
    }

    function validate() {
        var ans = "0";
        for (i = 0; i < document.payment.elements.length; i++) {
            if (document.payment.elements[i].type == "checkbox") {
                if (document.payment.elements[i].checked) {
                    ans = "1";
                    break;
                }
            }
        }
        if (ans == "0") {
            alert("Please select payment(s) to " + (document.getElementById('action').value).toLowerCase()+".");
            return false;
        } else {
            var answer = confirm('Are you sure you want to ' + document.getElementById('action').value + ' payment(s)');
            if (!answer)
                return false;
        }
        return true;
    }


    function CheckAll(chk)
    {
        var fmobj = document.getElementById('PaymentDeleteForm');
        for (var i = 0; i < fmobj.elements.length; i++)
        {
            var e = fmobj.elements[i];
            if (e.type == 'checkbox')
                fmobj.elements[i].checked = document.getElementById('PaymentCheck').checked;
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
