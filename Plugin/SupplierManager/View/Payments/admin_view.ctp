<?php echo $this->element('admin/message'); ?>
<div style="background:#f6f6f6; padding:8PX; max-height:auto;" id="view2">
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Transaction ID: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?=$payment['Payment']['txn_id']?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Payer ID: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?php if(isset($payment['Payment']['payer_id'])){echo $payment['Payment']['payer_id'];}else{echo 'N/A';}?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Payer Email: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?php if(isset($payment['Payment']['payer_email'])){echo $payment['Payment']['payer_email'];}else{echo 'N/A';}?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Amount: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?='€'.$payment['Payment']['amount']?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Item: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?=$payment['Payment']['item_name']?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Quantity : </b></div>
		<div align="justify;" style="float:left; width:400px;"><?=$payment['Payment']['quantity']?></div>
		<div style="clear:both;"></div>
	</div>
	
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Supplier: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?=$payment['Supplier']['title']." ".$payment['Supplier']['first_name']." ".$payment['Supplier']['middle_name']." ".$payment['Supplier']['last_name']; ?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<div style="float:left; width:110px;"><b>Payment Date: </b></div>
		<div align="justify;" style="float:left; width:400px;"><?=date("d, F Y h:i:s A",strtotime($payment['Payment']['created_date']));?></div>
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<b>New Buyer</b>
			<table class="table table-bordered no-more-tables">
					<thead style="background-color: #d0d0d0;">
						<tr>
						<th class="h-align-center" style="width:10%">Organisation</th>
						<th class="h-align-center" style="width:10%">Name</th>
						<th class="h-align-center" style="width:10%">Email</th>
						<th class="h-align-center" style="width:10%">Designation</th>
					</tr>
				</thead>
				<?php if(!empty($new_buyers_list)){ ?>
					<tbody>
						<?php
							foreach ($new_buyers_list as $new_buyer) {
						?>
						<tr>
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
							</tr>
					<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$new_buyers_list) { ?>
				<div style='color:#FF0000;text-align:center;'>No Record Found</div>
				<?php } ?>
		
		<div style="clear:both;"></div>
	</div>
	<div style="padding:10px 0;">
		<b>Existing Buyer</b>
		<table class="table table-bordered no-more-tables">
				<thead style="background-color: #d0d0d0;">
					<tr>
						<th class="h-align-center" style="width:10%">Organisation</th>
						<th class="h-align-center" style="width:10%">Name</th>
						<th class="h-align-center" style="width:10%">Email</th>
					</tr>
				</thead>
				<?php if(!empty($existing_buyers_list)){ ?>
					<tbody>
						<?php
							foreach ($existing_buyers_list as $existing_buyer) {
						?>
						<tr>
							<td class="v-align-middle">
								<span class="muted">
							<?php echo  $existing_buyer['ExistingBuyer']['org_name'];?>
					
								</span></td>
							<td class="v-align-middle  h-align-center">
								<span class="muted">
								<?php echo  $existing_buyer['ExistingBuyer']['first_name'];?>
								</span></td>
							<td class="v-align-middle  h-align-center">
							<span class="muted">
							<?php echo  $existing_buyer['ExistingBuyer']['email_id'];?>
							</span></td>
							</tr>
					<?php }?>
					</tbody>
					<?php } ?>
				</table>
				<?php if (!$existing_buyers_list) { ?>
				<div style='color:#FF0000;text-align:center;'>No Record Found</div>
				<?php } ?>
		
		<div style="clear:both;"></div>
	</div>
	
</div>
