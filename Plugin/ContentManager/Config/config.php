<?php
$config = array();
$config['Name']['Plugin'] = "Content Manager";
$config['Request']['Dashboard'] = array(
								'position'=>1,
								'title'=>'Content',
								'url'=>array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_dashboard','admin'=>true)
								);
$config['Menu']['Left'] = array(
					array(
					'position'=>1,
					'icon'=>'fa-copy',
					'title'=>'Content Manager',
					'url'=>array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_index','admin'=>true),
					'sub_menus'=> array(
						array(
							'title'=>'Manage Content',
							'url'=>array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_index','admin'=>true),
							'right'=>array('title'=>'Add new content','url'=>array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_add','admin'=>true))
							),
						array(
							'title'=>'Manage Menu',
							'url'=>array('plugin'=>false,'controller'=>'links','action'=>'admin_index',1,'admin'=>true)
							),
						array(
							'title'=>'Page Settings',
							'url'=>array('plugin'=>'content_manager','controller'=>'pages','action'=>'admin_settings','admin'=>true)
							)
						)
					)
				);
$config['ShortCode'][] = array(
					'key'=>'default_home_page',
					'mode'=>'page',
					'name' =>'Page Default HOme Page',
					'group'=>'Page Settings',
					'description'=>'This is goes to tool-tip'
					);
$config['ShortCode'][] = array(
					'key'=>'banner_image',
					'mode'=>'page',
					'name' =>'Page Banner image',
					'group'=>'Page Settings',
					'description'=>'This is goes to tool-tip banner image'
					);

$config['Folder']['Banner'] = "banner";
$config['Path']['Banner'] =  WWW_ROOT.'img'.DS.$config['Folder']['Banner'].DS;

$config['Folder']['Blog'] = "blogimage";
$config['Path']['Blog'] =  WWW_ROOT.'img'.DS.$config['Folder']['Blog'].DS;


$config['Path']['Gallery'] =  WWW_ROOT.'img'.DS.'gallery'.DS;
$config['Path']['NoImage'] =  WWW_ROOT.'img'.DS.'site'.DS.'noimage.jpg';
$config['Admin']['Limit'] = 20;

$config['Path']['FolderName'] =  'files';
$config['Path']['Assisted'] =  WWW_ROOT.'img'.DS.$config['Path']['FolderName'].DS;


/***These below code is used  for admin purpose*/
$config['image_list_width'] = "80";
$config['image_list_height'] = "80";
$config['image_edit_width'] = "290";
$config['image_edit_height'] = "240";
$config['image_front_width'] = "220";
$config['image_front_height'] = "200";

$config['image_front_list_width'] = "211";
$config['image_front_list_height'] = "156";

$config['image_admin_edit_width'] = "80";
$config['image_admin_edit_height'] = "80";


$config['default_banner_thumb_width'] = "568";
$config['default_banner_thumb_height'] = "350";

/**Belowe code is used for front banner image on front**/
$config['banner_image_width']="1366";
$config['banner_image_height']="390";
//$config['banner_image_height']="550";
$config['image_crop_ratio']='4:3';

$config['Template'] = array(
        'default'=>'Default',
        /*'home'=>'Home Page',*/
       /* 'assisted'=>'Right Side 1 Page(Gallery)',*/
        'right_side_page2'=>'Right Side 2 Page(Contact)',
        /*'full_width'=>'Full Width Page',*/
        /*'sub_page_listing'=>'Sub-Page Listing Page'*/
			);


/* Default word length of short description */

$config['default_home_block_length']=300;


/* Settings for on/off section of Page Manager*/
$config['Section'] = array(
	'default_banner_image'=>true,  //Default Banner Image
	'default_home_page' => true, //Home Page
	'special_page' =>true, //Special Page
	'home_page_block' =>true, //Home Page Blocks 
	'gallery'=>true, /*Manage Gallery Drop down on pages add/edit form*/
	'default_child_pages'=>true, //Show Child Pages like services
	'home_block_length'=>false, //Home block text length in words
);
$config['Settings']['expand'] = false; //Expand all page settings except default banner image

$config['CustomFields'] = array(
	'page_form'=>'Page',
	'page_settings'=>'Page Settings	'
);

$config['testv'][] = "test"; 




?>
