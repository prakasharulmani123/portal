<?php
class Leave extends AppModel {
	public $name = 'Leave';

    public $hasMany = array(
        'SubLeave' => array(
            'className' => 'SubLeave',
            'foreignKey' => 'leave_id',
            'dependent' => true
        )
    );
}
