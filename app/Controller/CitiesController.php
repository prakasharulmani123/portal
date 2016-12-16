<?php
class CitiesController extends AppController {
	public $name='Cities';

	 public function get_all_cities($state_id = NULL)
	 {
		$result = "";
		$cities = $this->City->find('all', array('conditions' => array('City.state_id' => $state_id),'order'=>array('City.city' => 'ASC')));
		$result .= '<select name="data[User][city_id]" class="validate[required]"><option value="">Select city</option>';
		foreach($cities as $city)
		{
			$result .= '<option value="'.$city['City']['id'].'">'.$city['City']['city'].'</option>';
		}
		$result .= '</select>';
		echo $result; exit;
	 }
	 
	 public function get_cities($state_id = NULL){
		 return $this->City->find('all', array('conditions'=>array('City.state_id' => $state_id)));
	 }

	 public function get_cities_list($state_id = NULL){
		 return $this->City->find('list', array('fields'=>array('City.city'), 'conditions'=>array('City.state_id' => $state_id), 'order'=>array('City.city ASC')));
	 }
}
?>