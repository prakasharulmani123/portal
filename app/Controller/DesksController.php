<?php
class DesksController extends AppController {
	public $name='Desks';

/////////////////////////////////////////////////////////////////////////	
	
	public function beforeFilter(){
//		$this->set('cpage', 'work');
//		parent::beforefilter();
//		$this->__validateLoginStatus();
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function page() {
		$this->layout = 'desk';
	}
	
}
