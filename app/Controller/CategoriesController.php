<?php
class CategoriesController extends AppController {
	public $name='Categories';

/////////////////////////////////////////////////////////////////////////	
	
	public function beforeFilter(){
		$this->set('cpage', 'category');
		parent::beforefilter();
		$this->__validateLoginStatus();
	}

/////////////////////////////////////////////////////////////////////////	
	
	public function admin_index() {
		$this->set('categories', $this->Category->find('all',array('order'=>array('Category.category'=>'ASC')))); 
	}
	
/////////////////////////////////////////////////////////////////////////	

	public function admin_add() {
		if($this->request->is('put') || $this->request->is('post')){
			if($this->Category->save($this->request->data)){
				echo $this->Session->setFlash('Project Category Added', 'flash_success');
			}
			else{
				echo $this->Session->setFlash('Failed to Add Category', 'flash_error');
			}
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_edit($id=NULL) {
		if($this->request->is('put') || $this->request->is('post')){
			$this->Category->id = $id;
			if($this->Category->save($this->request->data)){
				echo $this->Session->setFlash('Project Category Updated', 'flash_success');
			}
			else{
				echo $this->Session->setFlash('Failed to Update Category', 'flash_error');
			}
		}
		else{
			$this->data = $this->Category->find('first',array('conditions'=>array('Category.id'=>$id)));
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function admin_category_delete($id=NULL) {
		if ($this->Category->delete($id)){
			$this->Session->setFlash('Category Deleted Successfully', 'flash_success');
			return $this->redirect('/admin/categories');
		}
		else{
			$this->Session->setFlash('Failed to Delete Category', 'flash_error');
		}
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_all_categories(){
		return $this->Category->find('all', array('order'=>array('Category.category'=>'ASC')));
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_category_by_id($category_id=NULL){
		$this->recursive = -1;
		return $this->Category->find('first', array('conditions'=>array('Category.id'=>$category_id)));
	}
}
?>