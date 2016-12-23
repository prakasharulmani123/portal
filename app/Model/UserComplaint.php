<?php
class UserComplaint extends AppModel {
	public $name = 'UserComplaint';

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
        'Sender' => array(
            'className' => 'User',
			'foreignKey' => 'sender_id'
        ),
        'Receiver' => array(
            'className' => 'User',
			'foreignKey' => 'receiver_id'
        )
    );
}
