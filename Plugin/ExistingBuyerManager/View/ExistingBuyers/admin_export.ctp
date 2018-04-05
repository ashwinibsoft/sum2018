<?php
$this->CSV->addRow(array("First Name","Middle Name","Last/Family Name","Job Title","Organisation Name","Address","City","State/Province","Zip/Postcode","Country","Email","Relationship To Supplier","Status"));
//echo "<pre>";	print_r($ExistingBuyerInfos); die;
foreach ($ExistingBuyerInfos as $ExistingBuyerInfo)
{
	$status = ($ExistingBuyerInfo['ExistingBuyer']['status']==1) ? 'Active' : 'Inactive';
	$this->CSV->addRow(array(ucfirst($ExistingBuyerInfo['ExistingBuyer']['first_name']),ucfirst($ExistingBuyerInfo['ExistingBuyer']['middle_name']),ucfirst($ExistingBuyerInfo['ExistingBuyer']['last_name']),$ExistingBuyerInfo['ExistingBuyer']['job_title'],$ExistingBuyerInfo['ExistingBuyer']['org_name'],$ExistingBuyerInfo['ExistingBuyer']['address1'].', '.$ExistingBuyerInfo['ExistingBuyer']['address2'],$ExistingBuyerInfo['ExistingBuyer']['city'],$ExistingBuyerInfo['ExistingBuyer']['state'],$ExistingBuyerInfo['ExistingBuyer']['zipcode'],$ExistingBuyerInfo['Country']['country_name'],$ExistingBuyerInfo['ExistingBuyer']['email_id'],$ExistingBuyerInfo['ExistingBuyer']['relationship'],$status));
}
$currntdate=date('d/m/Y-H:i:s'); 
$filename='existing_buyers_list('.$currntdate.')';
echo  $this->CSV->render($filename);
?>
