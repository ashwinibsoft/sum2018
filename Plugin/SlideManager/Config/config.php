<?php
$config = array();
$config['Name']['Plugin'] = "Slide Manager";
$config['Path']['FolderName'] =  'slide';
$config['Path']['Slide'] =  WWW_ROOT.'img'.DS.$config['Path']['FolderName'].DS;
$config['Request']['Dashboard'] = array(
								'position'=>3,
								'title'=>'Slide',
								'url'=>array('plugin'=>'slide_manager','controller'=>'slides','action'=>'admin_dashboard','admin'=>true)
								);
$config['Menu']['Left'] = array(
					array(
					'position'=>3,
					'icon'=>'fa-picture-o',
					'title'=>'Slide Manager',
					'url'=>array('plugin'=>'slide_manager','controller'=>'slides','action'=>'admin_index','admin'=>true),
					)
				);
$config['Admin']['Limit'] = 20;
$config['image_list_width'] = "80";
$config['image_list_height'] = "80";
$config['image_edit_width'] = "290";
$config['image_edit_height'] = "240";

$config['slide_image_width']="1366"; //Use to set slide image width that shows on front side

$config['slide_image_height']="670"; //Use to set slide image height that shows on front side

$config['slide_logo_width']="281";
$config['slide_logo_height']="59";
$config['image_crop_ratio']='4:3';


$config['Fields']['Options'] =  array(
	'slide_title'=>true, //Use for slide Title field
	'slide_description'=>false //Use for slide Description field
);

?>
