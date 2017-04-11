<?php
class WorksController extends AppController {
	public $name='Works';

/////////////////////////////////////////////////////////////////////////	
	
	public function beforeFilter(){
		$this->set('cpage', 'work');
		parent::beforefilter();
		$this->__validateLoginStatus();
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function admin_index() {
              $this->layout = "admin-inner";
		$this->set('works', $this->Work->find('all',array('order'=>array('Work.work'=>'ASC')))); 
	}
	
/////////////////////////////////////////////////////////////////////////	

	public function admin_add() {
              $this->layout = "admin-inner";
		if($this->request->is('put') || $this->request->is('post')){
			if($this->Work->save($this->request->data)){
				echo $this->Session->setFlash('Project Work Added', 'flash_success');
			}
			else{
				echo $this->Session->setFlash('Failed to Add Work', 'flash_error');
			}
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_edit($id=NULL) {
              $this->layout = "admin-inner";
		if($this->request->is('put') || $this->request->is('post')){
			$this->Work->id = $id;
			if($this->Work->save($this->request->data)){
				echo $this->Session->setFlash('Project Work Updated', 'flash_success');
			}
			else{
				echo $this->Session->setFlash('Failed to Update Work', 'flash_error');
			}
		}
		else{
			$this->data = $this->Work->find('first',array('conditions'=>array('Work.id'=>$id)));
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_work_delete($id=NULL) {
              $this->layout = "admin-inner";
		if ($this->Work->delete($id)){
			$this->Session->setFlash('Work Deleted Successfully', 'flash_success');
			return $this->redirect('/admin/works');
		}
		else{
			$this->Session->setFlash('Failed to Delete Work', 'flash_error');
		}
	}

/////////////////////////////////////////////////////////////////////////	

	 public function get_category_works($category_id = NULL)
	 {
		$result = "";
		
		$works = $this->Work->find('all', array('conditions' => array('Work.category_id' => $category_id),'order'=>array('Work.work' => 'ASC')));
		$result .= '<select name="data[DailyStatus][work_id]" class="validate[required]" id="DailyStatusWorkId"><option value="">Select Work</option>';
		foreach($works as $work)
		{
			$result .= '<option value="'.$work['Work']['id'].'">'.$work['Work']['work'].'</option>';
		}
		$result .= '</select>';
		echo $result; exit;
	 }

/////////////////////////////////////////////////////////////////////////	

	public function get_work_by_id($work_id=NULL){
		$this->recursive = -1;
		return $this->Work->find('first', array('conditions'=>array('Work.id'=>$work_id)));
	}

////////////////////////////////////////////////////////////////////////
	
	public function get_all_works(){
		return $this->Work->find('all', array('order'=>array('Work.work'=>'ASC')));
	}
}
?>