<?php
$config = array();
$config['Name']['Plugin'] = "Supplier Manager";

$config['Menu']['Left'] = array(
					array(
					'position'=>4,
					'icon'=>'fa-user',
					'title'=>'Supplier Manager',
					'url'=>array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'admin_index','admin'=>true),
					'sub_menus'=> array(
							array(
								'title'=>'Supplier',
								'url'=>array('plugin'=>'supplier_manager','controller'=>'suppliers','action'=>'admin_index','admin'=>true),
							),
							array(
								'title'=>'Payment',
								'url'=>array('plugin'=>'supplier_manager','controller'=>'payments','action'=>'admin_index','admin'=>true)
							),
						)
				)
);

$config['paypal'] = array(
			'status'=>'0',//0 for Test Mode, 1 for Live
			//'business_email'=>'jakegyl21-facilitator@gmail.com'
			'business_email'=>'jakegyl21@gmail.com'
			//'business_email'=>'amit.burgeon-facilitator@gmail.com'
		);
		
	/* For cancel subscription */
	$config['ApiUserName'] = 'jakegyl21-facilitator_api1.gmail.com';
	$config['ApiPassword'] ='ZGCPLXTDBA7GFBK2';
	$config['ApiSignature'] = 'AFcWxV21C7fd0v3bYYYRCpSSRl31A3cnHMbiz.kSfvszBKx-zfmDR.Cs';
	/* For category(tiers) */
$config['Category'] = array(
           't1' => 'tier1',
           't2' => 'tier2',
           't3' => 'tier3',
           't4' => 'tier4'
       );
?>
