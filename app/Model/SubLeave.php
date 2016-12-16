<?php
class SubLeave extends AppModel {
	public $name = 'SubLeave';
	
    public $belongsTo = array(
        'Leave' => array(
            'className' => 'Leave',
            'foreignKey' => 'leave_id'
        )
    );
}
