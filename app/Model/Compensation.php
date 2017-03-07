<?php

class Compensation extends AppModel {

    var $name = 'Compensation';
    public $components = array('Session', 'Cookie', 'Email', 'RequestHandler');
    var $belongsTo = array(
        'DailyStatus' => array(
            'className' => 'DailyStatus',
            'foreignKey' => 'user_id',
            'dependent'    => true,
                          ),
        );
    
}
?>