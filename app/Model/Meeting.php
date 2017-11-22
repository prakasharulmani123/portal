<?php
class Meeting extends AppModel {
    public $test = 'Arivu';
	public $name = 'Meeting';
	
//	public $belongsTo = "Category";

    public $belongsTo = array(
        'Project' => array(
            'className' => 'Project',
			'foreignKey' => 'project_id'
        ),
        'User' => array(
            'className' => 'User',
			'foreignKey' => 'user_id'
        )
    );
}
