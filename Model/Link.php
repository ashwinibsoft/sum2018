<?php
Class Link extends AppModel {
	public $name = "Link";
	var $actsAs = array('Multivalidatable');
	/*Validation for form data*/
		
	public $validate = array(
				'name' =>
					array(
						'rule1' =>
						array(
							'rule' => array('maxLength', 255),
							'message' => 'Link name should be less than 255 charcter(s).'
						),
						array(
							'rule' => 'notEmpty',
							'message' => 'Please enter link name.'
						) 
					),
					'url_key' =>
						array(
						'rule1' =>
						array(
							'rule' => array('maxLength', 255),
							'message' => 'SEO key should be less than 255 charcter(s).'
						),
						array(
							'rule' => 'notEmpty',
							'message' => 'Please enter SEO keyword.'
						),
						array(
							'rule'     => '/^[a-zA-Z.-]{1,}$/i',
							'message'  => 'Only alphabets and underscore allowed',
						),      
						'isUnique'=>array(
							'rule'=>array('isUnique'),
							'message'=>'This SEO keyword already been taken.',
										 
						) 
					)
        );
        
        var $validationSets=array(
			'CustomLink'=>array(
				'url'=>
					array(					
						array(
							'rule' => 'notEmpty',
							'message' => 'Please enter url.'
						),
						array(
							'rule' => array('url', true),
							'message' => 'Please enter valid url.'
						)
					),
				'name'=>
					array(					
						array(
							'rule' => 'notEmpty',
							'message' => 'Please enter link text.'
						)						
					),
			)
		);
}
?>
