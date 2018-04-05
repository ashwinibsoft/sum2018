<?php
 
App::import('Vendor','xtcpdf');
 
$pdf = new XTCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// set default font subsetting mode
$pdf->setFontSubsetting(true);

// set font
//$pdf->SetFont('times', 'BI', 12);
$pdf->AddPage();
$site=Configure::read('Site');
$html = '<img src="'.$site['url'].'/img/site/temp/logo_1464343299_59359746_180_50.jpg">';
$html .= '</pre><h1>Existing Buyers</h1><pre>';

$html .= '<table style="width:800px; text-align:left;border: 1px solid #eee;" border="0" cellpadding="0" cellspacing="0"><thead>';
$html .= '<tr>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">First Name</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Middle Name</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Last Name</th>
            <th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Email Address</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">City</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">State/Province</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Zipcode</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Organisation Name</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Job Title</th>
			<th style="padding:10px; background-color:#f45661; color:#fff;border-right: 1px solid #000;">Relationship</th>
        </tr></thead><tbody>';
        
    if(!empty($e_buyers)){
		foreach ( $e_buyers as $e_buyer ){
			$html .= '<tr>
		<td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['first_name'].'</td>
		<td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['middle_name'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['last_name'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['email_id'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['city'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['state'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['zipcode'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['org_name'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['job_title'].'</td>
        <td style="padding:10px;border-right: 1px solid #000;border-bottom: 1px solid #000;">'.$e_buyer['ExistingBuyer']['relationship'].'</td>
		</tr>';
		}
	}else{
		$html .= '<tr><td style="padding:10px;border-right: 1px solid #eeeeee;border-bottom: 1px solid #eeeeee;" colspan="7">No data To Show</td></tr>';
	}
    
 
$html .= '</tbody></table>';

$pdf->writeHTML($html, true, false, true, false, '');
 
$pdf->lastPage();
//$currntdate=date('d/m/Y-H:i:s'); 
echo $pdf->Output('existing_buyer_list('.$currntdate.').pdf', 'D');

?>
