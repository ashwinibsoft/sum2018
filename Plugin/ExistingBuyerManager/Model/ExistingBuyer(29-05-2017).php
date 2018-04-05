<?php
Class ExistingBuyer extends ExistingBuyerManagerAppModel {
	public $name = "ExistingBuyer";
	public $actsAs = array('Multivalidatable');
	public $belongsTo = array(
		'Country' => array(				
			 'foreignKey' => false,
			 'conditions' => array('ExistingBuyer.country=Country.country_code_char2'),
		)
	);
	public $validationSets = array(
	'ebuyer_add'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title.'),
				),
			'first_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter name.'),
				'rule2' => array('rule' => array('maxLength', 20),'message' => 'Name should be less than 20 charcter(s).')
				),
			'last_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter last name.'),
				'rule2' => array('rule' => array('maxLength', 20),'message' => 'Name should be less than 20 charcter(s).')
				),
			'job_title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter job title.'),
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Name should be less than 30 charcter(s).')
				),
			'org_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter company/organisation name.'),
				'rule2' => array('rule' => array('maxLength', 50),'message' => 'Name should be less than 50 charcter(s).')
				),
			'address1'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address.'),
				),
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city.'),
				),
			/*'state'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter state.'),
				),*/
			'zipcode'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter zip code.'),
				'rule2' => array('rule' => array('maxLength', 10),'message' => 'Please enter valid zip code.'),
				//'rule3' => array('rule' => 'check_zip','message' => 'Please enter valid zip code.')
			    'rule3' => array('rule'=>array('custom', '/^[a-zA-Z0-9 ]*$/i'),'message' => 'Please enter valid zip code.'),
				'rule4' => array('rule' => 'check_zip','message' => 'Please enter valid zip code.')
				),
			'country'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select country.'),
				),
			'relationship'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select realtionship.'),				
				),			
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter email address.'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address.'),
			//	'rule3' => array('rule' => 'isUnique','message' => 'Existing buyer with this email already added.'),
				)
		),
		
	'ebuyer_add2'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title for each existing buyer(s).'),
				),
			'first_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter name for each existing buyer(s).'),
				'rule2' => array('rule' => array('maxLength', 20),'message' => 'Name should be less than 20 charcter(s).')
				),
			'last_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter last name for each existing buyer(s).'),
				'rule2' => array('rule' => array('maxLength', 20),'message' => 'Name should be less than 20 charcter(s).')
				),
			'job_title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter job title for each existing buyer(s).'),
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Name should be less than 30 charcter(s).')
				),
			'org_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter company/organisation name for each existing buyer(s).'),
				'rule2' => array('rule' => array('maxLength', 50),'message' => 'Name should be less than 50 charcter(s).')
				),
			'address1'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter address for each existing buyer(s).'),
				),
			'city'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter city for each existing buyer(s).'),
				),
			/*'state'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter state.'),
				),*/
			'zipcode'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter zip code for each existing buyer(s).'),
				'rule2' => array('rule' => array('maxLength', 10),'message' => 'Please enter valid zip code for each existing buyer(s).'),
				//'rule3' => array('rule' => 'check_zip','message' => 'Please enter valid zip code.')
				
				'rule3' => array('rule'=>array('custom', '/^[a-zA-Z0-9 ]*$/i'),'message' => 'Please enter valid zip code for each existing buyer(s).'),
				'rule4' => array('rule' => 'check_zip','message' => 'Please enter valid zip code for each existing buyer(s).')
				),
			'country'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select country for each existing buyer(s).'),
				),
			'relationship'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select realtionship for each existing buyer(s).'),				
				),			
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter email address for each existing buyer(s).'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address for each existing buyer(s).'),
			//	'rule3' => array('rule' => 'isUnique','message' => 'Existing buyer with this email already added.'),
				)
		),	
		
	 'ebuyer_email'=>array(					
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter email address.'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address.'),
				'rule3' => array('rule' => 'isUnique','message' => 'Existing buyer with this email already added.'),
				)
		),
			
		'existing_buyer_login'=>array(
			'email_id'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter registered email address.'),
				'rule2' => array('rule' => 'email','message' => 'Please enter valid email address'),
				'unique' => array('rule'=> array('isEmailExists'), 'message' => 'Account with this email does not exists')
			),
				
			'password'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please  enter password.'),
				'rule2' => array('rule' => array('maxLength', 255),'message' => 'Password should be less than 255 charcter(s).')
			),
			'captchamatch'=>array(
				'rule1' =>array('rule' => 'notEmpty','message' => 'Please enter security code.'),
				'rule2' =>array('rule' => 'matchCaptcha','message' => 'Failed validating human check.')	
												
			)
		),
		'existing_buyer_info'=>array(
			'job_title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter your job title.'),
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Job title should be less than 20 character(s)'),
			),
				
			'org_name'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please enter organisation name.'),
				'rule2' => array('rule' => array('maxLength', 30),'message' => 'Organisation name should be less than 30 character(s).')
			),
			
		),
		
		'feedback'=>array(
			'video'=> array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please upload video.'),
				'rule2' => array('rule' => array('extension',array('mp4','MOV','mov')),'message' => 'Please upload only mp4/mov video'),
				'rule3' => array('rule' => array('fileSize', '<=', '5MB'),'message' => 'Video must be less than 5MB') 
			),	
		)
	
	);
	
	function isEmailExists($check)
	{
        $email = $this->find('first',array('fields' => array('ExistingBuyer.id'),'conditions' => array('ExistingBuyer.email_id' =>$check['email_id'])));
 
        if(!empty($email)){
                return true;
        }else{
            return false;
        }
    }
    
    function isEmailRegistered($check)
	{
		//print_r($this->data); die;
		
        $email = $this->find('first',array('fields' => array('ExistingBuyer.id'),'conditions' => array('ExistingBuyer.email_id' =>$check['email_id'],'ExistingBuyer.supplier_id'=>$this->data['ExistingBuyer']['supplier_id'])));
        
        if(!empty($email)){
			if($this->data['ExistingBuyer']['id'] == $email['ExistingBuyer']['id']){
				return true;
			}
                return false;
        }else{
            return true;
        }
    }
    
    function matchCaptcha($nputValue)
    {
		App::import('Component', 'Session');
		$Captch = CakeSession::read('security_code');
		if($Captch != $this->data['ExistingBuyer']['captchamatch'])
		{
			return false;
		}
		return true;
	}
	
	 function check_zip_bkp()  
	{  
		if(!empty($this->data['ExistingBuyer']['zipcode'])) 
		{
			$zipcode=$this->data['ExistingBuyer']['zipcode'];
			if(!preg_match('/^[0-9]{5,10}$/', $zipcode)){
				return false; 
			}
		    return true;
		}
        return false; 
	}
	
	function check_zip()  
	{  
		if(!empty($this->data['ExistingBuyer']['zipcode'])) 
		{
			$zipcode=$this->data['ExistingBuyer']['zipcode'];
			if($result = ctype_alpha($zipcode)){
				return false; 
			}
		    return true;
		}
        return false; 
	}
	
	
}
?>
