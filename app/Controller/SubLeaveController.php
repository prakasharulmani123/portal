<?php
App::uses('AppController', 'Controller');
class SubLeavesController extends AppController {
	public $name='SubLeave';
    public $helpers = array('Html', 'Form', 'Js', 'Paginator');
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler');

///////////////////////////////////////////////////////////////////////////////

	public function beforeFilter(){
		parent::beforefilter();
		$this->__validateLoginStatus();
	}
}
