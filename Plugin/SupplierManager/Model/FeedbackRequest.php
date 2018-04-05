<?php Class FeedbackRequest extends SupplierManagerAppModel {
	public $name = "FeedbackRequest";
	public $actsAs = array('Multivalidatable');
	
	 public $belongsTo = array(
		 'Supplier' => array(
			 'className' => 'Supplier',
			 'foreignKey' =>'supplier_id',
		 ),
		 'SupplierBuyer' => array(
		         'foreignKey' => false,
                 'displayField'=>'supplier_id',
                 'primaryKey'=>'supplier_id',
				 'conditions' => array('Supplier.id=SupplierBuyer.supplier_id'),
		 ),
	 );
	
}
?>
