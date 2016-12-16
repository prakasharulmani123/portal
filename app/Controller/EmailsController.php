<?php
class EmailsController extends AppController {
	public $name='Emails';
    public $components = array('RequestHandler');
	
/////////////////////////////////////////////////////////////////////////	
	
	public function beforeFilter(){
		$this->set('cpage', 'email');
		parent::beforefilter();
		if($this->action != 'get_email'){
			$this->__validateLoginStatus();
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_index() {
		$this->set('emails', $this->Email->find('all'));
	}


/////////////////////////////////////////////////////////////////////////	

	public function admin_add() {
		if($this->request->is('post')){
			foreach($this->data['Email']['options'] as $key => $option){
				if($option == 0){
					unset($this->request->data['Email']['options'][$key]);
				}
			}
			$this->request->data['Email']['options'] = implode(',',$this->data['Email']['options']);
			if($this->Email->save($this->request->data)){
				$this->Session->setFlash('Email Added Successfully','flash_success');
				return $this->redirect('/admin/emails/add');
			}
			else{
				$this->Session->setFlash('Failed to Add Email','flash_error');
			}
		}
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function admin_edit($id=null) {
		$this->Email->id = $id;
		if($this->request->is('post') || $this->request->is('put')){
			foreach($this->data['Email']['options'] as $key => $option){
				if($option == 0){
					unset($this->request->data['Email']['options'][$key]);
				}
			}
			$this->request->data['Email']['options'] = implode(',',$this->data['Email']['options']);
			
			if($this->Email->save($this->request->data)){
				$this->Session->setFlash('Email Updated Successfully','flash_success');
				return $this->redirect('/admin/emails');
			}
			else{
				$this->Session->setFlash('Failed to Update Email','flash_error');
			}
		}
		else{
			$this->data = $this->Email->find('first', array('conditions'=>array('Email.id'=>$id)));
		}
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function admin_email_delete($id=null){
		if ($this->Email->delete($id)){
			$this->Session->setFlash('Email deleted Successfully', 'flash_success');
			return $this->redirect('/admin/emails');
		}
		else{
			$this->Session->setFlash('Failed to deleted', 'flash_error');
		}
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function all_email(){
		return $this->Email->find('all');
	}

/////////////////////////////////////////////////////////////////////////	

	public function all_to_email(){
		return $this->Email->find('all', array('conditions'=>array('Email.to_cc'=>'to', 'Email.active'=>1)));
	}

/////////////////////////////////////////////////////////////////////////	

	public function all_cc_email(){
		return $this->Email->find('all', array('conditions'=>array('Email.to_cc'=>'cc', 'Email.active'=>1)));
	}

/////////////////////////////////////////////////////////////////////////	

	public function all_bcc_email(){
		return $this->Email->find('all', array('conditions'=>array('Email.to_cc'=>'bcc', 'Email.active'=>1)));
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_email_options(){
		$options = array('1'=>'Daily Status Report', '2'=>'Leave Request', '3'=>'Database Backup');
		return $options;
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_email($type){
		return $this->Email->find('all', array('conditions'=>array('Email.to_cc'=>$type, 'Email.active'=>1)));;

	}
}
?>