<?php
class UserAttendance extends AppModel {
	public $name = 'UserAttendance';

//    public $validate = array(
//        'sender_id' => array(
//            'required' => true,
//            'message' => 'Sender Required'
//        ),
//        'receiver_id' => array(
//            'required' => true,
//            'message' => 'Receiver Required'
//        ),
//        'reason' => array(
//            'required' => true,
//            'message' => 'Reason Required'
//        ),
//    );
    
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
			'foreignKey' => 'user_id'
        ),
    );
}
