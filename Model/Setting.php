<?php
// app/Model/Setting.php
class Setting extends AppModel {
	public $name = "Setting";
	var $actsAs = array('Multivalidatable'); 
	var $validationSets=array(
			'SiteSetting'=>array(
					'site_name'=>  array( 
						array( 
						'rule' =>'notEmpty', 
						'message'=> 'Please enter name.'
						)
					),
			'site_contact_email'=>
					array( 
					'rule1' =>
						array(
						'rule' =>'notEmpty',
						'message'=> 'Please enter contact email.'
						),
					array(
					'rule' =>array('email'),
					'message'=> 'Please enter valid contact email.'
					)
					),
			'site_contact_address'=>array( 
					array( 
					'rule' =>'notEmpty', 
					'message'=> 'Please enter address.'
					),
					),
			'site_payment_fee'=>array( 
					array( 
					'rule' =>'notEmpty', 
					'message'=> 'Please enter fee amount for suppliers.'
					),
					array(
					 'rule' => array('money', 'left'),/*'rule' => array('decimal', 2),*/
					'message'=> 'Please enter valid fee amount.'
					)
					)	
				),
			'googleac'=>array(
					'site_google_analytic_code'=>  array( 
						array( 
						'rule' =>'/^ua-\d{4,9}-\d{1,4}$/i', 
						'message'=> 'Please enter google analytic code in correct format',
						'allowEmpty'=>true
						),
					)
				),
			'SmtpSetting'=>array(
				'smtp_status'=>  array( 
					array( 
					'rule' =>'notEmpty', 
					'message'=> 'Please select smtp status.'
					)
				),
				'smtp_host'=>  array( 
					array( 
					'rule' =>'__is_smtp_active', 
					'message'=> 'Please enter smtp host.'
					)
				),
				'smtp_email'=>  
					array( 
					'rule1' =>
						array(
						'rule' =>'__is_smtp_active',
						'message'=> 'Please enter email .'
						),
						array(
						'rule' =>array('email'),
						'message'=> 'Please enter valid email address.',
						'allowEmpty'=>true
						)
					),
				'smtp_password'=>  array( 
					array( 
					'rule' =>'__is_smtp_active', 
					'message'=> 'Please enter password.'
					)
				),
				'smpt_port'=>  array( 
					array( 
					'rule' =>'__is_smtp_active', 
					'message'=> 'Please enter port number.'
					),
				)
			),
			'SeoSetting'=>array(
					'site_title'=>  array( 
						array( 
						'rule' =>array('maxLength',50), 
						'message'=> 'Please enter title below the 50 characters'
						)
					),
					'site_metakeyword'=>  array( 
						array( 
						'rule' =>array('maxLength',250), 
						'message'=> 'Please enter metakeyword below the 250 characters'
						)
					),
					'site_metadescription'=>  array( 
						array( 
						'rule' =>array('maxLength',160), 
						'message'=> 'Please enter metadescription below the 160 characters'
						),
					)
			),
			'SocialMedia'=>array(
				'facebook' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid facebook url .',
							'allowEmpty'=>true
						)
						
					),
					
				'twitter' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid twitter url .',
							'allowEmpty'=>true
						)
					),
				'google' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid google url .',
							'allowEmpty'=>true
						)
					),
				'linkedin' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid linkedin url .',
							'allowEmpty'=>true
						)
					),
				'p_interest' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid P Interest url .',
							'allowEmpty'=>true
						)
					),
				'tumblr' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid Tumblr url .',
							'allowEmpty'=>true
						)
					),
				'instagram' =>
					array(
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid Instagram url .',
							'allowEmpty'=>true
						)
					),
				)
		);
	public function __is_smtp_active($data){
		$val = array_pop($data);
		$val = trim($val);
		if((int)$this->data['Setting']['smtp_status']==2){
			return true;
		}else{
			if(!empty($val)){
				return true;
			}
		}
		return false;
	}
	public function chkImageExtension($data) {
		if($data['banner_image']['name'] != ''){
				$fileData= pathinfo($data['banner_image']['name']);
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


}
?>
