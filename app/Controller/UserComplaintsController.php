<?php
class UserComplaintsController extends AppController {
	public $name='UserComplaints';
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
        
	public function index() {
                $this->layout = "user-inner";
                $this->set('cpage', 'mycomplaints');
		$this->set('user_complaints', $this->UserComplaint->find('all', array('conditions' => array('UserComplaint.sender_id' => $this->Session->read('User.id')), 'order' => array('UserComplaint.created DESC'))));
	}
        
/////////////////////////////////////////////////////////////////////////	
        
	public function admin_index() {
                $this->set('cpage', 'mycomplaints');
		$this->set('user_complaints', $this->UserComplaint->find('all', array('order' => array('UserComplaint.created DESC'))));
	}
        
/////////////////////////////////////////////////////////////////////////	
        
	public function theirs() {
                $this->layout = "user-inner";
                $this->set('cpage', 'theircomplaints');
		$this->set('user_complaints', $this->UserComplaint->find('all', array('conditions' => array('UserComplaint.receiver_id' => $this->Session->read('User.id')), 'order' => array('UserComplaint.created DESC'))));
	}
        
/////////////////////////////////////////////////////////////////////////	
        
	public function add() {
		if($this->request->is('post')){
			if($this->UserComplaint->save($this->request->data)){
				$this->Session->setFlash('Complaint Sent Successfully','flash_success');
				return $this->redirect('/user_complaints/add');
			}
			else{
				$this->Session->setFlash('Failed to Send Complaint','flash_error');
			}
                }
                $this->layout = "user-inner";
                $this->set('cpage', 'mycomplaints');
                $this->set('complaint_users', ClassRegistry::init('User')->getActiveUserList());
	}

/////////////////////////////////////////////////////////////////////////	
        
	public function edit($id=null) {
		$this->UserComplaint->id = $id;
		if($this->request->is('post') || $this->request->is('put')){
			if($this->UserComplaint->save($this->request->data)){
				$this->Session->setFlash('Complaint Updated Successfully','flash_success');
				return $this->redirect('/user_complaints/add');
			}
			else{
				$this->Session->setFlash('Failed to Update Complaint','flash_error');
			}
                }
                $this->layout = "user-inner";
                $this->set('cpage', 'mycomplaints');
                $this->set('complaint_users', ClassRegistry::init('User')->getActiveUserList());
                $this->data = $this->UserComplaint->find('first', array('conditions'=>array('UserComplaint.id'=>$id)));
	}
/////////////////////////////////////////////////////////////////////////	
        
	public function delete($id=null) {
		if ($this->UserComplaint->delete($id)){
			$this->Session->setFlash('Complaint deleted Successfully', 'flash_success');
			return $this->redirect('/user_complaints');
		}
		else{
			$this->Session->setFlash('Failed to deleted', 'flash_error');
		}
	}
        
///////////////////////////////////////////////////////////////////////////////

    public function admin_add_remarks() {
        $update = array(
            'UserComplaint' => array(
                'id' => $this->data['id'],
                'approved' => $this->data['status'],
                'remarks' => $this->data['remarks'],
            )
        );

        if ($this->UserComplaint->save($update)) {
            $status = $this->data['status'];
            $return = array();

            if ($status == 0) {
                $return['class'] = 'btn btn-mini btn-danger dropdown-toggle';
                $return['status'] = 'Pending';
            }
            if ($status == 1) {
                $return['class'] = 'btn btn-mini btn-success dropdown-toggle';
                $return['status'] = 'Approved';
            }
            if ($status == 2) {
                $return['class'] = 'btn btn-mini btn-inverse dropdown-toggle';
                $return['status'] = 'Declined';
            }
            $return['remarks'] = $this->data['remarks'];

            echo json_encode($return);
        }
        exit;
    }

///////////////////////////////////////////////////////////////////////////////
    
    public function user_get_their_complaint_count() {
        return $this->UserComplaint->find('count', array('conditions' => array('UserComplaint.receiver_id' => $this->Session->read('User.id'), 'UserComplaint.approved' => 0)));
    }

///////////////////////////////////////////////////////////////////////////////
    
    public function admin_get_complaints_count() {
        return $this->UserComplaint->find('count', array('conditions' => array('UserComplaint.approved' => 0)));
    }
    
///////////////////////////////////////////////////////////////////////////////

    public function get_user_their_complaint_by_user_id($user_id = NULL) {
        return $this->UserComplaint->find('all', array(
            'conditions' => array(
                'UserComplaint.receiver_id' => $user_id, 
//                'MONTH(UserComplaint.created)' => date('m'), 
//                'YEAR(UserComplaint.created)' => date('Y'), 
                'UserComplaint.approved' => 1)));
    }
}
?>