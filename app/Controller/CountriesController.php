<?php
class CountriesController extends AppController {
	public $name='Countries';
	
	public function get_all_countries(){
		return $this->Country->find('list', array('fields'=>array('Country.country')));
	}
}
?>