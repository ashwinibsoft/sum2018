<?php
$config = array();
$config['Name']['Plugin'] = "Existing Buyer Manager";

$config['Menu']['Left'] = array(
					array(
					'position'=>5,
					'icon'=>'fa-user',
					'title'=>'Existing Buyer Manager',
					'url'=>array('plugin'=>'existing_buyer_manager','controller'=>'existing_buyers','action'=>'admin_index','admin'=>true)
				)
); 
?>
