<?php Class FeedbackResponse extends SupplierManagerAppModel {
	public $name = "FeedbackResponse";
	public $actsAs = array('Multivalidatable');
	
	public $belongsTo = array(
		'ExistingBuyer' => array(
			'className' => 'ExistingBuyer',
			'foreignKey' =>'existing_buyer_id',
		)
	);
}
?>
