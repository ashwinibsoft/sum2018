<?php
$config['Path']['site'] =  WWW_ROOT.'img'.DS.'site'.DS;
$config['Path']['user'] =  WWW_ROOT.'img'.DS.'user'.DS;
$config['Path']['no_image'] =  $config['Path']['site'].'noimage.jpg';
$config['Path']['NewBuyerLogo'] =  WWW_ROOT.'img'.DS.'newbuyer'.DS.'logo'.DS;
$config['Path']['user_default_image'] =  WWW_ROOT.'img'.DS.'admin'.DS.'profiles'.DS.'no_profile_image.jpg';
$config['Site']['url'] =  'http://summer.checkyourprojects.com/';

$config['Site']['logo'] =  $config['Site']['url'].'img/logo.png';
$config['Site']['admin_logo'] =$config['Site']['logo'];
$config['Site']['fav_icon'] =  $config['Site']['url'].'img/favicon.ico';

$config['Resize']['user_image_admin_1x_width'] = 35 ;
$config['Resize']['user_image_admin_1x_height'] = 35 ;

$config['Resize']['user_image_admin_2x_width'] = 69 ;
$config['Resize']['user_image_admin_2x_height'] = 69 ;

$config['Resize']['user_image_admin_3x_width'] = 312 ;
$config['Resize']['user_image_admin_3x_height'] = 312 ;

$config['Resize']['user_image_admin_4x_width'] = 225 ;
$config['Resize']['user_image_admin_4x_height'] = 225 ;

$config['ShortCode'][] = array(
					'key'=>'site_name',
					'mode'=>'site',
					'name' =>'Site Name',
					'group'=>'Settings',
					'description'=>'This will show site name.'
					);
$config['ShortCode'][] = array(
					'key'=>'site_contact_email',
					'mode'=>'site',
					'name' =>'Site Contact Email',
					'group'=>'Settings',
					'description'=>'This is will show site contact email'
					);
$config['ShortCode'][] = array(
					'key'=>'site_contact_phone',
					'mode'=>'site',
					'name' =>'Site Contact Phone',
					'group'=>'Settings',
					'description'=>'This is will show site contact phone'
					);
$config['ShortCode'][] = array(
					'key'=>'site_contact_fax',
					'mode'=>'site',
					'name' =>'Site Contact Fax',
					'group'=>'Settings',
					'description'=>'This is will show site contact fax'
					);
$config['ShortCode'][] = array(
					'key'=>'site_contact_address',
					'mode'=>'site',
					'name' =>'Site Contact Address',
					'group'=>'Settings',
					'description'=>'This is will show site contact address'
					);
$config['ShortCode'][] = array(
					'key'=>'facebook',
					'mode'=>'social',
					'name' =>'Site facebook link',
					'group'=>'Settings',
					'description'=>'This is will show site facebook url'
					);

$config['ShortCode'][] = array(
					'key'=>'twitter',
					'mode'=>'social',
					'name' =>'Site twitter link',
					'group'=>'Settings',
					'description'=>'This is will show site twitter url'
					);
$config['ShortCode'][] = array(
					'key'=>'google',
					'mode'=>'social',
					'name' =>'Site google link',
					'group'=>'Settings',
					'description'=>'This is will show site google  url'
					);
$config['ShortCode'][] = array(
					'key'=>'google2',
					'mode'=>'social',
					'name' =>'Site google link',
					'group'=>'Settings',
					'description'=>'This is will show site google  url'
					);
$config['ipaddres']['allow'] =  array();
$config['ipaddres']['disallow'] =  array('182.73.242.201','182.73.242.200');



/**
 * Recaptcha settings
 */
Configure::write('reCAPTCHA.publicKey', '6LekvyETAAAAAFkV35MMQiTFCwLye4byMwzVb8pC');  
Configure::write('reCAPTCHA.privateKey', '6LekvyETAAAAAGdkw0NUoV0YQKCAR9cuuuLCfkR0');    

?>
