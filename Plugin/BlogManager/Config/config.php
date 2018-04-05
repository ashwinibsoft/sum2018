<?php
$config = array();
$config['Name']['Plugin'] = "Blog Manager";
$config['Menu']['Left'] = array(
					array(
					'position'=>12,
					'icon'=>'fa fa-rss',
					'title'=>'Blog Manager',
					'url'=>array('plugin'=>'blog_manager','controller'=>'posts','action'=>'admin_index','admin'=>true),
					'sub_menus'=> array(
						array(
							'title'=>'Manage Post',
							'url'=>array('plugin'=>'blog_manager','controller'=>'posts','action'=>'admin_index','admin'=>true),
							'right'=>array('title'=>'Add new post','url'=>array('plugin'=>'blog_manager','controller'=>'posts','action'=>'admin_add','admin'=>true))
							),
						array(
							'title'=>'Manage Category',
							'url'=>array('plugin'=>'blog_manager','controller'=>'blog_categories','action'=>'admin_index','admin'=>true)
							),
						array(
							'title'=>'Manage Comments',
							'url'=>array('plugin'=>'blog_manager','controller'=>'comments','action'=>'admin_index','admin'=>true)
							),	
						array(
							'title'=>'Post Settings',
							'url'=>array('plugin'=>'blog_manager','controller'=>'posts','action'=>'admin_settings','admin'=>true)
							)
						)
					)
				);
$config['Settings']['post_gallery']=true;
$config['Settings']['post_category']=true;
$config['Settings']['post_image']=true;
$config['Settings']['post_seo']=true;
$config['Settings']['post_attribute']=true;
$config['Folder']['Post'] = "post";
$config['Path']['Post'] =  WWW_ROOT.'img'.DS.$config['Folder']['Post'].DS;
$config['Folder']['PostCategory'] = "postcategory";
$config['Path']['PostCategory'] =  WWW_ROOT.'img'.DS.$config['Folder']['PostCategory'].DS;
$config['Path']['Gallery'] =  WWW_ROOT.'img'.DS.'gallery'.DS;
$config['Folder']['Profile'] = "user";
$config['Path']['Profile'] =  WWW_ROOT.'img'.DS.$config['Folder']['Profile'].DS;
$config['Path']['NoImage'] =  WWW_ROOT.'img'.DS.'site'.DS.'noimage.jpg';
$config['Path']['NoProfile'] =  WWW_ROOT.'img'.DS.'site'.DS.'noprofile.jpg';
$config['Admin']['Limit'] = 20;
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


$config['default_post_thumb_width'] = "625";
$config['default_post_thumb_height'] = "350";

/**Belowe code is used for front post image on front**/
$config['post_image_width']="1400";
$config['post_image_height']="550";
$config['image_crop_ratio']='4:3';

$config['default_postcategory_thumb_width'] = "625";
$config['default_postcategory_thumb_height'] = "350";

/**Belowe code is used for front post image on front**/
$config['postcategory_image_width']="1400";
$config['postcategory_image_height']="550";
$config['Blog']['templates'] = array(
'two_page_template' =>'Two Page Template',
'template2' =>'Template2',
'template3' => 'Template3'
);
$config['Section'] = array(
	'default_post_image'=>true,  //Default Post Image
	'gallery'=>true, /*Manage Gallery Drop down on pages add/edit form*/

);


?>
