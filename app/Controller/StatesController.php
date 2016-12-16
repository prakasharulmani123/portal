<?php
class StatesController extends AppController {
	public $name='States';
	
	public function get_states($country_id=null){
		return $this->State->find('list', array('conditions'=>array('State.country_id'=>$country_id), 'fields'=>array('State.state'), 'order'=>array('State.state'=>'ASC')));
	}
}
?>