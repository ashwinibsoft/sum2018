<?php
Class Supplier extends SupplierManagerAppModel {
	public $name = "Supplier";
	public $actsAs = array('Multivalidatable');
	public $belongsTo = array(
		'Country' => array(				
			 'foreignKey' => false,
			 'conditions' => array('Supplier.country=Country.country_code_char2'),
		),
		//~ 'SupplierBuyer' => array(
		        //~ 'foreignKey' => false,
                //~ 'displayField'=>'supplier_id',
                //~ 'primaryKey'=>'supplier_id',
				//~ 'conditions' => array('Supplier.id=SupplierBuyer.supplier_id'),
		//~ )
	);
	public $validationSets = array(
	'supplier_add'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title.'),
				),
			'first_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter first name.'),
				'rule2' => array('rule' => array('maxLength', 200),'message' => 'Name should be less than 200 charcter(s).')
				),
			'last_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter last name.'),
				'rule2' => array('rule' => array('maxLength', 100),'message' => 'Name should be less than 100 charcter(s).')
				),
			'address1'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
				),
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
				),
			/*'state'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter state.'),
				),
			*/
			'zipcode'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter zip code.'),
				'rule2' => array('rule' => array('maxLength', 10),'message' => 'Zip code should be greater than 10 charcter(s).'),
				 // 'rule3' => array('rule'=>array('custom', '/^[0-9 ]{5,7}$/i'),'message' => 'Please enter valid zip code.'),
				'rule4' => array('rule' => 'check_zip','message' => 'Please enter valid zip code.')
				),
			'country'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select country.'),
				),			
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter email address.'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address')
				),
				
			/*'contact_number'=>array(
					'rule1'	=> array('rule' => array('maxLength', 16),'message' => 'Please enter valid contact number.'),
					'rule2'	=> array('rule' => array('phone','/^[!0]*[+-.]*[0-9-\)\(]+$/'),'message' => 'Please enter valid contact number','allowEmpty' => true),
					/*array('rule' => array('phone','/^[!0]*[+-.]*[0-9-\)\(]+$/'),
						'message' => 'Please enter valid contact number',
						'allowEmpty' => true
					)
				),*/
			
		/*	'contact_number'=>array(
					array('rule' => array('isValidUSPhoneFormat'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => false
					)
				),	
		*/
	
		'contact_number'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter contact number.'),
				'rule2'=>array('rule' => array('isValidPhone'),
						'message' => 'Please enter a valid contact number.',
						'allowEmpty' => false
					),
			
				),	
		
		/*'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),*/
				
			/*'area_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid area code.'),
				'rule2' => array('rule' => 'area_code','message' => 'Enter valid area code.')
				
				),*/
			/*'country_code'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter valid country code.'),
				'rule2' => array('rule' => 'country_code','message' => 'Enter valid country code.'),
				), */
			
			'service_cat'=>array(			
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Service category should be less than 30 charcter(s).')
				),
			'industry'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter industry.')		
				//'rule2' => array('rule' => array('maxLength', 20),'message' => 'Industry name should be less than 20 charcter(s).')
				),
				
			'nationality'=>array(			
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Please enter valid nationality.')
				),
				
			'experience'=>array(			
				'rule2' => array('rule' => 'notEmpty','message' => 'Please select experience.')
				),	
			'company_name'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter company name.'),			
				'rule2' => array('rule' => array('maxLength', 50),'message' => 'Company name should be less than 50 charcter(s).')
				),
			'xxx'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter xxx.')			
		
				),
				
			'dob'=>array(	
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter date of birth.')		
				),
			'nationality'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select nationality.'),
				),
			
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
	
			
		'add_card'=>array(
			'card_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter the name on card.'),
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Name should be less than 30 charcter(s).')
				),
			'card_type'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select the card type.'),
				),
			'card_number'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter card number.'),
				),
			'exp_date'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select the expiry date.'),
				),
			'cvv'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter cvv number.'),
				'rule2' => array('rule' => array('maxLength', 4),'message' => 'Please enter the correct cvv number.'),
				'rule3' => array('rule' => array('numeric'),'message' => 'Please enter correct cvv number.')
				),
			'exp_month'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select the expiry month.'),
				),
			'exp_year'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select the expiry year.'),
				),
			
			),	
			
		'supplier_registration'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title.'),
				),
			'first_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter name.'),
				'rule2' => array('rule' => array('maxLength', 200),'message' => 'Name should be less than 200 charcter(s).')
				),
			'last_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter  surname.'),
				'rule2' => array('rule' => array('maxLength', 100),'message' => 'Name should be less than 100 charcter(s).')
				),
			'address1'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
				),
		
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
				),
			//~ 'state'=>array(
				//~ 'rule1' => array('rule' => 'notEmpty','message' => 'Please enter state.'),
				//~ ),
			'zipcode'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter zip code.'),
					  //'rule3' => array('rule'=>array('custom', '/^[0-9 ]{5,7}$/i'),'message' => 'Please enter valid zip code.'),
			
				'rule2' => array('rule' => array('maxLength', 10),'message' => 'Name should be less than 10 charcter(s).')
				),
			'country'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select country.'),
				),
			
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter email address.'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address'),
				'unique' => array('rule'=> array('isUnique'), 'message' => 'This email is already in use')
				),
			'confirm_email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter confirm email address.'),
				'checkmail' => array('rule'=> array('checkemail'), 'message' => 'Your Email Id and Confirm Email Id does not match.'),
				'rule2' => array('rule' => array('maxLength', 255),'message' => 'Name should be less than 255 charcter(s).')
				),
			'password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please  enter password.'),
				//'rule2' => array('rule'  => array('lengthBetween', 8, 20),'message' => 'Password should be at least 8 chars long.'),
				'rule3' => array('rule' => array('custom', '$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$'),'message' => 'Password must contain minimum 8 characters with one capital letter, one number, one special character.')
				),
			'confirm_pass'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter confirm password.'),
				'checkpass' => array('rule'=> array('checkpassword'), 'message' => 'Your password and confirm password does not match.'),
				),
				
			'terms'=>array(
				'rule1' => array('rule'=> array('checkterms'), 'message' => 'Please check to accept our terms and conditions')
				),
			),
				
		'supplier_login'=>array(	
			  'email_id'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter registered email address.'),
					'rule2' => array('rule' => 'email','message' => 'Please enter valid email address'),
					'unique' => array('rule'=> array('isEmailExists'), 'message' => 'Account with this email does not exists.')
					),					
				'password'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter password.'),
					'rule2' => array('rule' => array('maxLength', 25),'message' => 'Password should be less than 25 charcter(s).')
					),
				'captchamatch'=>array(
					'rule1' =>array('rule' => 'notEmpty','message' => 'Please enter security code.'),
					'rule2' =>array('rule' => 'matchCaptcha','message' => 'Failed validating human check.')								
				)		
			
			),
			
		/*'supplier_profile'=>array(			
				'address1'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
					),
				'address2'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
					),
				'city'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
					),
				'state'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter state.'),
					),
				'zipcode'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please enter zip code.'),
					'rule2' => array('rule' => array('maxLength', 10),'message' => 'Name should be less than 10 charcter(s).')
					),
				'country'=>array(
					'rule1' => array('rule' => 'notEmpty','message' => 'Please select country.')
					)
			),*/
			
		'forgot_pass'=>array(	
				'email_id' =>	
					array(
						'rule1' =>
							array(
								 'rule' => 'notEmpty',
								'message' => 'Please enter email address here.'
							),
							array(
								'rule' => array('email', true),
								'message' => 'Please enter valid email address.'
							),
							array(
								'rule' => array('isEmailExists'),
								'message' => 'Your email is not registered.'
							) 
					),
			
			),
		'reset_pass'=>array(	
			'password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please  enter password.'),
				'rule2' => array('rule' => array('custom', '$\S*(?=\S{8,})(?=\S*[a-z])(?=\S*[A-Z])(?=\S*[\d])(?=\S*[\W])\S*$'),'message' => 'Password must contain minimum 8 characters with one capital letter, one number, one special character.')
				),
				
			'confirm_pass'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter confirm password.'),
				'checkpass' => array('rule'=> array('checkpassword'), 'message' => 'Your password and confirm password does not match.'),
				),
		)	
	);
	
	function check_zip()  
	{  
		if(!empty($this->data['Supplier']['zipcode'])) 
		{
			$zipcode=$this->data['Supplier']['zipcode'];
			if($result = ctype_alpha($zipcode)){
				return false; 
			}
		    return true;
		}
        return false; 
	}
	
	
	function checkpassword()     // to check pasword and confirm password
	{  
		if($this->data['Supplier']['password']===$this->data['Supplier']['confirm_pass']) 
		{
		    return true;
		}
        return false; 
        
	}
	function checkoldexist()     // to check pasword and confirm password
	{  
		$pass=$this->find('first',array('fields' => array('Supplier.password'),'conditions' => array('Supplier.id' =>$this->data['Supplier']['id'])));
		$hash=Security::hash(Configure::read('Security.salt').$this->data['Supplier']['password']);
		if($hash===$pass['Supplier']['password']) 
		{
		    return false;
		}
        return true; 
        
	}
	
	function checkemail()     // to check email and confirm email
	{  
		if(strcmp($this->data['Supplier']['email_id'],$this->data['Supplier']['confirm_email_id']) == 0 ) 
		{
		    return true;
		}
        return false; 
	}
	
	function isUniqueEmail($check) {
 
        $email = $this->find('first',array('fields' => array('Supplier.id'),'conditions' => array('Supplier.email_id' =>$check['email_id'])));
 
        if(!empty($email)){
                return false;
        }else{
            return true;
        }
    }
    
    function checkterms()     // to check terms and conditions
	{  
		if($this->data['Supplier']['terms'] == 1 ) 
		{
		    return true;
		}
        return false; 
	}
	
	 function country_code()  
	{  
		if(!empty($this->data['Supplier']['country_code'])) 
		{
			$country_code=$this->data['Supplier']['country_code'];
			if($country_code == '00'){
				return false; 
			}else{
				if(!preg_match('/^[+]{0,1}[0-9]{1,6}$/', $country_code)){
					return false; 
				}
			}
		    return true;
		}
        return false; 
	}
	
	 function area_code()  
	{  
		if(!empty($this->data['Supplier']['area_code'])) 
		{
			$country_code=$this->data['Supplier']['area_code'];
			if(!preg_match('/^[0-9]{2,6}$/', $country_code)){
				return false; 
			}
		    return true;
		}
        return false; 
	}
	
	
	function isEmailExists($check)
	{
        $email = $this->find('first',array('fields' => array('Supplier.id'),'conditions' => array('Supplier.email_id' =>$check['email_id'])));
 
        if(!empty($email)){
                return true;
        }else{
            return false;
        }
    }
    
    function matchCaptcha($nputValue)
    {
		App::import('Component', 'Session');
		$Captch = CakeSession::read('security_code');
		if($Captch != $this->data['Supplier']['captchamatch'])
		{
			return false;
		}
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
			}else if (!preg_match('/^[0-9 .\-]{7,14}$/i', $phone_no)){  			
			 			
						$errors [] = "Please enter valid contact number";
				return false;
			} 

			if (!empty($errors))
			return implode("\n", $errors);

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
		//	}else if (!preg_match('/^[+\s(]{0,1}[0-9]{0,2}[\s-.]{0,1}[0-9]{2,4}[)]{0,1}[-\s.]{0,1}[0-9]{2,4}[-\s.]{0,1}[0-9]{0,4}[-\s.]{0,1}[0-9]{0,4}$/', $phone_no)) {
	
			}else if (!preg_match('/^[+\s(]{0,1}[0-9]{0,2}[\s-.]{0,1}[0-9]{3}[)]{0,1}[-\s.]{0,1}[0-9]{3}[-\s.]{0,1}[0-9]{4}$/', $phone_no)){  		
		//	}else if (!preg_match('/^[0-9]{4,8}$/', $phone_no)) {
				$errors [] = "Please enter valid contact number";
				return false;
			} 

			if (!empty($errors))
			return implode("\n", $errors);

			return true;
		}
		
		function checkoldpass()     // to check pasword and confirm password
		{  
			$pass=$this->find('first',array('fields' => array('Supplier.password'),'conditions' => array('Supplier.id' =>$this->data['Supplier']['id'])));
			$hash=Security::hash(Configure::read('Security.salt').$this->data['Supplier']['old_password']);
			if($hash===$pass['Supplier']['password']) 
			{
		    return true;
			}
			return false; 
        
		}
}
?>
