<?php
Class Menu extends AppModel {
	public $name = "Menu";
	
	/*Validation for form data*/
	
	public $validate = array(
	
	'name' =>
        array(
            'rule1' =>
            array(
                'rule' => array('maxLength', 255),
                'message' => 'Menu name should be less than 255 charcter(s).'
            ),
            array(
                'rule' => 'notEmpty',
                'message' => 'Please enter menu name.'
            ) 
        )
        );
}
?>
