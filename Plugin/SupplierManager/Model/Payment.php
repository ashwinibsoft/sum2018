<?php
Class Payment extends SupplierManagerAppModel {
	public $name = "Payment";
	public $actsAs = array('Multivalidatable');
	public $belongsTo = array(
		'Supplier' => array(				
			 'foreignKey' => false,
			 'conditions' => array('Payment.supplier_id=Supplier.id'),
		),
		'FeedbackRequest'
	);
	/*public $hasOne = array(
		'FeedbackRequest' => array(				
			 'foreignKey' => false,
			 'conditions' => array('Payment.id=FeedbackRequest.payment_id'),
		)
	);*/
	
}
?>
