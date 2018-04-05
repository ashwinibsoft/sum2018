<?php
App::uses('Component', 'Controller');
class UploadComponent extends Component{
	var  $settings = array();
	var $components = array('Session');
	public function __construct(ComponentCollection $collection, $settings = array()){
		parent::__construct($collection);
	}
	public function startup(Controller $controller){
		parent::startup($controller);
	}
	public function move_uploaded_file($new_file = array(),$options = array()){
		$destination = "";
		$existing = "";
		$resize = array();
		if(!empty($options['destination'])){
			$destination = $options['destination'];
		}
		if(!empty($options['existing'])){
			$existing = $options['existing'];
		}
		
		if(!empty($options['resize'])){
			$resize = $options['resize'];
		}
		if ($new_file['error'] > 0) {
			if(!empty($new_file['name'])){
				$this->Session->setFlash('Some error was found on image','default','','wraning' );
			}
			return $existing;
		}else{
			if ($new_file['error'] > 0) {
				return $existing;
			} else {
				if(empty($destination)){
					$this->Session->setFlash('Uploading Error: destination is missing','default','','wraning' );
					return $existing;
				}
				if(!file_exists($destination)) {
					App::uses('Folder', 'Utility');
					mkdir($destination, 0777);
				}
				$dir = new Folder();
				$dir->chmod($destination, 0777, true, array());	
				
				$image_breaks = explode('.', $new_file['name']);
				$ext = array_pop($image_breaks);
				$image_name = implode('.',$image_breaks).'_'.rand(0,999999) .'_' . time() . '.' . $ext;
				move_uploaded_file($new_file['tmp_name'], $destination . $image_name);
				if (!empty($existing) && file_exists($destination . $existing)) {
					unlink($destination . $existing);
				}
				return $image_name;
			}
		}
	}
}
?>
