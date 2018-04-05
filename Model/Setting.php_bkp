<?php
// app/Model/User.php
class Setting extends AppModel 
{
    public $name = "Setting";
    	public $validate = array(
							'facebook' =>
								array(
									'rule1' =>
									array(
										'rule' => 'notEmpty',
										'message' => 'Please enter facebook url.'
									), 
									array(
										'rule' => array('url', true),
										'message' => 'Please enter valid facebook url .'
									)
									
								),
								
							'twitter' =>
								array(
									'rule1' =>
									array(
										'rule' => 'notEmpty',
										'message' => 'Please enter twitter url.'
									), 
									array(
										'rule' => array('url', true),
										'message' => 'Please enter valid twitter url .'
									)
									
								),	
							'linkedin' =>
								array(
									'rule1' =>
									array(
										'rule' => 'notEmpty',
										'message' => 'Please enter linkedin url.'
									), 
									array(
										'rule' => array('url', true),
										'message' => 'Please enter valid linkedin url .'
									)
									
								),		
    
          
    );
   
}
?>
