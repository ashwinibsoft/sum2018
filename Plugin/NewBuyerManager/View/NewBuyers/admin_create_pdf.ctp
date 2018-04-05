<?php  
require_once(APP . 'Vendor' . DS . 'dompdf' . DS . 'dompdf_config.inc.php');

$html ='<div style="width:94%; padding:6%; margin:0px auto; max-width:1170px;">
			<table style="width:100%; font-family:Arial, Helvetica, sans-serif;">
				<tr>
					<td style="width:25%; height:66px; text-align:center" colspan="3" ><img src="'.WWW_ROOT.'img/logo.png" alt="summer" /></td>
				</tr>
			</table> 
			<div style="text-align:center; background-color:#000; color:#fff; font-family:Arial, Helvetica, sans-serif; padding:10px; margin:10px 0px; font-weight:bold;">NEW BUYER’S LIST</div>';
			
		if(!empty($n_buyers)){
			$i=0;
		foreach ( $n_buyers as $n_buyer ){
			$i++;
			if($n_buyer['NewBuyer']['s_title'] || $n_buyer['NewBuyer']['s_first_name'] || $n_buyer['NewBuyer']['s_middle_name'] || $n_buyer['NewBuyer']['s_last_name']){	
				$name = $n_buyer['NewBuyer']['s_title'].' '.$n_buyer['NewBuyer']['s_first_name'].' '.$n_buyer['NewBuyer']['s_middle_name'].' '.$n_buyer['NewBuyer']['s_last_name'];
			}else{
				$name = 'N/A';
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
			
$html .='	<div style="page-break-inside: avoid;">
			<div style="padding:15px; background:#000; color:#fff; float:left; width:2%; text-align:center;">'.$i.'.</div>
			<div style="float:left;width:92%;">
				<table style="width:50%; font-family:Arial, Helvetica, sans-serif; float:left;">
				  <tr>
					  <td colspan="2" bgcolor="#000" style="color:#fff;text-align:left; font-weight:bold; padding:7px;">
					  First Contact Person</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Name</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['title'].' '.$n_buyer['NewBuyer']['first_name'].' '.$n_buyer['NewBuyer']['middle_name'].' '.$n_buyer['NewBuyer']['last_name'].'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Contact Number</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['country_code'].' '.$n_buyer['NewBuyer']['area_code'].' '.$n_buyer['NewBuyer']['contact_number'].'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Email Address</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['email_id'].'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:8px;">Position in Organisation</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['designation'].'</td>
				  </tr>
			</table>

			<table style="width:50%; font-family:Arial, Helvetica, sans-serif; float:right;">
				  <tr>
					  <td colspan="2" bgcolor="#000" style="color:#fff;text-align:left; font-weight:bold; padding:7px;">Second Contact Person</td>
				  </tr>
				 <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Name</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$name.'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Contact Number</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$s_contact.'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Email Address</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$s_email.'</td></td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Position in Organisation</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$s_designation.'</td>
				  </tr>
			</table>
			<div style="clear:both;"></div>
			<table style="width:100%; font-family:Arial, Helvetica, sans-serif; float:right;margin-bottom:5px;">
				  <tr>
					  <td colspan="2" bgcolor="#000" style="color:#fff;text-align:left; font-weight:bold; padding:7px;">Other Information</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:8px;">Organisation Name</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['org_name'].'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">State</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['state'].'</td>
				  </tr>
				  <tr>
					  <td bgcolor="#2e74b5" style="color:#fff; width:30%;text-align:left; font-weight:bold; padding:7px;">Country</td>
					  <td bgcolor="#deeaf6" style="width:70%;padding:7px;">'.$n_buyer['NewBuyer']['country'].'</td>
				  </tr>
				  
			</table>
			<div style="clear:both;"></div>
		</div>
		</div>
		<div style="clear:both;"></div>';
		}
		}else{
		$html .= '<div style="padding:10px;border-right: 1px solid #eeeeee;border-bottom: 1px solid #eeeeee;">No data To Show</div>';
		}
$html .= '</div>';
 
$dompdf = new dompdf();
$papersize = 'legal';
$orientation = 'landscape';
$dompdf->load_html($html);
$dompdf->set_paper($papersize, $orientation);
$dompdf->render();
$dompdf->stream('newbuyer_list('.$currntdate.').pdf');

?>
