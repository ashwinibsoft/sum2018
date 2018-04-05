<?php  
require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');

$html ='<div style="padding:6%; margin:0px auto; width:1170px;">
			<table style="width:100%; font-family:Arial, Helvetica, sans-serif;">
				<tr>
					<td style="width:25%; height:66px; text-align:center" colspan="3" ><img src="'.WWW_ROOT.'img/logo.png" alt="summer" /></td>
				</tr>
			</table> 
			<div style="text-align:center; background-color:#000; color:#fff; font-family:Arial, Helvetica, sans-serif; padding:10px; margin:10px 0px; font-weight:bold;">SUPPLIER’S LIST</div>';
			
$html .= '<table cellpadding="0" cellspacing="0" style="width:100%; font-family:Arial, Helvetica, sans-serif; margin-bottom:10px; border:solid 1px #ccc;"><thead>';
$html .= '<tr>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Supplier Name</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">DOB</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Contact Number</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Email Address</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Postal Address</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Country</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Company Info</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Experience</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Account Info</th>
          </tr></thead><tbody>';
        
    if(!empty($suppliers)){
		foreach ( $suppliers as $supplier ){
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
			
			$html .= '<tr>
		<td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$supplier['Supplier']['title'].' '.$supplier['Supplier']['first_name'].' '.$supplier['Supplier']['middle_name'].' '.$supplier['Supplier']['last_name'].'</td>
		<td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$dob.'</td>
		<td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$contact_no.'</td>
		<td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$supplier['Supplier']['email_id'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'
        .'<b>Address - </b>'.$supplier['Supplier']['address1'].' '.$supplier['Supplier']['address2'].'<br>'
        .'<b>City - </b>'.$supplier['Supplier']['city'].'<br>'
        .'<b>State - </b>'.$supplier['Supplier']['state'].'<br>'
        .'<b>Zipcode - </b>'.$supplier['Supplier']['zipcode']
        .'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$supplier['Country']['country_name'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'
        .'<b>Company Name - </b>'.$company.'<br>'
        .'<b>Industry - </b>'.$industry.'<br>'
        .'<b>Service Category - </b>'.$service_cat.'<br>'
        .'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$experience.'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'
        .'<b>Want to receive company information - </b>'.$info.'<br>'
        .'<b>Account Status - </b>'.$status.'<br>'
        .'</td>
		</tr>';
		}
	}else{
		$html .= '<tr><td style="padding:10px;border-right: 1px solid #eeeeee;border-bottom: 1px solid #eeeeee;" colspan="7">No data To Show</td></tr>';
	}
    
 
$html .= '</tbody></table>';	
			
			
			
		
$html .= '</div>';
$dompdf = new dompdf();
$papersize = 'legal';
$orientation = 'landscape';
$dompdf->load_html($html);
$dompdf->set_paper($papersize, $orientation);
$dompdf->render();
$dompdf->stream('supplier_list('.$currntdate.').pdf');
?>
