<?php
Class Country extends AppModel {
	public $name = "Country";
	public $actsAs = array('Multivalidatable');
	public $belongsTo = array(
			'Continent' => array(
				'className' => 'Continent',
				'foreignKey' =>false,
				'conditions' => array('Country.continent_code=Continent.code'),
				'fields' => array('Continent.id','Continent.code', 'Continent.name'),
			)
		);
	
	public function country_list(){	
		$countries = $this->find('list',array('fields' => array('Country.country_code_char2','Country.country_name'),'order'=>array('Country.country_name'=>'ASC')));
		return $countries;
	}
	public function country_list2($code){	
		$countries = $this->find('list',array('conditions'=>array('Country.continent_code'=>$code),'fields' => array('Country.country_code_char2','Country.country_name'),'order'=>array('Country.country_name'=>'ASC')));
		return $countries;
	}
	public function continent_list2($code){	
		$continent = $this->find('first',array('conditions'=>array('Country.country_code_char2'=>$code),'fields' => array('Country.country_code_char2','Continent.code')));
		return $continent;
	}
	public function continent_list(){	
		$continent = $this->Continent->find('list',array('fields' => array('Continent.code','Continent.name'),'order'=>array('Continent.code'=>'ASC')));
		return $continent;
	}
}
?>
