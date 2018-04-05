<?php 
App::uses('Component', 'Controller');
class ExportXlsComponent extends Component {
 
	function export($fileName, $headerRow, $data) {
	 ini_set('max_execution_time', 10000); //increase max_execution_time to 10 min if data set is very large
	  $fileContent = implode("\t ", $headerRow)."\n";
	  foreach($data as $result) {
	   $fileContent .=  implode("\t ", $result)."\n";
	 //  $fileContent .="\t \n";
	  }
	//  echo '';print_r($fileContent);die;
	 //header('Content-type: application/ms-excel'); /// you can set csv format
	 header('Content-Type: application/vnd.ms-excel');
	 header('Content-Disposition: attachment; filename='.$fileName);
	 echo $fileContent;
	exit;
	}
}
?>
