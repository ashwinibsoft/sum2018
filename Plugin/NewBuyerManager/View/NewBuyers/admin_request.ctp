<?php echo $this->element('admin/message'); ?>
<?=$this->Form->create('NewBuyer', array('name' => '', 'action' => 'delete', 'id' => 'NewBuyerDeleteForm', 'onSubmit' => 'return validate(this)', 'class' => 'table-form')); ?>
<?=$this->Form->hidden('action', array('id' => 'action', 'value' => '')); ?>
<div class="row">
	<div class="col-md-12">
		<div class="grid simple horizontal green no-margin-grid">
			<div class="grid-title no-border " style="padding: 0px 15px 0px;"></div>
			<div class="grid-body no-border">
				<div style="margin-top:5px;"></div>
				<table class="table table-bordered no-more-tables">
					<thead style="background-color: #d0d0d0;">
						<tr>
							<th class="h-align-center" style="width:10%">S.No.</th>
							<th class="h-align-center" style="width:10%">Organisation Request</th>
							<th class="h-align-center" style="width:10%">Email Id</th>
							<th class="h-align-center" style="width:15%">Action</th>
						</tr>
					</thead>
					<?php if(!empty($requests)){ ?>
					<tbody>
						<?php
							$i = $this->Paginator->counter('{:start}');
							foreach ($requests as $request) {
						?>
						<tr id="sort_<?= $request['NewBuyer']['id'] ?>" style="">
							<td class="v-align-middle h-align-center"><?=$i++; ?></td>
							<td class="v-align-middle"><?= $request['NewBuyer']['org_name']; ?></td>
							<td class="v-align-middle  h-align-center"><?=$request['NewBuyer']['email_id'] ?></td>
							<td class="v-align-middle h-align-center">
								<!--<a href="<?//=Router::url(array('controller' => 'new_buyers', 'action' => 'add',  $new_buyer['NewBuyer']['id']));?>" class="tip link-color" title="" data-toggle="tooltip" data-original-title="Edit">
								<i class="fa fa-edit"></i></a>-->
								<?php if($request['NewBuyer']['request_status'] == 1) {?>
								<a href="<?=Router::url(array('controller' => 'new_buyers', 'action' => 'request_response',$request['NewBuyer']['id']));?>" class="btn btn-primary dropdown-toggle btn-demo-space" title="Send Mail with userid and temporary password">Respond to Request</a>
								<?php }else{?>
									<p style="font-weight:bold;">Waiting for completion of profile</p>
								<?php } ?>
							</td>
						</tr>
							<?php } ?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$requests) { ?>
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
     /*   $( "tbody" ).sortable({
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
        $( "tbody" ).disableSelection();     */    
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
