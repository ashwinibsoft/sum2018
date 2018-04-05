<?php
$this->CSV->addRow(array("S.No.","First Name","Middle Name","Last Name","DOB","Contact No.","Email","Address","City","State/Province","Zip/Postcode","Country","Company Name","Industry","Service Category","Experience","Recieve Info","Account Status"));
$i = 0;
 foreach ($supplierInfos as $supplier)
 {
	 $i++;
	 if($supplier['Supplier']['status'] == 1){ 
				$status =  'Active'; 
	}elseif($supplier['Supplier']['status'] == 1){ 
		$status = 'Blocked'; 
	}else{
		$status = 'Inactive';
	}
	if($supplier['Supplier']['receive_info'] == 1){ 
		$info =  'Yes'; 
	}else{ 
		$info = 'No'; 
	}
	
	if($supplier['Supplier']['dob']){
		$dob  = date("m/d/Y", strtotime($supplier['Supplier']['dob']));
	}else{
		$dob = 'N/A';
	}
	if($supplier['Supplier']['country_code'] && $supplier['Supplier']['area_code'] && $supplier['Supplier']['contact_number']){
		$contact_no = $supplier['Supplier']['country_code'].' '.$supplier['Supplier']['area_code'].' '.$supplier['Supplier']['contact_number'];
	}else{
		$contact_no = 'N/A';
	}
	if($supplier['Supplier']['company_name']){
		
		$company = $supplier['Supplier']['company_name'];
	}else{
		$company = 'N/A';
	}
	if($supplier['Supplier']['industry']){
		
		$industry = $supplier['Supplier']['industry'];
	}else{
		$industry = 'N/A';
	}
	
	if($supplier['Supplier']['service_cat']){
		
		$service_cat = $supplier['Supplier']['service_cat'];
	}else{
		$service_cat = 'N/A';
	}
	if($supplier['Supplier']['experience']){
		
		$experience = $supplier['Supplier']['experience'].' years';
	}else{
		$experience = 'N/A';
	}
 //$action = ($supplierInfo['Supplier']['receive_info']==1) ? 'Yes' : 'No';
 
	$this->CSV->addRow(array(
	$i,
	ucfirst($supplier['Supplier']['first_name']),
	ucfirst($supplier['Supplier']['middle_name']),
	ucfirst($supplier['Supplier']['last_name']),
	$dob,
	$contact_no,
	$supplier['Supplier']['email_id'],
	$supplier['Supplier']['address1'].', '.$supplier['Supplier']['address2'],
	$supplier['Supplier']['city'],
	$supplier['Supplier']['state'],
	$supplier['Supplier']['zipcode'],
	$supplier['Country']['country_name'],
	$company,
	$industry,
	$service_cat,
	$experience,
	$info,
	$status
	));
 }
 $currntdate=date('d/m/Y-H:i:s'); 
 $filename='supplier_list('.$currntdate.')';
 echo  $this->CSV->render($filename);
?>
