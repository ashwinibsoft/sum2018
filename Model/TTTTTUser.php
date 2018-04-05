<?php
// app/Model/User.php
App::uses('AuthComponent', 'Controller/Component');
class User extends AppModel {
    public $name = "User";
	
    var $actsAs = array('Multivalidatable');
	var $validationSets=array(
							'ResetPassword'=>array(
								'email' =>	
									array(
										'rule1' =>
											array(
												 'rule' => 'notEmpty',
												'message' => 'Please enter email address here.'
											),
											array(
											   'rule' => array('email', true),
												'message' => 'Please enter email address in a correct form.'
										) 
									)
							),
							'AdminLogin'=>array(
								'username' =>	
									array(
										'rule1' =>
											array(
												 'rule' => 'notEmpty',
												'message' => 'Please enter username.'
											)
								),
								'password' =>	
									array(
										'rule1' =>
											array(
												 'rule' => 'notEmpty',
												'message' => 'Please enter your password.'
											)
									)
							),
							'NewUserForm'=>array(
									'name' => array(
										'notEmpty' => array(
											'rule' => array('notEmpty'),
											'message' => 'Please enter user\'s first name.'
										)
									),
									
								'lname' => array(
										'notEmpty' => array(
											'rule' => array('notEmpty'),
											'message' => 'Please enter user\'s last name.'
										)
									),
									'email' => array(
											'notEmpty'=>array(
											'rule' =>array('notEmpty'),
											'message'=> 'Please enter user\'s email address.'
											),
											'isUnique'=>array(
											'rule'=>array('isUnique'),
											'message'=>'This email has already been registered.'
										), 
									'email'=>array(
										'rule' =>array('email'),
										'message'=> 'Please enter valid email address.'
											)
										),
									'username' => array(
										'notEmpty' => array(
											'rule' => array('notEmpty'),
											'message' => 'Please enter user\'s name.'
										),
									'isUnique'=>array(
										'rule'=>array('isUnique'),
										'message'=>'This username has already been registered.'
										)
									),
							),
							'ResetRegistrationPasswordForm'=>array(
								'password'=>  array( 
										array( 
											'rule' =>'notEmpty', 
											'message'=> 'Please enter password.'
											),
											array(
											'rule'    => array('minLength', 5),
											'message' => 'Password should be at least 6 digit long.'
											)
											 
										),
									'password2'=>array( 
										array( 
											'rule' =>'notEmpty', 
											'message'=> 'Confirm your password here.'
											 ),
										array(
											'rule' => 'checkpassword',
											//'required' => true,
											'message' => 'Your password and confirm password does not match.'
											//'on'=>'create'
										)
									)
							),
							
							'UserProfileUpdate'=>array(
								'name'=>  array( 
										array( 
											'rule' =>'notEmpty', 
											'message'=> 'Please enter name.'
											),
										array(
											'rule' => '/^[A-Za-z ]*$/',
											'message' => 'Please enter name in alphabet.'
											)	
										),
								'lname'=>array( 
									array( 
										'rule' =>'notEmpty', 
										'message'=> 'Please enter last name.'
										 ),
									 array(
										'rule' => '/^[A-Za-z ]*$/',
										'message' => 'Please enter last name in alphabet.'
									)
								),
								'email' => array(
											'notEmpty'=>array(
											'rule' =>array('notEmpty'),
											'message'=> 'Please enter user\'s email address.'
											),
											'isUnique'=>array(
											'rule'=>array('isUnique'),
											'message'=>'This email has already been registered.'
										), 
									'email'=>array(
										'rule' =>array('email'),
										'message'=> 'Please enter valid email address.'
											)
										),
								'image'=>array(
									'image_format'=>array(
										'rule'=>array('validate_image'),
										'message'=>'Pleaser upload valid image'
										)
									)
								
							),
	);
	public function beforeSave($options = array()) {
		if (isset($this->data[$this->alias]['password'])) {
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}
	
	function checkpassword()     // to check pasword and confirm password
	{  
		if(strcmp($this->data['User']['password'],$this->data['User']['password2']) == 0 ) 
		{
		    return true;
		}
        return false; 
	}
	function validate_image(){
		if(!empty($this->data['User']['image']['name']) && $this->data['User']['image']['error'] < 1 ) {
			$file_part = explode('.',$this->data['User']['image']['name']);
			$ext = array_pop($file_part);		
			if(!in_array(strtolower($ext),array('gif', 'jpeg', 'png', 'jpg'))) {
				return false;
			}
		}
		return true;
	}
	
	

	

    
}




?>
