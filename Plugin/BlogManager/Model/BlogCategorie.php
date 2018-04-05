<?php
Class BlogCategorie extends BlogManagerAppModel {
	public $name = "BlogCategorie";
	public $validate = array(
	'cat_name'=>array(
				'rule1' => 
				array('rule' => 'notEmpty','message' => 'Please enter Category name.'),
				array('rule' => array('maxLength', 255),'message' => 'Category Name should be less than 255 charcter(s).')
			),
	'cat_detail'=>array(
				
				'rule1' =>	array('rule' => 'notEmpty','message' => 'Please enter Category description.'), 
				array('rule' => array('maxLength', 500),'message' => 'Category description should be less than 255 charcter(s).')
			),		
	'cat_image'=>array(
					'rule1'=>
					array(
							'rule' => array('validate_image'),
							'message' => 'Only png, gif, jpg, jpeg images are allowed.'
						),
		)	
	);
	
	
	function validate_image(){
		//print_r($this->data);die;
		if((!empty($this->data['BlogCategorie']['id'])) && $this->data['BlogCategorie']['cat_image']['name']=='') {
			return true;
		}else{
			if(!empty($this->data['BlogCategorie']['cat_image']['name'])) {
				//echo 'hello';die;
				$file_part = explode('.',$this->data['BlogCategorie']['cat_image']['name']);
				$ext = array_pop($file_part);		
				if(!in_array(strtolower($ext),array('gif', 'jpeg', 'png', 'jpg'))) {
					return false;
				}
			}
			return true;
		}
	}
	
	

	
	
	

}
?>
