<?php
Class EbLoginDetail extends ExistingBuyerManagerAppModel {
	public $name = "EbLoginDetail";
	public $actsAs = array('Multivalidatable');
	public $belongsTo = array(
		'ExistingBuyer' => array(
			'className' => 'ExistingBuyer',
			'foreignKey' =>'existing_buyer_id',
		),
		'FeedbackResponse' => array(
			'className' => 'FeedbackResponse',
			'foreignKey' =>false,
			'conditions'=>array('EbLoginDetail.request_id=FeedbackResponse.request_id','ExistingBuyer.id=FeedbackResponse.existing_buyer_id'),
		)
	);
	
	
}
?>
