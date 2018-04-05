<?php
Class Post extends BlogManagerAppModel {
	public $name = "Post";
	public $actsAs = array('Multivalidatable');
	//public $useTable = 'categories';
	public $post_action = "";
	public $validationSets = array(
	'post_add'=>array(
			'post_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter post title.'),
				'rule2' => array('rule' => array('maxLength', 255),'message' => 'Name should be less than 255 charcter(s).')
				),
			'description'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter post description.'),
				),	
			'post_image' => array(
					
					'rule1'=>array(
							'rule' => array('chkImageExtension'),
							'message' => 'Please Upload Valid Image.'
						),
					/*'rule2'=>array(
							'rule' => array('check_size'),
							'message' => 'Only png, gif, jpg, jpeg images are allowed. Please upload 1000x500 dimension of image for better resolution.'
						),*/
				),
			'slug_url'=>array(
					'rule1'=>array(
									'rule' => 'is_valid_url',
									'message' => 'Please enter text in lower case without any space.'),
					'rule2'=>array(
									'rule' => 'check_slug_url',
									'message' => 'This slug url is already associated with other page or module')
				)
			),
		'page_settings' =>array(
			'post_image' => array(
					
					'rule1'=>array(
							'rule' => array('validate_image'),
							'message' => 'Please Upload Valid Image.'
						),
					/*'rule2'=>array(
							'rule' => array('check_size'),
							'message' => 'Only png, gif, jpg, jpeg images are allowed. Please upload 1000x500 dimension of image for better resolution.'
						),*/
				)
		
		)
	);
	
	
    
	
	function validate_image(){
		if((!empty($this->data['Post']['id'])) && $this->data['Post']['post_image']['name']=='') {
			return true;
		}else{
			if(!empty($this->data['Post']['post_image']['name'])) {
				$file_part = explode('.',$this->data['Post']['post_image']['name']);
				$ext = array_pop($file_part);		
				if(!in_array(strtolower($ext),array('gif', 'jpeg', 'png', 'jpg'))) {
					return false;
				}
			}
		return true;
		}
	}
	public function chkImageExtension($data) {
		if($data['post_image']['name'] != ''){
				$fileData= pathinfo($data['post_image']['name']);
				$ext=$fileData['extension'];
				$allowExtension=array('gif', 'jpeg', 'png', 'jpg','JPG');
				if(in_array($ext, $allowExtension)) {
					$return = true; 
				} else {
					$return = false;
				}
			} else{
			
				$return = true; 
			}
			return $return;
		}


	public function check_size(){
		if($this->data['Post']['post_image']['tmp_name']=='') {
			return true;
		}else{
			if($this->data['Post']['post_image']['error'] < 1){
				$imgSize = @getImageSize($this->data['Post']['post_image']['tmp_name']);
				if(($imgSize[0]>=1000 ) && ($imgSize[1]>=500 ))
				{
					return true;
				}
			}
			return false;
		}
	}
	public function uploadFile( $check ) {
		$uploadData = array_shift($check);
		if ( $uploadData['size'] == 0 || $uploadData['error'] !== 0) {
			return false;
		}
		return true;
	}


	function check_slug_url(){
		if((!empty($this->data['Post']['id'])) && !empty($this->data['Post']['slug_url'])) {
			if($this->_check_uri_exist_on_other($this->data['Post']['slug_url'],'Post',$this->data['Post']['id'])){
				return false;
			}else{
				return true;
			}
		}
		return true;
	}
	function is_valid_url(){
		if((!empty($this->data['Post']['id'])) && !empty($this->data['Post']['slug_url'])) {
			return preg_match('|[a-z-.]+$|', $this->data['Post']['slug_url']);
		}
		return true;
	}
	
	

}
?>
