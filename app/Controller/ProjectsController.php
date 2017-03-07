<?php
class ProjectsController extends AppController {
	public $name='Projects';

/////////////////////////////////////////////////////////////////////////	
	public function find() {
              $this->autoRender = false;
                      $project = array();
        $pro_name = $this->Project->find('all');
        foreach ($pro_name as $key => $product) {
            array_push($project, ucfirst($product['Project']['projectname']));
        }
        asort($project);
        echo json_encode(array_unique($project));
    }
    ////////////////////////
	public function beforeFilter(){
		$this->set('cpage', 'project');
		parent::beforefilter();
		$this->__validateLoginStatus();
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_meeting_projects(){
		$this->loadModel('DailyStatus');
		$project = array();
		$daily_projects = $this->DailyStatus->query("SELECT DISTINCT(projectname) FROM daily_statuses WHERE projectname != '' AND category_id NOT IN (22,23,24)");
		
		foreach($daily_projects as $key => $daily_project){
			array_push($project,ucfirst($daily_project['daily_statuses']['projectname']));
		}
		
		$current_projects = $this->Project->find('all');
		foreach($current_projects as $key => $current_project){
			array_push($project,ucfirst($current_project['Project']['projectname']));
		}
		
		asort($project);
		echo json_encode(array_unique($project));
		exit;
	}

/////////////////////////////////////////////////////////////////////////	

	public function get_meeting_projects_array(){
		$project_array = $this->Project->find('all', array('order' => array('Project.projectname ASC')));
		$project = array();
		
		foreach($project_array as $project_arr){
			$project[$project_arr['Project']['id']] = ucfirst($project_arr['Project']['projectname']);
		}
		
		return $project;
	}

}