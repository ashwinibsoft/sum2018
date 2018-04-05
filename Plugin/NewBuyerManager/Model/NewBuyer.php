<?php
Class NewBuyer extends NewBuyerManagerAppModel {
	public $name = "NewBuyer";
	public $actsAs = array('Multivalidatable','Containable');
	public $belongsTo = array(
		'Country' => array(				
			 'foreignKey' => false,
			 'conditions' => array('NewBuyer.country=Country.country_code_char2'),
		)
	);
	public $hasMany = array(
        'NewBuyerQuestion' => array(
            'className' => 'NewBuyerQuestion',
            'foreignKey' => 'new_buyer_id',
            'conditions' => array('NewBuyerQuestion.new_buyer_id' => 'NewBuyer.id'),
            'dependent' => true
        )
    );
	
	public $validationSets = array(
		'new_buyer_add'=>array(
			'org_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Organisation Name should be less than 30 charcter(s).')
			),
			'state'=>array(			
				'rule1' => array('rule' => array('maxLength', 20),'message' => 'Please enter correct state name.')
			),
			'first_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'First Name should be less than 30 charcter(s).')
			),
			'middle_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Middle Name should be less than 30 charcter(s).')
			),
			'last_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Last Name should be less than 30 charcter(s).')
			),
			'designation'=>array(				
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Please enter suitable position of the person.')
			),				
			'email'=>array(
				array('rule' => array('email'),
					'message' => 'Please enter a valid email',				
				),
				'isUnique'=>array(
					'rule'=>array('isUnique'),
					'message'=>'This email has already been registered.'
				) 
			),
			
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
				),
		
			'zip'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),
				//'rule2' => array('rule' => 'area_code','message' => 'Enter valid area code.')
				
			),
			
			/*'country_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid country code.'),
				'rule2' => array('rule' => 'country_code','message' => 'Enter valid country code.'),
			),*/
				
			/*'contact_number'=>array(
			    'rule1' => array('rule' => 'notEmpty',
			       'message' => 'Please enter phone number.'),   
			 ),	*/
				
			'contact_number'=>array(
					array('rule' => array('isValidPhone'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => false
					)
			),	
						
			's_first_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'First Name should be less than 30 charcter(s).')
			),
			's_middle_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Middle Name should be less than 30 charcter(s).')
			),
			's_last_name'=>array(			
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Last Name should be less than 30 charcter(s).')
			),
			's_designation'=>array(				
				'rule1' => array('rule' => array('maxLength', 30),'message' => 'Please enter suitable position of the person.')
			),					
			's_email'=>array(
					array('rule' => array('email'),
						  'message' => 'Please enter a valid email',	
						  'allowEmpty' => true			
					)
			),
			/*'s_contact_number'=>array(
				array('rule' => array('isValidUSPhoneFormat'),
					'message' => 'Please enter a valid contact number.',
					'allowEmpty' => false
				)
			),*/
			
			's_contact_number'=>array(
			    'rule1' => array('rule' => 'notEmpty',
			       'message' => 'Please enter phone number.'),   
			 ),
			
			's_area_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),
				'rule2' => array('rule' => 'area_code','message' => 'Enter valid area code.')
				
				),
			's_country_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid country code.'),
				'rule2' => array('rule' => 'country_code','message' => 'Enter valid country code.'),
				),	
			
		),
			
		'new_buyer_login'=>array(						
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter registered email address.'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address'),
				'unique' => array('rule'=> array('isEmailExists'), 'message' => 'Account with this email does not exists')
			),
				
			'password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please  enter password.'),
				'rule2' => array('rule' => array('maxLength', 25),'message' => 'Password should be less than 25 charcter(s).')
			),
			
			'captchamatch'=>array(
				'rule1' =>array('rule' => 'notEmpty','message' => 'Please enter security code.'),
				'rule2' =>array('rule' => 'matchCaptcha','message' => 'Failed validating human check.')	
												
			),		
			
		),
		
		'reset_pass'=>array(	
			'password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter password.'),
			/*	'rule2' => array('rule' => 'checkoldexist','message' => 'Password must be different from current password.'),
				*/
				'rule4' => array('rule' => array('custom', '$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$'),'message' => 'Password must contain minimum 8 characters with one capital letter, one number, one special character.')
				),
				
			'confirm_pass'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter confirm password.'),
				'checkpass' => array('rule'=> array('checkpassword'), 'message' => 'Your password and confirm password does not match.'),
				),
		),
		
		'profile'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title.'),
			),
			'org_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter organisation name.'),
				),
				
			'address_one'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
				),	
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
				),
				
			/*'state'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter state.'),
				),*/
			'country'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select country.')
				),
			'first_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter first name.'),
				'rule2' => array('rule' => array('maxLength', 200),'message' => 'Name should be less than 200 charcter(s).')
			),
			'last_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter last name.'),
				'rule2' => array('rule' => array('maxLength', 100),'message' => 'Name should be less than 100 charcter(s).')
			),
			
			'contact_number'=>array(
			  'rule1'=> array('rule' => 'notEmpty','message' => 'Please enter contact number.'),
			  'rule2'=>array('rule' => array('isValidPhone'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => false
					)
					
			
			),	
			'zip'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),
				//'rule2' => array('rule' => 'area_code','message' => 'Enter valid area code.')
				
			),
			
			/*'area_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),
				'rule2' => array('rule' => 'area_code','message' => 'Enter valid area code.')
				
			),
			
			'country_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid country code.'),
				'rule2' => array('rule' => 'country_code','message' => 'Enter valid country code.'),
			),
			'contact_number'=>array(
				//array('rule' => array('phone','/^[!0]*[+-.]*[0-9-\)\(]+$/'),
				array('rule' => array('isValidUSPhoneFormat'),
					'message' => 'Please enter a valid contact number.',
					'allowEmpty' => false
				)
			),*/	
			
			/*'contact_number'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter contact number.'),
				'rule2'	=> array('rule' => array('maxLength', 15),'message' => 'Please enter valid contact number.'),
				'rule3'	=> array('rule' => array('phone','/^[!0]*[+-.]*[0-9-\)\(]+$/','message' => 'Please enter valid contact number.'),
						
					),
			),*/
			
			/*'s_contact_number'=>array(				
				'rule1'	=> array('rule' => array('maxLength', 16),'message' => 'Please enter valid contact number.'),
				'rule2'	=> array('rule' => array('phone','/^[!0]*[+-.]*[0-9-\)\(]+$/'),'message' => 'Please enter valid contact number','allowEmpty' => true)						
			),
			
			  's_area_code'=>array(	
				
				'rule2' => array('rule' => 's_contact_valid','message' => 'Please enter a valid contact number with country code and area code.')
				
				),
		     's_country_code'=>array(	
				
				'rule2' => array('rule' => 's_contact_valid','message' => 'Please enter a valid contact number with country code and area code.'),
				),*/
			
			/*'s_contact_number'=>array(
				array('rule' => array('s_contact_valid'),
					'message' => 'Please enter a valid contact number with country code and area code.',
					'allowEmpty' => true
				)
			),	*/
			
			's_contact_number'=>array(
			  'rule1'=>array('rule' => array('isValidPhone'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => true
					)
			),
			
			
			'designation'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter designation.')
			)
		),
		
		
	  'profile_edit'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title.'),
			),
			'org_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter organisation name.'),
				),
				
			'address_one'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
				),	
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
				),
			'zip'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),
				//'rule2' => array('rule' => 'area_code','message' => 'Enter valid area code.')
				
			),
			'country'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select country.')
				),
			'first_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter first name.'),
				'rule2' => array('rule' => array('maxLength', 200),'message' => 'Name should be less than 200 charcter(s).')
			),
			'last_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter last name.'),
				'rule2' => array('rule' => array('maxLength', 100),'message' => 'Name should be less than 100 charcter(s).')
			),
			
			'contact_number'=>array(
			  'rule1'=> array('rule' => 'notEmpty','message' => 'Please enter contact number.'),
			  'rule2'=>array('rule' => array('isValidPhone'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => false
					),
			
			/*'rule3'=>array('rule' =>'numeric',
						'message' => 'Please enter a valid contact number.',
						)*/
			),	
			's_contact_number'=>array(
			  'rule1'=>array('rule' => array('isValidPhone'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => true
					)
			),
			
			
			'designation'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter designation.')
			),
		  'logo' => array(	
					'rule1'=>array('rule'=>'chkImageExtension','message' => 'Please Upload Valid Image.'),
					'rule2'=>array(
							'rule' => array('chkImageSize'),
							'message' => 'Image size should not be more than 1MB.'
					)
					
				)		
		),
			
		'change_pass'=>array(		
			'old_password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please  enter current password.'),
				'rule2' => array('rule' => array('checkoldpass'), 
									'message' => 'Wrong password, please enter your correct password.',
								),
							),
			'password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please  enter new password.'),
				'rule2' => array('rule' => 'checkoldexist','message' => 'Password must be different from current password.'),
				'rule3' => array('rule' => array('custom', '$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$'),'message' => 'Password must contain minimum 8 characters with one capital letter, one number, one special character.')
				),
				
			'confirm_pass'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please confirm new password.'),
				'checkpass' => array('rule'=> array('checkpassword'), 'message' => 'Your password and confirm password does not match.'),
				),
		),
		
		'forgot_pass'=>array(	
				'email_id' =>	
					array(
						'rule1' =>
							array(
								 'rule' => 'notEmpty',
								'message' => 'Please enter your registered email address.'
							),
							array(
								'rule' => array('email', true),
								'message' => 'Please enter valid email address.'
							),
							array(
								'rule' => array('isEmailExists'),
								'message' => 'This email address is not registered.'
							) 
					),
			
			),
			
			'new_buyer_setting'=>array(
				'logo' => array(	
					'rule1'=>array('rule'=>'chkImageExtension','message' => 'Please Upload Valid Image.'),
					'rule2'=>array(
							'rule' => array('chkImageSize'),
							'message' => 'Image size should not be more than 1MB.'
					)
					
				),
				'required_feedback'=>array(
					'rule1'=>array(
							'rule' => array('chkFeedback'),
							'message' => 'Please Enter Required Feedbacks.'
					),
					'rule2'=>array(
					       'rule'=>array('chkFeedbackValue'),
					       'message'=>'You can select maximum 5 feedbacks.'
					)
				)
			),	
			
			
			'new_buyer_request'=>array(
				'org_name'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter organisation name.'),
				),
				'email_id'=>array(
					array('rule' => array('email'),
						'message' => 'Please enter a valid email address.',				
					),
					'isUnique'=>array(
						'rule'=>array('isUnique'),
						'message'=>'This email has already been registered.'
					) 
				)
			)	
	);
	
	function isEmailExists($check)
	{
        $email = $this->find('first',array('fields' => array('NewBuyer.id'),'conditions' => array('NewBuyer.email_id' =>$check['email_id'])));
 
        if(!empty($email)){
                return true;
        }else{
            return false;
        }
    }
    
    function checkpassword()     // to check pasword and confirm password
	{  
		if($this->data['NewBuyer']['password']===$this->data['NewBuyer']['confirm_pass']) 
		{
		    return true;
		}
        return false; 
        
	}
	
	function checkoldpass()     // to check pasword and confirm password
	{  
		$pass=$this->find('first',array('fields' => array('NewBuyer.password'),'conditions' => array('NewBuyer.id' =>$this->data['NewBuyer']['id'])));
		$hash=Security::hash(Configure::read('Security.salt').$this->data['NewBuyer']['old_password']);
		if($hash===$pass['NewBuyer']['password']) 
		{
		    return true;
		}
        return false; 
        
	}

	function checkoldexist()     // to check pasword and confirm password
	{  
		$pass=$this->find('first',array('fields' => array('NewBuyer.password'),'conditions' => array('NewBuyer.id' =>$this->data['NewBuyer']['id'])));
		$hash=Security::hash(Configure::read('Security.salt').$this->data['NewBuyer']['password']);
		if($hash===$pass['NewBuyer']['password']) 
		{
		    return false;
		}
        return true; 
        
	}
	
	function chkFeedback($data)     // to check reuired feedbacks
	{  
		//print_r($data); die;
		if($this->data['NewBuyer']['required_feedback'] == 0) 
		{
		    return false;
		}
        return true; 
        
	}
	
	
	function chkFeedbackValue($data)     // to check reuired feedbacks value
	{  
		if($this->data['NewBuyer']['required_feedback'] > 5) 
		{
		    return false;
		}
        return true; 
        
	}
	
	public function chkImageExtension($data) {
		//echo 1;die;
		
		if(!empty($data['logo']['name'])){
				$fileData= pathinfo($data['logo']['name']);
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
	
	public function chkImageSize($data) {
		if(!empty($data['logo']['name'])){
				$fileSize = $data['logo']['size'];
				//$allowSize = 1048576;
				$allowSize = 1000000;
				if($fileSize < $allowSize) {
					$return = true; 
				} else {
					$return = false;
				}
			} else{
			
				$return = true; 
			}
			return $return;
	}
	
	function matchCaptcha($nputValue)
    {
		App::import('Component', 'Session');
		$Captch = CakeSession::read('security_code');
		if($Captch != $this->data['NewBuyer']['captchamatch'])
		{
			return false;
		}
		return true;
	}
	
	function isValidUSPhoneFormat($phone){
		if(!empty($phone['contact_number'])){
				$phone_no=$phone['contact_number'];
			}else if($phone['s_contact_number']){
				$phone_no=$phone['s_contact_number'];
			}
		
		 $errors = array();
			if(empty($phone_no)) {
				$errors [] = "Please enter contact number";
				return false;
			}else if (!preg_match('/^[+\s(]{0,1}[0-9]{0,2}[\s-.]{0,1}[0-9]{3}[)]{0,1}[-\s.]{0,1}[0-9]{3}[-\s.]{0,1}[0-9]{4}$/', $phone_no)){  			
			/*}else if (!preg_match('/^[+\s(]{0,1}[0-9]{0,2}[\s-.]{0,1}[0-9]{2,4}[)]{0,1}[-\s.]{0,1}[0-9]{2,4}[-\s.]{0,1}[0-9]{0,4}[-\s.]{0,1}[0-9]{0,4}$/', $phone_no)){ */ 			
						$errors [] = "Please enter valid contact number";
				return false;
			} 

			if (!empty($errors))
			return implode("\n", $errors);

			return true;
		}
		
	function isValidPhone($phone){
		if(!empty($phone['contact_number'])){
				$phone_no=$phone['contact_number'];
			}else if($phone['s_contact_number']){
				$phone_no=$phone['s_contact_number'];
			}
		
		 $errors = array();
			if(empty($phone_no)) {
				$errors [] = "Please enter contact number";
				return false;
			}else if (!preg_match('/^[a-z0-9 .\-]+$/i', $phone_no)){  			
			 			
						$errors [] = "Please enter valid contact number";
				return false;
			} 

			if (!empty($errors))
			return implode("\n", $errors);

			return true;
		}	
		
		
		
	function country_code()  
	{  
		if(!empty($this->data['NewBuyer']['country_code'])) 
		{
			$country_code=$this->data['NewBuyer']['country_code'];
			if($country_code == '+00' || $country_code == '00'){
				return false;
			}else{
				if(!preg_match('/^[+]{0,1}[0-9]{1,6}$/', $country_code)){
					return false; 
				}
			}
		    return true;
		}/*elseif(!empty($this->data['NewBuyer']['s_country_code'])){
			$country_code=$this->data['NewBuyer']['s_country_code'];
			if(!preg_match('/^[+]{1}[0-9]{1,6}$/', $country_code)){
				return false; 
			}
		    return true;
			
		}*/
        return false; 
	}
	
	 function area_code()  
	{  
		if(!empty($this->data['NewBuyer']['area_code'])) 
		{
			$country_code=$this->data['NewBuyer']['area_code'];
			if(!preg_match('/^[0-9]{2,6}$/', $country_code)){
				return false; 
			}
		    return true;
		}elseif(!empty($this->data['NewBuyer']['s_area_code'])){
			$country_code=$this->data['NewBuyer']['s_area_code'];
			if(!preg_match('/^[0-9]{2,6}$/', $country_code)){
				return false; 
			}
		    return true;
			
		}
        return false; 
	}
	
	function s_contact_valid()
	{
		if(empty($this->data['NewBuyer']['s_area_code']) && empty($this->data['NewBuyer']['s_country_code']) && empty($this->data['NewBuyer']['s_contact_number'])){
			return true;	
		}else{
			
			if(!empty($this->data['NewBuyer']['s_country_code'])){
				$country_code=$this->data['NewBuyer']['s_country_code'];
				if($country_code == '+00' || $country_code == '00'){
					return false;
				}else{
					if(!preg_match('/^[+]{0,1}[0-9]{1,6}$/', $country_code)){
						return false; 
					}
		    	}
			}else{
				return false;
			}
			
			if(!empty($this->data['NewBuyer']['s_area_code'])){
				$country_code=$this->data['NewBuyer']['s_area_code'];
				if(!preg_match('/^[0-9]{2,6}$/', $country_code)){
					return false; 
				}
			}else{
				return false;
			}
			
			if(!empty($this->data['NewBuyer']['s_contact_number'])){
				$phone_no=$this->data['NewBuyer']['s_contact_number'];
				if(!preg_match('/^[+\s(]{0,1}[0-9]{0,2}[\s-.]{0,1}[0-9]{2,4}[)]{0,1}[-\s.]{0,1}[0-9]{2,4}[-\s.]{0,1}[0-9]{0,4}[-\s.]{0,1}[0-9]{0,4}$/', $phone_no)){
					return false; 
				}
			}else{
				return false;
			}
				
			return true;
		}
	}
}
?>
