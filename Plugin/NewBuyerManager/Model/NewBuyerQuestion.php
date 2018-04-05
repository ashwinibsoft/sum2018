<?php
Class NewBuyerQuestion extends NewBuyerManagerAppModel {
	public $name = "NewBuyerQuestion";
	public $actsAs = array('Multivalidatable');
	/*public $belongsTo = array(
		'Question' => array(
			'foreignKey' => false,
			'conditions' => array('NewBuyerQuestion.question_id = Question.id')
		)
	);
	public $belongsTo = array(
		'Question' => array(
			'className' => 'Question',
			'foreignKey' =>false,
			'conditions' => array('Question.id=NewBuyerQuestion.question_id'),
			'fields' => array('Question.id','Question.question', 'Question.category_id','Question.is_descriptive', 'Question.options','Question.status')
		)
		
	);
	
}
?>
