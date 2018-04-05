<?php
$config = array();
$config['Name']['Plugin'] = "New Buyer Manager";

$config['Menu']['Left'] = array(
					array(
					'position'=>6,
					'icon'=>'fa-chain',
					'title'=>'New Buyer Manager',
					'url'=>array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'admin_index','admin'=>true),
					'sub_menus'=> array(
						array(
							'title'=>'Manage New Buyer',
							'url'=>array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'admin_index','admin'=>true)
							),
						array(
							'title'=>'New Buyer Requests',
							'url'=>array('plugin'=>'new_buyer_manager','controller'=>'new_buyers','action'=>'admin_request','admin'=>true)
							)
						)
				
					)
				);

$config['Folder']['TeamImage'] = "teamimage";
$config['Path']['NewBuyerImage'] =  WWW_ROOT.'img'.DS.$config['Folder']['TeamImage'].DS;
$config['Path']['Gallery'] =  WWW_ROOT.'img'.DS.'gallery'.DS;
$config['Path']['NoImage'] =  WWW_ROOT.'img'.DS.'site'.DS.'noimage.jpg';
$config['Admin']['Limit'] = 20;

$config['Path']['FolderName'] =  'files';
$config['Path']['Assisted'] =  WWW_ROOT.'img'.DS.$config['Path']['FolderName'].DS;
/***These below code is used  for admin purpose*/
$config['image_edit_width'] = "100";
$config['image_edit_height'] = "100";

/*Front side Team image dimension*/
$config['front_list_width'] = "500";
$config['front_list_height'] = "500";

$config['NewBuyer']['templates'] = array(
'2_page_template' =>'2 Page Template',
'template2' =>'Template2',
'template3' => 'Template3'
);






?>
