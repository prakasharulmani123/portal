<?php
class Email extends AppModel {
	public $name = 'Email';

    public $validate = array(
        'email' => array(
            'required' => array(
                'rule' => array('notEmpty', array('email', true)),
                'message' => 'Email Required'
            )
        ),
        'email' => array(
            'login' => array(
                'rule' => 'isUnique',
                'message' => 'Email Already Exists'
            )
        )
    );
}
