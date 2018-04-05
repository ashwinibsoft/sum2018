<?php Class SupplierBuyer extends SupplierManagerAppModel {
	public $name = "SupplierBuyer";
	public $actsAs = array('Multivalidatable');
	 public $belongsTo = array(
			'Supplier' => array(
				'className' => 'Supplier',
				'foreignKey' =>'supplier_id',
			),
			'NewBuyer' => array(
				'className' => 'NewBuyer',
				'foreignKey' =>'buyer_id',
			),
			'Country' => array(				
			 'foreignKey' => false,
			 'conditions' => array('Supplier.country=Country.country_code_char2'),
		    ),
		);
		
	public $validationSets = array(
	'supplier_add_buyer'=>array(
			'title'=>array(
				'rule1' => array('rule' => 'notEmpty','message' => 'Please select title.'),
				),
			)	
	);
	
	
}
?>
