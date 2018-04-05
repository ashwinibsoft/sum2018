<?php
$this->CSV->addRow(array("S.No","Organisation Name","State","Country","First Contact Person's Full Name","First Contact Person's Email"," First Contact Person's Contact Number"," First Contact Person's Designation","Second Contact Person's Full Name"," Second Contact Person's Email"," Second Contact Person's Contact Number","Second Contact Person's Designation","Account Status"));
 $i = 0;
 foreach ($newbuyerInfos as $n_buyer)
 {
	 
	$i++;
	if($n_buyer['NewBuyer']['s_title'] || $n_buyer['NewBuyer']['s_first_name'] || $n_buyer['NewBuyer']['s_middle_name'] || $n_buyer['NewBuyer']['s_last_name']){	
		$name = $n_buyer['NewBuyer']['s_title'].' '.$n_buyer['NewBuyer']['s_first_name'].' '.$n_buyer['NewBuyer']['s_middle_name'].' '.$n_buyer['NewBuyer']['s_last_name'];
	}else{
		$name = 'N/A';
	}
	if($n_buyer['NewBuyer']['status'] == 1){ 
		$status =  'Active'; 
	}elseif($n_buyer['NewBuyer']['status'] == 1){ 
		$status = 'Blocked'; 
	}else{
		$status = 'Inactive';
	}

	if($n_buyer['NewBuyer']['s_email']){	
		$s_email = $n_buyer['NewBuyer']['s_email'];
	}else{
		$s_email = 'N/A';
	}
	
	if($n_buyer['NewBuyer']['s_designation']){	
		$s_designation = $n_buyer['NewBuyer']['s_designation'];
	}else{
		$s_designation = 'N/A';
	}
	
	if($n_buyer['NewBuyer']['s_country_code'] && $n_buyer['NewBuyer']['s_area_code'] && $n_buyer['NewBuyer']['s_contact_number']){	
		$s_contact = $n_buyer['NewBuyer']['s_country_code'].' '.$n_buyer['NewBuyer']['s_area_code'].' '.$n_buyer['NewBuyer']['s_contact_number'];
	}else{
		$s_contact = 'N/A';
	}
	//$action = ($newbuyerInfo['NewBuyer']['status']==1) ? 'Yes' : 'No';
	
	$fullname1=$n_buyer['NewBuyer']['title'].' '.ucfirst($n_buyer['NewBuyer']['first_name']).' '.ucfirst($n_buyer['NewBuyer']['middle_name']).' '.ucfirst($n_buyer['NewBuyer']['last_name']);
	
	$contact1=$n_buyer['NewBuyer']['country_code'].' '.$n_buyer['NewBuyer']['area_code'].' '.$n_buyer['NewBuyer']['contact_number'];
	
	$fullname2=$n_buyer['NewBuyer']['s_title'].' '.ucfirst($n_buyer['NewBuyer']['s_first_name']).' '.ucfirst($n_buyer['NewBuyer']['s_middle_name']).' '.ucfirst($n_buyer['NewBuyer']['s_last_name']);
	
	$contact2=$n_buyer['NewBuyer']['s_country_code'].' '.$n_buyer['NewBuyer']['s_area_code'].' '.$n_buyer['NewBuyer']['s_contact_number'];
	
	$this->CSV->addRow(array(
	$i,
	$n_buyer['NewBuyer']['org_name'],
	$n_buyer['NewBuyer']['state'],
	$n_buyer['Country']['country_name'],
	$fullname1,
	$n_buyer['NewBuyer']['email_id'],
	$contact1,
	ucfirst($n_buyer['NewBuyer']['designation']),
	$fullname2,
	$n_buyer['NewBuyer']['s_email'],
	$contact2,
	$n_buyer['NewBuyer']['s_designation'],
	$status
	));
 }

 $currntdate=date('d/m/Y-H:i:s'); 
 $filename='new_buyer_list('.$currntdate.')';
 echo  $this->CSV->render($filename);
?>
