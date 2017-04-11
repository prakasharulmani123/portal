<?php
class MeetingsController extends AppController {
	public $name='Meetings';

/////////////////////////////////////////////////////////////////////////	
	
	public function beforeFilter(){
		$this->set('cpage', 'meeting');
		parent::beforefilter();
		$this->__validateLoginStatus();
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function index() {
		$this->layout = 'user-inner';

		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->Session->write('Meeting.from_date', '');
			$this->Session->write('Meeting.to_date', '');
			$this->Session->write('Meeting.project_id', '');
			
			if(!empty($this->request->data['Meeting']['from_date']) && !empty($this->request->data['Meeting']['to_date'])) 
			{
				$this->Session->write('Meeting.from_date', date('Y-m-d', strtotime($this->request->data['Meeting']['from_date'])));
				$this->Session->write('Meeting.to_date', date('Y-m-d', strtotime($this->request->data['Meeting']['to_date'])));
			} 

			if(!empty($this->request->data['Meeting']['project_id'])) 
			{
				$this->Session->write('Meeting.project_id', $this->request->data['Meeting']['project_id']);
			} 
			
			return $this->redirect(array('action' => 'index'));
		}
		
		if($this->Session->check('Meeting')){
			$all = $this->Session->read('Meeting');
			
			$query = "Select * ";
			$query .= "From meetings Meeting ";
//			$query .= "Join projects Project ";
//			$query .= "On Project.id = Meeting.project_id ";
			$query .= "Where Meeting.user_id = ".$this->Session->read('User.id')." ";
			if($all['from_date'] != '' && $all['to_date'] != ''){
				$query .= "And Meeting.meeting_date Between '".$all['from_date']."' And '".$all['to_date']."' ";
			}
			if($all['project_id'] != ''){
				$query .= "And Meeting.project_id = ".$all['project_id']." ";
			}
			$query .= "Group By Meeting.meeting_date ";
			$query .= "Order By Meeting.meeting_date";
			
			$meetings = $this->Meeting->query($query);
//			echo $query;
//			pr($meetings);
//			exit;
			
		}
		else{ 
			$all = array('from_date' => '', 'to_date' => '', 'project_id' => '');
			$meetings = $this->Meeting->find('all',array('conditions' => array('Meeting.user_id' => $this->Session->read('User.id')), 'order'=>array('Meeting.meeting_date'=>'ASC'), 'group' => array('Meeting.meeting_date')));
		}
		
		$this->set(compact('all', 'meetings'));
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function meetings_su() {
		if($this->Session->read('User.super_user') == 0){
			$this->redirect('index');
		}
		$this->layout = 'user-inner';

		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->Session->write('Meeting.from_date', '');
			$this->Session->write('Meeting.to_date', '');
			$this->Session->write('Meeting.project_id', '');
			$this->Session->write('Meeting.user_id', '');
			
			if(!empty($this->request->data['Meeting']['from_date']) && !empty($this->request->data['Meeting']['to_date'])) 
			{
				$this->Session->write('Meeting.from_date', date('Y-m-d', strtotime($this->request->data['Meeting']['from_date'])));
				$this->Session->write('Meeting.to_date', date('Y-m-d', strtotime($this->request->data['Meeting']['to_date'])));
			} 

			if(!empty($this->request->data['Meeting']['project_id'])) 
			{
				$this->Session->write('Meeting.project_id', $this->request->data['Meeting']['project_id']);
			} 

			if(!empty($this->request->data['Meeting']['user_id'])) 
			{
				$this->Session->write('Meeting.user_id', $this->request->data['Meeting']['user_id']);
			} 
			
			return $this->redirect(array('action' => 'meetings_su'));
		}
		
		if($this->Session->check('Meeting')){
			$all = $this->Session->read('Meeting');
			
			$query = "Select * ";
			$query .= "From meetings Meeting ";
			$query .= "Join users User ";
			$query .= "On User.id = Meeting.user_id ";
			$query .= "Where Meeting.user_id != '' ";
			if($all['from_date'] != '' && $all['to_date'] != ''){
				$query .= "And Meeting.meeting_date Between '".$all['from_date']."' And '".$all['to_date']."' ";
			}
			if($all['project_id'] != ''){
				$query .= "And Meeting.project_id = ".$all['project_id']." ";
			}
			if($all['user_id'] != ''){
				$query .= "And Meeting.user_id = ".$all['user_id']." ";
			}
			$query .= "Group By Meeting.meeting_date, Meeting.user_id ";
			$query .= "Order By Meeting.meeting_date";
			
			$meetings = $this->Meeting->query($query);
//			echo $query;
//			pr($meetings);
//			exit;
			
		}
		else{ 
			$all = array('from_date' => '', 'to_date' => '', 'project_id' => '', 'user_id' => '');
			$meetings = $this->Meeting->find('all',array('order'=>array('Meeting.meeting_date'=>'ASC'), 'group' => array('Meeting.meeting_date', 'Meeting.user_id')));
		}
		
		$this->loadModel('User');
		
		$users = $this->User->find('list', array('fields' => array('User.id', 'User.employee_name'), 'conditions' => array('User.role !=' => 'admin', 'User.active' => 1)));
		$this->set(compact('all', 'meetings', 'users'));
		$this->set('cpage', 'meeting_su');
	}

/////////////////////////////////////////////////////////////////////////	

	public function add() {
		$this->layout = 'user-inner';
		
		if($this->request->is('post')){
			$meeting_date = date('Y-m-d', strtotime($this->request->data['Meeting']['meeting_date']));
			$this->request->data['Meeting']['meeting_date'] = $meeting_date;
			$this->request->data['Meeting']['meeting_schedule_start'] = $this->get_date_from_array($meeting_date, 'meeting_schedule_start');
			$this->request->data['Meeting']['meeting_actual_start'] = $this->get_date_from_array($meeting_date, 'meeting_actual_start');
			$this->request->data['Meeting']['meeting_schedule_end'] = $this->get_date_from_array($meeting_date, 'meeting_schedule_end');
			$this->request->data['Meeting']['meeting_actual_end'] = $this->get_date_from_array($meeting_date, 'meeting_actual_end');
			
			$this->loadModel('Project');
			$check_project = $this->Project->findByProjectname($this->request->data['Meeting']['project']);
			
			if(empty($check_project)){
				$this->Project->save(array('Project' => array('projectname' => $this->request->data['Meeting']['project'])));
				$this->request->data['Meeting']['project_id'] = $this->Project->getLastInsertId();
			}
			else{
				$this->request->data['Meeting']['project_id'] = $check_project['Project']['id'];
			}
			
			$this->Meeting->save($this->request->data);
			$this->Session->setFlash('Meeting Added successfully', 'flash_success');
			$this->redirect('add');
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function edit($id=NULL) {
		$meeting = $this->Meeting->findByIdAndStatus($id, 0);
		if(empty($meeting)){
			return $this->redirect('index');
		}
		
		$this->layout = 'user-inner';
		
		if($this->request->is('post') || $this->request->is('put')){
			$this->Meeting->read($id, null);
			$meeting_date = date('Y-m-d', strtotime($this->request->data['Meeting']['meeting_date']));
			$this->request->data['Meeting']['meeting_date'] = $meeting_date;
			$this->request->data['Meeting']['meeting_schedule_start'] = $this->get_date_from_array($meeting_date, 'meeting_schedule_start');
			$this->request->data['Meeting']['meeting_actual_start'] = $this->get_date_from_array($meeting_date, 'meeting_actual_start');
			$this->request->data['Meeting']['meeting_schedule_end'] = $this->get_date_from_array($meeting_date, 'meeting_schedule_end');
			$this->request->data['Meeting']['meeting_actual_end'] = $this->get_date_from_array($meeting_date, 'meeting_actual_end');
			
			$this->loadModel('Project');
			$check_project = $this->Project->findByProjectname($this->request->data['Meeting']['project']);
			
			if(empty($check_project)){
				$this->Project->save(array('Project' => array('projectname' => $this->request->data['Meeting']['project'])));
				$this->request->data['Meeting']['project_id'] = $this->Project->getLastInsertId();
			}
			else{
				$this->request->data['Meeting']['project_id'] = $check_project['Project']['id'];
			}
			
			$this->Meeting->save($this->request->data);
			$this->Session->setFlash('Meeting Added successfully', 'flash_success');
			$this->redirect('index');
		}
		else{
			$this->data = $meeting;
		}
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function meetings_view($user_id, $date) {
		if($this->Session->read('User.super_user') == 0){
			$this->Session->read('User.id') != $user_id ? $this->redirect('index') : '';
		}
		$this->layout = 'user-inner';
		$meetings = $this->Meeting->find('all',array('conditions' => array('Meeting.user_id' => $user_id, 'Meeting.meeting_date' => $date), 'order'=>array('Meeting.meeting_schedule_start'=>'ASC')));
		
		$this->set(compact('meetings', 'date')); 
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_date_from_array($date, $name){
		$s_date = $this->data['Meeting'][$name];
		return date('Y-m-d H:i:s', strtotime($date.' '.$s_date['hours'].':'.$s_date['minutes'].' '.$s_date['meridian']));
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_meeting_by_userid_date($user_id = NULL, $date = NULL){
		return $this->Meeting->findAllByUserIdAndMeetingDate($user_id, $date);
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_report_reset($redirect) 
	{
		$this->Session->delete('Meeting');
		return $this->redirect(array('action' => $redirect));
	}
	
/////////////////////////////////////////////////////////////////////////	

	public function report_reset($redirect) 
	{
		$this->Session->delete('Meeting');
		return $this->redirect(array('action' => $redirect));
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function admin_index() {
              $this->layout = "admin-inner";
		if ($this->request->is('post') || $this->request->is('put')) 
		{
			$this->Session->write('Meeting.from_date', '');
			$this->Session->write('Meeting.to_date', '');
			$this->Session->write('Meeting.project_id', '');
			$this->Session->write('Meeting.user_id', '');
			
			if(!empty($this->request->data['Meeting']['from_date']) && !empty($this->request->data['Meeting']['to_date'])) 
			{
				$this->Session->write('Meeting.from_date', date('Y-m-d', strtotime($this->request->data['Meeting']['from_date'])));
				$this->Session->write('Meeting.to_date', date('Y-m-d', strtotime($this->request->data['Meeting']['to_date'])));
			} 

			if(!empty($this->request->data['Meeting']['project_id'])) 
			{
				$this->Session->write('Meeting.project_id', $this->request->data['Meeting']['project_id']);
			} 

			if(!empty($this->request->data['Meeting']['user_id'])) 
			{
				$this->Session->write('Meeting.user_id', $this->request->data['Meeting']['user_id']);
			} 
			
			return $this->redirect(array('action' => 'admin_index'));
		}
		
		if($this->Session->check('Meeting')){
			$all = $this->Session->read('Meeting');
			
			$query = "Select * ";
			$query .= "From meetings Meeting ";
			$query .= "Join users User ";
			$query .= "On User.id = Meeting.user_id ";
			$query .= "Where Meeting.user_id != '' ";
			if($all['from_date'] != '' && $all['to_date'] != ''){
				$query .= "And Meeting.meeting_date Between '".$all['from_date']."' And '".$all['to_date']."' ";
			}
			if($all['project_id'] != ''){
				$query .= "And Meeting.project_id = ".$all['project_id']." ";
			}
			if($all['user_id'] != ''){
				$query .= "And Meeting.user_id = ".$all['user_id']." ";
			}
			$query .= "Group By Meeting.meeting_date, Meeting.user_id ";
			$query .= "Order By Meeting.meeting_date";
			
			$meetings = $this->Meeting->query($query);
//			echo $query;
//			pr($meetings);
//			exit;
			
		}
		else{ 
			$all = array('from_date' => '', 'to_date' => '', 'project_id' => '', 'user_id' => '');
			$meetings = $this->Meeting->find('all',array('order'=>array('Meeting.meeting_date'=>'ASC'), 'group' => array('Meeting.meeting_date', 'Meeting.user_id')));
		}
		
		$this->loadModel('User');
		
		$users = $this->User->find('list', array('fields' => array('User.id', 'User.employee_name'), 'conditions' => array('User.role !=' => 'admin', 'User.active' => 1)));
		$this->set(compact('all', 'meetings', 'users'));
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function admin_meetings_view($user_id, $date) {
              $this->layout = "admin-inner";
		$meetings = $this->Meeting->find('all',array('conditions' => array('Meeting.user_id' => $user_id, 'Meeting.meeting_date' => $date), 'order'=>array('Meeting.meeting_schedule_start'=>'ASC')));
		
		$this->set(compact('meetings', 'date')); 
	}

/////////////////////////////////////////////////////////////////////////	

	public function meeting_delete($date = NULL, $redirect = NULL, $id = NULL){
		$meetings = $this->Meeting->findAllByStatusAndMeetingDate(0, $date);
		
		if(!empty($meetings)){
			foreach($meetings as $meeting){
				$this->Meeting->delete($meeting['Meeting']['id']);
			}
			$this->Session->setFlash('Meeting Deleted successfully', 'flash_success');
		}
		else{
			$this->Session->setFlash('Not found', 'flash_error');
		}
		
		if($redirect == 'dailystatus'){
			$redirect = '../dailystatus';
		}elseif($redirect == 'pending_reports'){
			$redirect = '../pending_reports/dailystatus/'.$id;
		}
		
		return $this->redirect($redirect);
	}

/////////////////////////////////////////////////////////////////////////	

	public function meeting_delete_row($id = NULL, $redirect = NULL, $p_id = NULL){
		$meetings = $this->Meeting->findById($id);
		
		if(!empty($meetings)){
			$this->Meeting->delete($id);
			$this->Session->setFlash('Meeting Deleted successfully', 'flash_success');
		}
		else{
			$this->Session->setFlash('Not found', 'flash_error');
		}
		
		if($redirect == 'dailystatus'){
			$redirect = '../dailystatus';
		}elseif($redirect == 'pending_reports'){
			$redirect = '../pending_reports/dailystatus/'.$p_id;
		}
		return $this->redirect($redirect);
	}
	
}
?>